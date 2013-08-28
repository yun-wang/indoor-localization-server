<html>
<head>
</head>
<body>
<?php
	$mysql_hostname = 'mysql-people.eos.ncsu.edu:3306';
	$mysql_database_name = 'Pywang51';
	$php_admin_username = 'Pywang51A';
	$php_admin_password = 'e*-Jn@HfrD3lXcl3';
	
	// Connect to database
	$db_connection = mysql_connect($mysql_hostname, $php_admin_username, $php_admin_password);
	if (!$db_connection)
	{
		die("Error connecting to database server '$mysql_hostname': " . mysql_error() . "");
	}
	$db_connection_select = mysql_select_db($mysql_database_name);
	if (!db_connection_select)
	{
		die("Error connecting to database '$mysql_database_name': " . mysql_error() . "");
	}

	// Drop all tables
	// From http://www.ebrueggeman.com/blog/drop-all-tables-in-mysql
	// (inherited from Andrew Williams)
	$sql = "SHOW TABLES FROM $mysql_database_name";
	if($result = mysql_query($sql)){
  		/* add table name to array */
  		while($row = mysql_fetch_row($result)){
    		$found_tables[]=$row[0];
  	}}else{
  		die("Error, could not list tables. MySQL Error: " . mysql_error());
	}
	/* loop through and drop each table */
	foreach($found_tables as $table_name){
  		$sql = "DROP TABLE $mysql_database_name.$table_name";
  		if($result = mysql_query($sql)){
    		echo "Table '$table_name' deleted successfully.<br />";
  		}  else{
    		echo "Error deleting '$table_name'. MySQL Error: " . mysql_error() . "";
  		}
	}
	
	// Create LOCATIONS table
	$sql = "CREATE TABLE LOCATIONS (L_ID INT(11) NOT NULL AUTO_INCREMENT, 
								    LATITUDE VARCHAR(20) NOT NULL, 
								    LONGITUDE VARCHAR(20) NOT NULL, 
								    PRIMARY KEY (L_ID)) ENGINE=MyISAM";
								   
	mysql_query($sql) || die("Error creating table: " . mysql_error() . "");
	echo "Created table 'LOCATIONS'<br />";
	
	// Create SIGNAL_STRENGTHS table
	$sql = "CREATE TABLE SIGNAL_STRENGTHS (S_ID INT(11) NOT NULL AUTO_INCREMENT, 
										   MAC_ADDRRESS VARCHAR(30) NOT NULL, 
										   STRENGTH INT(11) NOT NULL,
										   L_ID INT(11) NOT NULL,
										   FOREIGN KEY (L_ID) REFERENCES LOCATIONS ON DELETE CASCADE,
										   PRIMARY KEY (S_ID, L_ID)) ENGINE=MyISAM";
										   
	mysql_query($sql) || die("Error creating table: " . mysql_error() . "");
	echo "Created table 'SIGNAL_STRENGTHS'<br />";
	
	//// Add in the signal strength measurements
	//$fileName = "data_files/areas.txt";
	//$handle = fopen($fileName,"rb");
	//if($handle == FALSE){
    //// Couldn't open the file
    //echo "Error, couldn't open file $fileName<br />";
	//} else {
	////    while($data = fgetcsv($handle)){
    //if($data = fgetcsv($handle)){
      //  // Just add the first floor for now
        //$mapName = $data[2];
        //$fileName2 = "data_files/" . $mapName . ".sql";
        //$handle2 = fopen($fileName2, "rb");
        //if($handle2 == FALSE){
         //   // Couldn't open the file
           // echo "Error, couldn't open file $fileName2<br />";
        //} else {
          //  while($data2 = fgets($handle2)){
            //    $sql = chop($data2);
              //  mysql_query($sql) || die(mysql_error());
                //echo "Executed: $sql<br />\n";
            //}
            //fclose($handle2);
        //}
    //}
    //fclose($handle);
}
	
?>
</body>
</html>