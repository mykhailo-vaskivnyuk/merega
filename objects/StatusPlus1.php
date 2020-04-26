<?php

class StatusPlus1
{
    private $site;
	private $data;
	private $user;
	private $response;
	private $parent;
	private $command;
	private $Circle;		//object
	private $Tree;			//object
	private $net = array();	//array

    public function __construct(&$site, &$parent)
    {
		$this->site = &$site;
		$this->data = &$site->data;
		$this->user = &$site->user;
		$this->net = &$site->data->net;
		$this->response = &$site->response;
		$this->parent = $parent;
		$this->command = &$site->data->command;
		
		require_once 'Net.php';
		$this->Circle = new Circle($site, $this);
		$this->Tree = new Tree($site, $this);
	}
    
	public function runStatusPlus1()
    {
		//print_array($this->command);
	
		if(!empty($this->command['data']['user_id'])){
			if($this->user->getUser('id') != $this->command['data']['user_id'])		
				$this->command['type'] = '';
		}

		if(!empty($this->command['data']['net_id'])){
			if($this->net['id'] != $this->command['data']['net_id'])		
				$this->command['type'] = '';
		}
		
		//IN та OUT перенести в роутер
		if($this->command['type'] == 'out'){
			if(!$this->command['data']['net'])return false;
			$net_id = $this->command['data']['net'];
			$this->command['data']['net'] = '';
			$this->user->resetUser(+1);
			//якщо $net_id in user['parent_nets'] тоді в статус 0 можна не скидати
			//інший варіант перевіряти відповідність статусу в setUser
			//$this->user->setUser('status', 0);
			$this->user->setUser('net', $net_id);
			return true;
		}
			
		if($this->command['type'] == 'in'){
			if(!empty($this->command['data']['net'])){
				$net_id = $this->command['data']['net'];
				$this->command['data']['net'] = '';
				$this->user->resetUser(+1);
				//якщо $net_id in user['parent_nets'] тоді в статус 0 можна не скидати
				$this->user->setUser('status', 0);
				$this->user->setUser('net', $net_id);
				return true;
			}
		}
	
		If(!$this->user->setUser('', '', +1)) return false;
		//print_array($this->user->getUser());
		//повідомлення
		
		
		
		if($this->command['type'] != 'in'){
			if($this->user->getIam('notifications_count_new')){
				//server/data , але не server/data/circle/1/ чи  server/data/tree/1/
				$this->data->next_command = $this->command;
				$this->user->resetUser(+1);
			}
		}
		
		//якщо команда IN встановлюється до роутера команд, то на рекурсію можна не йти
		
		if($this->data->redirection !== false){ //ПОДУМАТИ де краще вставити
			//reset net (circle, tree, net)?
			return true;
		}
		
		//контроль структури
		//...
		//print_array($this->user->getUser());
		$user_status = $this->user->getUser('status');			
		switch($user_status){
			case +5;
			case +1;
				switch ($this->command['type']){
					case 'create':
						if(!$this->setNet('active_node')) break;
						if($this->net['active_node']['node_status'] != 1){
							$this->data->messages[] = array('type' => 'error', 'text' => 'Ви ще не ідентифіковані!');					
							$this->data->redirection = 'inner'; //????
							$this->command['type'] = '';
							break;
						}					
						if($this->command['operation'] == 'net_create'){
							$this->user->setUser('net_name', $this->command['data']['net_name']);
							$this->createNet();
							//print_array($this->command);
						}
						break;
					case 'in':
						//print_array('HELLO');
						//print_array($this->Circle->getCircle(), 1);						
						$this->inStatusPlus1();
						//print_array($this->user->getUser());
						//print_array($this->Circle->getCircle());
						break;
					case 'invitation':
						//print_array($this->command, 1);
						//setNet в процедурі
						$this->doInvitation();
						//print_array($this->command);
						break;
					case 'disconnect':
						if(!$this->setNet('active_node')) break;
						if($this->command['operation'] == 'disconnect'){
							$this->chgNet('disconnect');
							$this->data->redirection = 'inner';
							$this->command['type'] = 'in';
						}
						break;
					case 'statistic':
						if(!$this->setNet('active_node')) break;
						//якщо count не записувати в сесію, а доставати в active_node, то перезавантаження робити не потрібно
/*
						if($this->data->prev_command && $this->data->prev_command['type'] != 'in'){
							$this->data->next_command = $this->command;//поки робимо оновлення автоматичне при заході на пункт статистика
							$this->data->next_command['redirection'] = 'inner';
							$this->user->resetUser(+1); //може просто на in без ресет
							$this->data->prev_command = array();
							break;							
						}
*/
						if($this->command['data']['circle_tree'] == 'net'){
							$this->chgNets('set_net_count');
							break;
						}
						$node_status = $this->net['active_node']['node_status'];
						$node_key = $this->net['active_node']['key'];
						if(	($node_status != 1 && $node_status != -1) || $user_status == 5){
							$this->data->redirection = 'inner';
							$this->command['type'] = $this->net['circle_tree'];
							//$this->command['data']['circle_tree'] = '';
						}
						if($node_key <> 1 && $node_status == 1){
							if($this->command['operation']){
								if(!$this->setMember('dislike')){
									$this->data->redirection = 'inner';
									$this->command['type'] = $this->net['circle_tree'];
									break;
								}
								if($this->command['data']['dislike'] == 'on')
									$this->chgNet('dislike', '', $this->net['active_node']['parent_node']);
							}
						}
						break;
					case 'vote':
						if(!$this->setNet('active_node')) break;
						$active_node = $this->net['active_node'];
						$node_key = $active_node['key'];
						$node_status = $active_node['node_status'];
						$user_voice = $this->user->getUser('voice_for_key');
						if($node_key == 0){
							$this->Circle->setCircle('voices');
							break;						
						}
						if($node_status != 1 || !$this->command['operation']){
							$this->data->redirection = 'inner';
							$this->command['type'] = $this->net['circle_tree'];
						}
						//print_array($this->command);
						//print_array('HELLO');
						if($this->command['operation'] == 'voice_set'){
							($this->command['data']['voice'] == 'on') ? $voice = 'on' : $voice = 'off';
							//print_array($voice);
							//дублюєм перевірку, оскільки такаж є на рівні БД
							if($voice == 'on' && $user_voice == $node_key){
								//print_array('HELLO');
								$this->data->redirection = true;
								$this->command['data']['member'] = 0;
								break;
							}
							//частково дублюєм перевірк $voice == 'off' && !$user_voice, оскільки такаж є на рівні БД
							if($voice == 'off' && $user_voice != $node_key){
								//print_array('HELLO');
								$this->data->redirection = true;
								$this->command['data']['member'] = 0;
								break;
							}
							if($voice == 'on' && !$user_voice){
								//print_array($this->command);
								$this->setMember('voice_set');
								$this->chgNet('vote', 'voice_set', $active_node['parent_node']);
								//перенаправлення:
								//варіант 1: key == 1
								//варіант 2: key != 1
								break;
							}
							if($voice == 'off' && $user_voice == $node_key){
								//print_array('HELLO');
								$this->setMember('voice_set');
								break;							
							}
							$this->data->next_command = $this->command;
							$this->data->next_command['redirection'] = 'inner';
							$this->data->redirection = 'inner';
							$this->command['data']['member'] = $user_voice;
							$this->command['operation'] = 'voice_reset';
							$this->command['data']['voice'] = '';
							//print_array($this->command);
							break;
						}
						elseif($this->command['operation'] == 'voice_reset'){
							//print_array($this->command);
							$this->setMember('voice_reset');
							//$this->command = $this->data->next_command;
							//$this->data->redirection = 'inner';
							break;
						}
						$this->data->redirection = true;
						$this->command['data']['member'] = 0;				
						break;
					case 'notification':
						//а якщо notifications засовувати в active_node?
						//print_array($this->user->getUser());			
						if(!$this->setNet('active_node')) break; //if -> break
						if($this->command['operation'] == 'close'){
							//зробити перевірку на відповідність circle_tree та key сесії та notification_id перевіривши його в масиві
							//для key == 1 (net) перевірка проходитиме трохи інакше
							//хоча без перевірки глюку все-одно не буде
							$this->user->setIam('notification_close');
							//$this->data->redirection = true;
							//break;
						}
						elseif($this->command['operation'] == 'view'){
							//$this->data->redirection = true;
							$command = $this->user->getIam('notification_view');
							//print_array($command);
							if(!is_null($command)) $this->command['type'] = $command;
							elseif(count($this->net['active_node']['sub_menu']) == 1){ //ВАРІАНТ 1 (коли у вузлі лише повідомлення, а в шаблонах команда == null) //подумати ще раз як краще
								//print_array($this->command);
								$this->data->redirection = true;
								$this->command['type'] = 'data';
								$this->command['data']['circle_tree'] = '';
							}
							else{
								//print_array($this->command);
								$this->data->redirection = 'inner';
								$this->command['type'] = $this->net['circle_tree']; //можна на ''
								//if($this->net['active_node']['sub_menu'] == 1) //ВАРІАНТ 2 (коли у вузлі лише повідомлення, а в шаблонах команда == null)
								//	$this->command['data']['circle_tree'] = '';
							}
							break;
						}
						$this->command['operation'] = 'read';
						
						$node_key = $this->net['active_node']['key'];
						if($node_key == 1){
							$this->command['data']['circle_tree'] = 'net';
							$notifications = $this->user->getIam('notifications', array('circle_tree' => 'net', 'node_key' => 'all'));
						}
						else{
							$notifications = $this->user->getIam('notifications', array('circle_tree' => $this->net['circle_tree'], 'node_key' => $node_key));
							
						}
						//$notifications = $this->user->getIam('notifications', 'control_node');
						//print_array($this->user->getUser(), 1);
						//print_array($notifications);
						if(!$notifications){
						//if($node_key == 1){
							$this->data->redirection = 'inner';
							$this->command['type'] = $this->net['circle_tree']; //можна на ''
							//$this->command['data']['circle_tree'] = '';
						}
						else $this->user->setIam('notifications_shown');
						//print_array($this->net['active_node']);
						break;				
					case 'data':					
						if(!$this->setNet('active_node')) break; //перевірити переадресацію
						//print_array($this->command);
						if($this->command['data']['circle_tree'] == 'net'){
							//if(!$this->chkNet()) break; //можливо не потрібно, перевірити
							if($this->command['operation'] == 'write'){
								//$this->data->redirection = 'true';
								if(	$this->command['data']['net_id'] &&
									$this->command['data']['user_id'] &&
									$this->net['active_node']['level'] == 1){
									//key == 1 не перевіряємо, оскільки жорстко зашитий в команду
									//if($this->updateNet() === -1) break;
									$this->updateNet();
									//print_array($this->command);
									
								}
								else $this->command['operation'] = 'read';	
							}
							$this->chgNets('get_net_data');
							//print_array($this->net);
							//print_array($this->command);
							break;
						}
						
						
						$node = $this->net['active_node'];
						$node_status = $node['node_status'];
						$node_key = $node['key'];
						$circle_tree = $this->net['circle_tree'];
						//print_array($node);
						if(!isset($node['sub_menu'][0])){
						//if($node_status != 1 && $node_key != 1){
						
							$this->data->redirection = 'inner';
							$this->command['type'] = $circle_tree;	//'data'; ''; //$this->net['circle_tree'];
															//$this->command['data']['circle_tree'] = '';
							break;
						}
						//print_array($this->command);
						if(	$this->command['operation'] == 'write' &&
							($node_key != 1 || $circle_tree == 'circle') &&
							$this->command['data']['user_id']){
							//$this->data->redirection = 'true';
							if($node_key == 1){
								if(!$this->data->setNet('user_data')) break;
								$this->data->redirection = 'true';
								break;
							}
							elseif($this->command['data']['member_id'] && $this->command['data']['member_node']){
								//$success = $this->data->setNet('user_for_member');
								$this->setMember();
								break;
								//if($success == 1){
								//	$this->data->next_command = $this->command;
								//	$this->data->redirection = 'inner';
								//	$this->command['type'] = 'in';
								//}
							}
						}
						elseif($this->command['operation'] == 'edit' && ($node_key != 1 || $circle_tree == 'circle')){
							break;
						}
						$this->command['operation'] = 'read';
						//print_array($this->net);
						break;
					case 'goal':
						if(!$this->setNet('active_node')) break; //може просто chkNet? мабуть не можна, бо з цієї команди ідемо на response
						//print_array($this->command);
						if($this->command['operation'] == 'write'){
							if($this->net['active_node']['level'] == 1){
								//$this->data->redirection = 'true';
								//if($this->updateNet() === -1) break;
								$this->updateNet();
							}
							else $this->command['operation'] = 'read';
						}
						$this->chgNets('get_net_goal');
						break;
					case 'circle':
						//print_array($this->command);
						$this->command['data']['circle_tree'] = 'circle';
						if(empty($this->command['data']['member'])){
							$notifications = $this->user->getIam('notifications', array('circle_tree' => 'circle', 'node_key' => 'all'));
							if($notifications && $notifications['not_shown']){
								for($c = 0; $c <= 6; $c++){
									if(empty($notifications[$c]) ||  !$notifications[$c]['not_shown']) continue;
									$this->command['data']['member'] = $c;
								}
							}
						}
						if(!$node = $this->chkNet()) break;						
						$this->data->redirection = true;
						$this->command['type'] = 'data';
						//print_array($node);
						$node_status = $node['node_status'];
						$node_key = $this->command['data']['member'];
						//для $node_key == 1 $notifications не потрібні
						//нам фактично можна отримувати лише $not_shown 0, >0, null
						//print_array($this->command);
						$notifications = $this->user->getIam('notifications', array('circle_tree' => $this->net['circle_tree'], 'node_key' => $node_key));
						//print_array($this->user->getUser());
						//print_array($notifications);
						if(	$notifications &&
							($notifications['not_shown'] > 0 ||
							($node_key == 0 && $user_status == 5) ||
							$node_status == 0)){
							$this->command['type'] = 'notification';
						}
						elseif($node_status == -1)
							$this->command['type'] = 'statistic';
						elseif($node_status == 0)
							$this->command['data']['circle_tree'] = '';
						break;
					case 'tree':
						$this->command['data']['circle_tree'] = 'tree';
						if(empty($this->command['data']['member'])){
							$notifications = $this->user->getIam('notifications', array('circle_tree' => 'tree', 'node_key' => 'all'));
							if($notifications && $notifications['not_shown']){
								for($c = 2; $c <= 7; $c++){
									if(empty($notifications[$c]) ||  !$notifications[$c]['not_shown']) continue;
									$this->command['data']['member'] = $c;
								}
							}
						}
						if(!$node = $this->chkNet()) break;
						$this->data->redirection = true;
						$this->command['type'] = 'data';
						$node_status = $node['node_status'];
						$node_key = $this->command['data']['member'];
						//print_array($this->net);
						$notifications = $this->user->getIam('notifications', array('circle_tree' => $this->net['circle_tree'], 'node_key' => $node_key));
						//print_array($notifications);
						if(	$notifications &&
							$notifications['not_shown'] > 0){	
							$this->command['type'] = 'notification';
						}
						elseif($node_status == -1)
							$this->command['type'] = 'statistic';
						elseif($node_status != 1 )
							$this->command['type'] = 'invitation';
						break;
					case '':
						//А де chkNet ?
						//$this->data->redirection = true;
						$this->data->redirection = 'inner';
						$this->command['type'] = $this->net['circle_tree'];
						$this->command['data']['circle_tree'] = '';
						$this->command['data']['member'] = '';
						//$notifications = $this->user->getIam('notifications', array('circle_tree' => 'net', 'node_key' => 'all'));
						$notifications = $this->user->getIam('notifications', array('circle_tree' => ''));
						//print_array($notifications);					
						(empty($notifications['net'])) 		? $notif_net = '' 		: $notif_net = $notifications['net'];
						(empty($notifications['circle'])) 	? $notif_circle = '' 	: $notif_circle = $notifications['circle'];
						(empty($notifications['tree'])) 	? $notif_tree = ''	 	: $notif_tree = $notifications['tree'];
						if(	$notif_net &&
							$notif_net['not_shown']){
							$this->data->redirection = true;
							$this->command['type'] = 'notification';
							//$this->command['data']['circle_tree'] = '';
							}
						else {
							if($notif_circle && $notif_tree &&
								$notif_circle['not_shown'] && $notif_tree['not_shown']){
								//$this->data->redirection = 'inner';
								//$this->command['type'] = $this->net['circle_tree'];
								//$this->command['data']['circle_tree'] = $this->net['circle_tree'];
							}
							elseif($notif_circle && $notif_circle['not_shown']){
								//$this->data->redirection = 'inner';
								$this->command['type'] = 'circle';
								//$this->command['data']['circle_tree'] = 'circle';
							}
							elseif($notif_tree && $notif_tree['not_shown']){
								//$this->data->redirection = 'inner';
								$this->command['type'] = 'tree';
								//$this->command['data']['circle_tree'] = 'tree';							
							}
							//else
								//$this->command['type'] = 'data'; //поки
							//$this->command['data']['circle_tree'] = ''; //можливо не потрібно
						}
						//circle_tree ?
						//$this->command['data']['circle_tree'] = '';
						$this->user->setUser('enter', false);
						//print_array($this->command);
						break;
					default:
						$this->data->redirection = 'inner';
						$this->command['type'] = '';
				}
				break;
			default:
				//...
		}
		
		if(!$this->data->redirection){
			$this->response->setResponse('', '', $this->command, $this->user, $this->data, $this->Circle, $this->Tree, $this->net);
		}
//		elseif($this->data->redirection === 'inner'){
//			//print_array($this->data->command, 1);
//			$this->data->redirection = false;
//			//	$this->data->getCommand();
//			//print_array($this->data->command);
//			$this->runStatusPlus1();
//		}
//print_array($this->command);
		return true;
	}
	
