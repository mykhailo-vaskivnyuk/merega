<?php

class User
{
    private $site;				//object
	private $data;				//object
	private $user = array();	//array
	
	public function __construct(&$site)
    {
		$this->site = &$site;
		$this->data = &$site->data;
		$this->user = &$site->data->user;
	}
    
	public function setUser($data_name = '', $data_value = '', $module = '')
    {		
		if(!$data_name){
			switch($module){
				case -1:
					if($this->user['status'] === ''){
						$this->user['status'] = -1;
						$this->user['enter'] = true;
					}
					return true;
				case 0:
					$user = $this->data->getUser('user');
					if(!$user) return false;
					$this->data->setUser('notifications_count');
					$this->user['name'] = $user['name'];
					$this->user['email'] = $user['email'];
					$this->user['password'] = $user['password'];				
					$this->user['mobile'] = $user['mobile'];
					if($this->user['status'] >= 0) return true;
					
					$user['status'] = -5;
					if(!$user['link']) $user['status'] = 0;
					
					if($this->user['status'] == -1){
						//...
						$this->user['enter'] = true;
					}
					if($this->user['status'] == -5){
					//command 'enter' - подумати, аналогічно зробити для +1
						
					// ВАРІАНТ 1
						//if($user['status'] == 0){
						//	$this->data->command['type'] = 'enter';
						//	$this->user['enter'] = true;
						//}
						
					// ВАРІАНТ 2
						if($user['status'] == 0){
							$this->data->messages[] = array('type' => 'error', 'text' => 'Виконайте, будь-ласка, вхід в акаунт ще раз!');
							return false;
						}
					}
					$this->user['status'] = $user['status'];
					
					//if($this->user['status'] == 0){
					//	if($user['invite']){
					//		$this->data->setUser('invite_db', ''); //можна це робити при $user = $this->data->getUser('user');
					//		$this->user['invite'] = $user['invite'];
					//	}
					//}
					return true;
				case +1:
					do{
						$user = $this->data->getNet('user');
						if(!$user){
							if($this->user['parent_nets']){
								$this->user['net'] = key($this->user['parent_nets']);
								//$user['parent_net'] = key($this->user['parent_nets']);
								unset($this->user['parent_nets'][$this->user['net']]);
								//unset($this->user['parent_nets'][$user['parent_net']]);
								continue;
							}
							else return false;
						}
						if($user['id']) break;
						//reset !!!
						$this->user['net'] = $user['parent_net'];
					
					}while(empty($user['id']) && $this->user['net']);
/*				
					$user = $this->data->getNet('user');
					while(!$user && $this->user['parent_nets']){
						$this->user['net'] = key($this->user['parent_nets']);
						unset($this->user['parent_nets'][$this->user['net']]);
						$user = $this->data->getNet('user');
					}
*/					
					if(empty($user['id'])) return false;
					
					($user['invite']) ? $user['status'] = +5 : $user['status'] = +1;
					
					if($this->user['status'] > $user['status']) $this->user['status'] = $user['status']; //для виконання команди out
					
					$this->data->setIam('notifications_count');
					//print_array($user);
					if($this->user['status'] == +5){
						if($user['status'] == +1){
						//ВАРІАНТ 1		
							//$this->data->messages[] = array('type' => 'error', 'text' => 'Виконайте, будь-ласка, вхід в спільноту ще раз!');
							//return false;
						//ВАРІАНТ 2
							$this->user['enter'] = true;
							$this->user['status'] = +1; //на фіга
							$this->user['node'] = $user['node_id']; //на фіга?
							$this->resetUser(+1);
							//print_array($this->user);
						}
					}
					elseif($this->user['status'] == 0){
						//if($user['status'] == +1) $this->setIam('notification_close', 11);
						$this->user['enter'] = true;
						$this->user['status'] = $user['status'];
						$this->user['node'] = $user['node_id'];
					}
					else{
						$this->user['changes_in_tree'] = $user['changes'];
					}
					//$this->data->setIam('notifications');
					//print_array($this->user);
					//elseif($this->user['status'] == +1) //може так?
					if(!$this->user['node']) $this->user['node'] =  $user['node_id']; //у випадку виконання command['type'] == out;

					if($this->user['node'] != $user['node_id']){
						$this->user['node'] = $user['node_id']; //$this->user['node'] = ''; - оця штука не пройшла, ще варіант - command = in але без редірекшн
						$this->resetUser(+1);
						$this->user['status'] = $user['status']; //перевірити, це якщо в статуса +5 змінився вузол, але така ситуація не можлива
					}
					return true;
				case 10:
					//print_array($this->user, 1);
					$this->user['net'] = '';
					//print_array($this->user);
					return true;
				default:
			}
		}
		
		switch($data_name){		
			case 'id':		
				return $this->data->setUser('user_id');
			case 'user':		
				return $this->data->setUser('user');
			case 'restore':		
				return $this->data->setUser('restore');
			case 'notifications_new_reset':
				return $this->data->setUser('notifications_new_reset');
			case 'notifications_not_shown_reset':
				return $this->data->setUser('notifications_not_shown_reset');			
			case 'notification':
				return $this->data->setUser('notification', $data_value);
			case 'notification_close':
				return $this->data->setUser('notification_close', $data_value);				
			case 'nets':	
				return $this->data->setNet('user_nets'); //перенести в status +1
			case 'parent_nets':
				return $this->data->setNet('user_parent_nets'); //перенести в status +1
			case 'invite':
				$this->user['net_name'] = '';
				return $this->user['invite'] = $data_value;
			case 'net_name':
				$this->user['invite'] = '';			
				return $this->user['net_name'] = $data_value;
			default:
				$this->user[$data_name] = $data_value;
		}
    }
	
