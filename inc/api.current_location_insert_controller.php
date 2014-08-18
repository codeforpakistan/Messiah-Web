<?php

require 'autoload.php';
  /* --------------------------------------
  /* File to handle all API requests
  /* Accepts GET and POST
  /* --------------------------------------
  /* Each request will be identified by TAG
  /* Response will be JSON data
  /* --------------------------------------
  /* check for POST request 
  /*/
  if ((isset($_GET['Operation'])) && (isset($_GET['Latitude']) && $_GET['Latitude'] != '') && (isset($_GET['Longitude']) && $_GET['Longitude'] != '') && (isset($_GET['PhoneNumber']) && $_GET['PhoneNumber'] != '')) {
	// get tag
	$Operation = $_GET['Operation'];
	$Latitude = $_GET['Latitude'];
	$Longitude = $_GET['Longitude'];
	$PhoneNumber = $_GET['PhoneNumber'];

	// include db handler
	$db = new \ClassLibrary\DBFunctions();

	// response Array
	$response = array("Status" => 0);
	$currentLocation = NULL;
	// check for user
	if($Operation === "Insert"){
		$currentLocation = $db->insertCurrentLocOnReg($PhoneNumber, $Latitude, $Longitude);
	} else if($Operation === "Update"){
		$currentLocation = $db->updateCurrentLocOnMapLoad($PhoneNumber, $Latitude, $Longitude);
	} else{
		//Incorrect Operation Selected
		$response["Status"] = 0;
	  	echo json_encode($response);
	}
	if ($currentLocation != false) {
	  // Location Inserted/Updated
	  // echo json with success = 1
	  $response["Status"] = 1;
	  echo json_encode($response);
  } else {
	  // Location not Inserted/Updated
	  // echo json with error = 1
	  $response["Status"] = 0;
	  echo json_encode($response);
  }
} else {
	echo "Access Denied";
}