	public function doAuthStatusPlus1()
    {
		//...
    }
	
	public function doConnectionStatusPlus1() //$check_invite = false) //$chk = false
    {
		$operation = $this->data->getNet('node_id');
		if(!$operation) return false;
		
		$user_id = $this->user->getUser('id');
		if(!$user_id) return $operation;
		//print_array($this->user->getUser());
		$node['node'] = $this->user->getUser('node');
		$node['parent_node'] = $this->user->getUser('parent_node');
		//print_array($node);
		if(!$this->data->chgNet('set_block', $node, 'connection')) return false;
		//print_array($node);
		$user_net = $this->data->setNet('connection');
		//print_array($node);
		$this->data->chgNet('unset_block', $node, 'connection');
		return $user_net;
    }
	
	public function inStatusPlus1()
	{
		//print_array($this->user->getUser());
		$this->command = array();
		
		$this->data->redirection = 'inner';
		$this->command['type'] = '';
		
		//зчитуємо дані мережі
		$this->setNet();
		//print_array($this->net);
		//зчитуємо дані Я
		$this->user->setIam();

		if($this->user->getUser('status') == +1) //можна перенести в setIam
			$this->user->setUser('nets');
		
		//$this->user->setIam('parent_nets', [0 => '']);
		//if($this->net['parent_net'])
			$this->user->setUser('parent_nets');

		//print_array($this->user->getUser());

		//зчитуємо дані кола та дерева
		$this->net['circle_tree'] = ''; //ЛАЖА ЗІ СКИДАННЯМ СЕСІЇ ДЛЯ NET
		if($this->Tree->setTree()) $this->net['circle_tree'] = 'tree';
		if($this->Circle->setCircle()) $this->net['circle_tree'] = 'circle';
		//print_array($this->net['circle_tree']);
		//print_array($this->user->getUser());
		//print_array($this->Circle->getCircle());
		if(!$this->net['circle_tree']){
			$this->Circle->setCircle('iam');
			$this->data->redirection = 'inner';
			$this->command['type'] = 'invitation';
			$this->command['operation'] = 'approve';
			$this->command['data']['circle_tree'] = 'circle';
			$this->command['data']['member'] = 1;
		}
		//зчитуємо повідомлення
		$notifications_count_new = $this->user->getIam('notifications_count_new');
		if($notifications_count_new)
			$this->user->setIam('notifications_new_reset');
		
		//if($notifications_count_new || ($this->user->getUser('enter') && $this->user->getIam('notifications_count_all'))){
			$this->user->setIam('notifications', 'all');
			//print_array($this->user->getUser());
		//}поки перечитуємо повідомлення завжди, оскільки при виконанні команди refuse ще не зрозуміло де краще обнуляти повідомлення в user
		//це можливо можна робити в notification_close
	

		if($this->data->next_command){ //можливо next_command не потрібно писати в сесію
			$this->data->prev_command = $this->command; //?
			if(!empty($this->data->next_command['redirection']))		
				$this->data->redirection = $this->data->next_command['redirection'];
			else $this->data->redirection = true; //перевірити
			$this->command = $this->data->next_command;
			$this->data->next_command = array();
		}
		//print_array($this->command);
		return true;
	}
	
