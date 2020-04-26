<?php

class Status0
{
    private $site;
	private $data;
	private $user;
	private $response;
	private $parent;
	private $command;
	private $status_plus_1;	//object

    public function __construct(&$site, &$parent)
    {
		$this->site = $site;
		$this->data = &$site->data;
		$this->user = &$site->user;
		$this->response = &$site->response;
		$this->parent = $parent;
		$this->command = &$site->data->command;
	}
	
	public function runStatus0()
	{
		if($this->command['type'] == 'exit'){
			return false;
		}

		If(!$this->user->setUser('', '', 0)) return false;		

		//контроль структури
		if(isset($this->command['data']['user_id']) &&
			$this->command['data']['user_id']){
			if($this->user->getUser('id') != $this->command['data']['user_id']){
				$this->data->redirection = 'inner';
				$this->command['type'] = '';
			}
		}
		
		//повідомлення
		if($this->command['type'] != 'enter'){
			if($this->user->getUser('notifications_count_new')){
				$this->data->next_command = $this->command;
				$this->user->resetUser(0);
			}
		}
		
		//якщо команда ENTER встановлюється до роутера команд, то на рекурсію можна не йти
		
		if($this->data->redirection !== false) return true;
		
		if($this->user->getUser('status') > 0){
			switch ($this->command['type']){
				case 'invite':
					//echo 'stop'; exit;
					$this->data->next_command = $this->command;
					$this->data->next_command['redirection'] = 'inner'; //???
					$this->user->resetUser(0);
					return true;
				default:
			}
		}

		$user_status = $this->user->getUser('status');
		if($user_status <= 0){
				switch ($this->command['type']){
					case 'enter':
						$this->enterStatus0();
						//print_array($this->command);
						//print_array($this->data->redirection);
						break;
					case 'invite':
						//print_array('HELLO!');
						$invite = $this->command['data']['link'];
						$this->user->setUser('invite', $invite); //перевірку на пусто?
						if($user_status == 0) $this->doConnectionStatusPlus1();
						else{
							$this->data->messages[] = array('type' => 'error', 'text' => 'Спочатку необхідно підтвердити реєстрацію!');						
							$this->data->redirection = 'inner';
							$this->command['type'] = '';
						}
						break;
					case 'create':
						//print_array($this->command);
						//$net_name = $this->command['data']['net_name'];
						//$this->user->setUser('net_name', $net_name);
						//$this->command['type'] = 'edit';
						if($user_status == 0){
							$this->user->setUser('net_name', $this->command['data']['net_name']);
							$this->doConnectionStatusPlus1();
						}
						else{
							$this->data->messages[] = array('type' => 'error', 'text' => 'Спочатку необхідно підтвердити реєстрацію!');						
							$this->data->redirection = 'inner';
							$this->command['type'] = '';
						}
						break;
					case '':
						$this->data->redirection = true;
						//$messages = $this->data->messages; //ПЕРЕВІРИТИ ЩЕ РАЗ НЕОБХІДНІСТЬ (поки для видачі Лінк не дійсний)
						if($this->user->getUser('enter'))
							$notifications = $this->user->getUser('notifications_count_all');
						else
							$notifications = $this->user->getUser('notifications_count_not_shown');
						if($notifications)
							$this->command['type'] = 'notification';
						elseif($this->user->getUser('enter')){ //elseif($this->user->getUser('enter') && !messages){
							$this->data->redirection = 'inner';
							$this->command['data']['net'] = '';
							$this->command['type'] = 'in'; //inner
						}
						else $this->command['type'] = 'data';
						$this->user->setUser('enter', false);
						break;
					case 'notification':
						//print_array($this->command);
						if($this->command['operation'] == 'close'){
							$this->user->setUser('notification_close');
						}
						elseif($this->command['operation'] == 'view'){
							$this->data->redirection = true;
							//$command = $this->user->getIam('notification_view');
							//if(!is_null($command)) $this->command['type'] = $command;
							//else 
								$this->command['type'] = 'data';
							break;
						}
						$this->command['operation'] = 'read';
						$notifications_count = $this->user->getUser('notifications_count_all');
						//print_array($this->command, 1);
						//print_array($notifications_count);
						if(!$notifications_count){
							$this->data->redirection = 'inner';
							$this->command['type'] = '';
						}
						else $this->user->setUser('notifications_not_shown_reset');
						break;
					case 'delete':
						//print_array($this->command);
						if($this->deleteStatus0()) return false;
						break;
					case 'data':
						//САМЕ тут можна зчитувати дані користувача!!!
						//А не в setUser чи ентер; окрім таких даних як імя, яке потрібне для відображення в менюшках,
						//чи email, який потрібен для відправки листів
						//if(empty($this->command['operation'])){
						//	$this->command['operation'] = '';
						//	break;
						//}
						//КОНТРОЛЬ СТРУКТУРИ
						if($this->command['operation'] == 'write' || $this->command['operation'] == 'reset_password')
							$this->setUser();
						break;
					case 'in':
						$user_net = $this->command['data']['net'];
						$this->command['data']['net'] = ''; //а можна і по іншому
						//if(!$user_net) $user_net = $this->user->getUser('net'); //можна при редіректі встановлювати command['data']['net']
						if(!$user_net){
							$user_nets = $this->user->getUser('nets');
							if(count($user_nets) == 1)
								//reset($user_nets);
								$user_net = key($user_nets);
							else{
								$this->data->redirection = true;
								$this->command['type'] = 'data';
								break;
							}
						}
						$this->user->setUser('net', $user_net);												
						$this->runStatusPlus1();
						break;
					//case 'connect':
					//	...
					//	break;							
					default:
						$this->data->redirection = 'inner';
						$this->command['type'] = '';
				}
				if(!$this->data->redirection){
					$this->response->setResponse('', '', $this->command, $this->user, $this->data);
				}
				/*
				elseif($this->data->redirection === 'inner'){
					//print_array($this->data->command, 1);
					$this->data->redirection = false;
					//$this->data->getCommand();
					//print_array($this->data->command);
					$this->runStatus0();
				}
				*/
		}
		else $this->runStatusPlus1();
		return true;
	}
	
