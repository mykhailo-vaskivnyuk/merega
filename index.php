<?php
/*
echo 'САЙТ ТИМЧАСОВО НЕ ПРАЦЮЄ! ЙДЕ ОНОВЛЕННЯ!';
exit;
*/

//ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//ini_set('sendmail_path', '/usr/sbin/sendmail -t -i -f admin@vspilnoty.vinnica.uaa');
//ini_set('sendmail_from', 'admin@vspilnoty.vinnica.ua');

error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_NOTICE);

//для роботи url запиту від скріпта
	if(isset($_REQUEST['psid']))
		session_id($_REQUEST['psid']);
//---------------------------------

require_once 'objects/Functions.php';
set_error_handler('exception_error_handler');

//print_array($_SERVER);
//print_array(phpinfo());

try{
	require_once "objects/Site.php";	
	$site = new Site();
	$site->runSite();
}
catch(Exception $e){
/* Методы
	final public int getSeverity ( void )

   Наследуемые методы
	final public string Exception::getMessage ( void )
	final public Exception Exception::getPrevious ( void )
	final public mixed Exception::getCode ( void )
	final public string Exception::getFile ( void )
	final public int Exception::getLine ( void )
	final public array Exception::getTrace ( void )
	final public string Exception::getTraceAsString ( void )
	public string Exception::__toString ( void )
	final private void Exception::__clone ( void )
*/
	print_array($e->getMessage(), 1);
	print_array('SEVERITY: ' . $e->getSeverity(), 1);
	print_array('LINE: ' . $e->getLine(), 1);
	print_array('FILE: ' . $e->getFile(), 1);

	write_log('error', ["MESSAGE\t\t" => $e->getMessage(),
						"SEVERITY\t" => $e->getSeverity(),
						"LINE\t\t" => $e->getLine(),
						"FILE\t\t" => $e->getFile()]);
/*
	write_log('error', $e->getMessage() . "\r\n" .
						'--> SEVERITY: ' . $e->getSeverity() . "\r\n" .
						'--> LINE: ' . $e->getLine() . "\r\n" .
						'--> FILE: ' . $e->getFile());			
*/	
	//print_array($e->getTrace(), 1);
}

//echo '!END!';