	public function chkNet($data_name = '', $data_value = '')
	{		
		//перенести в setNet
		$changes_in_tree = $this->user->getUser('changes_in_tree');
		if($changes_in_tree){
			$node_id = $this->user->getUser('node');
			//print_array($this->command, 1);
			$this->chgNet('constrict', '', $node_id);
			//$this->data->next_command = $this->command;
			$this->data->redirection = 'inner';
			//print_array($this->command);
			return false;
		}
		
		//перенести в setNet
		$changes_in_circle = $this->Circle->getCircle('changes');
		if($changes_in_circle){
			$node_id = $changes_in_circle;
			$this->chgNet('constrict', '', $node_id);
			//$this->data->next_command = $this->command;
			$this->data->redirection = 'inner';
			return false;
		}

		//в node можна засовувати key, або тут або в getCircle (getTree)
		$node_key = &$this->command['data']['member'];
		if($node_key === '') $node_key = 1;
		$circle_tree = $this->command['data']['circle_tree'];
		if(!$circle_tree || $circle_tree == 'net') $circle_tree = $this->net['circle_tree'];
		//if(!$circle_tree) $circle_tree = $this->net['circle_tree'];
		
		//print_array($this->command);
		//print_array($this->net);
		$node = false; //ПЕРЕВІРИТИ
		if($circle_tree == 'circle'){
			$node = $this->Circle->getCircle('node', $node_key);
			//print_array($this->Circle->getCircle());
			//if(!$node || !$node['node_status']) $node = false;
			if(!$node) $node = false; //може це не потрібно?
		}	
		elseif($circle_tree == 'tree')
			$node = $this->Tree->getTree('node', $node_key);

		if(!$node){
			$this->data->redirection = true; //'inner'
			$this->command['type'] = 'data'; //можна на ''
			$this->command['data']['circle_tree'] = '';
			return false;
		}
		
		//if($circle_tree != 'net')
			$this->net['circle_tree'] = $circle_tree;
		//print_array($this->command);
		if(!empty($this->command['data']['member_id'])){
			if($node['user'] != $this->command['data']['member_id']){
/*			ВАРІАНТ 1
				$this->data->redirection = true;
				$this->command['type'] = $circle_tree;
*/
/*			ВАРІАНТ 2			
				$this->data->next_command['type'] = $this->command['type'];			
				$this->data->next_command['data']['circle_tree'] = $this->command['data']['circle_tree'];
				$this->data->next_command['data']['member'] = $this->command['data']['member'];
				$this->data->next_command['redirection'] = 'inner';
				$this->data->redirection = 'inner';
				$this->command['type'] = 'in';
*/
/*			ВАРІАНТ 3
				$command = $this->command;
				$this->command = array();
				$this->command['type'] = $this->command['type'];			
				$this->command['data']['circle_tree'] = $this->command['data']['circle_tree'];
				$this->command['data']['member'] = $this->command['data']['member'];
*/
/*			ВАРІАНТ 4	*/
				$this->command['operation'] = '';
				
				return true;
			}
		}
		
		if(!empty($this->command['data']['member_node'])){
			if($node['node'] != $this->command['data']['member_node']){
/*			ВАРІАНТ 1
				$this->data->redirection = true;
				$this->command['type'] = $circle_tree;
*/
/*			ВАРІАНТ 3
				$command = $this->command;
				$this->command = array();
				$this->command['type'] = $this->command['type'];			
				$this->command['data']['circle_tree'] = $this->command['data']['circle_tree'];
				$this->command['data']['member'] = $this->command['data']['member'];
*/
/*			ВАРІАНТ 4	*/
				$this->command['operation'] = '';
				
				return true;

			}
		}			
		
		return $node;
	}
	
