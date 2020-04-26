<?php
//public function chgNet($data_name = '', $data_value = '', $operation = '')
//{
	//ПРОДУМАТИ як можна передавати node по ссилці, або зробити змінну net['current_node']
	//disconnect
	$node = $data_value;
	$node_id = $node['node'];
	$net = $this->net['id'];
	if($data_name == 'set_block' || $data_name == 'unset_block' || $data_name == 'vote_reset'){
		empty($node['user']) ? $user_id = '' : $user_id = $node['user'];	
		empty($node['parent_node']) ? $parent_node = '' : $parent_node = $node['parent_node']; //не завжди потрібен, іноді підставляється пусте значення або додатково вибирається з БД для роботи цієї стрічки
		$parent_net = '';
		empty($node['node_status']) ? $status = '' : $status = $node['node_status'];
		empty($node['reason']) ? $reason = '' : $reason = $node['reason'];
	}
	else{
		$user_id = $node['user'];
			//node_status == 5 ...
			$reason = $node['reason'];
		if($data_name != 'connect' || $reason != 'revote'){
			$net = $this->user['net']; //стерти оскільки є вище рядок
			$count = $node['count'];
			$status = $node['node_status'];
			//constrict
			$level = $node['level'];
			$address = $node['address'];
			$full_address = $node['full_address'];
			$parent_node = $node['parent_node'];
			$net_max_level = $this->net['max_level'];
		}
	}

	if($data_name == 'set_block'){
		switch($operation){
			case 'disconnect':
				if($status != +1){
					$sql = "UPDATE nodes SET blocked = 1 WHERE node_id = $parent_node"; //user блокувати не потрібно
					$this->DB->getWork($sql, $affected_rows);
					if($affected_rows == 0) break;
				}
				else{
					if($reason != 'limit_on_vote'){  //&& $reason != ''
						//блокування net
					}
				}
				if($status == -5){
					$sql = "UPDATE nodes SET blocked = 1 WHERE node_id = $node_id"; //скасування запрошення
					$this->DB->getWork($sql, $affected_rows);
					if($affected_rows == 1) return $answer = true;		
				}
				else{
					//if($status == +1){
					$user_nodes_in_nets = $this->setNet('user_nets', $node);
					foreach($user_nodes_in_nets as $user_node_in_net){
						if($this->chgNet('set_block', $user_node_in_net, 'disconnect')){
							$blocked_nodes[] = $user_node_in_net;
						}
						else{
							foreach($blocked_nodes as $user_node_in_net){
								$this->chgNet('unset_block', $user_node_in_net, 'reset_disconnect');
							}
							return false;
						}
					}
					//}
					$sql = "UPDATE nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id	
							SET blocked = 1
							WHERE nodes.node_id = $node_id AND user_id = $user_id";
					$this->DB->getWork($sql, $affected_rows);
					if($affected_rows == 1) return $answer = true;
				}
				if($status != +1){
					$sql = "UPDATE nodes SET blocked = 0 WHERE node_id = $parent_node";
					$this->DB->getWork($sql);				
				}
				else{
					if($reason != 'limit_on_vote'){  //&& $reason != ''
						//розблокування net
					}
				}
				break;
			case 'approve':
				//блокування net
				//print_array($node);
				if($parent_node){
					$sql = "UPDATE nodes SET blocked = 1 WHERE node_id = $parent_node"; //user блокувати не потрібно
					$this->DB->getWork($sql, $affected_rows);
					if($affected_rows == 0) break;
				}
				$sql = "UPDATE nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id	
						SET blocked = 1
						WHERE nodes.node_id = $node_id AND user_id = $user_id";
				$this->DB->getWork($sql, $affected_rows);
				if($affected_rows == 1) return $answer = true;
				if($parent_node){
					$sql = "UPDATE nodes SET blocked = 0 WHERE node_id = $parent_node";
					$this->DB->getWork($sql);
				}
				//розблокування net
				break;
			case 'constrict':
				//блокування net
				$sql = "UPDATE nodes LEFT JOIN nodes_users ON nodes.node_id = nodes_users.node_id
						SET blocked = 1
						WHERE nodes.node_id = $node_id AND ISNULL(user_id)";
				$this->DB->getWork($sql, $affected_rows);
				if($affected_rows == 1) return $answer = true;
				break;
			case 'connection':
				if($parent_node){
					$sql = "UPDATE nodes SET blocked = 1 WHERE node_id = $parent_node";
					$this->DB->getWork($sql, $affected_rows);
					if($affected_rows == 0) break;
				}
				$sql = "UPDATE nodes SET blocked = 1 WHERE node_id = $node_id";
				$this->DB->getWork($sql, $affected_rows);
				if($affected_rows == 1) return $answer = true;
				break;
			case 'net_create':
				$sql = "UPDATE nodes SET blocked = 1 WHERE node_id = $node_id";
				$this->DB->getWork($sql, $affected_rows);
				if($affected_rows == 1) return $answer = true;
				break;
			case 'unvote';
			case 'vote':
				if($user_id){
					//$first_node = $node['first_node']; //можна брати або з user['net'] або з net['id']
											//тут продумати і перевірити, оскільки запуск може бути від команди користувача, а також від crone
					//
					//при cron можна при обробці кожної мережі запускати chgNets('net')
					//а при user parent_net завжди в net['parent_net']
					//отже можна обійтись без SELECT нижче
					$sql = "SELECT parent_net_id FROM nets WHERE net_id = $net";
					$sql = $this->DB->getOneArray($sql);
					if(!empty($sql['parent_net_id'])){
						$parent_net = $sql['parent_net_id'];					
						$sql = "UPDATE	nodes JOIN
										nodes_users ON nodes.node_id = nodes_users.node_id
								SET blocked = 1
								WHERE user_id = $user_id AND first_node_id = $parent_net";
						$this->DB->getWork($sql, $affected_rows);
						if($affected_rows == 0) break;						
					}
					$sql = "UPDATE nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id	
							SET blocked = 1
							WHERE nodes.node_id = $node_id AND user_id = $user_id";
					$this->DB->getWork($sql, $affected_rows);
					if($affected_rows == 1) return $answer = true;
					if($parent_net){
						$sql = "UPDATE	nodes JOIN
										nodes_users ON nodes.node_id = nodes_users.node_id
								SET blocked = 0 WHERE user_id = $user_id AND first_node_id = $parent_net";
						$this->DB->getWork($sql);						
					}
					break;								
				}
				else{
					$sql = "UPDATE nodes LEFT JOIN nodes_users ON nodes.node_id = nodes_users.node_id
							SET blocked = 1
							WHERE nodes.node_id = $node_id AND ISNULL(user_id)";
					$this->DB->getWork($sql, $affected_rows);
					if($affected_rows == 1) return $answer = true;
					break;
				}
			case 'limit_on_vote':
				//блокування net
				$sql = "UPDATE nodes LEFT JOIN nodes_users ON nodes.node_id = nodes_users.node_id
						SET blocked = 1
						WHERE nodes.node_id = $node_id AND ISNULL(user_id)";
				$this->DB->getWork($sql, $affected_rows);
				if($affected_rows == 1) return $answer = true;
				break;
			default:
				$sql = "UPDATE nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id	
						SET blocked = 1
						WHERE nodes.node_id = $node_id AND user_id = $user_id";
				$this->DB->getWork($sql, $affected_rows);
				if($affected_rows == 1) return $answer = true;
		}
		
		$this->messages[] = array('type' => 'error', 'text' => 'Операцію відхилено!');
		return $answer = false;
	}
	elseif($data_name == 'unset_block'){
		switch($operation){
			case 'reset_disconnect':
				$user_nodes_in_nets = $this->setNet('user_nets', $node);
				foreach($user_nodes_in_nets as $user_node_in_net){
					$this->chgNet('unset_block', $user_node_in_net, 'disconnect');
				}
				$sql = "UPDATE nodes SET blocked = 0 WHERE node_id = $node_id";
				$this->DB->getWork($sql);
				break;
			case 'disconnect':
				$sql = "UPDATE nodes SET blocked = 0 WHERE node_id = $node_id";
				$this->DB->getWork($sql);
				if($status != +1 && $parent_node){ //$status != +1 - можливо тут не потрібно
					$sql = "UPDATE nodes SET blocked = 0 WHERE node_id = $parent_node";
					$this->DB->getWork($sql);			
				}
				//розблокування net, якщо $reason != 'limit_on_vote'
				break;		
			case 'simple_disconnect':
				$sql = "UPDATE nodes SET blocked = 0 WHERE node_id = $node_id";
				$this->DB->getWork($sql);
				if($status != +1){
					$sql = "UPDATE nodes SET blocked = 0 WHERE node_id = $parent_node";
					$this->DB->getWork($sql);			
				}
				break;
			case 'connection':
				$sql = "UPDATE nodes SET blocked = 0 WHERE node_id = $node_id";
				$this->DB->getWork($sql, $affected_rows);
				if($parent_node){
					$sql = "UPDATE nodes SET blocked = 0 WHERE node_id = $parent_node";
					$this->DB->getWork($sql, $affected_rows);
				}
				break;
			case 'unvote';
			case 'vote';
			case 'revote':
				$sql = "SELECT parent_net_id FROM nets WHERE net_id = $net";
				$sql = $this->DB->getOneArray($sql);
				if(!empty($sql['parent_net_id'])){
					$parent_net = $sql['parent_net_id'];					
					$sql = "UPDATE	nodes JOIN
									nodes_users ON nodes.node_id = nodes_users.node_id
							SET blocked = 0
							WHERE user_id = $user_id AND first_node_id = $parent_net";
					$this->DB->getWork($sql);						
				}
				$sql = "UPDATE nodes
						SET blocked = 0	
						WHERE node_id = $node_id";
				$this->DB->getWork($sql);
				break;
			case 'limit_on_vote':
				$sql = "UPDATE nodes
						SET blocked = 0	
						WHERE node_id = $node_id";
				$this->DB->getWork($sql);
				//розблокування net
				break;
			case 'constrict':
				$sql = "UPDATE nodes
						SET blocked = 0	
						WHERE node_id = $node_id";
				$this->DB->getWork($sql);
				//розблокування net, якщо $reason != 'limit_on_vote'
				break;
			default:
				$sql = "UPDATE nodes
						SET blocked = 0	
						WHERE node_id = $node_id";
				$this->DB->getWork($sql);
		}
		
		return $answer = true;
	}
	elseif($data_name == 'disconnect'){
	//Функція відключення має працювати не залежно від поточного юзера.
	//Вона просто запускається як подія.
	//Потім юзер просто перегружається на локалхост.
	
	//(0)видалення запрошення - реалізовано окремо
		//(статус 1)->(статус -5 count 0)
	//(1)видалення себе
		//(статус 5 count 0; статус 1 count 0 (-5, 5), count > 0 (-5, 5, -1, 1))
	//(2)видалення учасника шляхом відмови в ідентифікації
		//(статус 1)->(статус 5 count 0)
	//(3)видалення учасника через dislike
		//(статус 1 count 0 (-5, 5), count > 0 (-5, 5, -1, 1))
	//(4)видалення учасника через вибори/перевибори
		//(статус 1 count 0 (-5, 5), count > 0 (-5, 5, -1, 1))
		//(статус 1 count > 0 (-5, 5, -1, 1))
	//!(5)видалення учасника (учасників) через ліміт (ліміти)
		//ще не зробив

	//припускаємо, що зміни в базі можуть відбутись між двома зовнішніми командами (від браузерів)
		//але не між двома запитами чи внутрішніми редіректами
	
	//count
		//про зміну кількості в деревах своїх гілок учасник поки не повідомляється
		//один зі варіантів як це можна зробити - помітка в таблиці nodes
		//другий варіант - запис в nets_events пустого повідомлення
		//кількість в учасників кола та дерева завжди не актуальна, хоча для мого координатора вона може не мати значення
		//перед видаленням себе count завжди актуальний
		//якщо статус 1: count = count - 1
		//якщо статус 5: count = 0
		//якщо статус -1: count = count ?
	
	/*	begin */
	
		//ПЕРЕНЕСТИ В КІНЕЦЬ записуємо новий count
		if($reason == 'vote'){
			//ОПТИМІЗУВАТИ
			$this->setNet('count', $node); //спробувати передавати по ссилці, попередньо протестити (вивчити) передачу даних в методи по ссилці
			$count = $count - 1;
		}
		elseif($reason == 'revote'){
			//...
		}
		elseif($reason == 'unvote'){
			//...
		}
		elseif($count){
			$this->setNet('count', $node);
			//статус 1: count = count - 1
			//для статуса 5 count не має значення, бо $count == 0
			$count = $count - 1;
		}
		
		//node_users: видаляємо рядок з node 
		//nets_events: видаляємо всі рядки з user in net
		//nets_users_data: видаляємо всі рядки з user		
		//members_users: видаляємо всі рядки з member = user та user = user in net
		//ПЕРЕНЕСТИ nodes_tmp: видаляємо рядок з node (для статуса +5)
		if($reason == 'vote' || $reason == 'revote' || $reason == 'unvote'){
			//пробувати використовувати ПРЕДСТАВЛЕНИЯ та вкладені запити, а також розбивати на кілька запитів
			//а також використовувати ПРОЦЕДУРЫ з пакетом запитів
			//при revote та unvote user_nodes можна не видаляти
			$sql = "DELETE nodes_users.*, nodes_tmp.*
					FROM 	nodes_users LEFT JOIN
							nodes_tmp ON nodes_users.node_id = nodes_tmp.node_id
					WHERE	nodes_users.node_id = $node_id";
													//nodes_tmp вписано для уніфікації застосування цього запиту для інших випадків
													//в данову випадку nodes_tmp не потрібно
			$this->DB->getWork($sql);
			$sql = "DELETE FROM nets_events WHERE user_id = $user_id AND net_id = $net";
			$this->DB->getWork($sql);
			if($reason == 'unvote'){
				//$this->setNet('user_data', 'show_all') але продумати передачу node; один із варіантів ($data_name = '', $data_value = '', $node = '')
				// для даної операції нам потрібен лише user, тому можна так $this->setNet('user_data', $user_id)
				$sql = "REPLACE INTO nets_users_data (net_id, user_id, email_show, name_show, mobile_show)
						VALUES ($net, $user_id,  1, 1, 1)";
											//для виборів/перевиборів не потрібно, хоча потрібно
											//оскільки скинути show... рівнозначно видаленню рядка
											//але це поки ми зберігаємо лише show
				$this->DB->getWork($sql);
				if($parent_node){
					$sql = "DELETE members_users
							FROM   (SELECT	user_id
									FROM 	nodes JOIN
										nodes_users ON nodes.node_id = nodes_users.node_id
									WHERE	parent_node_id = $parent_node || nodes.node_id = $parent_node) AS circle JOIN
									members_users ON circle.user_id = members_users.user_id
							WHERE	member_id = $user_id AND net_id = $net";
					$this->DB->getWork($sql);
					$sql = "DELETE members_users
							FROM   (SELECT	user_id
									FROM 	nodes JOIN
										nodes_users ON nodes.node_id = nodes_users.node_id
									WHERE	parent_node_id = $parent_node || nodes.node_id = $parent_node) AS circle JOIN
									members_users ON circle.user_id = member_id
							WHERE	members_users.user_id = $user_id AND net_id = $net";
					$this->DB->getWork($sql);
				}
			}
			else{
				$sql = "DELETE FROM nets_users_data WHERE user_id = $user_id AND net_id = $net";
											//для виборів/перевиборів не потрібно, хоча потрібно
											//оскільки скинути show... рівнозначно видаленню рядка
											//але це поки ми зберігаємо лише show
				$this->DB->getWork($sql);
				$sql = "DELETE members_users
						FROM   (SELECT	user_id
								FROM 	nodes JOIN
									nodes_users ON nodes.node_id = nodes_users.node_id
								WHERE	parent_node_id = $node_id) AS tree JOIN
								members_users ON tree.user_id = members_users.user_id
						WHERE	member_id = $user_id AND net_id = $net";
				$this->DB->getWork($sql);
				$sql = "DELETE members_users
						FROM   (SELECT	user_id
								FROM 	nodes JOIN
									nodes_users ON nodes.node_id = nodes_users.node_id
								WHERE	parent_node_id = $node_id) AS tree JOIN
								members_users ON tree.user_id = member_id
						WHERE	members_users.user_id = $user_id AND net_id = $net";
				$this->DB->getWork($sql);
			}
		}
		else{
			$sql = "DELETE 	nodes_users.*, nets_events.*, nets_users_data.*, nodes_tmp.*,  m_u_1.*, m_u_2.*
					FROM 	nodes_users LEFT JOIN
							nets_events ON
								nodes_users.user_id = nets_events.user_id AND nets_events.net_id = $net LEFT JOIN
							nets_users_data ON
								nodes_users.user_id = nets_users_data.user_id AND nets_users_data.net_id = $net LEFT JOIN
							nodes_tmp ON
								nodes_users.node_id = nodes_tmp.node_id LEFT JOIN
							members_users AS m_u_1 ON
								nodes_users.user_id = m_u_1.member_id AND m_u_1.net_id = $net LEFT JOIN
							members_users AS m_u_2 ON
								nodes_users.user_id = m_u_2.user_id AND m_u_2.net_id = $net
					WHERE 	nodes_users.node_id = $node_id ";
			$this->DB->getWork($sql);
		}
		
		if($status == -5){
			$node['event_code'] = 35;
			$this->setNet('notifications', $node);
		}
		elseif($status == 5){
			//закриття статусних повідомлень
			//формування повідомлень
			if($reason == 'refuse'){
				$this->setIam('notification_close', 1);
				$node['event_code'] = 31;
			}
			elseif($reason == 'mentor_disconnect'){
				$node['event_code'] = 34;			
			}
			elseif($reason == 'disconnect_from_parent'){
				$this->setIam('notification_close', 2);
				$node['event_code'] = 21;		
			}			
			else{
				$this->setIam('notification_close', 2);
				$node['event_code'] = 32;
			}
			$this->setNet('notifications', $node);
		}
		//статус 1:
		else{
			//видалення запрошень (скасування), можна подумати чи зробити аналогічно до видалення очікуючих
			//ПОСТАВИТИ взагалі на початок, хоча при використанні блокування це може не бути необхідним
			//user вже підчистив свої повідомлення, але оскільки скасування запрошень іде наступним кроком,
			//а в процесі відповіді на запрошення формуються повідомлення, тому попередню рекомендацію необхідно продумати
			$sql = "DELETE nodes_users.*, nodes_tmp.*
					FROM 	nodes_users JOIN
							nodes ON nodes_users.node_id = nodes.node_id LEFT JOIN
							nodes_tmp ON nodes.node_id = nodes_tmp.node_id
					WHERE parent_node_id = $node_id AND NOT (ISNULL(invite) OR invite = '')";	
			$this->DB->getWork($sql);
			
			//можна подумати $node = $tis->getNet('node');
			$sql = "SELECT nodes.node_id AS node, 0 AS count, 5 AS node_status, user_id AS user, 'mentor_disconnect' AS reason
					FROM nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id
					WHERE parent_node_id = $node_id AND NOT ISNULL(user_id) AND NOT (ISNULL(invite) OR invite = '')";
			$sql = $this->DB->getArray($sql);
			foreach($sql as $node_5){
				$node_5['level'] = '';
				$node_5['address'] = '';
				$node_5['full_address'] = '';
				$node_5['parent_node'] = '';
				$this->chgNet('disconnect', $node_5);
			}
			unset($node_5);
			//print_array('HELLO');
			//скасування статусних повідомлень
			//формування повідомлення для видаленого
			//формування повідомлень для статусів 1 в колі
			//count > 0 (реалізовано в запиті): формування повідомлень для статусів 1 в дереві (про вибори)
			
			if($reason == 'dislike_in_circle'){
				$node['event_code'] = 36;
				//$this->setNet('notifications', $node);
			}
			elseif($reason == 'dislike_in_tree'){
				$node['event_code'] = 37;
				//$this->setNet('notifications', $node);
			}
			elseif($reason == 'vote' || $reason == 'revote'){
				$this->setIam('notification_close', $node); //лише для vote, оскільки статусне повідомлення про вибори є, коли немає координатора
				$node['event_code'] = 38;
				//$this->setNet('notifications', $node);
			}
			elseif($reason == 'unvote'){
				$node['event_code'] = 39;
				//$this->setNet('notifications', $node);			
			}
			elseif($reason == 'disconnect_from_parent'){
				$node['event_code'] = 22;
			}
			else{
				$node['event_code'] = 33;
				//$this->setNet('notifications', $node);
			}
			$this->setNet('notifications', $node);
			//print_array('HELLO');
			//count = 0: delete nodes where parent_node = node_id
/*
			if($count == 0){ //&& $reason != 'revote' в цьому немає необхідності, оскільки при revote count не змінюється
				$sql = "DELETE FROM nodes WHERE parent_node_id = $node_id";
				$this->DB->getWork($sql);
			}
*/
		}
	/*	end   */

	//ВИБОРИ ПЕРЕВИБОРИ
	//якщо count > 1 (статус тільки 1):
		//nets_events - видаляємо рядки user_id = user['id'] AND net_id = user['net']
		//nets_events - видаляємо рядки event_node_id = user['node'] AND net_id = user['net']
		//nets_events - реорганізовуємо рядки user_id = user['id'] AND net_id = user['net']
		//nets_events - реорганізовуємо рядки event_node_id = user['node'] AND net_id = user['net']
	}
	elseif($data_name == 'constrict'){
		//дані іноді можна брати getTree
		$sql = "SELECT nodes.node_id AS node, full_node_address, user_id AS user FROM nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id
				WHERE parent_node_id = $node_id AND count_of_members > 0";
		$sql = $this->DB->getArray($sql);
		if(!$sql){ //ВАРІАНТ 2 не робити запит а if count == 0
			//count = 0: delete nodes where parent_node = node_id
			//ВАРІАНТ 1
			$sql = "DELETE FROM nodes WHERE parent_node_id = $node_id";
			//ВАРІАНТ 2 якщо випадково залишились users з status -5 та +5, хоча це глюк
/*			не потрібно, оскільки при limit_on_vote всі nodes з user_id мають статус 1 (+1, +5, -5)
			$sql = "DELETE 	nodes.*, nodes_users.*, nets_events.*, nets_users_data.*, nodes_tmp.*
					FROM 	nodes JOIN
							nodes_users ON
								nodes.node_id = nodes_users.node_id LEFT JOIN
							nets_events ON
								nodes_users.user_id = nets_events.user_id AND nets_events.net_id = $net LEFT JOIN
							nets_users_data ON
								nodes_users.user_id = nets_users_data.user_id AND nets_users_data.net_id = $net LEFT JOIN
							nodes_tmp ON
								nodes_users.node_id = nodes_tmp.node_id
					WHERE 	nodes.parent_node_id = $node_id";
*/
			$this->DB->getWork($sql);
			//if($node_id != 45 && $node_id != 46 && $node_id != 47) print_array($node);
			$this->chgNet('unset_block', $node, 'disconnect');
			return $answer = NULL;
		}
		if(count($sql) != 1) return $answer = NULL;
		else{
			//блокування net - переносимо в set_block
			//...
			//якщо блокування не вдалось та operation == set_block - розблокування
			if($operation == 'set_block'){
				if(!$this->chgNet('set_block', $node, 'constrict'));
				//set_changes
				return false;
			}
			$sql = $sql[0];
			if(!$this->chgNet('set_block', $sql)){
				$this->chgNet('unset_block', $node); //з розблокуванням можна відразу ставити changes, якщо його передавати параметром operation
				//set_changes
				//розблокування net - переносимо в unset_block
				return false;
			}
		}
		$min_level = $full_address; // 1340000
		$max_level = $full_address + pow(10, $net_max_level - $level); //1340000 + 10000 = 1350000
		$node_id_new = $sql['node'];
		$user_id_new = $sql['user'];
		//$level_new = $sql['node_level'];
		//$address_new = $sql['node_address'];
		$full_address_new = $sql['full_node_address'];

		$sql = "DELETE nets_events.* FROM nets_events JOIN nodes ON nets_events.event_node_id = nodes.node_id
				WHERE user_id = $user_id_new AND (parent_node_id = $node_id OR node_id = $node_id)";			
		$sql = $this->DB->getWork($sql);		
			
		$sql = "DELETE FROM nodes WHERE node_id = $node_id OR (parent_node_id = $node_id AND full_node_address <> $full_address_new)";
		//$sql = "DELETE FROM nodes WHERE node_id = $node_id OR (first_node_id = $net AND full_node_address > $min_level AND full_node_addres < $max_level AND full_node_address <> $full_address_new)";
		//$sql = "DELETE FROM nodes WHERE first_node_id = $net AND full_node_address >= $min_level AND full_node_addres < $max_level AND full_node_address <> $full_address_new";

		$sql = $this->DB->getWork($sql);
		$parent_node or $parent_node = 'NULL';
		$sql = "UPDATE nodes SET node_level = $level, node_address = $address, parent_node_id = $parent_node, full_node_address = $full_address WHERE node_id = $node_id_new";
		//print_array($sql);
		$sql = $this->DB->getWork($sql);
		$sql = "UPDATE nodes SET node_level = node_level - 1, full_node_address = (full_node_address - $full_address_new) * 10 + $full_address WHERE first_node_id = $net AND full_node_address > $min_level AND full_node_address < $max_level";

		$sql = $this->DB->getWork($sql);
		//можливо має бути вище в іншому місці
		$sql = "UPDATE nets_events SET event_node_id = $node_id_new WHERE event_node_id = $node_id";
		//ЧИ ПРОДУМАНО ВИДАЛЕННЯ попереднього повідомлення?
		$sql = $this->DB->getWork($sql);
		//nets_events (повідомлення) які повинні перезавантажити юзера при необхідності

		//УВАГА перетираємо старий node, якщо буде передача по ссилці- ПЕРЕВІРИТИ!
		$node['node'] = $node_id_new;
		$node['user_id'] = $user_id_new; //user чи user_id?
		$node['event_code'] = 40;
		$this->setNet('notifications', $node);

		$this->chgNet('unset_block', $node);
		//розблокування net - перенесли в unset_block
		return $answer = true;
	}
	elseif($data_name == 'dislike'){
		//$parent_node = $node['parent_node'];
		//$node_key = $node['key']; //тут не вірно, має бути node['node_address']
/*
				...
				...
						IF(nodes.node_id = $parent_node, 0,
							IF(nodes.node_address = $node_key, 1,
								IF(nodes.node_address < $node_key, nodes.node_address + 1, nodes.node_address))) AS node_key
				...
				...
				ORDER BY node_key";										
*/										
		$sql = "SELECT	nodes.node_id, SUM(dislike) AS dislike
				FROM 	nodes JOIN
						nodes_users ON nodes.node_id = nodes_users.node_id AND
										(ISNULL(nodes_users.invite) OR nodes_users.invite = '') LEFT JOIN
						members_users ON nodes_users.user_id = members_users.member_id LEFT JOIN
						nodes_users AS nodes_users_1 ON members_users.user_id = nodes_users_1.user_id LEFT JOIN
						nodes AS nodes_1 ON nodes_users_1.node_id = nodes_1.node_id
				WHERE	(nodes.parent_node_id = $node_id OR nodes.node_id = $node_id) AND
						(nodes_1.parent_node_id = $node_id OR nodes_1.node_id = $node_id OR ISNULL(nodes_1.node_id))
				GROUP BY nodes.node_id";
		$sql = $this->DB->getArray($sql);
		$members_count = count($sql);
		//print_array($sql);
		foreach($sql as $node_dislike){
			$dislike_count = $node_dislike['dislike'];
			if(($dislike_count > 1) && (($members_count - $dislike_count) == 1)) return $answer = $node_dislike['node_id'];
		}
		return $answer = NULL;
	}
	elseif($data_name == 'vote'){
		$sql = "SELECT	nodes.node_id AS node, nodes_users.user_id AS user, parent_node_id AS parent_node, SUM(voice) AS voices
				FROM    nodes JOIN
						nodes_users ON nodes.node_id = nodes_users.node_id AND
									(ISNULL(nodes_users.invite) OR nodes_users.invite = '') LEFT JOIN
						members_users ON nodes_users.user_id = members_users.member_id
				WHERE	nodes.parent_node_id = $node_id AND (net_id = $net OR ISNULL(net_id))
				GROUP BY nodes.node_id
        ORDER BY voices DESC";
		$sql = $this->DB->getArray($sql);
		//print_array($sql);
		$members_count = count($sql);
		($user_id) ? $condition = 1 : $condition = 0;
		foreach($sql as $member_voices){
			if(($member_voices['voices'] > $condition) && ($member_voices['voices'] == $members_count)){
				//блокування
				//...
				//скидання виборів
/*				
				$vote_user = $member_voices['user'];
				$sql = "UPDATE members_users SET voice = 0 WHERE member_id = $vote_user AND net_id = $net";
				$this->DB->getWork($sql);
				$sql = "DELETE FROM members_users
						WHERE member_id = $vote_user AND net_id = $net
								AND list_name = '' AND note = '' AND dislike = 0 AND voice = 0"; //voice = 0 не обовязково
				$this->DB->getWork($sql);
*/
				return $answer = $member_voices;
			}
			break;
		}
		return $answer = NULL;
	}
	elseif($data_name == 'vote_reset'){
		$sql = "UPDATE members_users SET voice = 0 WHERE member_id = $user_id AND net_id = $net";
		$this->DB->getWork($sql);
		$sql = "DELETE FROM members_users
				WHERE member_id = $user_id AND net_id = $net
					AND list_name = '' AND note = '' AND dislike = 0 AND voice = 0"; //voice = 0 не обовязково
		$this->DB->getWork($sql);
		return $answer = true;	
	}
	elseif($data_name == 'connect'){
		$sql = "INSERT INTO nodes_users (node_id, user_id) VALUES($node_id, $user_id)";
		$this->DB->getWork($sql);
		$sql = "UPDATE nodes SET node_date = NOW() WHERE node_id = $node_id"; //якщо unvote вузол не видаляти, а очищати,
		$this->DB->getWork($sql);																			//то можна одним запитом оновлювати nodes_users та nodes 
		if($reason == 'unvote'){
			$node['event_code'] = 14;
			$this->setNet('notifications', $node);
		}
		elseif($reason == 'revote'){
			$node['event_code'] = 15;
			$this->setNet('notifications', $node);			
		}
		else{
			//...
		}
		return $answer = true;
	}
	return $answer = true; //???
//}