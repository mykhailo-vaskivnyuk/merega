<?php

class Site
{
    public	$data;				//object
	public	$user;				//object
	public	$mail;				//object
	private	$status_minus_1;	//object
	public	$response; 			//object
	
    public function __construct()
    {
		//загальні функції
		//переніс в індекс require_once 'objects/Functions.php';

		//дані
        require_once 'objects/Data.php';
        $this->data = new Data();
		
		//користувач
		require_once 'objects/User.php';
		$this->user = new User($this);

		//мейл
		require_once 'objects/SendMail.php';
		$this->mail = new SendMail();
		
		//модель
		require_once 'objects/StatusMinus1.php';
		$this->status_minus_1 = new StatusMinus1($this, $this);
		
		//відповідь
		require_once 'objects/Response.php';
		$this->response = new Response($this, $this);
		
	}
    
	public function runSite()
    {
		//$test = $test['test'];
		
		write_log('runSite');
		//print_array($_SERVER);

		//отримуємо команду
		$this->data->initData();
		write_log('command', $this->data->command);
		
		//$this->response->setResponse('command', $this->data->command);
		
		//обробка команди
		if($this->user->getUser('status') == 10){
			//print_array($this->user->getUser('status'));
			write_log('limits', 'limits begin', 'limits_log');
			require_once 'objects/StatusAdministrator.php';
			$status_administrator = new StatusAdministrator($this, $this);
			$status_administrator->runStatusAdministrator();
			write_log('limits', 'limits end', 'limits_log');
			$this->user->resetUser(10);
			//print_array($_SESSION);
			return;
		}
		
		$this->runStatusMinus1();
		
		//print_array($this->data->command);
		
		//перенаправлення
		if($this->data->redirection){
			//exit;
			//http_response_code(xxx);
			//      | "300"  ; Section 10.3.1: Multiple Choices
			//      | "301"  ; Section 10.3.2: Moved Permanently
			//     >| "302"  ; Section 10.3.3: Found / Moved Temporarily
			//     >| "303"  ; Section 10.3.4: See Other
			//      | "304"  ; Section 10.3.5: Not Modified
			//      | "305"  ; Section 10.3.6: Use Proxy
			//     >| "307"  ; Section 10.3.8: Temporary Redirect
			//echo '<script type="text/javascript">window.location="' . $this->data->getRedirection() . '"</script>';
			//echo $this->data->getRedirection(); exit;
			header('Location: ' . $this->data->getRedirection(), true, 303);
			exit;
		}

//		$this->response->setResponse('', '', $this->data->command, $this->user, $this->data);

		$this->response->setResponse('session', $_SESSION);
		
		//відповідь клієнту
		
		$this->response->getResponse();
		//print_array($_SESSION, 1);
		write_log('endSite');
    }
	
	public function runStatusMinus1()
	{
		if(!$this->status_minus_1->runStatusMinus1()){
			$this->user->resetUser(-1);
		}				
		elseif($this->data->redirection === 'inner'){
			$this->data->redirection = false;
			$this->runStatusMinus1();
		}
		
		unset($this->status_minus_1);
	}
}
?>