	public function setNet($data_name = '', $data_value = '')
	{
		if(!$data_name){
			$this->data->chgNets('net');
			//print_array($this->net);
			return;
		}
		
		switch($data_name){
			case 'active_node':
				//ДОДАТИ ОНОВЛЕННЯ active_node в СЕСІЇ
				$active_node = &$this->net['active_node'];
				if(!$active_node = $this->chkNet()) return false;
				//print_array($active_node);
				//print_array($this->command);
				$circle_tree = $this->net['circle_tree'];
				//if(empty($this->command['data']['member'])) print_array($this->command);
				$node_key = $this->command['data']['member']; //на фіга
				//print_array($this->command);
				//$active_node = $node;
				$active_node['key'] = $node_key; //на фіга
				//name дописати
				$active_node['email'] = ''; // поки так $this->user->getUser('email');
				$active_node['mobile'] = '';
				if($active_node['user']){
					$user = $this->parent->getUser('user', $active_node['user']);
					//print_array($user, 1);
					//print_array($this->user->getUser());
					$active_node['user_name'] = $user['name'];
					$active_node['email'] = $user['email'];
					$active_node['mobile'] = $user['mobile'];
				}
				//print_array($user);
				
				//ЯКЩО key == 1, то можна брати з user
				$node = $this->data->getNet('node');
				
				$node_status = $active_node['node_status']; //статус з сесії, а не свіжий з node
				
				//print_array($this->command); 
				if($node_key != 0 && $node_key != 1 && $node_status == -1){
					$changes = $node['changes'];
					if($changes){
						$node_id = $active_node['node'];
						$this->chgNet('constrict', '', $node_id);
						//$this->data->next_command = $this->command;
						$this->data->redirection = 'inner';
						return false;
					}
				}			
				
				$name_show = $node['name_show'];
				$email_show = $node['email_show'];
				$mobile_show = $node['mobile_show'];
				$active_node['email_show'] = $email_show;
				$active_node['name_show'] = $name_show;
				$active_node['mobile_show'] = $mobile_show;				
				$name = $active_node['user_name'];
				
				if($node_key == 1 || $node_key == 0){
					$name_show = 1;
					$email_show = 1;
					$mobile_show = 1;
				}
				
				if($node_key == 0 && $this->user->getUser('status') == 5){
					$name_show = 0;
				}
				
				if($active_node['user']){ //if($active_node['user'] && $node_key <> 1 && $node_key <> 0){
					//if($node_key <> 1){
						if(!$email_show) $active_node['email'] = NULL;
						
						if(!$name_show) $active_node['user_name'] = NULL;
						if(!$mobile_show) $active_node['mobile'] = NULL;
					//}
				}
				
				if($node['email']) $active_node['email'] = $node['email'];
				
				if($node_key == 1) $name = 'Я (' . $name . ')';
				elseif($node['list_name']){
					if($name && $name_show) $name = '.' . $node['list_name'] . ' (' . $name . ')';
					else $name = '.' . $node['list_name'];
				}
				elseif($name && !$name_show){
					If($node_key == 0) $name = 'Координатор';
					else $name = 'Учасник ' . ($node_key - 1);
				}
				elseif(!$name){
					If($node_key == 0) $name = 'Координатор';
					elseif($circle_tree == 'tree') $name = 'Комірка ' . ($node_key - 1);
					else $name = 'Комірка ' . ($node_key - 1);
				}
				
				$active_node['content_menu_left_text'] = $name;
				$active_node['level'] = $node['node_level'];
				$active_node['address'] = $node['node_address']; //необхідно для стискання, хоча можна доставати потім
				$active_node['parent_node'] = $node['parent_node_id']; //необхідно для стискання, хоча можна доставати потім
				$active_node['full_address'] = $node['full_node_address'];
				$active_node['list_name'] = $node['list_name'];
				$active_node['note'] = $node['note'];
				$active_node['first_node'] = $node['first_node_id'];
				$active_node['count'] = $node['count_of_members'];
				$active_node['date'] = $node['node_date'];
				$active_node['changes'] = $node['changes'];
				//$active_node['email_show'] = $email_show;
				//$active_node['name_show'] = $name_show;
				//$active_node['mobile_show'] = $mobile_show;
				
				// 0 - МОЇ ДАНІ/ДАНІ
				// 1 - ЗАПРОШЕННЯ
				// 2 - ІДЕНТИФІКАЦІЯ
				// 3 - СТАТИСТИКА			
				// 4 - ПОВІДОМЛЕННЯ
				// 5 - ОБРАННЯ
				
				$sub_menu = array();
				//$node_status = $active_node['node_status']; //переніс вище
				$server = $this->data->getServer('HTTP_HOST');
				$link_node = $circle_tree . '/' . $node_key . '/';
				$user_status = $this->user->getUser('status');
				$node_notifications = $this->user->getIam('notifications', array('circle_tree' => $circle_tree, 'node_key' => $node_key));

				if($node_key == 1){
					$sub_menu[0] = 	array(	'text' => 'МОЇ ДАНІ',
											'link' => $server . 'data/' . $link_node,
											'active' => false);
				}
				elseif($node_status == 1 && $user_status != 5){
					$sub_menu[0] = 	array(	'text' => 'ДАНІ',
											'link' => $server . 'data/' . $link_node,
											'active' => false);
				}
			
				if($circle_tree == 'tree' && (!$node_status || $node_status == -5)) $sub_menu[1] = 
									array(	'text' => 'ЗАПРОШЕННЯ',
											'link' => $server . 'invitation/' . $link_node,
											'active' => false); //в дереві
				elseif($node_status == 5 && $node_key != 1) $sub_menu[2] =
									array(	'text' => 'ІДЕНТИФІКАЦІЯ',
											'link' => $server . 'invitation/' . $link_node,
											'active' => false); //в дереві, в колі лише для Я
				
				if(($node_status == 1 || $node_status == -1 || $node_key == 0) && $user_status != 5) $sub_menu[3] =
									array(	'text' => 'СТАТИСТИКА',
											'link' => $server . 'statistic/' . $link_node,
											'active' => false);
											
				if($node_key == 0 && $user_status != 5) $sub_menu[5] =
									array(	'text' => 'ОБРАННЯ',
											'link' => $server . 'vote/' . $link_node,
											'active' => false);

				if($node_notifications) $sub_menu[4] =
									array(	'text' => 'ПОВІДОМЛЕННЯ',
											'link' => $server . 'notification/' . $link_node,
											'active' => false);								
				
				$active_node['sub_menu'] = $sub_menu;																				
				//print_array($this->net['active_node']);
				return true;
			case 'user_nets':
				return $this->data->setNet('user_nets', $data_value);
			default:
		}
	}
/*
	public function getNet($data_name = '', $data_value = '')
	{
		switch($data_name){
			case 'net_data':
				$this->data->chgNets('get_net_data');
				break;
			default:
				//...
		}
	}
*/	
	public function updateNet($operation = '')
	{
		// -1 невірні значення полів
		// NULL дані не змінились
		// true нові дані успішно записано
		// помилка при виконанні функції
		
		$success = true; //може false? і аналогічно в setMembers та інших?
		$data = &$this->command['data'];
		
		if($this->command['type'] == 'goal'){
			$net_goal = $data['net_goal'];
			//print_array($this->command);
			if(strlen($net_goal) <= 5000 ){
				//...
			}
			else{
				//$this->data->messages[] = array();
				return -1; //залишаємось в команді write
			}
			$this->command['operation'] = 'read';
			return $this->chgNets('set_net_goal');
		}
		
		//if($operation){
		//	$list_name = '';
		//	$note = '';
		//}
		//else{
			$net_name = $data['net_name'];

		//}
		
		if(strlen($net_name) && strlen($net_name) <= 200 ){
			//...
		}
		else{
			//print_array('hello');
			$success = -1;
			$this->data->messages[] = array('type' => 'error', 'text' => 'Введіть назву спільноти!');
		}
		
		if($operation == 'net_create') return $success;
		
		for($link_key = 1; $link_key <= 4; $link_key++){
			//print_array('hello');
			$data['links'][$link_key]['resource_name'] = $data['link_name_' . $link_key];
			$data['links'][$link_key]['resource_link'] = $data['link_value_' . $link_key];
		}		
		
		foreach($data['links'] as $net_link){
			if(strlen($net_link['resource_name']) <= 50){
				//...
			}
			else{
				$success = -1;
				//$this->data->messages[] = array();
			}
			
			if(strlen($net_link['resource_link']) <= 100){
				//...
			}
			else{
				$success = -1;
				//$this->data->messages[] = array();
			}
		}
		
		if($success === -1) return $success; //залишаємось в команді write

		$success = $this->chgNets('set_net_data'); //, $operation);
		//return $success;
		
//		if($operation == 'invite')
//			$this->command['operation'] = 'waiting'; //подумати де краще розмістити роутер тут чи там де викликається
//		elseif($operation == 'dislike'){
//			//...
//		}		
//		else
			$this->command['operation'] = 'read';
		
		if(!$success) return $success;
		//print_array('HELLO');
		//$this->data->next_command = $this->command;
		//$this->data->redirection = 'inner';
		//$this->command['type'] = 'in'; //ще можливі варіанти 'data' та ''
		return $success;
	}
	
