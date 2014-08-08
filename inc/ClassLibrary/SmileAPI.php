<?php
  
  namespace ClassLibrary;

  class SmileAPI {
    public function get_session() {
      $username = "6";      //Put your API Username here
      $password = "smileit8531";  //Put your API Password here
      $data = file_get_contents("http://api.smilesn.com/session?username=" . $username . "&password=" . $password);
      $data = json_decode($data);
      $sessionid = $data->sessionid;
      $file2 = fopen('session.txt', 'w');
      $file1 = fopen('session.txt', 'a');
      fputs($file1, $sessionid);
      fclose($file1);
      return $sessionid;
    }

    public function send_sms($receivenum, $sendernum, $textmessage) {
      $receivenum = urlencode($receivenum);
      $sendernum = urlencode($sendernum);
      $textmessage = urlencode($textmessage);
      
      $session_file = file("session.txt");
      $session_id = trim($session_file[0]);
  
      if(empty($session_id)) {
        $session_id = $this->get_session();
      }
  
      $data=file_get_contents("http://api.smilesn.com/sendsms?sid=" . $session_id . "&receivenum=" . $receivenum."&sendernum=8333&textmessage=" . $textmessage);
  
      $data2 = json_decode($data);
      $response_status = $data2->status;
  
      #===========================================================================#
      # START - IF SESSION EXPIRED IS RETURN, GENERATE ANOTHER SESSION & RETRY  #
      #===========================================================================#
      if($response_status == "SESSION_EXPIRED") {
        $session_id = $this->get_session();
        $data=file_get_contents("http://api.smilesn.com/sendsms?sid=" . $session_id . "&receivenum=" . $receivenum . "&sendernum=8333&textmessage=" . $textmessage);
      }
      #===========================================================================#
      # END - IF SESSION EXPIRED IS RETURN, GENERATE ANOTHER SESSION & RETRY  #
      #===========================================================================#
      return $data;
    }
    public function receive_sms() {
      $session_file = file("session.txt");
      $session_id = trim($session_file[0]);
      if(empty($session_id)) {
        $session_id = $this->get_session();
    }
  
      $data = file_get_contents("http://api.smilesn.com/receivesms?sid=" . $session_id);
  
      $data2 = json_decode($data);  
      $response_status = $data2->status;
      
      #===========================================================================#
      # START - IF SESSION EXPIRED IS RETURN, GENERATE ANOTHER SESSION & RETRY  #
      #===========================================================================#
      if($response_status == "SESSION_EXPIRED") {
        $session_id = $this->get_session();
        $data=file_get_contents("http://api.smilesn.com/receivesms?sid=" . $session_id);
      }
      #===========================================================================#
      # END - IF SESSION EXPIRED IS RETURN, GENERATE ANOTHER SESSION & RETRY  #
      #===========================================================================#
      return $data;
    }
  }
  #=======================================================================#
  # START - Smile API Class                       #
  #=======================================================================#
  ?>