	public function getUser($data_name = '')
    {
		if(!$data_name) return $this->user;
		
		switch($data_name){
			case 'notifications':
				return $this->data->getUser('notifications');
			case 'notifications_count_new':
				return $this->user['notifications_count']['new'];
			case 'notifications_count_all':
				return $this->user['notifications_count']['all'];
			case 'notifications_count_not_shown':
				return $this->user['notifications_count']['not_shown'];				
			default:
				return $this->user[$data_name];
		}
	}

	public function updateUser($data_name = '')
    {
		$user_data = &$this->data->command['data'];
		$action = false;
		$success = true;
		//print_array($this->data->command);
		if($this->data->command['operation'] == 'reset_password'){
			if(!$this->user['password']) return null;
			$this->data->setUser('password', '') or $success = false;
			return $success;
		}
		
		if($user_data['user_name'] && $user_data['user_name'] != $this->user['name']){
			$action = true;
			$this->data->setUser('name', $user_data['user_name']) or $success = false;
		}
		else{
			//$this->data->message_error = 'Поле \'ІМ\'Я\' є обов\'язковим!';
		}
		
		if($user_data['user_mobile'] != $this->user['mobile']){
			$action = true;
			$this->data->setUser('mobile', $user_data['user_mobile']) or $success = false;
		}
		
		if($user_data['password'] && $user_data['password'] != $this->user['password']){
			$action = true;
			//чи потрібно підтвердження через мейл для зміни паролю?
			$this->data->setUser('password', $user_data['password']) or $success = false;
		}
		
		//if($user_data['email']){
			//чи можна змінювати мейл і, якщо так, то як?
		//}

		if(!$action) return null;

		return $success;
	}
	
	public function setIam($data_name = '', $data_value = '')
	{
		if(!$data_name){
			$this->data->setIam('iam');
			return true;
		}
		
		switch($data_name){
			case 'notifications_new_reset':
				return $this->data->setIam('notifications_new_reset');
			case 'notifications':
				return $this->data->getIam('notifications', 'all');
			case 'notifications_shown':
				return $this->data->setIam('notifications_shown');
			case 'notification_close':
				return $this->data->setIam('notification_close');
//			case 'parent_nets':
//				$this->user['parent_nets']= $data_value; //можна використати default
//				break;
			default:
				return $this->user[$data_name] = $data_value;
		}	
	}

