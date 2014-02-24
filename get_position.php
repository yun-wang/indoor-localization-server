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

	//$arr = get_defined_vars();
	//print_r($arr);
	//$postParameters = array_keys($_POST);
	//print_r($postParameters);
	$values = array_values($_POST);
	//print_r($values);
	$signalStrengths = array();
	$json_array = array();
	//$postParameters = array_keys($_POST);
	if(!empty($_POST)){
		//print_r($values);
		$json_array["coordinate"] = array();
		foreach($_POST as $parameter => $value){
			// NOTE mysql_real_escape_string function requires open database connection to work
			$cleanParameter = mysql_real_escape_string(strip_tags($parameter));
			$cleanValue = mysql_real_escape_string(strip_tags($value));
			//print_r($cleanParameter);
			//print_r($cleanValue);
			//print_r($parameter+"--"+$value+"\n");
			//echo " - $cleanParameter -- $cleanValue\n";
			//echo " - $parameter -- $value\n";
			// Ensure that the parameter is a mac address and the value is a number
			if(!preg_match('/^success$/', $cleanParameter)){
				$signalStrengths[$cleanParameter] = $cleanValue;
			}
			/*if(preg_match('/^[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}$/', $cleanParameter) &&
				preg_match('/^[0-9]{1,3}$/', $cleanValue)){
				// MAC addres in proper format
				// Value between 0 and 999;
				//echo " -- Successfully validated entry\n";
				$signalStrengths[$cleanParameter] = $cleanValue;
            // Save the data Work the magic
            // database name signal_strengths
			}*/
			/*else if(preg_match('/^success$/', $cleanParameter)){
				//echo " -- Got header\n";
				$success = $cleanValue;
			}*/
		}
		//print_r($signalStrengths);
	}
	else{
	
		echo json_encode('Something blew up');
	}
	
	//get JSON data
	//var_dump($_SERVER);
	/*if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Page was posted:<br />";
    foreach($_POST as $key=>$var) {
        echo "[$key] => $var<br />";
    }
	}*/
	/*print_r($_SERVER);
	echo "------------------------\n";
	print_r($_GET);
	echo "------------------------\n";
	print_r($_POST);
	echo "------------------------\n";
	//$json = $_SERVER['json'];
	//$json = file_get_contents('php://input');
	$json = $_POST['json'];
	var_dump($json);
	$signalStrengths = json_decode($json, true);
	var_dump($signalStrengths);*/
	//$mac_address = strtoupper($json_data->mac_address);
	//$user_name = $json_data->user_name;

    // Save the data Work the magic
    // database name signal_strengths

    // TODO Implement the method from Dr. Sichitius paper
    // http://www4.ncsu.edu/~mlsichit/Research/Publications/losecIJNS.pdf

    // For now it is just the method that was implemented before me... here it is:

    // 1. Retrieves all database entries for each MAC address (an entry is the signal strength and room number in which that AP was observed previously)

    // Holds a sum of all locations in range for each signal strengths
    // The one with the highest total should be the most, since it has
    // the most signal strengths/mac addresses within the threshold
    // of the strength recorded by the user
    $totals = array();
    $shortCut = 0;
    $shortCutId = 0;
	//print_r($signalStrengths);
    foreach ($signalStrengths as $mac => $strength){
        // 2. For each MAC address, it goes through each database entry and make a list of all the locations associated with the mac address entries that have similar signal strengths (within a threshold, if none found then the threshold is increased and the process repeated)
        // TODO Should we order it a specific way?
        $withinRange = array();
		//print_r($mac);
        $sql = "SELECT L_ID, STRENGTH FROM SIGNAL_STRENGTHS WHERE MAC_ADDRRESS = '$mac'";
        $res = mysql_query($sql);
		//print_r($res);
        // TODO Given that our algorithm has more points than they had, may need to knock 5 down
            #starts with a small range, and see's if it can find a value somwhere in the
            #training data for a given MAC within that range.  If not it will increase range
            #and keep on trying until it can.

        for($i = 5; $i < 100; $i += 5){

            while($row = mysql_fetch_row($res)){
                $locationId = $row[0];
                $signalStrength = $row[1];

                if(($signalStrength <= $strength + $i) && ($signalStrength >= $strength - $i)){
                    // Found a place within the signal strength within the threshold of ours
                    array_push($withinRange, $locationId);
                }
            }

            if(count($withinRange) > 0){
                // No need to expand the threshold
                break;
            }
        }

        foreach ($withinRange as $locationId){
            if(isset($totals[$locationId])){
                $totals[$locationId] += 1;
                if($totals[$locationId] > $shortCut){
                    $shortCut = $totals[$locationId];
                    $shortCutId = $locationId;
                }
            } else {
                $totals[$locationId] = 1;
                if($totals[$locationId] > $shortCut){
                    $shortCut = $totals[$locationId];
                    $shortCutId = $locationId;
                }
            }
			//print_r($shortCutId);
			//print_r("~~~~");
        }
		
		//print_r($shortCutId);
    }
        
    // 3. Given the list of potential rooms where each mac address has been seen previously with similar strengths, it finds the one that is the best match (room where the most number of similar strength AP's were seen)
    // 4. The user is then said to be at the lat/lon associated with that room, so the server sends these coordinates back

    if(count($totals) == 0){
		echo json_encode('Could not find any matches in the db');
        return;
    }

	// Select the point from the locations table
	$query = "SELECT LATITUDE, LONGITUDE FROM LOCATIONS WHERE L_ID = $shortCutId";
	$result = mysql_query($query);
	//print_r($result);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	}

	//check for empty result
	if($row = mysql_fetch_row($result)){
		//print_r($row[0]);
		//$json_array = array();
		$json_array["lat"] = $row[0];
		$json_array["lng"] = $row[1];
		
		//echoing JSON response
		echo json_encode($json_array);
	}
    // Use the shortcut, since we really don't need the full list with this scheme
    //$sql = "INSERT INTO player_positions (pseudonym, device_key, area_identifier, territory_identifier, latitude, longitude) SELECT '$pseudonym', '$deviceKey', area_identifier, territory_identifier, latitude, longitude FROM locations WHERE id=$shortCutId";
    //mysql_query($sql) || die(mysql_error());

    // Location has been stored, the webpage will pull the result soon :)
?>
