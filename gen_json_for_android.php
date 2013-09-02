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

	// Select all the rows in the locations table
	$query = "SELECT * FROM LOCATIONS WHERE 1";
	$result = mysql_query($query);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}
	
	// Initialize variables
	$json_array = array();

	//check for empty result
	if(mysql_num_rows($result) > 0){
		//loop through all results
		//points node
		$json_array["points"] = array();
		
		while($row = mysql_fetch_array($result)){
			//temp user array
			$points_array = array();
			$points_array["lat"] = $row["LATITUDE"];
			$points_array["lng"] = $row["LONGITUDE"];
			
			array_push($json_array["points"], $points_array);
		}
		//success
		$json_array["success"] = 1;
		
		//echoing JSON response
		echo json_encode($json_array);
	}
	else{
		//no points found
		$json_array["success"] = 0;
		echo json_encode($json_array);
	}
?>