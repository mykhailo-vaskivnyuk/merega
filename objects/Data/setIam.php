<?php
//	public function setIam($data_name = '', $data_value = '')
//	{
		switch($data_name){
			case 'iam':
				//можна уніфікувати getNet('node')
				$sql = 'SELECT nodes.node_id, node_level, node_address, parent_node_id, full_node_address, count_of_members FROM nodes JOIN nodes_users
						ON nodes_users.node_id = nodes.node_id
						WHERE user_id = ' . $this->user['id'] . ' AND first_node_id = ' . $this->user['net'];
				$sql = $this->DB->getOneArray($sql);
				$this->user['node'] = $sql['node_id'];
				$this->user['level'] = $sql['node_level'];
				$this->user['node_address'] = $sql['node_address'];				
				$this->user['parent_node'] = $sql['parent_node_id'];
				$this->user['full_node_address'] = $sql['full_node_address'];
				$this->user['count'] = $sql['count_of_members'];
				return $answer = true;
			case 'notifications_count':
				$notifications_count = &$this->user['notifications_count'];
				$notifications_count['new'] = array();
				$notifications_count['all'] = array();				
				$sql = 'SELECT COUNT(user_id) AS count_all, SUM(new) AS count_new
						FROM nets_events WHERE net_id = ' . $this->user['net'] . ' AND user_id = ' . $this->user['id'] . ' GROUP BY user_id, net_id';
				$sql = $this->DB->getOneArray($sql);
				//print_array($this->user, 1);
				//print_array($sql);
				if($sql){
					$notifications_count['new'] = $sql['count_new'];
					$notifications_count['all'] = $sql['count_all'];				
				}
				return $answer = $notifications_count;
			case 'notifications_new_reset':
				$sql = 'UPDATE nets_events SET new = 0 WHERE net_id = ' . $this->user['net'] . ' AND user_id = ' . $this->user['id'];
				$sql = $this->DB->getWork($sql);
				$this->user['notifications_count']['new'] = 0;
				return $answer = true;
			case 'notifications_shown':
				$user_id = $this->user['id'];
				$node = $this->net['active_node'];
				$circle_tree = $this->net['circle_tree'];
				if($node['key'] == 1){
					$circle_tree = 'net';
					$net_id = $this->user['net'];
					$sql = "UPDATE nets_events SET shown = 1 WHERE user_id = $user_id AND ISNULL(event_node_id) AND net_id = $net_id";
				}
				else{
					$node = $this->net['active_node'];
					$event_node_id = $node['node'];
					$sql = 'UPDATE nets_events SET shown = 1 WHERE user_id = ' . $this->user['id'] . ' AND event_node_id = ' . $event_node_id;
					$node_key = $node['key'];
					$this->user['notifications'][$circle_tree][$node_key]['not_shown'] = 0;
					}
				$sql = $this->DB->getWork($sql, $affected_rows);
				//ВАРІАНТ 1
				//$this->setIam('notifications');
				//ВАРІАНТ 2
				$not_shown = &$this->user['notifications'][$circle_tree]['not_shown'];
				$not_shown = $not_shown - $affected_rows; //$not_shown = $not_shown - $this->user['notifications'][$circle_tree][$node_key]['not_shown']
				$this->user['notifications']['not_shown'] = $this->user['notifications']['not_shown'] - $affected_rows;
				//print_array($this->user['notifications']);
				return $answer = true;
			case 'notification_close':
				if(is_array($data_value)){
					$node = $data_value;
					$data_value = 3; //перенести в if($data_value !== ''){
				}
				else{
					$node = $this->net['active_node'];
					$user_id = $this->user['id'];
				}
				if($data_value !== ''){
				//1)код 11: запрошений/ідентифікація:
					//ідентифікація тип 0
					//відмова (відєднання запрошеного) тип 1 
					//відєднання запрошеного (повідомлення для запрошеного видаляються) тип 2
					//відєднання координатора = відєднання запрошеного + відєднання координатора (цієї функції не потрібно)
				//2)код 335, 365: вибори
					//тип 3 (при виборах одного ця функція не потрібна)
				//ПЕРЕВІРИТИ ДЛЯ ІНШИХ ВИПАДКІВ
				//параметр 1:
					//тип 0: від Я до active_node і навпаки
					//тип 1: від active_node до Я
					//тип 2: від Я до node 0 (потрібен його user_id); або від Я до parent_node
					//тип 3: від Я['parent_node'] до nodes where parent_node = Я['parent_node']
				//параметр 2: цільовий node (або active_node) поки не потрібен
				//параметр 3: notification_close = 0 поки не потрібен
				//параметр 4: event_code + notification_code поки не потрібен
					if($data_value == 0){
						$user_1 = $user_id;
						$node_1 = $this->user['node'];
						$user_2 = $node['user'];
						$node_2 = $node['node'];
						$sql = "DELETE nets_events.* FROM nets_events
								WHERE (event_node_id = $node_1 AND user_id = $user_2) OR (event_node_id = $node_2 AND user_id = $user_1)";
					}
					elseif($data_value == 1){
						$node_1 = $node['node'];
						$user_2 = $user_id;
						$sql = "DELETE nets_events.* FROM nets_events
								WHERE event_node_id = $node_1 AND user_id = $user_2";
					}
					elseif($data_value == 2){
						$node_1 = $this->user['node']; //$node['node']; оскільки active_node = Я
						$node_2 = $this->user['parent_node']; //$node['parent_node']; оскільки active_node = Я
						//$sql = "DELETE nets_events.* FROM nets_events
						//		WHERE event_node_id = $node_1 AND user_id = $user_2";
						$sql = "DELETE nets_events.*
								FROM nets_events JOIN nodes_users ON nets_events.user_id = nodes_users.user_id
								WHERE event_node_id = $node_1 AND node_id = $node_2";
					}
					else{ //тип 3
						$node_1 = $node['parent_node']; //або передати node з функції, яка видає результат виборів				
						$sql = "DELETE nets_events.*
								FROM (nets_events JOIN nodes_users ON nets_events.user_id = nodes_users.user_id)
								JOIN nodes ON nodes_users.node_id = nodes.node_id
								WHERE event_node_id = $node_1 AND nodes.parent_node_id = $node_1";	
					}
					$this->DB->getWork($sql, $affected_rows);
					return $answer = true;
				}
				//ЯКЩО використовувати count замість самого масиву повідомлень, то все можна спростити
				//ключ notification можна залишити лише перший, а решту прибрати
				//else{
					$notification_id = $this->command['data']['notification_id'];
					if(!$notification_id) return $answer = true;
					//user_id в запиті мабуть не потрібно
					$sql = "DELETE nets_events.* FROM nets_events JOIN notifications_tpl ON nets_events.notification_tpl_id = notifications_tpl.notification_tpl_id
							WHERE user_id = $user_id AND event_id = $notification_id AND notification_close = 1 AND shown = 1";
				//}
				$this->DB->getWork($sql, $affected_rows);
				if(!$affected_rows) return $answer = true;
				$node_key = $node['key'];
				if($node_key == 1){
					unset($this->user['notifications']['net']['notifications'][$notification_id]);
					if(!$this->user['notifications']['net']['notifications']) unset($this->user['notifications']['net']);
				}
				else{
					$circle_tree = $this->net['circle_tree'];
					//можна просто перечитувати повідомлення або в вузлі або всі, краще мабуть всі
					//print_array($this->user['notifications'], 1);
					unset($this->user['notifications'][$circle_tree][$node_key]['notifications'][$notification_id]);
					//print_array($this->user['notifications'], 1);
					//print_array($this->user['notifications'][$circle_tree][$node_key], 1);
					if(!$this->user['notifications'][$circle_tree][$node_key]['notifications']) unset($this->user['notifications'][$circle_tree][$node_key]);
					//print_array($this->user['notifications'][$circle_tree], 1);
					if(count($this->user['notifications'][$circle_tree]) == 2) unset($this->user['notifications'][$circle_tree]);
				}
				if(count($this->user['notifications']) == 1) $this->user['notifications'] = array();
				//print_array($this->user['notifications']);
				return $answer = true;
			default:
		}
		return true;
//	}