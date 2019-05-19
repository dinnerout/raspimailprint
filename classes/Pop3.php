<?php

class Pop3 {

	var $strStatus;
	var $pop3;
	var $arrMessages;
	var $arrMessage;

	function __construct(){
		$this->strStatus = array();
		$this->pop3 = 0;
		$arrMessages = $arrMessage = '';
	}

	function open($strServer, $intPort = 110) { 
		$this->pop3 = fsockopen($strServer, $intPort); 
		if (!is_resource($this->pop3)) return FALSE; 
		$line = fgets($this->pop3, 1024); 
		return $this->getresult($line);
	} 

	function user($strUser) { 
		fputs($this->pop3, "USER $strUser\r\n"); 
		$line = fgets($this->pop3, 1024); 
		return $this->getresult($line);
	} 

	function pass($strPass) { 
		fputs($this->pop3, "PASS $strPass\r\n"); 
		$line = fgets($this->pop3, 1024); 
		return $this->getresult($line);
	}
		
	function mess() {
		fputs($this->pop3, "LIST\r\n"); 
		$line = fgets($this->pop3, 1024); 
		if ($this->getresult($line)) {
			unset($this->arrMessages);
			while(substr($line = fgets($this->pop3, 1024),0,1) != '.')
			{ 
				$this->arrMessages[] = $line; 
			}
			return TRUE;
		} else {
			return FALSE;
		}
	} 

	function retr($strMessage) { 
		list($intMessage) = explode(' ', $strMessage);
		fputs($this->pop3, "RETR $intMessage\r\n"); 
		$line = fgets($this->pop3, 1024);
		$ll = $line;
		
		if ($this->getresult($line)) {
			unset($this->arrMessage);
			
			while(true){
				$line = fgets($this->pop3, 1024);
				$this->arrMessage[] = $line;
				
				if( substr($line,0,1)=='.' && substr($ll,0,2)=='--' ) break 1;
				$ll = $line; 
			}
	 		return TRUE; 
		} else {
			echo "false";
			return FALSE;
		}
	} 

	function dele($strMessage) {
		list($intMessage) = explode(' ', $strMessage);
		fputs($this->pop3, "DELE $intMessage\r\n"); 
	 	$line = fgets($this->pop3, 1024); 
	 	// echo "....".$line.".....";
		return TRUE; 
	}

	function quit() { 
		fputs($this->pop3, "QUIT\r\n"); 
		$line = fgets($this->pop3, 1024); 
		return $this->getresult($line);
	} 

	function getresult($line)
	{
	    $this->strStatus = substr($line, 0, 1024); 
    	if (substr($this->strStatus, 0, 1) != '+') {
			return FALSE; 
		} else {
			return TRUE;
		}
	}
	
	function showresult()
	{
		 echo '<code style="color:red">';
		 print_r($this->strStatus);
		 echo '</code><br>';
	}

}