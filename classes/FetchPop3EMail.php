<?php
/**
 * fetches Emails from POP3 account
 * 
 * @author Sebastian.Martens
 * @svn $Id$
 * @copyright Sebastian Martens (c)2015
 */
class FetchPop3EMail{
	
	private $_popServer;
	private $_username;
	private $_password;
	
	private $_mailList;
	
	private $_popBox;
	private $_error = false;
	
	private $_messages = array();
	private $_deleteAttachments = true; // if true messages will be removed from the mailbox
	
	private $reportText = "";
	
	private $validImportMimeTypes = array('application/xml');
	
	/**
	 * class constructor
	 * @param $popServer - address of postbox server
	 * @param $popUser - username of postbox
	 * @param $popPassword - password for postbox
	 */
	function __construct( $popServer, $popUser, $popPassword ){
		
		$this->_popServer = $popServer;
		$this->_username = $popUser;
		$this->_password = $popPassword;
		
		$this->_mailList = array(); // location for fetched mails
		
	}
	
	/**
	 * 
	 */
	private function in_array_first($array,$string){
		for($i=0; $i<count($array); $i++){
			if(substr($array[$i],0,strlen($string))==$string) return $i;
		}
		return false;
	}
	
	/**
	 * connects to POP mailbox
	 * @return Boolean - true if connection established
	 */
	private function connectPostbox(){
		$this->_popBox = new Pop3();
		// connect to server
		if (!$this->_popBox->open( $this->_popServer )) { 
   			echo "[ERROR] Connection to POP3 Server failed.\n"; 
   			$this->_popBox->showresult();
   			return false;
		} else {
   			echo "[OK] Connection to POP3 Server established.\n";
   			$this->_popBox->showresult();
   			
   			// connect user
			if (!$this->_popBox->user( $this->_username )) { 
	   			echo "[ERROR] Unkown user\n"; 
	   			$this->_popBox->showresult();
			   	return false;
			} else {
	   			echo "[OK] User connected.\n";
	   			$this->_popBox->showresult();
	   			
	   			// connect by password
				if (!$this->_popBox->pass( $this->_password )) { 
	   				echo "[ERROR] Invalid Password\n"; 
	   				$this->_popBox->showresult();
   	   				return false;
				} else {
	   				echo "[OK] User password accepted.\n";
	   				$this->_popBox->showresult();
	   				return true;
				}
			}
		}
	}
		
