<?php
/*
 * Created on 22 ���. 2011 15:02:47 in our-slavic-world
 * By noon-ehos
 */

class SendMail
{
	public $message = NULL;
	public $error = false;
	private $method = 'sendMail';
	private $PEARMailUrl = "Mail.php";
	private $PEARMailMimeUrl = "Mail/mime.php";
	//дописав
	private $params = array(
				'host'			=>'ssl://smtp.gmail.com',
				'port'			=>'465',
				'username'		=>'m.vaskivnyuk@gmail.com',
				'password'		=>'vaskivnyuk',
				'From'			=>'USERS_DATABASE',
				'To'			=>'m.vaskivnyuk@gmail.com',
				'Subject'		=>'SUBJECT',
				'html_charset'	=>'utf-8',
				'html'			=>'MAIL TEXT');
	
	function __construct()
	{
		/*
		$this->method = $method;
		
		switch($this->method)
		{
			case 'sendMail': 
			case 'socket': 
			case 'socket-auth': 
			case 'ssl': 
			case 'ssl-auth': $this->sslAuth($params); break;
			default: $this->message = "Failed send method!"; $this->error = true; break; 
		}
		*/
	}
	
	/*
	 * PARAMS:
	 * host ('ssl://smtp.gmail.com') Required
	 * port ('465') Required
	 * username ('server@timelymaintenance.com'|'root') Required
	 * password ('q5ga5oo') Required
	 * From ('Sarra Konor <server@timelymaintenance.com>') Required
	 * To  ('Felix Ditrih <noon-ehos@meta.ua>') Required
	 * Reply-To Not Required
	 * Return-path Not Required. May be replace senter field `From`
	 * Subject  ('Testing') Required, proccessed by ('=?UTF-8?B?'.base64_encode($params['Subject']).'?=')
	 * text Required, if you use "\n" you will need to use only double quotes like "\n" but not '\n'
	 * text_charset Not Required
	 * html Required
	 * html_charset ('utf-8') Not Required
	 */
	public function sslAuth($params)
	{
		/*
		 * For PEAR mail you need to install PEAR and PEAR modules:
		 * ~ # pear channel-update pear.php.net
		 * ~ # pear upgrade-all
		 * ~ # pear install Mail
		 * ~ # pear install Net_SMTP
		 * ~ # pear install Auth_SASL
		 * ~ # pear install mail_mime
		 * ~ # apt-get install php-pear
		 * Also you need to install opensll and install it into php:
		 * ~ # apt-get install openssl
		 * ~ # apt-get install php5-openssl
		 * Then restart Apache:
		 * ~ # /etc/init.d/apache2 restart
		 */
		
		require_once($this->PEARMailUrl);
	    require_once($this->PEARMailMimeUrl);

	    $Headers = array();

		if(isset($params['head_charset'])) 	$Headers['head_charset']               = $params['head_charset'];
		if(isset($params['To_mail']) && isset($params['To_name'])) 	$Headers['To'] = '=?UTF-8?B?'.base64_encode($params['To_name']).'?=' . " <" . $params['To_mail'] . ">";
		elseif(isset($params['To_mail'])) $Headers['To'] = $params['To_mail'];
		else $Headers['To'] = $params['To'];
		if(isset($params['From'])) 			$Headers['From']                       = '=?UTF-8?B?'.base64_encode($params['From']).'?=';
		if(isset($params['Reply-To'])) 		$Headers['Reply-To']                   = $params['Reply-To'];
		if(isset($params['Return-path'])) 	$Headers['Return-path']                = $params['Return-path'];
		if(isset($params['Bcc'])) 			$Headers['Bcc']              		   = $params['Bcc'];
		
		if(isset($params['Subject'])) 		$Headers['Subject']                    = '=?UTF-8?B?'.base64_encode($params['Subject']).'?=';
	    #print_array($params);
	    #print_xarray($Headers);


		$mime = new Mail_mime("\n");

		$get_params = array();
		if(isset($params['text'])) $mime->setTXTBody($params['text']);
		if(isset($params['text_charset'])) $get_params['text_charset'] = $params['text_charset'];
		if(isset($params['html'])) $mime->setHTMLBody($params['html']);
		if(isset($params['html_charset'])) $get_params['html_charset'] = $params['html_charset'];

		//do not ever try to call these lines in reverse order
		$Body = $mime->get($get_params);
		$Headers = $mime->headers($Headers);

		$smtp = Mail::factory(
	        'smtp',
	        array (
	            'host'=>$params['host'],
	            'port'=>$params['port'],
	            'auth'=>true,
	            'username'=>$params['username'],
	            'password'=>$params['password']
	        )
	    );

	    $mail = $smtp->send($Headers['To'],$Headers,$Body);
		if((PEAR::isError($mail))) {$this->error = true; $this->message = $mail->getMessage();}
		else {$this->error = false; $this->message = "Mail successfully sended!";}
	    return $this->error;
	}
	
	public function sendMail($type = '', $mail_data = array())
	{
		if(!$mail_data){
			$this->params['To'] = $this->params['To'];
			$mess = 'ТЕСТОВИЙ ЛИСТ!';
			$html = '<div style=\'font-size: 12pt\'>
						<p style=\'color: green; font-weight: bold\'>' . $mess . '</p>
					</div>';
			$this->params['Subject'] = 'ТЕСТОВИЙ ЛИСТ!';
			$this->params['html'] = $html;
		}		
		else
			$this->params['To'] = $mail_data['email'];
			
		if($type == 'confirm'){
			$mess = 'Для підтвердження реєстрації відкрийте лінк:';
			$html = '<div style=\'font-size: 12pt\'>
						<p style=\'color: green; font-weight: bold\'>' . $mess . '</p>
						<p><a href=\'' . $mail_data['link'] . '\'>' . $mail_data['link'] . '</a></p>
					</div>';
			$this->params['Subject'] = 'Підтвердження реєстрації!';
			$this->params['html'] = $html;
		}
		
		if($type == 'restore'){
			$mess = 'Для входу в акаунт відкрийте лінк:&nbsp;';
			$html = '<div style=\'font-size: 12pt\'>
						<p style=\'color: green; font-weight: bold\'>' . $mess . '</p>
						<p><a href=\'' . $mail_data['link'] . '\'>' . $mail_data['link'] . '</a></p>
					</div>';
			$this->params['Subject'] = 'Вхід через e-mail!';
			$this->params['html'] = $html;
		}		
		
		if($type == 'invite'){
			$mess = 'Для долучення до спільноти відкрийте лінк:&nbsp;';
			$html = '<div style=\'font-size: 12pt\'>
						<p style=\'color: green; font-weight: bold\'>' . $mess . '</p>
						<p><a href=\'' . $mail_data['link'] . '\'>' . $mail_data['link'] . '</a></p>
					</div>
					<div>
						Лист відправлено від імені ' . $mail_data['sender'] . '
					</div>';
			$this->params['Subject'] = 'Запрошення в спільноту!';
			$this->params['html'] = $html;
		}
		
		//print_array(setEncoding($html));
		//$this->sslAuth($this->params);
		$this->sendPhpMail();
		return $this->error;
	}
	
	private function SendPhpMail()
	{	
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'From: net_admin@mike.sl.org.ua' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		if(mail($this->params['To'], $this->params['Subject'], $this->params['html'],  $headers))		
			$this->error = false;
	}
}