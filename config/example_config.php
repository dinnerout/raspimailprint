<?php
/**
 * 
 * 
 * @author Sebastian Martens sm@nonstatics.com
 * @copyright 2015 Sebastian Martens
 * @version $Id$
 */

// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
// 
// General Config 
// 
define('APPLICATIONAME','RaspberryEMailPrinter');
define('SESSIONTIMEOUT', (60*60) ); // session timeout in seconds

// where emails will be saved
define('FILE_PATH','data');

define('DELETE_SAVED_MAILS',true);

define('PRINT_FILE_ON_SAVE',true);

define('DELETE_FILES_AFTER_PRINT',true);

// E-Mail Account
define('EMAIL_SERVER','{server.com:110/pop3}INBOX');
define('EMAIL_USER','username');
define('EMAIL_PASSWORD','password');

// Translations
$i18n = array(

	'Subject'	=> ' Betreff: ',
	'Date'		=> ' Datum: ',
	'Message'	=> ' Nachricht: ',
	'Sender'	=> ' Absender: ',
	'ContainsAttachment' => ' Diese E-Mail enthaelt Anhaenge'

);

// 
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
