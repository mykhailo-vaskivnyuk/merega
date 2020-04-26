<?php
//	public function getIam($data_name = '', $data_value = '')
//	{
		switch($data_name)
		{		
			case 'notifications':
				$notifications = array();
				$user_id = $this->user['id'];
				if($data_value == 'response'){
					$node_key = $this->net['active_node']['key'];
					if($node_key == 1){
						$net_id = $this->user['net'];
						$sql = "ISNULL(event_node_id) AND net_id = $net_id";
					}
					else{
						$event_node_id = $this->net['active_node']['node'];
						$sql = "event_node_id = $event_node_id";
					}
					$sql = "SELECT 	event_id,
										nets_events.notification_text,
										notification_close
							FROM nets_events JOIN notifications_tpl ON nets_events.notification_tpl_id = notifications_tpl.notification_tpl_id
							WHERE user_id = $user_id AND $sql";			
					$sql = $this->DB->getArray($sql);
					//foreach($sql as $notification)
					//	$notifications[] = $notification;
					return $answer = $sql;
				}
				$this->user['notifications'] = array(); //обовязково встановлювати, бо при рісеті не очищається
				$notifications = &$this->user['notifications'];
				$net_id = $this->user['net'];
				$user_node = $this->user['node'];
				$parent_node = $this->user['parent_node'];
				if(is_null($parent_node)) $parent_node = 0; //цю штуку можливо потрібно перевіряти в іншому місці
				$node_address = $this->user['node_address'];
				$sql = "SELECT 	event_id,
								event_node_id,
								event_code,
								notification_text, 
								IF(ISNULL(event_node_id), 'net',
									IF(parent_node_id = $user_node, 'tree', 'circle')) AS circle_tree,			
								IF(parent_node_id = $parent_node,
									IF(node_address < $node_address, node_address + 1, node_address),
									IF(nodes.node_id = $parent_node, 0, node_address + 1)) AS node_key,
								IF(shown = 0, 1, 0) AS not_shown
						FROM nets_events LEFT JOIN nodes ON event_node_id = node_id
						WHERE net_id = $net_id AND user_id = $user_id";
/*
				$sql = "SELECT 	event_id,
								event_node_id,
								code,
								notification,
								IF(parent_node_id = $user_node, 'tree', 'circle') AS circle_tree,			
								IF(shown = 0, 1, 0) AS not_shown
						FROM nets_events LEFT JOIN nodes ON event_node_id = node_id
						WHERE user_id = $user_id";
*/
				//print_array($sql);
				$sql = $this->DB->getArray($sql);
				//print_array($sql);
				$not_shown['net'] = 0;
				$not_shown['circle'] = 0;
				$not_shown['tree'] = 0;
				foreach($sql as $notification){
					$circle_tree = $notification['circle_tree'];
					if($circle_tree == 'net')
						$not_shown_node = &$notifications[$circle_tree]['not_shown'];
					else{
						$key = $notification['node_key'];
						$not_shown_node = &$notifications[$circle_tree][$key]['not_shown'];
					}
/*
					if(isset($notifications[$circle_tree][$key]['not_shown']))
						$not_shown_node = $notifications[$circle_tree][$key]['not_shown'];
					else
						$not_shown_node = 0;
*/
					$not_shown_node = $not_shown_node + $notification['not_shown'];
					//$item['id'] = $notification['event_id'];
					$item['node_id'] = $notification['event_node_id'];
					$item['event_code'] = $notification['event_code'];
					$item['text'] = $notification['notification_text'];
					//$notifications[$circle_tree][$key]['not_shown'] = $not_shown_node;
					if($circle_tree == 'net')
						$notifications[$circle_tree]['notifications'][$notification['event_id']] = $item;
					else
						$notifications[$circle_tree][$key]['notifications'][$notification['event_id']] = $item;
					$not_shown[$circle_tree] = $not_shown[$circle_tree] + $notification['not_shown'];
				}
/*
				foreach($sql as $notification){
					//print_array($notification); exit;
					$circle_tree = $notification['circle_tree'];
					$event_node_id = $notification['event_node_id'];
					if(isset($notifications[$circle_tree][$event_node_id]['not_shown']))
						$not_shown_node = $notifications[$circle_tree][$event_node_id]['not_shown'];
					else
						$not_shown_node = 0;
					$not_shown_node = $not_shown_node + $notification['not_shown'];
					$item['id'] = $notification['event_id'];
					//$item['node_id'] = $notification['event_node_id'];
					$item['code'] = $notification['code'];
					$item['text'] = $notification['notification'];
					$notifications[$circle_tree][$event_node_id]['not_shown'] = $not_shown_node;
					$notifications[$circle_tree][$event_node_id]['notifications'][] = $item;
					$not_shown[$circle_tree] = $not_shown[$circle_tree] + $not_shown_node;
				}
*/
				if(isset($notifications['circle'])){
					$notifications['circle']['enter'] = true;
					$notifications['circle']['not_shown'] = $not_shown['circle'];				
				}
				if(isset($notifications['tree'])){
					$notifications['tree']['enter'] = true;
					$notifications['tree']['not_shown'] = $not_shown['tree'];					
				}
				if(isset($notifications['net'])){
					$notifications['net']['enter'] = true;
					$notifications['net']['not_shown'] = $not_shown['net'];					
				}				
				if($notifications){
					$notifications['not_shown'] = $not_shown['circle'] + $not_shown['tree'] + $not_shown['net'];					
				}
				//print_array($notifications);
				return $answer = true;
			case 'notification_view':
				$user_id = $this->user['id'];
				$event_id = $this->command['data']['notification_id'];
				//print_array($this->command);
				if(!$event_id) return $answer = NULL;
				//$this->setIam('notification_close');
				$sql = "SELECT notification_action
							FROM nets_events JOIN notifications_tpl ON nets_events.notification_tpl_id = notifications_tpl.notification_tpl_id
							WHERE user_id = $user_id AND event_id = $event_id"; //а для чого user, якщо є id?
				$sql = $this->DB->getOneArray($sql);
				if(!$sql) return $answer = NULL;
				//print_array($sql);
				return $answer = $sql['notification_action'];
			default:			
		}		
//	}