<?php

class StatusMinus1
{
    private $site;
	private $data;
	private $user;
	private $response;
	private $parent;
	private $command;
	private $status_0;	//object
	
    public function __construct(&$site, &$parent)
    {
		$this->site = &$site;
		$this->data = &$site->data;
		$this->user = &$site->user;
		$this->response = &$site->response;
		$this->parent = &$parent;
		$this->command = &$site->data->command;
	}
    
	public function runStatusMinus1()
    {
		$this->user->setUser('', '', -1);

		if(!($this->user->getUser('status') == -1))
			switch ($this->command['type']){
				case 'confirm':
					$this->data->next_command = $this->command;
					$this->data->next_command['redirection'] = 'inner';
					$this->user->resetUser(-1);
					break;
				case 'restore':
					$this->data->next_command = $this->command;
					$this->data->next_command['redirection'] = 'inner';
					$this->user->resetUser(-1);
					break;				
				default:
					//...
			}
		
		if($this->data->redirection) return true;
		
		if($this->user->getUser('status') == -1){
				switch ($this->command['type']){
					case 'authorize':
						$this->doAuthStatus0();
						break;
					case 'registration':
						$this->doRegistrationStatus0();
						break;
					case 'confirm':					
						$this->doAuthStatus0('link');
						break;
					case 'restore':
						$this->doAuthStatus0('restore');
						break;						
					case 'enter':
						$this->runStatus0();
						break;
					case 'invite':
						$invite = $this->command['data']['link'];
						if($invite){
							$this->user->setUser('invite', $invite);
							$this->data->redirection = true;
							$this->command['type'] = 'connect';
							break;
						}		
						$this->user->resetUser(-1);
						$this->data->messages[] = array('type' => 'success', 'text' => 'Ви успішно відмовились від долучення до спільноти!');
						break;
					case 'create':
						if($this->command['operation'] == 'cancel'){
							$this->user->resetUser(-1);
							$this->data->messages[] = array('type' => 'success', 'text' => 'Створення спільноти скасовано!');
							break;
						}
						//ставимо блокування на створення спільноти з статусу -1
						//можливо потрібно продублювати блокування в статусі +1
						//$this->user->setUser('net_name', '');
						//$this->command['operation'] = 'forbid';
						//break;
						if($this->command['operation'] == 'net_create'){
							$net_name = $this->command['data']['net_name'];
							$this->user->setUser('net_name', $net_name);
						}
						if($this->chkConnect()) break;
						$this->command['operation'] = 'edit';
						break;			
					case 'connect':
						//print_array($this->user->getUser());
						$this->chkConnect();				
						break;				
					//case 'notification':
						//...
						//якщо повідомлень немає зробити redirection
					//	break;
					case 'first':
						//...
						break;					
					default:
						$this->command = array();
						$this->data->redirection = true;
						$this->command['type'] = 'authorize';
						if($this->data->next_command){
							$this->data->prev_command = $this->command;
							if(!empty($this->data->next_command['redirection']))	
								$this->data->redirection = $this->data->next_command['redirection'];
							else $this->data->redirection = true;
							$this->command = $this->data->next_command;
							$this->data->next_command = array();
						}
				}
			if(!$this->data->redirection){
				$this->user->setUser('enter', false);
				$this->response->setResponse('', '', $this->command, $this->user, $this->data);
			}
		}
		else $this->runStatus0();
		return true;
	}
	
	private function doAuthStatusMinus1()
	{
		//фактично авторизація здійснюється: $this->user->setUser('', '', -1);
	}
	
	private function doAuthStatus0($type = '')
	{
		//$type можна брати з command
		//print_array($this->command, 1);
		//print_array($type);
		
		if($type == 'link'){
			if(!$this->command['data']['link']){
				$this->data->redirection = 'true'; //'innner'
				$this->command['type'] = 'authorize'; //''
				return;
			}
		}
		elseif($type == 'restore'){								
			if(!$this->command['data']['link']){
				if($this->command['data']['email']){
					require_once 'objects/Status0.php';
					$status_0 = new Status0($this->site, $this);
					$status_0->doRestoreStatus0();
					$this->data->redirection = 'true'; //'innner'
					$this->command['type'] = 'authorize'; //''				
					return;
				}
				else{
					$this->data->messages[] = array('type' => 'error', 'text' => 'Введіть, будь-ласка, e-mail в поле \'e-mail\' !');
				}
				$this->data->redirection = 'true'; //'inner';
				$this->command['type'] = 'authorize';
				return;					
			}
		}
		elseif(!$type){
			if(!$this->chkConnect()) return;

			$operation = $this->command['operation'];
			//print_array($operation);
			
			if($operation == 'allow_authorize'){
				if(!$this->command['data']['email']) return;
			}
			else{
				//print_array($operation);
				if(!$this->command['data']['email'] && !$this->command['data']['password']) return;
				
				if(!$this->command['data']['email'] || !$this->command['data']['password']){
					$this->data->messages[] = array('type' => 'error', 'text' => 'Необхідно ввести імя та пароль!');
					return;
				}
			}
		}

		//if(empty($this->command['operation'])) $this->command['operation'] = ''; //уточнити варіанти; вставив в getCommand
		
		require_once 'objects/Status0.php';
		$status_0 = new Status0($this->site, $this);
		$user_id = $status_0->doAuthStatus0();

		if($user_id){
			$this->data->redirection = 'inner';
			$this->command['type'] = 'enter';
			return;
		}

		//ПЕРЕНЕСТИ ПОВІДОМЛЕННЯ У ВІДПОВІДНІ МОДУЛІ!
		if($type == 'link' || $type == 'restore'){
			//$this->data->messages[] = array('type' => 'error', 'text' => 'Лінк, по якому Ви зайшли, невірнй або вже недійсний!');
			$this->user->resetUser(-1);
		}
		elseif($operation == 'allow_authorize'){
			//...
		}
		else
			//$this->data->messages[] = array('type' => 'error', 'text' => 'Невірні дані для входу!');
		
		return;
	}
	
