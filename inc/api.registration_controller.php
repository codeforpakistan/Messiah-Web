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
  if ((isset($_GET['FullName']) && $_GET['FullName'] != '') && (isset($_GET['PhoneNumber']) && $_GET['PhoneNumber'] != '')) {
    // get tag
    $FullName = $_GET['FullName'];
    $PhoneNumber = $_GET['PhoneNumber'];

    // include db handler
    $db = new \ClassLibrary\DBFunctions();

    // response Array
    $response = array("Status" => 0);

    // check for user
    $user = $db->userInitialInsertAndLogin($FullName, $PhoneNumber);
    if ($user != false) {
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