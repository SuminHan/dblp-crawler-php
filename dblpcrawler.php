<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
    
<title>DBLP Crawler</title>

    
    
<!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/datetime/1.1.1/js/dataTables.dateTime.min.js"></script> -->
    
    
</head>
<body>

<?php
	session_start();
	if(!isset($_SESSION['user_pw'])) {
		echo "<meta http-equiv='refresh' content='0;url=login.php'>";
		exit;
	}
?>
<h1>DBLP Crawler (<a href="/dblp/papersearch.php">HOME</a>)</h1>

<iframe src="https://dblp.org/" height="500px" width="100%" title="Iframe Example"></iframe>
<form method="post" action="/dblp/run_dblp_script.php">
  DBLP BHT key: <input type="text" name="bhtkey">
  <input type="submit">
</form>

<a href="/dblp/run_aggregation.php">Aggregate Result</a>
<img src="dblp_bht_key.png" width='100%'></img>

</body>
</html>
