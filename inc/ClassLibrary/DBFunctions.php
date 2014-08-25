<?php

namespace ClassLibrary;

use \ClassLibrary\DBConnect;
use \ClassLibrary\Config;

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
		
        $result = mysql_query($query);
        if($result){
        	return true;
        } else {
        	return false;
        }
        
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
        if($result){
        	return true;
        } else {
        	return false;
        }
	}

	/**
	 ** Get Nearby Messiahs
	 **/
	public function getNearbyMessiah($PhoneNumber, $latitudeFrom, $longitudeFrom) {
		$PhoneNumber = "+" . $PhoneNumber;
		$query = "SELECT * FROM messiah_current_location WHERE phone_no != '{$PhoneNumber}'";
        $result = mysql_query($query);
        $UsersArray = array();;
        while ($row = mysql_fetch_array($result)) {
        	$userDistance = $this->haversineDistance($latitudeFrom, $longitudeFrom, $row['latitude'], $row['longitude']);
        	if($userDistance <= 2){
        		$getUserName = mysql_fetch_array(mysql_query("SELECT full_name FROM messiah_users WHERE phone_no = '{$row['phone_no']}'"));
        		//$userData = $row['phone_no'] . ', ' . $getUserName['full_name'] . ', ' . $row['latitude'] . ', ' . $row['longitude'];
        		$userData = array('PhoneNumber' => $row['phone_no'], 'FullName' => $getUserName['full_name'], 'Latitude' => $row['latitude'], 'Longitude' => $row['longitude']);
	        	array_push($UsersArray, $userData);
	        }
        }
        return $UsersArray;
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
	 ** Upadte GCM id in User tables after varification
	 **/
	public function setGCMIDForUser($PhoneNumber, $GCMID){
		$PhoneNumber = "+" . $PhoneNumber;
		$result = mysql_query("SELECT * FROM messiah_users WHERE phone_no = '{$PhoneNumber}'") or die(mysql_error());
		// check for result 
		$no_of_rows = mysql_num_rows($result);
		if ($no_of_rows == 0) {
			return false;
		} else {
			//Get verification code for the already existing record
			if(!empty($GCMID)){
				$query = "UPDATE messiah_users SET
							gcm_id = '{$GCMID}'
						  WHERE phone_no = '{$PhoneNumber}'";
				$result = mysql_query($query);
				if($result){
					$message = array('message' => "Welcome to Messiah community");
					$sendWelcomeMsg = $this->send_notification($GCMID, $message);
				}
				return true;
			} else {
				return false;
			}
		}
		return false;
	}

	/**
	 ** Send Request to User using their GCM ID
	 **/
	public function sendGCMRequest($MyPhoneNumber, $HisPhoneNumber){
		$MyPhoneNumber = "+" . $MyPhoneNumber;
		$HisPhoneNumber = "+" . $HisPhoneNumber;
		$getMyLocation = mysql_fetch_array(mysql_query("SELECT * FROM messiah_current_location WHERE phone_no = '{$MyPhoneNumber}'")) or die(mysql_error());
		$MyLatitude = $getMyLocation['latitude'];
		$MyLongitude = $getMyLocation['longitude'];

		$getHisGCMID = mysql_fetch_array(mysql_query("SELECT * FROM messiah_users WHERE phone_no = '{$HisPhoneNumber}'")) or die(mysql_error());
		$HisGCMID = $getHisGCMID['gcm_id'];

		$message = array("message" => "Test Message");

	    $result = $this->send_notification($HisGCMID, $message);

	    return $result;
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
		
	
	private function haversineDistance($LatitudeFrom, $LongitudeFrom, $LatitudeTo, $LongitudeTo) {
		$Rm = 3961; // mean radius of the earth (miles) at 39 degrees from the equator
		$Rk = 6372.8; // mean radius of the earth (km) at 39 degrees from the equator
						
		// convert coordinates to radians
		$lat1 = deg2rad($LatitudeFrom);
		$lon1 = deg2rad($LongitudeFrom);
		$lat2 = deg2rad($LatitudeTo);
		$lon2 = deg2rad($LongitudeTo);
				
		// find the differences between the coordinates
		$dlat = $lat2 - $lat1;
		$dlon = $lon2 - $lon1;
				
		// here's the heavy lifting
		$a  = pow(sin($dlat/2),2) + cos($lat1) * cos($lat2) * pow(sin($dlon/2),2);
		$c  = 2 * atan2(sqrt($a),sqrt(1-$a)); // great circle distance in radians
		$dm = $c * $Rm; // great circle distance in miles
		$dk = $c * $Rk; // great circle distance in km
				
		// round the results down to the nearest 1/1000
		$mi = round($dm, 9);
		$km = round($dk, 9);

		return $km;
		
	}

	/**
     * Sending Push Notification
     */
    function send_notification($ids, $data )
	{
		$url = 'https://android.googleapis.com/gcm/send';

	    $post = array('registration_ids' => $ids, 'data' => $data);

	    var_dump(json_encode($post));
	    $headers = array('Authorization: key=' . Config::$GOOGLE_API_KEY, 'Content-Type: application/json');
	    $ch = curl_init();

	    curl_setopt( $ch, CURLOPT_URL, $url );
	    curl_setopt( $ch, CURLOPT_POST, true );
	    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($post));

	    $result = curl_exec( $ch );
	    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    if ( curl_errno( $ch ) )
	    {
	        echo 'GCM error: ' . curl_error( $ch );
	    }

	    curl_close( $ch );
		
		echo $status;
        if($result == '')
            echo "Empty Message";

        $lines = explode("\n", $result);
        $responseParts = explode('=', $lines[0]);

		die(var_dump($responseParts));
	    return $result;
	}   
}