	private function doRegistrationStatus0()
	{
		if(!$this->chkConnect()) return;
		
		$operation = $this->command['operation'];
		
		if($operation == 'allow_registration'){
			if(	!$this->command['data']['name']){ 
				// && !$this->command['data']['password']
				// && !$this->command['data']['mobile']
				return;
			}
		}	
		else{
			if(	!$this->command['data']['email'] &&
				!$this->command['data']['name']){
				// && !$this->command['data']['password']
				// && !$this->command['data']['mobile']
				return;
			}
		}
		
		if(	!$this->command['data']['email'] ||
			!$this->command['data']['name']){
			// || !$this->command['data']['password']
			//ПРОДУБЛЮВАТИ КОНТРОЛЬ ЗАПОВНЕНОСТІ В СТАТУСІ 0
			$this->data->messages[] = array('type' => 'error', 'text' => 'Для реєстрації обов\'язковими є всі поля!'); // окрім мобільного!
			return;
		}
		
		require_once 'objects/Status0.php';
		$status_0 = new Status0($this->site, $this);
		$user_id = $status_0->doRegistrationStatus0();
		if($user_id){
			$this->data->redirection = 'inner';
			$this->command['type'] = 'enter';
		}
		return;
	}
	
	private function runStatus0()
	{
		require_once 'objects/Status0.php';
		if(!isset($this->status_0))
			$this->status_0 = new Status0($this->site, $this);
			
		if(!$this->status_0->runStatus0()){
			//print_array('hello');
			$this->user->resetUser(-1);
		}
		elseif($this->data->redirection === 'inner'){
			$this->data->redirection = false;
			$this->runStatus0();	
		}
		
		unset($this->status_0);
	}
	
	private function chkConnect()
	{
		//print_array($this->command);
		$command = $this->command['type'];
		$operation = &$this->command['operation'];
		$invite = $this->user->getUser('invite');
		$net_name = $this->user->getUser('net_name');
		//print_array($this->user->getUser());
		if($command == 'create' && $operation != 'net_create' && !$net_name) return false;
		
		if(!$invite && !$net_name){
			if($command == 'authorize') return $operation = 'allow';
			if($command != 'create')
				return $operation = 'forbid';
		}
		
		$operation = $this->doConnectionStatusPlus1();
		//print_array($operation);
		
		if($operation == 'forbid_authorize'){
			if($command == 'authorize'){
				$operation = 'allow_authorize';
				return false;
			}
			//$this->command['data']['email'] = '';
			//redirect на connect з видачою повідомлення 'виберіть один із можливих варіантів
		}
		elseif($operation == 'forbid_registration'){
			$this->command['data']['email'] = '';
			if($command == 'registration'){
				$operation = 'allow_registration';
				return $operation = 'allow_registration';
			}
			//redirect на connect з видачою повідомлення 'виберіть один із можливих варіантів
		}	
		elseif($operation == 'allow_authorize'){
			if($command == 'authorize' && ($command == 'create' || $net_name)) return $operation = 'allow';
			elseif($command == 'authorize' || $command == 'connect' || $command == 'create') return $operation;			
			$this->data->next_command['type'] = 'registration';
			//redirect на connect з видачою повідомлення 'виберіть один із можливих варіантів'
		}
		elseif($operation == 'allow_registration'){
			if($command == 'registration' || $command == 'connect') return $operation;
			//redirect на connect з видачою повідомлення 'виберіть один із можливих варіантів'
		}
		elseif($operation == 'allow') return $operation;
		
		//print_array($this->command, 1);
		//print_array($operation);
		
		if(!$operation){
			if($command == 'create') return false;
			$operation = 'forbid';
			if($command == 'connect') return true;
			else $this->data->next_command['type'] = 'connect';
		}
		
		$this->user->resetUser(-1);
		return false;
	}

	private function doConnectionStatusPlus1()
	{
		require_once 'objects/StatusPlus1.php';
		$status_plus_1 = new StatusPlus1($this->site, $this);
		
		$net_name = $this->user->getUser('net_name');
		if($net_name || $this->command['type'] == 'create'){
			//print_array($this->command);
			if($status_plus_1->createNet()) return 'allow_authorize';
			return false;
		}
		
		return $status_plus_1->doConnectionStatusPlus1();
	}
}
?>