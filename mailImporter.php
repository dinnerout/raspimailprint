<?php
/** *****************************************************************************************************
	 * @author: Sebastian Martens
	 * @version: $Id$
	 ***************************************************************************************************** */
	 
	 
	require_once 'config/config.php';
	
	require_once 'classes/Helper.php';
	require_once 'classes/php-imap-master/src/ImapMailbox.php';
	
	$mailbox = new ImapMailbox( EMAIL_SERVER, EMAIL_USER, EMAIL_PASSWORD, '' );
	
	// 
	if ( $mailbox ){
		Helper::log( 'Connected to Mailbox' );
	
		// Get emails
		$mailsIds = $mailbox->searchMailBox('ALL');
		
		Helper::log( $mailbox->countMails() . " Messages existing" );
		
		// emails existing
		if($mailsIds) {
			
			// handle each email
	    	foreach( $mailsIds as $mailID ){
	    		
				Helper::log( " --- Handle MessageID: " . $mailID );
	    		
				// getting current email
				$mail = $mailbox->getMail( $mailID );
				$attachments = $mail->getAttachments();
				$mailText =  $mail->textPlain?$mail->textPlain:strip_tags( Helper::br2nl($mail->textHtml) );
				$mailText = Helper::strip_signature( $mailText );
				$mailText = Helper::removeEmptyLines( $mailText );
				
				Helper::log( "Message From: " . $mail->fromName . " (" . $mail->fromAddress . ")" );
				Helper::log( "Message Date: " . $mail->date );
				Helper::log( "Message Attachments: " . count($attachments) );
				
				if( substr(trim($mail->subject),0,6) == 'Status' ){
					
					$mailFile = $mail->date .': ' . trim($mail->subject) . "\n";
					
				}else{
				
					// 
					$mailFile = 
						"||||||||||||||||||||||||||||||||\n\n" .
						$i18n['Sender'] 	. Helper::removeUmlaute($mail->fromName) . " (" . $mail->fromAddress . ") \n" .
						$i18n['Date'] 		. $mail->date . "\n" . 
						$i18n['Subject'] 	. Helper::removeUmlaute($mail->subject) . "\n" .
						
						( count($attachments)
						 
							?	
							("\n" . 
							"********************************\n" .
							$i18n['ContainsAttachment'] . "\n" .
							"********************************\n") 
							: ''
							
						) .
						
						"\n".
						"||||||||||||||||||||||||||||||||\n\n" .
						Helper::removeUmlaute($mailText) . "\n\n" .
						"||||||||||||||||||||||||||||||||\n\n";
					
				}	
				// $mailFile = utf8_decode($mailFile);
				
				// write file
				$filename = md5( $mail->fromAddress . $mail->date ) . '.mail';
				if( file_put_contents(FILE_PATH . "/" . $filename , $mailFile) ){
					Helper::log('File written: ' . $filename );
					
					// delete successfully saved mail
					if( DELETE_SAVED_MAILS ){
						$mailbox->deleteMail( $mailID );
					}else{
						Helper::log('Saved message will not be deleted' );
					}
					
					// send file to printer
					if( PRINT_FILE_ON_SAVE ){
						// print downloaded data via python script
						system("cat /home/pi/raspimailprint/".FILE_PATH . "/" . $filename . " | python /home/pi/raspimailprint/mail-printer.py");
						// time to print
						sleep(4);
						// delete downloaded mails
						if( DELETE_FILES_AFTER_PRINT ){
							@unlink( FILE_PATH . "/" . $filename );
						}
					}
					
				}else{
					Helper::log('[ERROR] Could not write file: ' . $filename );
				}
				
	    	}
			
		// no emails existing
		}else{
			Helper::log('Mailbox is empty');
		}

	}else{
		Helper::log( 'Could not connect to Mailbox' );
	}
	

?>