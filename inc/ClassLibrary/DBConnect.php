<?php 
  
  namespace ClassLibrary;

  use \ClassLibrary\Config;

  class DBConnect {
    // constructor
    function __construct() {
      
    }

    // destructor
    function __destruct() {
      // $this->close();
    }

    // Connecting to database
    public function connect() {
      // connecting to mysql
      $con = mysql_connect(Config::$DB_SERVER, Config::$DB_USER, Config::$DB_PASS) or die(mysql_error());
      // selecting database
      if($con){
        mysql_select_db(Config::$MESSIAH_DB);
      }
 
      // return database handler
      return $con;
    }
 
    // Closing database connection
    public function close() {
      mysql_close();
    }
  }
