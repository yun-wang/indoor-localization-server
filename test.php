<?php
//$arr = get_defined_vars();
//print_r($arr);
//var_dump($_SERVER['REQUEST_METHOD']);
//var_dump($_POST);
//var_dump($_REQUEST);
	$json_array = array();
	if(!empty($_POST)){
		//$postParameters = array_keys($_POST);
		$json_array["test"] = array();
	foreach($_POST as $parameter => $value){
        // NOTE mysql_real_escape_string function requires open database connection to work
        //$cleanParameter = mysql_real_escape_string(strip_tags($parameter));
		//echo $parameter;
        //$cleanValue = mysql_real_escape_string(strip_tags($value));
		//echo $value;
        //echo " - $cleanParameter -- $cleanValue\n";
		$points_array = array();
		$points_array["val1"] = $parameter;
		$points_array["val2"] = $value;
		array_push($json_array["test"], $points_array);
	}
	echo json_encode($json_array);
	}
	else{
		echo json_encode('Something blew up');
	}
?>