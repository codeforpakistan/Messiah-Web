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
  if ((isset($_GET['Latitude']) && $_GET['Latitude'] != '') && (isset($_GET['Longitude']) && $_GET['Longitude'] != '') && (isset($_GET['PhoneNumber']) && $_GET['PhoneNumber'] != '')) {
	// get tag
	$Latitude = $_GET['Latitude'];
	$Longitude = $_GET['Longitude'];
	$PhoneNumber = $_GET['PhoneNumber'];

	// include db handler
	$db = new \ClassLibrary\DBFunctions();

	// response Array
	$response = array("Status" => 0);
	$currentLocation = NULL;

	// Update Current Location
	$currentLocation = $db->updateCurrentLocOnMapLoad($PhoneNumber, $Latitude, $Longitude);

	//Get Nearby Messiahs here
	$NearbyMessiah = $db->getNearbyMessiah($Latitude, $Longitude); ;
	
	if ($currentLocation != false) {
	  // Location Updated
	  // echo json with success = 1
	  $response["Status"] = 1;
	  echo json_encode($response);
  } else {
	  // Location not Updated
	  // echo json with error = 1
	  $response["Status"] = 0;
	  echo json_encode($response);
  }
} else {
	echo "Access Denied";
}