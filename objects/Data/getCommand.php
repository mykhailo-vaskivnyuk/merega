<?php
//public function getCommand()
//{	
		//$command['type'] = &$this->REQUEST->command['link_1'];
		switch($this->REQUEST->command['link_1']){
			case 'authorize':
				$command['type'] = 'authorize';
				$command['data']['email'] = $this->REQUEST->command['email'];
				$command['data']['password'] = $this->REQUEST->command['password'];
				break;
			case 'registration':
				$command['type'] = 'registration';
				//ПЕРЕПИСАТИ
				$command['data']['email'] = $this->REQUEST->command['email'];
				//$command['data']['password'] = $this->REQUEST->command['password'];
				$command['data']['name'] = $this->REQUEST->command['name'];
				//$command['data']['mobile'] = $this->REQUEST->command['mobile'];
				$command['operation'] = 'disabled'; // ? може forbid ?
				break;
			case 'delete':
				$command['type'] = 'delete';
				$command['data']['user_id'] = $this->REQUEST->command['user_id'];
				break;				
			case 'enter':
				$command['type'] = 'enter';
				break;
			case 'in':
				$command['type'] = 'in';
				$command['data']['net'] =  $this->REQUEST->command['link_2']; //net_id краще було б
				break;
			case 'out':
				$command['type'] = 'out';
				$command['data']['net'] =  $this->REQUEST->command['link_2']; //net_id краще було б
				break;		
			case 'confirm':
				$command['type'] = 'confirm';
				$command['data']['link'] = $this->REQUEST->command['link_2'];
				$command['operation'] = ''; //можна allow
				break;
			case 'restore':
				$command['type'] = 'restore';
				$command['data']['link'] = $this->REQUEST->command['link_2'];
				$command['data']['email'] = $this->REQUEST->command['email'];
				$command['operation'] = ''; //можна allow
				break;								
			case 'invite':
				$command['type'] = 'invite';
				$command['operation'] = $this->REQUEST->command['operation']; //refuse  - єдина команда, яка аналогічна
																			//$command['data']['link'] == ''
				$command['data']['link'] = $this->REQUEST->command['link_2'];
				break;
			case 'invitation':
				$command['type'] = 'invitation';
				$command['data']['circle_tree'] = $this->REQUEST->command['link_2'];
				$command['data']['member'] = $this->REQUEST->command['link_3'];
				$command['operation'] = $this->REQUEST->command['operation'];
				$command['data']['email'] = $this->REQUEST->command['email'];
				$command['data']['user_id'] = $this->REQUEST->command['user_id'];
				$command['data']['member_node'] = $this->REQUEST->command['member_node'];
				$command['data']['list_name'] = $this->REQUEST->command['list_name'];
				$command['data']['note'] = $this->REQUEST->command['note'];
				$command['data']['member_id'] = $this->REQUEST->command['member_id']; //перевірити чи потрібно, мабуть для ідентифікації		
				//print_array($command);
				break;
			case 'disconnect':
				$command['type'] = 'disconnect';
				$command['data']['circle_tree'] = 'net'; //$this->REQUEST->command['link_2'];
				$command['data']['member'] = 1; //$this->REQUEST->command['link_3'];
				$command['operation'] = $this->REQUEST->command['operation'];		
				$command['data']['user_id'] = $this->REQUEST->command['user_id'];
				$command['data']['net_id'] = $this->REQUEST->command['net_id'];	
				break;
			case 'connect':
				$command['type'] = 'connect';
				//$command['operation'] = 'forbid';
				break;
			case 'create':
				$command['type'] = 'create';
				$command['data']['circle_tree'] = 'net'; //$this->REQUEST->command['link_2'];
				$command['data']['member'] = 1; //$this->REQUEST->command['link_3'];
				$command['data']['net_name'] = $this->REQUEST->command['name'];
				$command['operation'] = $this->REQUEST->command['operation'];
				break;
			case 'goal':
				$command['type'] = 'goal';
				$command['data']['circle_tree'] = 'net'; //$this->REQUEST->command['link_2'];
				$command['data']['member'] = 1; //$this->REQUEST->command['link_3'];
				$command['data']['net_goal'] = $this->REQUEST->command['text'];
				$command['operation'] = $this->REQUEST->command['operation'];
				break;					
			case 'notification':
				$command['type'] = 'notification';
				$command['data']['circle_tree'] = $this->REQUEST->command['link_2'];
				$command['data']['member'] = $this->REQUEST->command['link_3'];
				$command['operation'] = $this->REQUEST->command['operation'];
				$command['data']['notification_id'] = $this->REQUEST->command['notification_id'];
				break;
			case 'first':
				$command['type'] = 'first';
				break;
			case 'exit':
				$command['type'] = 'exit';
				break;
			case 'data':
				$command['type'] = 'data';
				$command['operation'] = $this->REQUEST->command['operation'];		
				$command['data']['user_id'] = $this->REQUEST->command['user_id'];
				$command['data']['user_name'] = $this->REQUEST->command['name'];
				$command['data']['net_name'] = $this->REQUEST->command['name'];
				$command['data']['password'] = $this->REQUEST->command['password'];		
				$command['data']['user_mobile'] = $this->REQUEST->command['mobile'];
				$command['data']['circle_tree'] = $this->REQUEST->command['link_2'];
				$command['data']['member'] = $this->REQUEST->command['link_3'];
				$command['data']['list_name'] = $this->REQUEST->command['list_name'];
				$command['data']['note'] = $this->REQUEST->command['note'];
				$command['data']['user_email'] = $this->REQUEST->command['email'];
				$command['data']['net_id'] = $this->REQUEST->command['net_id'];
				$command['data']['member_id'] = $this->REQUEST->command['member_id'];
				$command['data']['member_node'] = $this->REQUEST->command['member_node'];
				
				$command['data']['link_name_1'] = $this->REQUEST->command['link_name_1'];
				$command['data']['link_value_1'] = $this->REQUEST->command['link_value_1'];
				$command['data']['link_name_2'] = $this->REQUEST->command['link_name_2'];
				$command['data']['link_value_2'] = $this->REQUEST->command['link_value_2'];
				$command['data']['link_name_3'] = $this->REQUEST->command['link_name_3'];
				$command['data']['link_value_3'] = $this->REQUEST->command['link_value_3'];
				$command['data']['link_name_4'] = $this->REQUEST->command['link_name_4'];
				$command['data']['link_value_4'] = $this->REQUEST->command['link_value_4'];				
				break;
			case 'circle':
				$command['type'] = 'circle';
				//$command['data']['circle_tree'] = 'circle';
				$command['data']['member'] = $this->REQUEST->command['link_2'];
				break;			
			case 'tree':
				$command['type'] = 'tree';
				//$command['data']['circle_tree'] = 'tree';
				$command['data']['member'] = $this->REQUEST->command['link_2'];
				break;
			case 'statistic':
				$command['type'] = 'statistic';
				$command['data']['circle_tree'] = $this->REQUEST->command['link_2'];
				$command['data']['member'] = $this->REQUEST->command['link_3'];
				$command['data']['dislike'] = $this->REQUEST->command['dislike'];				
				$command['operation'] = $this->REQUEST->command['operation'];
				break;
			case 'vote':
				$command['type'] = 'vote';
				$command['data']['circle_tree'] = 'circle'; //$this->REQUEST->command['link_2'];
				$command['data']['member'] = $this->REQUEST->command['link_3'];
				$command['data']['voice'] = $this->REQUEST->command['voice'];			
				$command['operation'] = $this->REQUEST->command['operation'];
				break;			
			default:
				$command['type'] = '';				
		}
		$this->command = $command;
//}
?>