	public function enterStatus0()
	{
		//print_array($this->user->getUser());
		//print_array($this->user->getUser('net_name'));
		$this->command = array();
		$this->data->redirection = 'inner';
		$this->command['type'] = ''; //'' або 'data'?
		
		$this->user->setUser('notifications_new_reset');

		//нехай invite має вищий пріоритет ніж notifications
		$invite = $this->user->getUser('invite');
		if($invite){ //&& ($this->user->getUser('status') == 0)
			$this->command['type'] = 'invite';
			$this->command['data']['link'] = $invite;
			return true;
		}
		else //$this->user->setUser('invite', ''); //???
		
		$net_name = $this->user->getUser('net_name');
		if($net_name){ //&& ($this->user->getUser('status') == 0)
			$this->command['type'] = 'create';
			$this->command['data']['net_name'] = $net_name;
			$this->command['operation'] = 'net_create';
			//print_array($this->command);
			return true;
		}
		else //$this->user->setUser('net_name', ''); //???	
		
		
		if($this->user->getUser('status') == 0){
			$user_nets = $this->doAuthStatusPlus1();
			//print_array($user_nets);
			if($this->user->getUser('enter')){
				if(!$user_nets){
					$this->data->messages[] = array('type' => 'error', 'text' => 'Вас немає в жодній з мереж!');
				}
			}
		}
		//print_array($this->data->next_command);
		if($this->data->next_command){
			$this->data->prev_command = $this->command;
			if(!empty($this->data->next_command['redirection']))		
				$this->data->redirection = $this->data->next_command['redirection'];
			else $this->data->redirection = true;
			$this->command = $this->data->next_command;
			$this->data->next_command = array();
			//return true;
		}		
		
		//if(count($user_nets) != 1) return true;
		//$messages = $this->data->messages;
		//$notifications = $this->user->getUser('notifications_count_all');						
		//if($messages || $notifications) return true;
		//$this->user->setUser('net', $user_nets[0]);		
		//$this->command['type'] = 'in';
		return true;
	}
	
	public function doAuthStatus0()
    {
		/* ДОДАТКОВИЙ (СВІЙ) КОНТРОЛЬ */
		//$this->chkConnect();
		$command = $this->command['type'];
		$command_data = $this->command['data'];
		//$operation = $this->command['operation']; //тут обережно: чи завжди є operation?
		
		//if($command == 'authorize'){
		//	$email = $command_data['email'];
		//	$password = $command_data['password'];
		//	if(!$password) ...
		//}
		if($command == 'confirm' || $command == 'restore'){
			if(!$command_data['link']) return false;
		}
		
		$user_id = $this->user->setUser('id');
		
		if(!$user_id) return false;
		
		//ПЕРЕНЕСТИ
		//if($this->command['type'] == 'confirm')
		//	$this->data->messages[] = array('type' => 'success', 'text' => 'Реєстрацію успішно підтверджено!');
		
		return $user_id;
    }
	