	private function doInvitation()
	{
		//ФОРМУВАТИ ЗАПРОШЕННЯ МОЖНА ЛИШЕ В СТАТУСІ 1
		if(!$this->setNet('active_node')){
//			$this->data->redirection = true; // 'inner'
//			$this->command['type'] = 'data'; // ''
//			$this->command['data']['circle_tree'] = '';
			return false;
		}
		
		$operation = &$this->command['operation'];
		if($operation == 'create' || $operation == 'delete' || $operation == 'write'){
			if(	!$this->command['data']['user_id'] ||
				!$this->command['data']['member_node']){
				$operation = '';
			}
		}
		elseif(($operation == 'refuse' || $operation == 'approve') && $this->net['active_node']['level'] != 1){
			if(	!$this->command['data']['user_id'] ||
				!$this->command['data']['member_node'] ||
				!$this->command['data']['member_id']){
				$operation = '';
			}
		}
		
		
		
		//print_array($this->command, 0);
		//print_array($this->command, 0);
		//print_array($this->net['active_node'], 0);
		$node_status = $this->net['active_node']['node_status'];
		$circle_tree = $this->net['circle_tree'];
		//print_array($this->net['active_node']);
		if(($circle_tree == 'circle' || $node_status == 1 || $node_status == -1) && $this->net['active_node']['parent_node']){
			$this->data->redirection = true; // 'inner'
			$this->command['type'] = 'data'; // ''
			$this->command['data']['circle_tree'] = '';
			return false;
		}		
		
		if($node_status == 0){
			//if($this->command['data']['member_node'] && $this->command['data']['user_id']){
				if($this->command['operation'] == 'create'){
					$this->data->setNet('invite');
					$email = $this->command['data']['email'];
					if($email){
						//print_array($email);
						$data['email'] = $email;
						$link = $this->net['active_node']['invite'];
						$data['link'] = $this->data->getServer('HTTP_HOST') . 'invite/' . $link . '/';
						$data['sender'] = $this->user->getIam('name');
						$mail_error = $this->site->mail->sendMail('invite', $data);		
						if($mail_error)
							$this->data->messages[] = array('type' => 'error', 'text' => 'Відправка мейла не вдалась! ' . $this->site->mail->message);
					}
					$this->command['operation'] = 'waiting';
				}
			//}
			else{
				$this->command['operation'] = 'ready';
				return true;	
			}
		}
		elseif($node_status == -5){
			//if($this->command['data']['member_node'] && $this->command['data']['user_id']){
				if($this->command['operation'] == 'delete'){
					//$this->data->setNet('invite');
					//$node['reason'] = 'delete';
					$this->chgNet('disconnect', 'delete');
					$this->command['operation'] = 'ready';	
				}
				elseif($this->command['operation'] == 'write'){
					$this->setMember('invite');
					//$this->command['operation'] = 'waiting';
					return;
				}
			//}
			elseif($this->command['operation'] == 'edit'){
				return true;
			}
			else{
				//print_array($this->net['active_node']);
				$this->command['operation'] = 'waiting';
				return true;
			}
		}
		elseif($node_status == 5){
			//if($this->command['data']['member_id'] && $this->command['data']['member_node'] && $this->command['data']['user_id']){
				if($this->command['operation'] == 'approve'){
					//print_array('EVRICA!!!');
					$this->data->setNet('invite');
					//print_array('EVRICA!!!');
					$this->command['type'] = 'data'; //шо за фігня?
				}
				elseif($this->command['operation'] == 'refuse'){
					//$this->data->setNet('invite');
					$this->chgNet('disconnect', 'refuse');
					$this->command['operation'] = 'ready';
				}
			//}
			else{
				$this->command['operation'] = 'connected';
				return true;
			}
		}
		else {
			//return false
		}
		//print_r($this->command);
		$this->data->next_command = $this->command;
		$this->data->redirection = 'inner';
		$this->command['type'] = 'in';
		//print_array($this->data->next_command, 1);
		//print_array($this->command);
		return true;
	}
	
