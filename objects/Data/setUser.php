<?php
//public function setUser($data_name = '', $data_value = '')
//{	
		if(!$data_name){
			$user = &$this->user;
			$session = &$this->SESSION->session;
			
			$user['status'] = &$session['user_status'];
			$user['id'] = &$session['user_id'];								
			$user['net'] = &$session['user_net'];
			$user['nets'] = &$session['user_nets'];
			$user['parent_nets'] = &$session['user_parent_nets'];
			$user['node'] = &$session['user_node'];
			$user['parent_node'] = &$session['user_parent_node'];			
			$user['node_address'] = &$session['user_node_address'];				//може визначатись по node_id
			$user['full_node_address'] = &$session['user_full_node_address'];	//перевірити
			$user['level'] = &$session['user_level'];							//перевірити			
			$user['count'] = &$session['user_count'];
			$user['notifications'] = &$session['user_notifications'];
			$user['invite'] = &$session['invite'];
			$user['net_name'] = &$session['user_net_name'];
			$user['enter'] = &$session['enter'];
			$user['voice_for_key'] = &$session['user_voice'];
			//$user['email'] = &$session['user_email']; //тмчасово до inner redirection
			
			$user['changes_in_tree'] = '';
			$user['name'] = '';
			$user['email'] = '';
			$user['mobile'] = '';
			$user['password'] = '';
			$user['notifications_count']['new'] = array();
			$user['notifications_count']['all'] = array();
			$user['notifications_count']['not_shown'] = array();
			
			$user['link'] = '';
			//print_array($user);
			return $answer = true;
		}
		
		switch($data_name){			
			case 'user':
				$email = $this->DB->getDB('real_escape_string', $this->command['data']['email']);
				$operation = $this->command['operation'];
				$sql = "SELECT user_id FROM users WHERE email = '$email'";
				$sql = $this->DB->getOneArray($sql);
				if($sql){
					$this->messages[] = array('type' => 'error', 'text' => 'Нажаль користувач з таким e-mail вже зареєстрований!');	
					return $answer = false;
				}
				$link = 'NULL';
				$invite = 'NULL';
				$net_name = 'NULL';
				//$password = $this->DB->getDB('real_escape_string', $this->command['data']['password']);
				$password = '';
				$name = $this->DB->getDB('real_escape_string', $this->command['data']['name']);
				//$mobile = $this->DB->getDB('real_escape_string', $this->command['data']['mobile']);
				$mobile = '';
				if($operation != 'allow_registration'){
					$link = uniqid();
					$this->user['link'] = $link;
					$link = "'" . $link . "'";
					$invite = "'" . $this->user['invite'] . "'"; //вже звірений з invite в базі
					$this->user['invite'] = ''; //протестити 02.12.16
					$net_name = "'" . $this->DB->getDB('real_escape_string', $this->user['net_name']) . "'";
					$this->user['net_name'] = '';				
				}
				$sql = "INSERT INTO users (email, password, name, mobile, link, invite, net_name)
						VALUES ('$email', '$password', '$name', '$mobile', $link, $invite, $net_name)";
				$sql = $this->DB->getWork($sql);
				if(!$sql) return $answer = false;
				$this->user['id'] = $this->DB->getDB('insert_id');
				return $answer = $this->user;
			case 'name':
				$name = $this->DB->getDB('real_escape_string', $data_value);
				$user_id = $this->SESSION->session['user_id'];
				$sql = 'UPDATE users SET name = "' . $name . '" WHERE user_id = ' . $user_id;
				$sql = $this->DB->getWork($sql);
				if(!$sql) return $answer = false;
				return $answer = true;
			case 'mobile':
				$mobile = $this->DB->getDB('real_escape_string', $data_value);
				$user_id = $this->SESSION->session['user_id'];
				$sql = 'UPDATE users SET mobile = "' . $mobile . '" WHERE user_id = ' . $user_id;
				$sql = $this->DB->getWork($sql);
				if(!$sql) return $answer = false;
				return $answer = true;
			case 'password':
				$password = $this->DB->getDB('real_escape_string', $data_value);
				$user_id = $this->SESSION->session['user_id'];
				$sql = 'UPDATE users SET password = "' . $password . '" WHERE user_id = ' . $user_id;
				$sql = $this->DB->getWork($sql);
				if(!$sql) return $answer = false;
				return $answer = true;
			case 'user_id':
				$user_id = &$this->user['id']; //ПЕРЕВІРИТИ
				$command = $this->command['type'];
				$command_data = $this->command['data'];
				$operation = $this->command['operation'];
				if(empty($command_data['link'])) $link = '';
				else $link = $this->DB->getDB('real_escape_string', $command_data['link']);
				if(empty($command_data['email'])) $email = '';
				else $email = $this->DB->getDB('real_escape_string', $command_data['email']);				
				if(empty($command_data['password'])) $password = '';
				else $password = $this->DB->getDB('real_escape_string', $command_data['password']);				
				if($command == 'confirm')
					$sql = "SELECT user_id, invite, net_name FROM users WHERE link = '$link'";
				elseif($command == 'restore')
					$sql = "SELECT user_id FROM users WHERE restore = '$link'";
				elseif(!$password && ($operation == 'allow_authorize')){
					$enter_on_invite = true;
					$sql = "SELECT user_id, invite FROM users WHERE email = '$email'"; //тут net_name не достаєм
				}
				else
					$sql = "SELECT user_id FROM users WHERE	email = '$email' AND password = '$password'";
				$sql = $this->DB->getOneArray($sql);				
				if($sql){
					$user_id = $sql['user_id'];
					if($command == 'confirm' || $operation == 'allow_authorize'){
						//if(isset($sql['invite']) && !$this->user['invite']) $this->user['invite'] = $sql['invite'];
						if(!$this->user['invite'] && !$this->user['net_name']){
							if(isset($sql['invite'])) $this->user['invite'] = $sql['invite'];
							if(isset($sql['net_name'])) $this->user['net_name'] = $sql['net_name'];
						}
						$sql = "UPDATE users SET invite = NULL, link = NULL, net_name = NULL WHERE user_id = $user_id";
						$sql = $this->DB->getWork($sql);
						//$sql = "DELETE FROM users_notifications WHERE code = 1001 AND user_id = $user_id";
						//$sql = $this->DB->getWork($sql);
						$this->setUser('notification_close', 0);
						$this->messages[] = array('type' => 'success', 'text' => 'Реєстрацію успішно підтверджено!');
					}
					if($command == 'restore'){
						$sql = "UPDATE users SET restore = NULL WHERE user_id = $user_id";
						$sql = $this->DB->getWork($sql);
					}
					//$this->user['id'] = $user_id;
					return $answer = $user_id;
				}
				
				if($command == 'confirm' || $command == 'restore'){
					$this->messages[] = array('type' => 'error', 'text' => 'Лінк, по якому Ви зайшли, невірнй або вже недійсний!');
				}
				else
					$this->messages[] = array('type' => 'error', 'text' => 'Невірні дані для входу!');
				
				return $answer = false;
			case 'restore':
				$email = $this->DB->getDB('real_escape_string', $this->command['data']['email']);
				$sql = 'SELECT user_id, link, restore FROM users WHERE email = "' . $email . '"';
				$sql = $this->DB->getOneArray($sql);
				if(!$sql) return $answer = false;
				if($sql['link']) return $answer = array('type' => 'confirm', 'value' => $sql['link']);
				$restore = $sql['restore'];
				if(!$restore){
					$restore = uniqid();
					$sql = 'UPDATE users SET restore = "' . $restore . '" WHERE user_id = ' . $sql['user_id'];
					$sql = $this->DB->getWork($sql);
				}
				return $answer = array('type' => 'restore', 'value' => $restore);
/*
				case 'invite_db': //ВЖЕ НЕ ПОТРІБНО
				//завжди '';
				//спробувати завжди брати з User, а якщо потрібно обнулити, то перед цим user['invite'] = ''
				($data_value != '') ? $data_value = '"' . $data_value . '"' : $data_value = 'NULL';
				//$sql = 'UPDATE users SET invite = "' . $this->user['invite'] . '" WHERE (ISNULL(invite) OR invite = "") AND user_id = ' . $user_id;
				//$sql = (ISNULL(invite) OR invite = "") AND 
				$sql = 'UPDATE users SET invite = ' . $data_value . ' WHERE user_id = ' . $this->user['id'];
				$sql = $this->DB->getWork($sql);
				return $answer = true;
*/
			case 'notifications_new_reset':
					$sql = 'UPDATE users_notifications SET new = 0 WHERE user_id = ' . $this->user['id'];
					$sql = $this->DB->getWork($sql);
					return $answer = true;
			case 'notifications_not_shown_reset':
					$sql = 'UPDATE users_notifications SET shown = 1 WHERE user_id = ' . $this->user['id'];
					$sql = $this->DB->getWork($sql);
					return $answer = true;
			case 'notifications_count':
				$user_id = $this->user['id'];
				$sql = "SELECT COUNT(user_id) AS count_all, SUM(new) AS count_new, SUM(IF(shown = 1, 0, 1)) AS count_not_shown
						FROM users_notifications WHERE user_id = $user_id GROUP BY user_id";
				$sql = $this->DB->getOneArray($sql);
				$notif_count = &$this->user['notifications_count'];
				if($sql){
					$notif_count['not_shown'] = $sql['count_not_shown'];
					$notif_count['new'] = $sql['count_new'];
					$notif_count['all'] = $sql['count_all'];
				}
				//print_array($notif_count);
				return $answer = $notif_count;
			case 'notification':
				$notification = $data_value;
				if(empty($notification['user'])) $user = $this->user['id'];
				else $user = $notification['user'];
				$code = $notification['code'];
				$text = $this->DB->getDB('real_escape_string', $notification['text']);
				if(empty($notification['close'])) $close = 1;
				else $close = $notification['close'];
				$sql = "INSERT INTO users_notifications (user_id, code, notification, close)
						VALUES ($user, $code, '$text', $close)";
				$sql = $this->DB->getWork($sql);
				return $answer = true;
			case 'notification_close':
				$user_id = $this->user['id'];
				if($data_value !== ''){
					if($data_value == 0){
						$sql = "DELETE users_notifications.* FROM users_notifications
								WHERE user_id = $user_id"; //AND close = 0";
					}
					$this->DB->getWork($sql, $affected_rows);
					return $answer = true;
				}
				$notification_id = $this->command['data']['notification_id'];
				if(!$notification_id) return $answer = true;
				$sql = "DELETE users_notifications.* FROM users_notifications
						WHERE user_id = $user_id AND close = 1 AND notification_id = $notification_id AND shown = 1";
				$this->DB->getWork($sql, $affected_rows);
				if(!$affected_rows) return $answer = true;
				//можна просто перечитувати повідомлення
				//unset($this->user['notifications'][$notification_id]);
				$this->user['notifications_count']['all'] = $this->user['notifications_count']['all'] - 1;
				return $answer = true;
			default:
				return $answer = false;
		}
//}
?>