	public function doRegistrationStatus0()
    {
		/* ДОДАТКОВИЙ (СВІЙ) КОНТРОЛЬ */
		//$this->chkConnect();
		$operation = $this->command['operation'];
		$user = $this->user->setUser('user');
		if(!$user['id']) return false;
		
		$this->data->messages[] = array('type' => 'success', 'text' => 'Реєстрація пройшла успішно!
																		При необхідності можна встановити пароль в розділі [Я :: мої дані].');

		if($operation == 'allow_registration') return $user['id'];
		
		$notification['text'] =	'Для підтвердження реєстрації необхідно перейти по лінку, який відправлений на Ваш email.
								Якщо лист не надійшов - вийдіть з акаунта і скористайтесь функцією \'ВХІД ЧЕРЕЗ EMAIL\'.
								Лінк для підтвердження реєстрації буде надіслано ще раз.';
		$notification['code'] = 21;
		$notification['close'] = 0;
		$this->user->setUser('notification', $notification);
		
		$data['email'] = $this->command['data']['email'];
		$data['link'] = $this->data->getServer('HTTP_HOST') . 'confirm/' . $user['link'] . '/';
		$mail_error = $this->site->mail->sendMail('confirm', $data);		
		if($mail_error)
			$this->data->messages[] = array('type' => 'error', 'text' => 'Відправка мейла не вдалась! ' . $this->site->mail->message);

		return $user['id'];
    }
	
	public function doRestoreStatus0()
    {	
		$link = $this->user->setUser('restore');
		if($link){
			$data['email'] = $this->command['data']['email'];
			$data['link'] = $this->data->getServer('HTTP_HOST') . $link['type'] . '/' . $link['value'] . '/';
			$mail_error = $this->site->mail->sendMail($link['type'], $data);		
			if($mail_error){
				$this->data->messages[] = array('type' => 'error', 'text' => 'Відправка мейла не вдалась! ' . $this->site->mail->message);				
				return;
			}
		}
		$this->data->messages[] = array('type' => 'success', 'text' => 'Лінк для входу в акаунт відправлено на e-mail: ' . $this->command['data']['email'] . ' !');		
		return;
	}
	
	private function doAuthStatusPlus1()
	{
		require_once 'objects/StatusPlus1.php';
		$status_plus_1 = new StatusPlus1($this->site, $this);
	
		$user_nets = $this->user->setUser('nets');

		return $user_nets;
	}
	
	private function runStatusPlus1()
	{
		require_once 'objects/StatusPlus1.php';
		if(!isset($this->status_plus_1))
			$this->status_plus_1 = new StatusPlus1($this->site, $this);
			
		if(!$this->status_plus_1->runStatusPlus1()){
			$this->user->resetUser(0);
		}
		
		elseif($this->data->redirection === 'inner'){
			$this->data->redirection = false;
			$this->runStatusPlus1();
			//echo $this->data->redirection;
			//print_array($this->command);
		}
		
		unset($this->status_plus_1);
		//print_array($this->command, 1);
		//print_array($this->user->getUser());
	}
		
	private function doConnectionStatusPlus1()
	{		
		require_once 'objects/StatusPlus1.php';
		$status_plus_1 = new StatusPlus1($this->site, $this);
		
		if($this->command['type'] == 'create'){
			if($this->command['operation'] == 'net_create')
				$status_plus_1->createNet();
			return;
		}
		
		$user_net = $status_plus_1->doConnectionStatusPlus1();
		//return false
		//return user_net
		//print_array($this->data->messages);	
		//print_array($user_net);
		if($user_net){
			//$this->command['data']['net'] = '';
			$this->command['data']['net'] = $user_net;
			$this->data->redirection = 'inner';
			$this->command['type'] = 'in';
			return true;
		}

		$this->user->resetUser(0);
		return false;
	}
	
	private function deleteStatus0()
	{
		if(!$this->command['data']['user_id']) return false;
		
		if($this->user->getUser('nets')){
			$this->data->messages[] = array('type' => 'error', 'text' => 'Для видалення акаунта від\'єднайтесь, будь-ласка, від всіх спільнот!');
			return false;
		}	
		
		$this->data->deleteUser();
		$this->data->messages[] = array('type' => 'success', 'text' => 'Ваш акаунт успішно видалено!');	
			//$this->user->resetUser(-1);
			//$this->data->redirection = true;
			//$this->command['type'] = '';
		return true;
	}
	
	private function setUser()
	{
		$success = $this->user->updateUser();
		
		if($success === false) return;
		
		$this->command['operation'] = 'read';
		
		if(is_null($success))return;

		$this->data->messages[] = array('type' => 'success', 'text' => 'Дані успішно оновлено!');
		$this->data->redirection = 'inner';
		//$this->command['type'] = 'data'; //ще можливі варіанти '' та 'enter'
	}
	
	public function getUser($data_name = '', $data_value = '')
	{
		switch($data_name){
			case 'user':
				return $this->data->getUser('user', $data_value);
			default:
		}
	}
	
	//зробити щоб свій user і просто user користувались цією та іншими подібними функціями
	public function setNotifications($notifications)
	{
		//print_array($notifications);
		foreach($notifications as $notification){
			if(empty($notification['code'])) $notification['code'] = 11;
			$this->data->setUser('notification', $notification);
		}
	}
}