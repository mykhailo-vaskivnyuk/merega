<?php
function exception_error_handler($severity, $message, $file, $line){
	//http://php.net/manual/ru/class.errorexception.php
    if(!(error_reporting() & $severity)){
        // Этот код ошибки не входит в error_reporting
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}

function setEncoding($text = '')
{
	$encoding_text = iconv( 'utf-8', 'windows-1251', $text);
	return $encoding_text;
}

function print_array($value, $continue = 0)
{
	echo '<pre>';
	print_r($value);
	echo '</pre>';
	if(!$continue) exit;
}

function write_log($key = '', $value = '', $log_type = '', $first_call = true)
{
	global $log_file;
	global $limits_log;
	global $tab;

	if($log_type == 'limits_log'){
		isset($limits_log) or $limits_log = fopen('cron/limits_log.txt', 'a');
		$file = &$limits_log;
	}
	else{
		isset($log_file) or $log_file = fopen('log_file.txt', 'a');
		$file = &$log_file;
	}
	
	!$first_call or fwrite($file, date('d.h.Y H:i:s') . "\r\n");
	if(!is_array($value)){
		fwrite($file, $tab . $key . "=>" . $value . "\r\n");
		return;
	}
	$key or $key = 'array';
	fwrite($file, $tab . $key . "=>" . "\r\n");
	$tab = $tab . "\t";
	foreach($value as $key => $item) write_log($key, $item, '', false);
	$tab = '';
	return;
}