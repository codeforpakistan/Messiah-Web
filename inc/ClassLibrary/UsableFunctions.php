<?php
namespace ClassLibrary;

use \ClassLibrary\DBConnect;

class UsableFunctions {

  public static function get_current_url(){
      //Getting URL of current webpage
    $currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    if ($_SERVER["SERVER_PORT"] != "80") {
      $currentURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
      $currentURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $currentURL;
  }

  public static function mysql_prep($value = NULL) {
      // Function for skiping slashes
    $magic_quotes_active = get_magic_quotes_gpc();
    $php_version = function_exists("mysql_real_escape_string");
    
    if ($php_version) {
      if ($magic_quotes_active) {
        $value = stripslashes($value);
      }
      $value = mysql_real_escape_string($value);
    } else {
      if (!$magic_quotes_active) {
        $value = addslashes($value);
      }
    }
    return $value;
  }
  
  public static function redirect_to( $location = NULL ) {
      // Function to redirect to other pages
    if ($location != NULL) {
      header("Location: {$location}");
      exit;
    }
  }

  public static function check_required_fields($required_array) {
    // Function for form validation i.e to validate that required data fields are entered
    $field_errors = array();
    foreach($required_array as $fieldname) {
      if (!isset($_POST[$fieldname]) || empty($_POST[$fieldname])) { 
        $field_errors[] = $fieldname; 
      }
    }
    return $field_errors;
  }
  
  public static function check_max_field_lengths($field_length_array) {
    //Function to validate the size of the fields of form
    $field_errors = array();
    foreach($field_length_array as $fieldname => $maxlength ) {
      if (strlen(trim(self::mysql_prep($_POST[$fieldname]))) > $maxlength) { $field_errors[] = $fieldname; }
    }
    return $field_errors;
  }

  public static function check_query($result_set) {
    // Function for checking wether the query is correct or not!!!
    if (!$result_set) {
      die("Database Query Failed: " . mysql_error());
    }
  }
}