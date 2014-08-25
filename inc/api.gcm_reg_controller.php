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
  if ((isset($_GET['PhoneNumber']) && $_GET['PhoneNumber'] != '') && (isset($_GET['GCMID']) && $_GET['GCMID'] != '')) {
    // get tag
    $PhoneNumber = $_GET['PhoneNumber'];
    $GCMID = $_GET['GCMID'];

    // include db handler
    $db = new \ClassLibrary\DBFunctions();

    // response Array
    $response = array("Status" => 0);

    // check for user
    $setGCM = $db->setGCMIDForUser($PhoneNumber, $GCMID);
	if ($setGCM != false) {
      // user found
      // echo json with success = 1
      $response["Status"] = 1;
      echo json_encode($response);
    } else {
      // user not found
      // echo json with error = 1
      $response["Status"] = 0;
      echo json_encode($response);
    }
  } else {
    echo "Access Denied";
  }