	/**
	 * starts fetching the email
	 */
	public function fetchMails(){
		
		if( $this->connectPostbox() ){
			
			if (!$this->_popBox->mess()) { 
       			echo "[ERROR] Could not get messages\n"; 
		  		return false;
			} else {
				$this->_messages = $this->_popBox->arrMessages;
				echo "[OK] Got ".count( $this->_messages )." messages.\n";
	   			$this->_popBox->showresult();
	   			// save attachments
	   			$this->handleMessages();
			}
			
		}else{
			// unable to connect to mailbox
			echo "[ERROR] Could not connect\n";
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $array
	 * @param unknown_type $index
	 */
	private function get_next_empty($array,$index){
		for($i=$index; $i<count($array); $i++){
			if(	trim($array[$i])=="" ){  
					return $i;
			}
		}
		return count($array);
	}
	
	/**
	 * saves the attchement of an given
	 * @param Array $array - current mail data
	 */
	private function countAttachments( $array ){
		$filenames = 0;
		
		$filestart = -1;
		$fileend = -1;
		$filename = "";
		$boundary = "";
		
		for($i=0; $i<count($array); $i++){
			// filename
			$strposF = strpos(trim($array[$i]),'filename=');
			if($strposF!==false){
				$filename = date("YmdHis").'--'.str_replace('"', '', trim(substr(trim($array[$i]),($strposF+9))) );
				// $filename = substr($filename,0,strpos($filename,' '));
				
				$filestart = $this->get_next_empty($array,$i)+1;
				$fileend = $this->get_next_empty($array,$filestart+1)-1;
				
				$filecontent = "";
			}
			
			// encoding
			$strposE = strpos(trim($array[$i]),'Content-Transfer-Encoding:');
			if($strposE!==false){
				$encoding = trim(substr(trim($array[$i]),($strposE+26)));
			}
			
			// boundary
			$strposB = strpos($array[$i],'boundary=');
			if($strposB!==false){
				$boundary = trim(substr(trim($array[$i]),9,strlen(trim($array[$i]))));
			}
			
			// file content
			if($i>=$filestart && $i<=$fileend && $array[$i]!=$boundary){ 
				$filecontent .= $array[$i];
			}
			
			// save attachment
			if($i>$filestart && ($i==$fileend || $array[$i]==$boundary)){
				
				if( strlen($filecontent)>0 ){
					// $fh = @fopen(MAILATTACHMENTTARGETDIR.$filename,"w-");
					
					switch( strtolower($encoding) ){
						case "7bit": $decodedContent = ""; break;
						case "8bit": $decodedContent = ""; break;
						case "base64": $decodedContent = base64_decode( $filecontent ); break;
						case "quoted-printable": $decodedContent = quoted_printable_decode($filecontent); break;
					}
					
					$filenames++;

				}
			}
		}

		return $filenames;
	}
	
	/**
	 * handles each messages, checks the mail sender and starts the saving of 
	 * the attachments
	 */
	private function handleMessages(){
		global $flashDataMailImportValidSender;
		$strMessage = "";
		$smF = "";
		$sF = "";
		
		$errorMail = "";
		$okMail = "";
		
		// if messages existing
		if( count($this->_messages) ){
			echo "\n";
			
			print_r( $this->_messages );
			
			// for each message
			foreach($this->_messages as $strMessage) {
				// get message content
				if ($this->_popBox->retr($strMessage)) {
					
					print_r( $this->_popBox->arrMessage );
					
					
					// parse messages
					$x = $this->in_array_first($this->_popBox->arrMessage,"Status:");
					$read = (substr($this->_popBox->arrMessage[$x],8,2)=="RO");
					
					$x = $this->in_array_first($this->_popBox->arrMessage,"Subject:");
					$subject = substr($this->_popBox->arrMessage[$x],9);
					
					$x = $this->in_array_first($this->_popBox->arrMessage,"From:");
					
					$sender = strtolower(substr(trim($this->_popBox->arrMessage[$x]),strpos($this->_popBox->arrMessage[$x],'<')+1,-1));
					
					$message_start = $this->get_next_empty($this->_popBox->arrMessage, $x);
					$message_end = $this->get_next_empty($this->_popBox->arrMessage, ($message_start+1) );
					$message = "";
					
					for($mi=$message_start;$mi<=$message_end;$mi++){
						$message .= $this->_popBox->arrMessage[ $mi ];
					}
					$message = strip_tags( $message );
					
					$this->reportText .= "\n\n";
					$this->reportText .= "Sender: ".$sender."\n";
					$this->reportText .= "Subject: ".str_replace(array("\n","\t","\r"),'',$subject)."\n";
					$this->reportText .= "Attachments: ".$this->countAttachments( $this->_popBox->arrMessage )."\n";
					$this->reportText .= "Message: ". $message ."\n";
					/* 
					// check if mail sender is in valid sender list
					if( in_array(strtolower($sender),$flashDataMailImportValidSender) ){
						// save attachment 
						// $sF = $this->saveAttachments( $this->_popBox->arrMessage );
						if( $sF!==false && $this->_deleteAttachments ){
							// delete message
							$this->_popBox->dele($strMessage);
						}
						$this->reportText .= ($sF!==false)?("Saved attachments: ".$sF):"Could not save attachments";
					}else{
						$this->reportText .= "ERROR - This sender was not accepted.";
					}
					*/
					 
					echo $this->reportText;
							
				} else {
					$this->_popBox->showresult();
				}
				
			} // end foreach
			$this->_popBox->quit();
		}
		
	}
	
}	


?>