	public function chgNet($operation, $reason = '', $node = '')
	{
		$origin_operation = $operation;
		$max_level = $this->net['max_level']; //перевірити чи доступна ця змінна при limit_on_vote
												//доступна, бо при створенні обєкта Site запускається initData
		//print_array($this->net['active_node']);
		if(!$node) $node = $this->net['active_node'];
		else {
			$data = $this->data->getNet('node', $node);
			$node = array();
			//оптимізувати
			$node['count'] = $data['count_of_members'];
			$node['node_status'] = $data['node_status'];
			$node['node'] = $data['node_id'];
			$node['user'] = $data['user_id'];
			$node['level'] = $data['node_level'];
			$node['address'] = $data['node_address'];
			$node['full_address'] = $data['full_node_address'];
			$node['parent_node'] = $data['parent_node_id'];
			$node['first_node'] = $data['first_node_id'];
			$node['date'] = $data['node_date'];
			$node['changes'] = $data['changes'];
		}
		
		$node['reason'] = $reason;

		if($operation == 'disconnect'){
			//можна спробувати передавати node по ссилці
			if($reason != 'limit_on_vote' && $reason != 'disconnect_from_parent' &&
				$reason != 'vote' && $reason != 'revote' && $reason != 'unvote'){
				if(!$this->data->chgNet('set_block', $node, 'disconnect')) return false;
			}
			if(!$reason ||
				$reason == 'dislike_in_circle' ||
				$reason == 'dislike_in_tree' ||
				$reason == 'limit_on_vote' ||
				$reason == 'disconnect_from_parent'){ //ЧИ обовязкові ці умови, може завжди перевіряти user_nets?
				
				$user_nodes_in_nets = $this->setNet('user_nets', $node);
				//print_array($user_nodes_in_nets);
				foreach($user_nodes_in_nets as $user_node_in_net){
					if(!$this->chgNet('disconnect', 'disconnect_from_parent', $user_node_in_net['node'])){
						return false;
					}
				}
			}
			$this->data->chgNet('disconnect', $node);
			//$this->data->chgNet('unset_block', $node, 'disconnect'); //перенести в constrict
			//ПЕРЕВІРИТИ
			if($this->net['notifications']){
				$this->parent->setNotifications($this->net['notifications']);
			}
			if($reason != 'revote' && $reason != 'unvote'){
				$node['user'] = ''; //якщо буде передача по ссилці ЗВЕРНУТИ УВАГУ
				if($node['node_status'] == 1){
					$node['count'] = $node['count'] - 1; //якщо передавати node по ссилці, то цей рядок буде не потрібний
					if($reason != 'dislike_in_tree') $operation = 'constrict';
				}
				else{
					$this->data->chgNet('unset_block', $node, 'simple_disconnect');
				}
			}
			else return true;
		}
		//print_array('hello');
		if($operation == 'limit_on_vote'){
			if($reason == 'changes'){
				$reason = ''; //перевірити подальше виконання процедури
				if(!$this->data->getLimits('limit_on_vote', $node)) return false; //попередня перевірка
			}
			elseif($node['changes'] == 1){ //перевірка changes
				$this->chgNet('constrict', 'changes', $node['node']);
				return;
			}
			//блокування
			if(!$this->data->chgNet('set_block', $node, 'limit_on_vote')){
				//set_changes
				return false;
			}
			//контрольна перевірка
			if(!$this->data->getLimits('limit_on_vote', $node['node'])) return false;
			
			//$this->Tree->setTree('', $node['node']); //цей рядок і наступний можна об'єднати
			//$tree = $this->Tree->getTree();
			$tree = $this->Tree->setTree('', $node['node']);
			foreach($tree as $member){
				if($member['node_status'] != 1) continue;
				//$member['parent_node'] = ''; //для універсалізації кода в $this->data->chgNet()
				if($this->data->chgNet('set_block', $member, 'disconnect')){
					$blocked_nodes[] = $member;
				}
				else{
					foreach($blocked_nodes as $member){
						$this->data->chgNet('unset_block', $member, 'reset_disconnect');
					}
					//set changes
					//...
					//розблокування
					$this->data->chgNet('unset_block', $node, 'limit_on_vote');
					return false;
				}
			}
			foreach($blocked_nodes as $member){ //$tree as $member
				//if($member['node_status'] != 1) continue;
				$this->chgNet('disconnect', 'limit_on_vote', $member['node']);
			}
			//розблокування
			$this->data->chgNet('unset_block', $node, 'limit_on_vote');
			$operation = 'constrict';
		}

		if($operation == 'constrict'){
			//print_array('hello');
			$constrict = NULL;
			$this->data->setNet('reset_changes', $node['node']); //можна при умові $node['changes'], перевірити на різних constrict скидання changes
			if($node['level'] == $max_level){
				//розблокування в т.ч. net, але net, якщо $reason != 'limit_on_vote' (або $user_status != +10)
			}
			elseif(!$node['user']){ //&& $node['count']){ //УТОЧНИТИ З КАУНТОМ, коли перенесем видалення пустого дерева з disconnect
				//print_array('hello');
				if($origin_operation == 'disconnect' || $origin_operation == 'limit_on_vote'){
					//print_array($node);
					$constrict = $this->data->chgNet('constrict', $node);
					//print_array('hello');
				}
				else
					$constrict = $this->data->chgNet('constrict', $node, 'set_block'); //ПЕРЕВІРИТИ ЩО ПОВЕРТАЄТЬСЯ
				if($constrict === false) return false;
				if($constrict){ //NULL або true
					//if($node['level'] == 1 && $node['count'] == 0){
					//	$this->chgNets('net_delete', $node['first_node']);
					//	return true;
					//}
					//else
						$reason = 'constrict';
				}
				//if($this->data->chgNet('constrict', $node) && $origin_operation == 'constrict') return true;
			}
			//print_array('HELLO');
			if(($reason == 'vote' || $reason == 'limit_on_vote' || $reason == 'constrict') && $origin_operation == 'constrict') $reason = '';
			//elseif($origin_operation != 'limit_on_vote' && $node['count'] && !$constrict) $operation = 'dislike'; //ПЕРЕВІРИТИ актуальність $node['count']
																							//якщо count < 2 то можна віцдразу на обрання?
			//if($origin_operation != 'limit_on_vote' && $node['count'] && !$constrict) $operation = 'dislike'; //ПЕРЕВІРИТИ актуальність $node['count']
			elseif($origin_operation != 'limit_on_vote' && !$constrict) $operation = 'dislike'; //ПЕРЕВІРИТИ актуальність $node['count']
			//else $operation = 'dislike';
		}
		
		if($operation == 'dislike'){
			$dislike_node = $this->data->chgNet('dislike', $node);
			//if($node['parent_node'] == 1)
			//	print_array($dislike_node);
			// 1)false				- невдача;
			// 2)null				- немає;
			// 3)dislike_node		- dislike або в колі або в дереві;
			if(!$dislike_node) $dislike = $dislike_node;
			elseif($dislike_node == $node['node']) $dislike = 'dislike_in_tree';
			else $dislike = 'dislike_in_circle';
			// 1)false				- невдача;
			// 2)null				- немає;			
			// 1)dislike_in_circle	- dislike в колі;
			// 4)dislike_in_tree	- dislike в дереві;
			//print_array($dislike);
			if($dislike == 'dislike_in_circle'){
				$this->chgNet('disconnect', 'dislike_in_circle', $dislike_node);
				return true;
			}
			elseif($dislike == 'dislike_in_tree'){
				$this->chgNet('disconnect', 'dislike_in_tree', $dislike_node); //$node['node']
				return true;				
			}
			//elseif(!$dislike && $origin_operation != 'disconnect'){
			elseif($origin_operation == 'dislike'){
				return true; //точно true?
			}
			$operation = 'vote';
		}
		
		if($operation == 'vote'){
			$vote_node = $this->data->chgNet('vote', $node);
			//не памятаю для чого $vote_node['parent_node']
			//print_array($vote_node);
			if($vote_node){
				//перевірити алгоритм при запуску від cron
				//блокуємо node
				//якщо user є, то блокуємо його вузол в parent_net
				//якщо user є, то обов'язково блокуємо з user
				//якщо user немає, то обов'язково блокуємо з user == NULL
				if(!$this->data->chgNet('set_block', $node, 'unvote')){
					//set_changes
					return false;
				}
				//блокуємо user з vote node в parent_net
				//блокуємо vote node
				if(!$this->data->chgNet('set_block', $vote_node, 'vote')){
					$this->data->chgNet('unset_block', $node, 'unvote');
					//set_changes
					return false;
				}
				//$vote_node['reason'] = '';
				$this->data->chgNet('vote_reset', $vote_node);
				//print_array($vote_node);
				$unvote_user = $node['user'];
				if($unvote_user){
					$this->chgNet('disconnect', 'revote', $vote_node['node']);
					$this->chgNet('disconnect', 'unvote', $node['node']);
					//$vote_node['reason'] = 'revote';
				}
				else{
					$this->chgNet('disconnect', 'vote', $vote_node['node']); //if($reason == 'voice_set') $this->chgNet('disconnect', 'vote');
					//$vote_node['reason'] = 'vote'
					//$this->data->chgNet('unset_block', $vote_node, 'vote'); перенесли в disconnect->constrict
				}
				$node['user'] = $vote_node['user'];
				$node['reason'] = 'unvote';
				$this->data->chgNet('connect', $node);
				$this->data->chgNet('unset_block', $node, 'unvote');
				//подумати з приводу $this->chgNet('connect', $node); краще все-таки обійтись без цього тим більше що user відірваний від node
				//все буде залежити від даних необхідних для формування повідомлень
				
				if($unvote_user){
					$vote_node['user'] = $unvote_user;
					$vote_node['reason'] = 'revote';
					$this->data->chgNet('connect', $vote_node);
					$this->data->chgNet('unset_block', $vote_node, 'revote');
				}
				$reason = 'vote';
			}
			else{
				if($reason == 'voice_set') return $vote_node; //можна повертати NULL
			}
		}
		
		//if($operation == 'limit_on_vote' || $reason == 'limit_on_vote') return true;
		if($reason == 'limit_on_vote') return true;
		if($origin_operation == 'limit_on_vote') $reason = 'limit_on_vote';
		
		if($origin_operation == 'disconnect' || !$node['user'] || $reason == 'vote' || $origin_operation == 'limit_on_vote'){ //level == 1 ?
			//$this->chgNet('constrict', '', $node['parent_node']);
			//print_array($reason, 1);
			//print_array($node);
			if($node['parent_node']) $this->chgNet('constrict', $reason, $node['parent_node']);
			elseif($node['count'] == 0) $this->chgNets('net_delete', $node['first_node']);
			
			if($reason == 'changes'){ //проконтролювати зміну reason від входу в процедуру
				$this->chgNet('limit_on_vote', $reason, $node['node']);
			}
		}
		
		return true;
	}