	public function getIam($data_name = '', $data_value = '')
	{	
		switch($data_name){
			case 'notifications_count_new':
				return $this->user['notifications_count']['new'];
			case 'notifications_count_all':
				return $this->user['notifications_count']['all'];
			case 'notifications':
				//часто повторюється isset($this->user['notifications'][$data_value['circle_tree']]
				//$data_value['node_key']
				//$data_value['circle_tree']
				if($data_value['circle_tree'] === '')
					return $this->user['notifications'];
				
				if($data_value['circle_tree'] === 'response')
					return $this->data->getIam('notifications', 'response');

				if($data_value['node_key'] === 'all'){
					if(isset($this->user['notifications'][$data_value['circle_tree']]))
						return $this->user['notifications'][$data_value['circle_tree']];					
					else return false;
				}
				
				//print_array($this->user['notifications']);
				if($data_value['node_key'] === ''){
					if(isset($this->user['notifications'][$data_value['circle_tree']]))
						return $this->user['notifications'][$data_value['circle_tree']]['not_shown'];					
					else return false;
				}
					
				//print_array($this->user['notifications'][$data_value['circle_tree']][$data_value['node_key']]['notifications']);
				if(isset($this->user['notifications'][$data_value['circle_tree']][$data_value['node_key']]['notifications']))
					return $this->user['notifications'][$data_value['circle_tree']][$data_value['node_key']];
				else return false;
			case 'notification_view':
				return $this->data->getIam('notification_view');								
			default:
				return $this->user[$data_name];
		}	
	}
	public function resetUser($status = '')
    {
		$user = &$this->user;
		//print_array($user['net_name']);
		//$user['status'] = '';
		//$user['id'] = '';
		//$user['net'] = '';
		$user['nets'] = array();
		//$user['parent_nets'] = array(); //можна подумати
		//$user['node'] = '';
		$user['parent_node'] = '';
		$user['node_address'] = '';
		$user['full_node_address'] = ''; 	//перевірити
		$user['level'] = '';				//перевірити
		$user['count'] = '';
		//$user['notifications'] = array();
		$user['invite'] = '';
		$user['net_name'] = '';
		//$user['enter'] = '';
		$user['voice_for_key'] = '';
		
		$changes_in_tree = '';
		$user['email'] = '';
		$user['name'] = '';
		$user['mobile'] = '';				
		$user['password'] = '';
		$user['notifications_count']['new'] = array();
		$user['notifications_count']['all'] = array();
		$user['notifications_count']['not_shown'] = array();
		$user['link'] = '';
//print_array($user);
		switch($status){
			case -1:
				$user['status'] = -1;
				$user['id'] = '';
				$user['net'] = '';
				$user['parent_nets'] = array();
				$user['node'] = '';
				$user['notifications'] = array();
				//$user['invite'] = ''; //ПОДУМАТИ при restore та link
				$this->data->redirection = 'inner'; 	//якщо робити redirect то restore та confirm потрібно писати в сесію ..., або
														//встановлювати redirection = false
														//ще був варіант getRedirection server + restore/invite + код
				$this->data->command['type'] = '';
				break;
			case 0:
				$user['status'] = 0;
				$user['net'] = '';
				$user['parent_nets'] = array();
				$user['node'] = '';
				$user['notifications'] = array();
				$this->data->redirection = 'inner';
				$this->data->command['type'] = 'enter';
				break;
			case +1:
				$user['status'] = +1;
				//$user['invite'] = '';
				$this->data->redirection = 'inner';
				$this->data->command['type'] = 'in';
				//circle_tree ?
				break;
			case 10:
				$user['status'] = '';
				$user['id'] = '';
				$user['net'] = '';
				$user['parent_nets'] = array();
				$user['node'] = '';
				$user['notifications'] = array();
				$user['enter'] = '';
				//print_array($this->user);
				break;				
			default:
		}			
	}	
}
?>