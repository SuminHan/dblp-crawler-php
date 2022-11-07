<?php
echo $_POST["bhtkey"];
exec('/4TBSSD/miniconda3/envs/TF/bin/python /var/www/html/dblp/aggregate_json.py', $output, $retval);
echo "<br />";
echo "Returned with status $retval and output:<br/>";
echo "<pre>";
print_r($output);
echo "</pre>";

header( "refresh:3;url=/dblp/dblpcrawler.php" );
?>
