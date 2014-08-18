<?php

namespace ClassLibrary;

use \ClassLibrary\DBConnect;

class DBFunctions {
	private $db;

		//put your code here
		// constructor
	function __construct() {
			// connecting to database
		$this->db = new DBConnect();
		$this->db->connect();
	}

		// destructor
	function __destruct() {
		
	}

	/**
	 * Get user by phone_no
	 */
	public function userInitialInsertAndLogin($FullName, $PhoneNumber) {
		$PhoneNumber = "+" . $PhoneNumber;
		$result = mysql_query("SELECT * FROM messiah_users WHERE phone_no = '{$PhoneNumber}'") or die(mysql_error());
		// check for result 
		$no_of_rows = mysql_num_rows($result);
		$GUID = NULL;
		$verificationCode = NULL;
		if ($no_of_rows == 0) {
			// Insert Record, Generate Verification No
			$GUID = $this->generate_guid();
			$verificationCode = $this->generate_verification_code();
			$query = "INSERT INTO messiah_users (
                  guid, phone_no, full_name, verification_code
                ) VALUES (
                  '{$GUID}', '{$PhoneNumber}', '{$FullName}', '{$verificationCode}'
                )";
       $result = mysql_query($query) or die(mysql_error());
		} else {
			//Get verification code for the already existing record
			$row = @mysql_fetch_array($result);
			$GUID = $row['guid'];
			$verificationCode = $this->generate_verification_code();
			$query = "UPDATE messiah_users SET
						verification_code = '{$verificationCode}'
					  WHERE guid = '{$GUID}'";
			$result = mysql_query($query);

		}
		//Send Verification Code
		$textmessage = "Your Verification Code is : \n{$verificationCode}";
		$smileAPIObject = new \ClassLibrary\SmileAPI();
		$smileAPIObject->get_session();
		$smileAPIObject->send_sms($PhoneNumber, '8333', $textmessage);
		return true;
	}

	/**
	 ** Insert Function for Current location on Registration
	 **/
	public function insertCurrentLocOnReg($PhoneNumber, $Latitude, $Longitude){
		$PhoneNumber = "+" . $PhoneNumber;
		$query = "INSERT INTO messiah_current_location (
                  	phone_no, latitude, longitude
                  ) VALUES (
                  	'{$PhoneNumber}', '{$Latitude}', '{$Longitude}'
                  )";
		
        $result = mysql_query($query) or die(mysql_error());
        return true;
	}

	/**
	 ** Update Function for Current location on Map Load
	 **/
	public function updateCurrentLocOnMapLoad($PhoneNumber, $Latitude, $Longitude){
		$PhoneNumber = "+" . $PhoneNumber;
		$query = "UPDATE messiah_current_location SET
					latitude = '{$Latitude}', 
					longitude = '{$Longitude}'
				  WHERE phone_no = '{$PhoneNumber}'";
		$result = mysql_query($query);
		return true;
	}

	/**
	 ** Get Nearby Messiahs
	 **/
	public function getNearbyMessiah($LatitudeFrom, $longitudeFrom) {

	}

	/**
	 ** Authorize using verification code and then change verification code
	 **/
	public function verifyUsingCode($PhoneNumber, $verificationCode){
		$PhoneNumber = "+" . $PhoneNumber;
		$result = mysql_query("SELECT * FROM messiah_users WHERE phone_no = '{$PhoneNumber}'") or die(mysql_error());
		// check for result 
		$no_of_rows = mysql_num_rows($result);
		$GUID = NULL;
		if ($no_of_rows == 0) {
			return false;
		} else {
			//Get verification code for the already existing record
			$row = @mysql_fetch_array($result);
			$GUID = $row['guid'];
			if($verificationCode === $row['verification_code']){
				$query = "UPDATE messiah_users SET
							verification_code = NULL
						  WHERE guid = '{$GUID}'";
				$result = mysql_query($query);
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Check user is existed or not
	 */
	public function UserExists($PhoneNumber) {
		$result = @mysql_query("SELECT * FROM messiah_users WHERE phone_no = '{$PhoneNumber}'");
		$no_of_rows = @mysql_num_rows($result);
		if ($no_of_rows > 0) {
			// user existed 
			return true;
		} else {
			// user not existed
			return false;
		}
	}

	private function generate_guid(){
		if (function_exists('com_create_guid')){
			return trim(com_create_guid(), "{, }");
		} else {
	    	mt_srand((double)microtime()*10000);
	    	$charid = strtoupper(md5(uniqid(rand(), true)));
				$hyphen = chr(45);
				$uuid = chr(123)
					      . substr($charid, 0, 8).$hyphen
					      . substr($charid, 8, 4).$hyphen
					      . substr($charid,12, 4).$hyphen
					      . substr($charid,16, 4).$hyphen
					      . substr($charid,20,12)
	              		  . chr(125);
	    	return  trim($uuid, "{, }");
		}
	}

	private function generate_verification_code(){
		$characters = 'ABCDEFGHIJ01234567890KLMNOPQRSTUVWXYZ0987654321234567890ABCDEFGHIJ01234567890KLMNOPQRSTUVWXYZ';

		$string = '';

		for ($i = 0; $i < 7; $i++) {
		    $string .= $characters[rand(0, strlen($characters) - 1)];
		}   

		return $string;
	}

	public function haversineDistanceRadians($lat1, $lon1, $lat2, $lon2) {
	    $radiusOfEarth = 6371;// Earth's radius in meters.
	    
        $x1 = $lat2 - $lat1;
        $dLat = deg2rad($x1);
        $x2 = $lon2 - $lon1;
        $dLon = deg2rad($x2);
        $a = sin($dLat / 2) * sin($dLat / 2) +
          cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
          sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = $radiusOfEarth * $c;
        return $d;
	}
}