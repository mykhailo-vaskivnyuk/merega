<?php
//public function getUser($data_name = '', $data_value = '')
//{
		switch($data_name){
			case 'user':
				//print_array($this->user);
				if(!$data_value){
					if(!$this->user['id']) return $answer = false;
					else $user_id = $this->user['id'];
				}			
				else $user_id = $data_value;			
				$sql = "SELECT 	user_id,
								name,
								email,
								password,
								mobile,
								link,
								invite
						FROM users
						WHERE user_id = $user_id";
				$sql = $this->DB->getOneArray($sql);
				if(!$sql) return $answer = false;
				return $answer = $sql;
			case 'notifications':
				$sql = 'SELECT notification_id, notification, close FROM users_notifications WHERE user_id = ' . $this->user['id'];
				$sql = $this->DB->getArray($sql);
				//if(!$sql) return $answer = array();
				//foreach($sql as $row)
					//$notifications[] = $row['notification'];
				return $answer = $sql; //$answer = $notifications;
			default:			
		}
		//return $answer = false;
//}
?>