	private function setMember($operation = '')
	{
		// -1 невірні значення полів
		// NULL дані не змінились
		// true нові дані успішно записано
		// помилка при виконанні функції
		
		$success = true;		
		$data = $this->command['data'];
		
		if($operation){
			$list_name = '';
			$note = '';
		}
		else{
			$list_name = $data['list_name'];
			$note = $data['note'];
		}
		
		if(strlen($list_name) <= 50 ){
			//...
		}
		else{
			$success = -1;
			//$this->data->messages[] = array();
		}
		
		if(strlen($note) <= 100){
			//...
		}
		else{
			$success = -1;
			//$this->data->messages[] = array();
		}
		
		if($success === -1) return $success; //залишаємось в команді write

		$success = $this->data->setNet('user_for_member', $operation);
		
        if($operation == 'invite')
			$this->command['operation'] = 'waiting'; //подумати де краще розмістити роутер тут чи там де викликається
		elseif($operation == 'dislike'){
			//...
		}
		elseif($operation == 'voice_set'){
			$this->data->redirection = true;
			//$this->command['operation'] = '';
			$this->command['data']['member'] = 0;
			//print_array($this->command);
		}
		elseif($operation == 'voice_reset'){
			$this->data->redirection = 'inner';
			$this->command = $this->data->next_command;
			//print_array($this->command, 1);
			//print_array($success);
		}		
		else
			$this->command['operation'] = 'read';
		
		if(!$success) return $success;
		//print_array('HELLO');
		$this->data->next_command = $this->command;
		$this->data->redirection = 'inner';
		$this->command['type'] = 'in'; //ще можливі варіанти 'data' та ''
		return $success;
	}
	
