<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">

<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    
<title>Paper Search Result</title>
    
    
<!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/datetime/1.1.1/js/dataTables.dateTime.min.js"></script> -->
    
    
</head>
<body style='padding:10px'>
<?php
	session_start();
	if(!isset($_SESSION['user_pw'])) {
		echo "<meta http-equiv='refresh' content='0;url=../login.php'>";
		exit;
	}
?>
<h1>Paper Search (<a href='/dblp/dblpcrawler.php'>DBLP crawler</a>)</h1>

<!--div id='conf'></div-->
<div id='mystats'></div>
<div id='myfilter'></div>
<br />

    
<table id="example" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Venue</th>
            <th>Year</th>
            <th>Title</th>
            <th>EE</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Venue</th>
            <th>Year</th>
            <th>Title</th>
            <th>EE</th>
        </tr>
    </tfoot>
</table>



<script>
function HtmlEncode(s)
{
  var el = document.createElement("div");
  el.innerText = el.textContent = s;
  s = el.innerHTML;
  return s;
}

$(document).ready(function() {
	var data = null;
	$.get('/dblp/summary_papers.json', function(original_data){
		data = original_data;
		console.log(data);
		
		var conf_list = {};
		var year_list = {};
		var conf_count = {};
		for (i in data){
			var item = data[i];
			var venue = item['venue'];
			var year = Number(item['year']);
			var key = venue + year;
			if (!conf_count.hasOwnProperty(key)){
				conf_count[key] = 0;
			}
			conf_count[key] += 1;

			if (!conf_list.hasOwnProperty(item['venue'])){
				conf_list[item['venue']] = 0;
			}
			conf_list[item['venue']] += 1;

			if (!year_list.hasOwnProperty(item['year'])){
				year_list[year] = 0;
			}
			year_list[year] += 1;
		}
		
		var stattext = '<b>CONF           ';
		for (y in year_list){
			stattext += '\t'+y;
		}
		stattext += '</b>\n';
		for (c in conf_list){
			cname = c.substring(0,15);
			for (var j = 0; j < 15-cname.length; j++)
				cname += ' ';
			stattext += cname;
			
			for (y in year_list){
				if (conf_count.hasOwnProperty(c+y)){
					stattext += '\t'+conf_count[c+y];
				}
				else{
					stattext += '\t'+'-';
				}
			}
			stattext += '\n';
		}

		$('#mystats').append('<pre>'+stattext+'</pre>');
		console.log(conf_count);
		
		var conf_checkboxes = '';
		for (c in conf_list){
			var checkbox = '<label><input type="checkbox" id="scales" name="'+c+'" checked />'+
							c+'</label>';
			conf_checkboxes += checkbox;
		}
		$('#myfilter').append('<div id="checkboxes">'+conf_checkboxes+'</div>');

		var select_from = '<select name="from_year" id="from_year">';
		var select_until = '<select name="until_year" id="until_year">';
		for (y in year_list){
			var option = '<option value=" name="'+y+'">'+y+'</option>';
			select_from += option;
			select_until += option;
		}
		select_from += '</select>'
		select_until += '</select>'
		$('#myfilter').append('<div>year_from: '+select_from + ', year_until:' +select_until+'</div>');

		$('#myfilter #from_year option:first').attr('selected', 'selected');
		$('#myfilter #until_year option:last').attr('selected', 'selected');


		function filter_condition(){
			var selected_conf = [];
			$('div#checkboxes input[type=checkbox]').each(function() {
			   if ($(this).is(":checked")) {
				   selected_conf.push($(this).attr('name'));
			   }
			});
			var from_year = $('#from_year option:selected').text();
			var until_year = $('#until_year option:selected').text();
			var new_list = []
			for (var i in original_data){
				item = original_data[i];
				if (selected_conf.includes(item['venue']) 
						& from_year <= Number(item['year']) 
						& Number(item['year']) <= until_year){
					new_list.push(item);
				}
			}
			return new_list;
		};


		
		var table = $('#example').DataTable( {
			/*ajax: {
				url: '/summary_papers.json',
				dataSrc: ''
			},*/
			dom: 'Blfrtip',
			buttons: [
				'copy', 'csv', 'excel', 'pdf', 
			],
			data: data,
			columns: [
				{ data: 'venue' },
				{ data: 'year' },
				{ data: 'title' },
				{ data: "ee",
				 render: function(data, type, row, meta){
					if(type === 'display'){
						data = '<a href="' + data + '" target="_blank">link</a>';
					}
					return data;
				 }
				} 
			],
			order: [[ 1, "desc" ], [ 0, 'asc']],
		} );
		table.columns.adjust().draw();
		//var data = table.buttons.exportData();
		$('#example_filter input').unbind();
		$('#example_filter input').bind('keyup', function(e) {
			if(e.keyCode == 13) {
				table.search(this.value).draw();
			}
			});
		

		$('div#checkboxes input[type=checkbox]').change(function(){
			data = filter_condition();
			table.clear().draw();
			table.rows.add(data).draw();
			console.log('added');
		});
		$('#from_year').change(function(){
			data = filter_condition();
			table.clear().draw();
			table.rows.add(data).draw();
			console.log('added');
		});
		$('#until_year').change(function(){
			data = filter_condition();
			table.clear().draw();
			table.rows.add(data).draw();
			console.log('added');
		});

	});



});
</script>
</body>
</html>
