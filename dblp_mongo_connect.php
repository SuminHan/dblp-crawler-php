<?php
   // connect to mongodb
   echo "Hello <br>";
   $m = new MongoClient("mongodb://localhost:27017/");
   echo "Connection to database successfully";
	
   // select a database
   $db = $m->dblp_crawled;
   echo "Database mydb selected";
   $collection = $db->papers;
   echo "Collection selected succsessfully";
   $cursor = $collection->find_one();
   // iterate cursor to display title of documents
	
   foreach ($cursor as $document) {
      echo $document["title"] . "\n";
   }
?>