	public function chkLimits($data_name = '', $data_value = '')
	{
		if(!$data_name){
			$limit_nodes = $this->data->getLimits('limit_on_vote');
			foreach($limit_nodes as $limit_node){
				$this->chgNet('limit_on_vote', '', $limit_node['node_id']);
			}
			return true;
		}
	}
	
	public function chgNets($data_name = '', $data_value = '')
	{
		if($data_name == 'net_delete'){
			$net = $this->data->chgNets('net', $data_value); //get_net
			$this->data->chgNets('net_delete', $net);
			return;
		}

		if($data_name == 'net_create'){
			$node['node'] = $this->user->getUser('node');
			if($node['node']){
				$node['parent_node'] = '';
				if(!$this->data->chgNet('set_block', $node, 'net_create')) return false;
			}
			//print_array('hello');
			return $this->data->chgNets('net_create');
		}
		
		if(	$data_name == 'get_net_data' ||
			$data_name == 'get_net_goal'){
			return $this->data->chgNets('get_net_data');
		}
		
		if($data_name == 'set_net_data'){
			return $this->data->chgNets('set_net_data');
		}
		
		if($data_name == 'set_net_goal'){
			return $this->data->chgNets('set_net_goal');
		}
		
		if($data_name == 'set_net_count'){
			return $this->data->chgNets('set_net_count');
		}
/*
		
		if($data_name == 'net_update')
		
		
		if($this->net['parent_net']){
			//...
		}
		return;
*/
	}
	
	public function createNet()
	{
		$this->command['data']['net_name'] = $this->user->getUser('net_name');
		//$this->user->setUser('net_name', '');
		if($this->updateNet('net_create') === -1){
			$this->user->setUser('net_name', '');
			return false;
		}
		
		if(!$this->user->getUser('id')) return true;
		
		if(!$invite = $this->chgNets('net_create')){
			//$this->user->setUser('net_name', '');
			return false;
		}
		
		$this->data->redirection = true;
		//$this->user->resetUser(?); //при команді інвайт і статусі +1 можливо всі необхідні ресети вже є ???
		$this->command['type'] = 'invite';
		$this->command['data']['link'] = $invite;
		//$this->command['operation'] = '';
		return true;
	}
}
?>