<?php

Class Data
{
	public	$command = array();
	public 	$next_command = array();
	public	$prev_command = array(); //в сесії не зберігаємо
	private	$server = array();
	public	$redirection = false;
	public	$messages = array();
	public	$user = array();
	public	$circle = array();
	public	$tree = array();
	public	$net = array();
	public 	$circle_tmp = array();
	
	private $DB;
    private $REQUEST;
	private $SESSION;
	private $INSPECTION;
	
    public function __construct()
    {
        require_once 'objects/DataBase.php';
        $this->DB = new DataBase();
		//print_array($this->DB->getArray('call p_users(1)'));
        
        require_once 'objects/Request.php';
        $this->REQUEST = new Request();

		require_once 'objects/Session.php';
        $this->SESSION = new Session();
		
		require_once 'objects/Inspection.php';
        $this->INSPECTION = new Inspection();		
		
		$this->messages = $this->SESSION->session['messages'];
		$this->SESSION->session['messages'] = array();
		$_SESSION['messages'] = array();
		
		$this->next_command = &$this->SESSION->session['next_command'];
		
		if(!empty($_SERVER['HTTP_HOST']))
			$this->server['HTTP_HOST'] = 'http://' . $_SERVER['HTTP_HOST'] . '/';
    }
	
	public function initData()
	{
		$this->getCommand();
		$this->setUser();
		$this->setCircle();
		$this->setTree();
		$this->setNet();
	}
/*-------------- COMMAND ----------------------------------------*/
    public function getCommand()
    {
		require 'objects/Data/getCommand.php';
    }
/*-------------- COMMAND ----------------------------------------*/

/*-------------- REDIRECTION ------------------------------------*/	
	public function getRedirection()
    {
		require 'objects/Data/getRedirection.php';
		return $link;
    }
/*-------------- REDIRECTION ------------------------------------*/

/*-------------- SESSION ----------------------------------------*/
	public function getSession()
	{
		return $this->SESSION->session;
	}
/*-------------- SESSION ----------------------------------------*/

/*-------------- SERVER -----------------------------------------*/		
	public function getServer($data_name)
	{
		return $this->server[$data_name];
	}
/*-------------- SERVER -----------------------------------------*/	

/*-------------- USER -------------------------------------------*/		
	public function getUser($data_name = '', $data_value = '')
    {
		require 'objects/Data/getUser.php';
		return $answer;
	}

	public function setUser($data_name = '', $data_value = '')
    {
		require 'objects/Data/setUser.php';
		return $answer;
	}
	
	public function deleteUser()
	{
		require 'objects/Data/deleteUser.php';
	}
/*-------------- USER -------------------------------------------*/		
	
/*-------------- NET --------------------------------------------*/
	public function setIam($data_name = '', $data_value = '')
	{
		require 'objects/Data/setIam.php';
		return $answer;
	}

	public function getIam($data_name = '', $data_value = '')
	{
		require 'objects/Data/getIam.php';
		return $answer;	
	}
	
	public function setCircle($data_name = '', $data_value = '')
    {
		if(!$data_name){
			$this->circle = &$this->SESSION->session['circle'];
			return true;
		}
		
		$this->circle = array();
		$user = &$this->user;
		$user_id = $user['id'];
		$node_id = $user['node'];
		$parent_node_id = $user['parent_node'];
		$net_id = $user['net'];
		//print_array($user, 1);
		//print_array($this->circle, 1);
		if(!$parent_node_id) return false; //node level = 1
		//print_array('hello');
		//node_status можна визначати по count, а не по invite
		$sql = "SELECT 	nodes.node_id,
						nodes_users.user_id,
						IF(LENGTH(list_name) > 0, CONCAT('.', list_name),
							IF(nodes.node_id = $parent_node_id, 
								name,
								IF(name_show = 1, name, NULL))) AS name,
						count_of_members,
						IF(NOT ISNULL(nodes_users.user_id), 
							IF(ISNULL(nodes_users.invite) OR nodes_users.invite = '', +1, +5),
							IF(count_of_members > 0, -1, 0)) AS node_status,
						dislike, voice
				FROM nodes
				LEFT JOIN nodes_users ON
					nodes.node_id = nodes_users.node_id AND
					(ISNULL(nodes_users.invite) OR nodes_users.invite = '' OR nodes_users.node_id = $node_id)
				LEFT JOIN users	ON
					nodes_users.user_id = users.user_id
				LEFT JOIN nets_users_data ON 
					nets_users_data.user_id = nodes_users.user_id AND
					nets_users_data.net_id = $net_id
				LEFT JOIN members_users ON
					members_users.member_id = nodes_users.user_id AND
					members_users.user_id = $user_id AND
					members_users.net_id = $net_id
				WHERE parent_node_id = $parent_node_id OR
						nodes.node_id = $parent_node_id
				ORDER BY full_node_address";
		//print_array($sql);				
		$sql = $this->DB->getArray($sql);
		//print_array($sql);
		$this->circle[0] = '';
		$this->circle[1] = '';
		$this->user['voice_for_key'] = '';
		foreach($sql as $key => $row){
			if(($key > 0) && ($key < $user['node_address'])) $key = $key + 1;
			elseif($key == $user['node_address']) $key = 1;
			$name = $row['name'];
			if($key == 1) $name = 'Я';
			elseif($key == 0 && (!$name || $user['status'] == 5)) $name = 'Координатор';
			elseif(!$name){
				//if($key == 0 ) $name = 'Координатор';
				if($row['user_id']) $name = 'Учасник ' . ($key - 1);
				elseif($row['node_status'] == -1) 'Комірка ' . ($key - 1);
			}
			$this->circle[$key] = array('node' => $row['node_id'],
										'user' => $row['user_id'],
										'user_name' => $name,
										'node_status' => $row['node_status'],
										'count' => $row['count_of_members'],
										'dislike' => $row['dislike'],
										'voices' => 0);
			if($row['voice']) $this->user['voice_for_key'] = $key;
		}
		//print_array($this->user);
		return true;
	}

	public function getCircle($data_name){
	
		if($data_name == 'voices'){
			$user_node_address = $this->user['node_address'];
			$parent_node = $this->user['parent_node'];
			$net_id = $this->user['net'];
			$sql = "SELECT	
							IF(nodes.node_address = $user_node_address, 1,
								IF(nodes.node_address < $user_node_address, nodes.node_address + 1, nodes.node_address)) AS node_key,
							SUM(IF(ISNULL(voice), 0, voice)) AS voices
					FROM	nodes JOIN
							nodes_users ON nodes.node_id = nodes_users.node_id AND
											(ISNULL(nodes_users.invite) OR nodes_users.invite = '') LEFT JOIN
							members_users ON nodes_users.user_id = members_users.member_id
					WHERE	nodes.parent_node_id = $parent_node AND (net_id = $net_id OR ISNULL(net_id))
					GROUP BY nodes.node_id
					ORDER BY voices DESC, node_key";
			$sql = $this->DB->getArray($sql);
			//print_array($sql);
			$this->circle_tmp['voices'] = $sql;
			return true; //$sql;	
		}
		elseif($data_name == 'changes'){
			$node_id = $this->circle[0]['node'];
			$sql = "SELECT changes FROM nodes WHERE node_id = $node_id";
			$sql = $this->DB->getOneArray($sql);
			if($sql)
				if($sql['changes']) return $node_id;
			return false;
		}
	}

	
	public function setTree($data_name = '', $data_value = '')
    {
		if(!$data_name){
			$this->tree = &$this->SESSION->session['tree'];		
			return true;
		}

		if($data_value){//!$user_id
			$node_id = $data_value;
			//таблиця users тут мабуть не потрібна
			$sql = "SELECT 	nodes.node_id,
							nodes_users.user_id,
							count_of_members,
							first_node_id
				FROM nodes
				LEFT JOIN nodes_users ON nodes.node_id = nodes_users.node_id
				LEFT JOIN users ON nodes_users.user_id = users.user_id				
				WHERE parent_node_id = $node_id
				ORDER BY node_address";
			$sql = $this->DB->getArray($sql);
			if(!$sql) return false;
			foreach($sql as $key => $row){
				$key = $key + 2;
				$user_id = $row['user_id'];
				$count = $row['count_of_members'];
				$node_status = 0;
				if($user_id) $node_status = 1;
				elseif($count > 0) $node_status = -1;
/*
				$this->tree[$key] = array(	'node' => $row['node_id'],
											'user' => '',
											'user_name' => '',
											'node_status' => $node_status,
											'count' => '', //$count,
											'invite' => '',
											'dislike' => '');
*/
				$tree[$key] = array(		'node' => $row['node_id'],
											'user' => $row['user_id'],
											'user_name' => '',
											'node_status' => $node_status,
											'count' => '', //$count,
											'invite' => '',
											'dislike' => '',
											'first_node' => $row['first_node_id']);										
			}
			return $tree;
		}
		
		$this->tree = array();
		$user = &$this->user;
		$user_id = $user['id'];
		$node_id = $user['node'];
		$net_id = $user['net'];
		$sql = "SELECT 	nodes.node_id,
						nodes_users.user_id,
						IF(LENGTH(members_users.list_name) > 0,
							CONCAT('.', members_users.list_name),
							IF(LENGTH(nodes_tmp.list_name) > 0,
								CONCAT('.', nodes_tmp.list_name),
								IF(name_show = 1,
									name,
									NULL)))	AS name,
						count_of_members,
						nodes_users.invite,
						dislike
				FROM nodes
				LEFT JOIN nodes_users ON nodes.node_id = nodes_users.node_id
				LEFT JOIN nodes_tmp ON nodes.node_id = nodes_tmp.node_id
				LEFT JOIN users ON nodes_users.user_id = users.user_id
				LEFT JOIN nets_users_data ON 
					nets_users_data.user_id = nodes_users.user_id AND
					nets_users_data.net_id = $net_id
				LEFT JOIN members_users ON
					members_users.member_id = nodes_users.user_id AND
					members_users.user_id = $user_id AND
					members_users.net_id = $net_id				
				WHERE parent_node_id = $node_id OR
						nodes.node_id = $node_id
				ORDER BY full_node_address";
		$sql = $this->DB->getArray($sql);
		if(count($sql) < 2) return false;
		foreach($sql as $key => $row){
			$key = $key + 1;
			$user_id = $row['user_id'];
			$name = $row['name'];
			$invite = $row['invite'];
			$count = $row['count_of_members'];
			$node_status = 0;
			if($user_id){
				if($invite) $node_status = 5;
				else $node_status = 1;
			}			
			elseif($count > 0) $node_status = -1;
			elseif($invite) $node_status = -5;		
			$name = $row['name'];
			if($key == 1) $name = 'Я';
			elseif(!$name){
				if($row['user_id'] || $node_status == -5) $name = 'Учасник ' . ($key - 1);
				if($node_status == -1) $name = 'Комірка ' . ($key - 1);
			}
			$this->tree[$key] = array(	'node' => $row['node_id'],
										'user' => $user_id,
										'user_name' => $name,
										'node_status' => $node_status,
										'count' => $count, //перевірити
										'invite' => $invite,
										'dislike' => $row['dislike']);
		}									
		return true;
	}
	
	public function setNet($data_name = '', $data_value = '')
    {
		if(!$data_name){
			//$this->net['id'] = $this->user['net'];
			$this->net['id'] = &$this->SESSION->session['net_id'];
			$this->net['circle_tree'] = &$this->SESSION->session['net_circle_tree'];		
			$this->net['parent_net'] = &$this->SESSION->session['net_parent_net'];
			$this->net['name'] = &$this->SESSION->session['net_name'];
			$this->net['max_level'] = 7;
			$this->net['max_level_nets'] = 5;
			$this->net['notifications'] = array();
			return true;
		}	
	
		switch($data_name){
			case 'connection':
				//print_array($this->user);
				$user_id = $this->user['id'];
				$node_id = $this->user['node'];
				$level = $this->user['level'];
				$parent_node_id = $this->user['parent_node'];
				$net_id = &$this->user['net'];
				$parent_net_id = '';
				$node = array();
				//СПРОБУВАТИ ОПТИМІЗУВАТИ ЗАПИТИ
				$sql = '	SELECT net_id, parent_net_id
							FROM nodes JOIN nets ON first_node_id = net_id
							WHERE node_id = ' . $node_id;
				$sql = $this->DB->getOneArray($sql);
				if(!$sql) return false; //ПЕРЕВІРИТИ
				$net_id = $sql['net_id'];
				$parent_net_id = $sql['parent_net_id'];				
				//перевіряємо чи немає user вже в мережі, в яку він долучається
				$sql = '	SELECT user_id AS user_in_net
							FROM nodes_users JOIN nodes ON nodes_users.node_id = nodes.node_id
								JOIN nodes AS nodes_1 ON nodes.first_node_id = nodes_1.first_node_id
							WHERE user_id = ' . $user_id . ' AND nodes_1.node_id = ' . $node_id;
				$sql = $this->DB->getOneArray($sql);
				if(isset($sql['user_in_net'])){
					$this->messages[] = array('type' => 'error', 'text' => 'Ви вже є учасником цієї спільноти!');					
					return $net_id;
				}
				//перевіряємо, чи входить user в parent мережу
				if($parent_net_id){
					$sql = "	SELECT user_id AS user_in_parent_net, nodes.node_id
								FROM nodes_users JOIN nodes ON nodes_users.node_id = nodes.node_id
								WHERE user_id = $user_id AND first_node_id = $parent_net_id";
					$sql = $this->DB->getOneArray($sql);
					if(empty($sql['user_in_parent_net'])){ //if(!$sql)
						$this->messages[] = array('type' => 'error', 'text' => 'Для долучення до спільноти необхідно спочатку стати учасником її батьківської спільноти!');
						return false;
					}
					$node['node'] = $sql['node_id'];
					$node['parent_node'] = '';					
					if($parent_node_id){ //значить це invite, а не create
						if(!$this->chgNet('set_block', $node, 'connection')) return false;
					}
				}
				$sql = 'UPDATE nodes_users SET user_id = ' . $user_id . ' WHERE node_id = ' . $node_id;
				$sql = $this->DB->getWork($sql);
				!$node or $this->chgNet('unset_block', $node, 'connection');
				$this->setNet('user_data', 'new');
				//якщо user['node'] буде в одному форматі з net['active_node'], то можна буде net['active_node'] = user['node']
				//або net['active_node'] = &user['node']
				//так само це можна буде робити при $node_key == 1
				$node = array();
				$node['event_code'] = 11;
				$node['node'] = $node_id;
				$node['parent_node'] = $parent_node_id;
				$node['user'] = $user_id;
				$this->setNet('notifications', $node);
				//перевірити
/*
				if(!$parent_node_id){
					$this->command['operation'] = 'approve';
					$this->setNet('invite');
					$this->messages[] = array('type' => 'success', 'text' => 'Спільноту успішно створено!');
				}
				else
*/
				if($parent_node_id)
					$this->messages[] = array('type' => 'success', 'text' => 'Ви долучились до спільноти і очікуєте на ідентифікацію!');
				//print_array('HELLO');
				return $net_id;
			
			case 'user_nets':
				//print_array($this->user);
				if($data_value){
					$node = $data_value;
					$net_id = $node['first_node'];
					$user_id = $node['user'];
					//parent_node_id витягуєм для універсалізації
					$sql = "	SELECT nodes.node_id AS node, parent_node_id AS parent_node, first_node_id AS first_node, user_id AS user,
								IF(ISNULL(invite) OR invite = '', 1, 5) AS node_status
								FROM nodes_users
								JOIN nodes ON nodes_users.node_id = nodes.node_id
								JOIN nets ON first_node_id = net_id
								WHERE user_id = $user_id AND parent_net_id = $net_id";
					$sql = $this->DB->getArray($sql);
					//$user_nets = array();
					//foreach($sql as $row)
					//	$user_nets[] = $row['node_id'];
					return $sql; //$user_nets;
				}
				$net_id = $this->user['net'];
				$user_id = $this->user['id'];
				if($net_id) $sql = 'parent_net_id = ' . $net_id;
				else $sql = 'ISNULL(parent_net_id)';
				if($user_id){
					$sql = "	SELECT nets.net_id, name FROM nodes_users
								JOIN nodes ON nodes_users.node_id = nodes.node_id
								JOIN nets ON first_node_id = net_id
								JOIN nets_data ON nets.net_id = nets_data.net_id
								WHERE user_id = $user_id AND $sql";
				}
				else $sql = "	SELECT net_id FROM nets WHERE $sql";
				//print_array($sql);
				$sql = $this->DB->getArray($sql);
				//print_array($sql);
				$user_nets = array();
				$this->user['nets'] = array();
				foreach($sql as $row){
					$user_nets[] = $row['net_id'];
					if($user_id) $this->user['nets'][$row['net_id']] = $row['name']; //при limit_on_vote не використовується
				}
				//$this->user['nets'] = $user_nets;
				//print_array($user_nets);
				return $user_nets;
			case 'user_parent_nets':
				$user_parent_nets = &$this->user['parent_nets'];
				$user_parent_nets = array();
				//print_array($this->net);
				$parent_net_id = $this->net['parent_net'];
				while($parent_net_id){
					//можна використати стандартний запит
					$sql = "SELECT nets.net_id, name, parent_net_id FROM nets JOIN nets_data ON nets.net_id = nets_data.net_id
							WHERE nets.net_id = $parent_net_id";
					$sql = $this->DB->getOneArray($sql);
					$user_parent_nets[$sql['net_id']] = $sql['name'];
					$parent_net_id = $sql['parent_net_id'];
				}			
				$this->user['parent_nets'] = $user_parent_nets;
				//$this->user['parent_nets'][0] = 'АКАУНТ'; //не зовсім коректно в цьому місці
				return $user_parent_nets;
			case 'invite': //invitation
				if($data_value){ //можливо потрібно command перевіряти на empty
					$node = $data_value;
					//if($this->command['operation'] != 'approve'){
						$node_id = $node;
						$invite = uniqid();
						$sql = "INSERT INTO nodes_users (node_id, invite) VALUES ($node_id, '$invite')";
						//print_array($sql);
						$sql = $this->DB->getWork($sql);
						return $invite;
					//}
				}			
				//else 
				$node = &$this->net['active_node'];
				//if($this->command['operation'] == 'approve'){ print_array($node); }
				$node_id = $node['node'];
				if(!$node['invite']){
					$this->setIam('notification_close', 1);
					$invite = uniqid();
					$sql = "INSERT INTO nodes_users (node_id, invite) VALUES ($node_id, '$invite')";
					$sql = $this->DB->getWork($sql);
					//if($this->command['data']['email']){
					//	$email = $this->command['data']['email'];
					//	$sql = "INSERT INTO nodes_tmp (node_id, email) VALUES ($node_id, '$email')";
					//	$sql = $this->DB->getWork($sql);
					//}
					$node['invite'] = $invite;
					$node['event_code'] = 13;
					//print_array($node);
					$this->setNet('notifications', $node);
					//print_array($node);
					$this->setNet('user_for_member', 'create'); //operation = create
					//print_array($node);
					return true;
				}
				
				if($this->command['operation'] == 'write'){
					//$this->setNet('user_for_member', 'invite'); //operation = write
					//return true;
				}
				//print_array($node);
				if($this->command['operation'] == 'approve'){
					if(!$this->chgNet('set_block', $node, 'approve')) return false;
/*
					if($node['level'] < $this->net['max_level']){
						$this->setNet('new_tree');
						$sql = "SELECT node_level FROM nodes WHERE node_id = $node_id";
						$this->DB->getOneArray($sql);
						if($sql['node_level'] != $node['level']);
						$sql = "DELETE FROM nodes WHERE parent_node_id = $node_id";
						$this->DB->getWork($sql);
						$this->messages[] = array('type' => 'error', 'text' => 'Операцію відхилено!');
						return false;
					}
*/					
					$sql = "UPDATE nodes_users JOIN nodes ON nodes_users.node_id = nodes.node_id
							SET invite = NULL, node_date = NOW() WHERE nodes_users.node_id = $node_id";
					$sql = $this->DB->getWork($sql);
					//запит нижче перенести в set active node
					//$sql = "SELECT node_level, full_node_address FROM nodes WHERE node_id = $node_id";
					//$sql = $this->DB->getOneArray($sql);
					//$node['level'] = $sql['node_level']; //? 
					//$node['full_address'] = $sql['full_node_address']; //?
					$this->setNet('count');
					//print_array('hello');
					if($node['level'] < $this->net['max_level']) $this->setNet('new_tree');
					//print_array('hello');
					$this->setIam('notification_close', 0);
					//print_array('hello');
					if($node['parent_node']){
						$node['event_code'] = 12;
						$this->setNet('notifications', $node);
					}
					else{
						$node['event_code'] = 16;
						$this->setNet('notifications', $node);					
					}
					//print_array('hello');
					$this->setNet('user_for_member', 'connect');
					$this->chgNet('unset_block', $node, 'disconnect');
					return true;
				}
				
				//$node['reason'] = 'delete';
				//$this->chgNet('disconnect', $node);
				return true;
			case 'user_for_member':
				/* ДЛЯ СПРОЩЕННЯ МОЖНА РОЗМІЩУВАТИ invite ТАКОЖ  в nodes_tmp */
				$control = false;
				$list_name_old = $this->net['active_node']['list_name'];
				$note_old = $this->net['active_node']['note'];
				$dislike_old = $this->net['active_node']['dislike'];
				($this->user['voice_for_key'] == $this->net['active_node']['key']) ? $voice_old = 1 : $voice_old = 0;
				$list_name = $list_name_old;
				$note = $note_old;
				($dislike = $dislike_old) or $dislike = 0;
				$voice = $voice_old;
				$node_id = $this->net['active_node']['node'];
				$condition = 2;
				$operation = $data_value;
				if($operation == 'connect'){
					//$node = $this->net['active_node']['node'];
					//$list_name = $list_name_old;
					//$note = $note_old;
					$sql_clean = "DELETE FROM nodes_tmp WHERE node_id = $node_id";
					//варіант 1: виконуємо запит відразу
					//для виконання запиту 'не відразу', а після запису даних в nets_users_data,
					//врахувати: наявність email в nodes_tmp та умову непустих list_name та note
					$sql = $this->DB->getWork($sql_clean, $affected_rows);
				}
//				elseif($data_value == 'create'){
//					$list_name = '';
//					$note = '';		
//				}
				elseif($operation == 'dislike'){
					($this->command['data']['dislike'] == 'on') ? $dislike = 1 : $dislike = 0;
					//print_array($this->command);
				}
				elseif($operation == 'voice_set' || $operation == 'voice_reset'){
					($this->command['data']['voice'] == 'on') ? $voice = 1 : $voice = 0;
				}
				else{
					$list_name = $this->DB->getDB('real_escape_string', $this->command['data']['list_name']);
					$note = $this->DB->getDB('real_escape_string', $this->command['data']['note']);
				}
				//print_array($list_name_old, 1);
				//print_array($note_old, 1);
				//print_array($list_name, 1);
				//print_array($note);
				
				if($this->command['operation'] != 'create'){
					if(!$list_name_old && !$note_old && !$dislike_old && !$voice_old){
						if(!$list_name && !$note && !$dislike && !$voice) return NULL;
						else $condition = 1;
					}
				}
				
				if($data_value == 'create'){
					$email = $this->DB->getDB('real_escape_string', $this->command['data']['email']);
					//if(!$email) return NULL;
					$sql = "INSERT INTO nodes_tmp (node_id, email, list_name, note)
								VALUES($node_id, '$email', '', '')";
					$condition = 0;		
				}
				elseif($data_value == 'invite'){
					//ЗРОБИТИ UPDATE
					//$node = $this->net['active_node']['node'];
					$email = $this->net['active_node']['email'];
/*
					if(!$list_name && !$note && !$email){
						$sql = "DELETE FROM nodes_tmp WHERE node_id = $node_id";
						$condition = 1;
					}
					else{
						$sql = "REPLACE INTO nodes_tmp (node_id, list_name, note)
								VALUES($node_id, '$list_name', '$note')";
						//
					}
*/
					$sql = "UPDATE nodes_tmp SET list_name = '$list_name', note = '$note' WHERE node_id = $node_id";
					$condition = 1;
				}
				else{
					$user_id = $this->user['id'];
					$net_id = $this->user['net'];
					$member = $this->net['active_node']['user'];
					if(!$list_name && !$note && !$dislike && !$voice){
						$sql = "DELETE FROM members_users WHERE member_id = $member AND user_id = $user_id AND net_id = $net_id";
						$condition = 1;
					}
					else{
						$control = true;
						$sql = "REPLACE INTO members_users (net_id, member_id, user_id, list_name, note, dislike, voice)
								VALUES($net_id, $member, $user_id, '$list_name', '$note', $dislike, $voice)";
					}
				}
				//print_array($sql);
				$sql = $this->DB->getWork($sql, $affected_rows);
				if($control){
					$sql = "SELECT * FROM nodes_users WHERE node_id = $node_id AND user_id = $member";
					$sql = $this->DB->getWork($sql);
					if(!$sql) $control = false;
					$user_node = $this->user['node'];
					$sql = "SELECT * FROM nodes_users WHERE node_id = $user_node AND user_id = $user_id";
					$sql = $this->DB->getWork($sql);
					if(!$sql) $control = false;
					if(!$control){
						$sql = "DELETE FROM members_users WHERE member_id = $member AND user_id = $user_id AND net_id = $net_id";
						$this->DB->getWork($sql);
						$this->messages[] = array('type' => 'error', 'text' => 'Операцію відхилено!');
						return false;
					}
				}
				//print_array($condition);
				if($affected_rows == $condition){
					if($operation == 'voice_reset') return true;
					$this->messages[] = array('type' => 'success', 'text' => 'Дані успішно оновлено!');
					//$node['event_code'] = xx;
					//$this->setNet('notifications', $node);
					return true;				
				}
				//else return NULL;
				return NULL;
			case 'user_data':
				//print_array($this->command);
				$user_id = $this->user['id'];
				$net_id = $this->user['net'];			
				$name_show = 0;
				$email_show = 0;
				$mobile_show = 0;
				if($data_value != 'new'){
					if($this->command['data']['user_email'] == 'on') $email_show = 1;
					if($this->command['data']['user_name'] == 'on') $name_show = 1;
					if($this->command['data']['user_mobile'] == 'on') $mobile_show = 1;
				}
				$sql = "REPLACE INTO nets_users_data (net_id, user_id, email_show, name_show, mobile_show)
						VALUES ($net_id, $user_id,  $email_show, $name_show, $mobile_show)";
				$sql = $this->DB->getWork($sql, $affected_rows);
				if($affected_rows == 2){
					$this->messages[] = array('type' => 'success', 'text' => 'Дані успішно оновлено!');
					//$node['event_code'] = xx;
					//$this->setNet('notifications', $node);	
					return true;
				}
				return NULL;
			case 'new_tree':
				$node = $this->net['active_node'];
				$node_id = $node['node'];
				$net_id = $this->user['net'];
				$max_level = $this->net['max_level'];
				$level = $node['level'];
				$full_node_address = $node['full_address'];
				$level = $level + 1;
				//$time = date('Y-m-d H:i:s');
				for($c = 1; $c <= 6; $c++){
					$full_address = $full_node_address + $c * pow(10, $max_level - $level);
					$sql = "INSERT INTO nodes (node_level, node_address, parent_node_id, first_node_id, full_node_address, count_of_members, node_date) VALUES(
								$level,
								$c,
								$node_id,
								$net_id,
								$full_address,
								0,
								NOW())";
								//варіант 2: FROM_UNIXTIME($time))";
								//варіант 3: встановити в БД defaul value NOW();
					$sql = $this->DB->getWork($sql);
				}
				return true;
			case 'count':
				if($data_value){
					//ТУТ $data_value завжди -1
					$node = $data_value;
					$data_value = -1;
					$node_id = $node['node'];
					//ПЕРЕНЕСТИ В chgNet ?
					if($node['reason'] == 'vote'){
						$sql = "UPDATE nodes SET count_of_members = count_of_members - 1, node_date = NOW() WHERE node_id = $node_id";
						$sql = $this->DB->getWork($sql);
						break;
					}
					else{
						$sql = "UPDATE nodes SET node_date = NOW() WHERE node_id = $node_id";
						$sql = $this->DB->getWork($sql);
					}
				}
				else{
					//ТУТ $data_value завжди +1
					$node = $this->net['active_node'];
					$data_value = 1;
				}
				//ВАРІАНТ 1
				//для використання даного варіанту необхідно поле full_node_address зробити індексованим
				$max_level = $this->net['max_level'];
				$net = $this->user['net'];
				$full_address = $node['full_address'];
				$level = $max_level - $node['level'];
				$sql = '';				
				$or = 'full_node_address = ';			
				while($level < $max_level){
					$level = $level + 1;
					$sql = $sql . $or . $full_address;
					$full_address = floor($full_address / pow(10, $level)) * pow(10, $level);
					$or = ' OR full_node_address = ';
				}
				//($data_value == -1) or $data_value = 1;
				$sql = "UPDATE nodes SET count_of_members = count_of_members + $data_value WHERE first_node_id = $net AND ($sql)";
				//print_array($sql);
				$sql = $this->DB->getWork($sql);
				//$sql = "UPDATE nets SET count_of_members = count_of_members + $data_value WHERE net_id = $net";
				//$sql = $this->DB->getWork($sql);
				break;				
				//ВАРІАНТ 2
				/*
				$sql = 'UPDATE nodes SET count_of_members = count_of_members + 1 WHERE node_id = ' . $data_value;
				$sql = $this->DB->getWork($sql);
				$sql = 'SELECT parent_node_id FROM nodes WHERE node_id = ' . $data_value;
				$sql = $this->DB->getOneArray($sql);
				if($sql['parent_node_id']) $this->setNet('count', $sql['parent_node_id']);
				break;
				*/
				//ВАРІАНТ 3
				//foreach
			case 'reset_changes':
				$node_id = $data_value;
				$sql = "UPDATE nodes SET changes = 0 WHERE node_id = $node_id";
				$this->DB->getWork($sql);
				return;
			case 'notifications':
				$node = $data_value;
				//print_array($node);
				//---------------------------------------
				$net_id = $this->user['net'];
				$event_code = $node['event_code'];
				$node_id = $node['node'];
				$parent_node = $node['parent_node'];
				
				$user_id = $node['user'];
				$notifications = array();
				//---------------------------------------
				$sql = "SELECT * FROM notifications_tpl WHERE event_code = $event_code";
				$sql = $this->DB->getArray($sql);
				//print_array($sql);
				foreach($sql as $notification_tpl){
					$notification_id = $notification_tpl['notification_tpl_id'];
					$notification_code = $notification_tpl['notification_code'];
					$notification_text = $this->DB->getDB('real_escape_string', $notification_tpl['notification_text']);
					$sql = '';
					if($notification_code == 0){
						$this->messages[] = array('type' => 'success', 'text' => $notification_tpl['notification_text']);
					}
					elseif($notification_code == 1){
						if($parent_net_id = $this->net['parent_net']){
							$sql = "INSERT INTO nets_events (net_id, user_id, event_node_id, notification_tpl_id, event_code, notification_text, new) 
									VALUES($parent_net_id, $user_id, NULL, $notification_id, ($event_code * 10 + $notification_code), '$notification_text', 1)";
									break; //перевірити
						}
						//if($event_code == 31)
							//без real_escape_string
							$this->net['notifications'][] = array('user' => $user_id, 'text' => $notification_tpl['notification_text']);
						//else{ //$event_code == 34
						//	$this->net['notifications'][] = array('user' => $user_id, 'code' => '', 'text' => $notification_text);
						//}
							//print_array($this->net['notifications']);
							continue;
						//}
						//$sql = "SELECT user_id FROM nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id
						//		WHERE parent_node_id = $node_id AND NOT ISNULL(user_id) AND (ISNULL(invite) || invite = '')";
						//$sql = $this->DB->getArray($sql);
						//foreach($sql as $row){
						//	$notifications[] = array('user' => $sql['user_id'], 'text' => $notification_text);
						//}
					}
					elseif($notification_code == 2){
						//тут ЛАЖА!!! просто копія "3"
					/*
						$sql = "INSERT INTO nets_events (net_id, user_id, event_node_id, notification_tpl_id, event_code, notification_text, new) 
									SELECT $net_id, user_id, $node_id, $notification_id, ($event_code * 10 + $notification_code), '$notification_text', 1
									FROM nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id
									WHERE nodes.node_id = $parent_node AND NOT ISNULL(user_id)";
					*/
						$sql = "INSERT INTO nets_events (net_id, user_id, event_node_id, notification_tpl_id, event_code, notification_text, new) 
									VALUES($net_id, $user_id, NULL, $notification_id, ($event_code * 10 + $notification_code), '$notification_text', 1)";									
					}
					elseif($notification_code == 3 && $parent_node){
						$sql = "INSERT INTO nets_events (net_id, user_id, event_node_id, notification_tpl_id, event_code, notification_text, new) 
									SELECT $net_id, user_id, $node_id, $notification_id, ($event_code * 10 + $notification_code), '$notification_text', 1
									FROM nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id
									WHERE nodes.node_id = $parent_node AND NOT ISNULL(user_id)";				
					}
					elseif($notification_code == 4 && $parent_node){
						$sql = "INSERT INTO nets_events (net_id, user_id, event_node_id, notification_tpl_id, event_code, notification_text, new) 
									SELECT $net_id, user_id, $node_id, $notification_id, ($event_code * 10 + $notification_code), '$notification_text', 1
									FROM nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id
									WHERE parent_node_id = $parent_node AND NOT ISNULL(user_id) AND (ISNULL(invite) || invite = '') AND nodes.node_id <> $node_id";					
					}
					elseif($notification_code == 5){
						$sql = "INSERT INTO nets_events (net_id, user_id, event_node_id, notification_tpl_id, event_code, notification_text, new) 
									SELECT $net_id, user_id, $node_id, $notification_id, ($event_code * 10 + $notification_code), '$notification_text', 1
									FROM nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id
									WHERE nodes.parent_node_id = $node_id AND NOT ISNULL(user_id)";
					}
					elseif($notification_code == 6 && $parent_node){
						$sql = "INSERT INTO nets_events (net_id, user_id, event_node_id, notification_tpl_id, event_code, notification_text, new) 
									SELECT $net_id, user_id, $parent_node, $notification_id, ($event_code * 10 + $notification_code), '$notification_text', 1
									FROM nodes JOIN nodes_users ON nodes.node_id = nodes_users.node_id
									WHERE nodes.node_id = $node_id AND NOT ISNULL(user_id)";
					}
					if($sql) $sql = $this->DB->getWork($sql);
				}
				//if($notifications) return $notifications;
				return true;
				//$event = $data_value;
				//$net_id = $this->user['net'];
				/*
				$sql = 'INSERT INTO nets_events (net_id, user_id, event_node_id, code, notification, new) VALUES(' .
					$net_id . ', ' .
					$event['user_id'] . ', ' .
					$event['event_node_id'] . ', ' .
					$event['code'] . ', "' .
					$event['notification'] . '",
					1)';
				*/
				//$sql = $this->DB->getWork($sql);
			default:
		}
	}
	
	public function getNet($data_name, $data_value = '')
    {
		switch($data_name)
		{		
			case 'user':
				$user_id = $this->user['id'];
				$net_id = $this->user['net'];
				if(!$net_id) return false; //мабуть контрольна перевірка
/*
				$sql = "SELECT user_id AS id, parent_net_id AS parent_net, nodes.node_id, invite
						FROM nets LEFT JOIN nodes ON nets.net_id = nodes.first_node_id JOIN nodes_users ON nodes_users.node_id = nodes.node_id
						WHERE nodes_users.user_id = $user_id AND net_id = $net_id";
*/				
				$sql = "SELECT user_id AS id, parent_net_id AS parent_net, node_id, invite, changes
						FROM nets LEFT JOIN 
							(	SELECT user_id, nodes.node_id, invite, first_node_id, changes
								FROM nodes JOIN nodes_users ON nodes_users.node_id = nodes.node_id
								WHERE nodes_users.user_id = $user_id AND first_node_id = $net_id) AS node
						ON nets.net_id = node.first_node_id
						WHERE net_id = $net_id";
/*				
				$sql = 'SELECT nodes.node_id, invite 
						FROM nodes JOIN nodes_users ON nodes_users.node_id = nodes.node_id
						WHERE nodes_users.user_id = ' . $this->user['id'] . ' AND first_node_id = ' . $this->user['net'];
*/
				$sql = $this->DB->getOneArray($sql);
				if(!$sql) return false;
				return $sql;			
			case 'node_id':
				//return false
				//return allow
				//return allow_authorize
				//return allow_registration
				//return forbid_authorize
				//return forbid_registration
				$invite = &$this->user['invite'];
				$invite = $this->DB->getDB('real_escape_string', $invite);
				if(!$invite) return false; //'лінк не дійсний'
				$sql = "SELECT nodes_users.node_id, parent_node_id, nodes_tmp.email AS invite_email,
								IF(ISNULL(users.email), 0, 1) AS user_in_base
						FROM nodes_users JOIN nodes ON nodes_users.node_id = nodes.node_id
						LEFT JOIN nodes_tmp ON nodes_users.node_id = nodes_tmp.node_id
						LEFT JOIN users ON nodes_tmp.email = users.email
						WHERE nodes_users.invite = '$invite' AND ISNULL(nodes_users.user_id)";						
				$sql = $this->DB->getOneArray($sql);
				if(!$sql){
					$this->messages[] = array('type' => 'error', 'text' => 'Лінк не дійсний!');
					$invite = ''; //можна не стирати, якщо всюди робити ресет
					return false; // 'лінк не дійсний'
				}
				//ЛІНК Є
				$invite_email = $sql['invite_email'];
				$user_in_base = $sql['user_in_base'];
				$user_id = $this->user['id'];
				//ДЛЯ СТАТУСА 0
				if($user_id){
					
					$user_email = $this->user['email'];
					if($invite_email && ($invite_email <> $user_email)){
						//print_array($sql);
						$this->messages[] = array('type' => 'error', 'text' => 'Вийдіть з акаунта і відкрийте лінк ще раз!');
						$invite = ''; //можна не стирати, якщо всюди робити ресет
						return false; // 'лінк не від того акаунта' можна true і видачу повідомлення в статусі 0
					}
					$this->user['node'] = $sql['node_id'];
					$this->user['parent_node'] = $sql['parent_node_id'];
					return 'allow'; //можна true
				}
				//ДЛЯ СТАТУСА -1
				//print_array($this->command['data']['email']);
				if(!$invite_email) return 'allow';
				if(!empty($this->command['data']['email']))
					$user_email = $this->command['data']['email'];
				else $user_email = '';
				if($user_email && ($user_email != $invite_email)){
					$this->messages[] = array('type' => 'error', 'text' => 'Невірний email!');
					$answer =  'forbid';
				}
				else $answer = 'allow';
				//print_array($this->data->messages);
				if($user_in_base) return $answer . '_authorize';
				//$this->user['email'] = $invite_email;
				$this->command['data']['email'] = $invite_email;
				return $answer . '_registration';
			case 'node':
				$net_id = $this->user['net'];
				$sql_fields = ", nodes_tmp.list_name";
				$sql_table = '';			
				if(!$data_value){
					$node_id = $this->net['active_node']['node'];
					$user_id = $this->user['id'];
					if($user_id){
						$sql_fields = ", IF(ISNULL(members_users.list_name), nodes_tmp.list_name, members_users.list_name) AS list_name,
										IF(ISNULL(members_users.note), nodes_tmp.note, members_users.note) AS note";
						$sql_table = "LEFT JOIN members_users ON nodes_users.user_id = member_id AND members_users.user_id = $user_id";
					}
				}
				else $node_id = $data_value;
				$sql = "SELECT nodes.node_id, node_level, node_address, parent_node_id, first_node_id, full_node_address, count_of_members, UNIX_TIMESTAMP(node_date) AS node_date, changes, nodes_users.user_id,
							IF(count_of_members > 0,
								IF(ISNULL(nodes_users.user_id), -1, 1),
								IF(ISNULL(invite) OR invite = '',
									0,
									IF(ISNULL(nodes_users.user_id), -5, 5))) AS node_status,
							email_show, name_show, mobile_show, email $sql_fields
						FROM nodes
							LEFT JOIN nodes_users ON nodes.node_id = nodes_users.node_id
							$sql_table
							LEFT JOIN nets_users_data ON nodes_users.user_id = nets_users_data.user_id AND nets_users_data.net_id = $net_id
							LEFT JOIN nodes_tmp ON nodes_users.node_id = nodes_tmp.node_id
						WHERE nodes.node_id = $node_id";
				//print_array($sql);						
				$sql = $this->DB->getOneArray($sql);
				return $sql;
			default:			
		}
	}

	public function chgNet($data_name = '', $data_value = '', $operation = '')
    {
		require 'objects/Data/chgNet.php';
		return $answer;
	}
	
	public function chgNets($data_name = '', $data_value = '')
    {
		switch($data_name)
		{	
			case 'net_delete':
				$net = $data_value;
				$net_id = $net['id'];
				$sql = "DELETE	nodes.*, nets.*, nets_data.*
				FROM 	nodes JOIN
						nets ON nodes.first_node_id = nets.net_id JOIN
						nets_data ON nets.net_id = nets_data.net_id
				WHERE	node_id = $net_id";
				$this->DB->getWork($sql);
				$this->chgNets('set_count', $net);
				//ВАРІАНТ 1
				$max_level_nets = $this->net['max_level_nets'];
				$net_level = $net['level'];
				$full_address = $net['full_address'];
				$address_correct = pow(10, $max_level_nets - $net_level);
				$address_max = (floor($full_address / $address_correct / 10) + 1) * $address_correct * 10;
				$first_net_id = $net['first_net'];
				$sql = "UPDATE nets SET net_address = IF(net_level = $net_level, net_address - 1, net_address),
										full_net_address = full_net_address - $address_correct
						WHERE first_net_id = $first_net_id AND full_net_address > $full_address AND full_net_address < $address_max";
				$this->DB->getWork($sql);
				break;
			case 'net_create':
				$parent_net = $this->user['net']; //$this->net['id']; //ПЕРЕВІРИТИ ВСІ РЕСЕТИ
				!$parent_net or $this->chgNets('net'); //А ДЛЯ ЧОГО? МАБУТЬ 
				$max_level = $this->net['max_level']; //7
				$max_level_nets = $this->net['max_level_nets']; //5
				if($parent_net){
					$level = $this->net['level'] + 1;
					if($level > $max_level_nets){
						$this->messages[] = array('type' => 'error', 'text' => 'Створення спільноти неможливо. Перевищено ліміт!');
						return false; //ДОРОБИТИ
					}
					$sql = "SELECT MAX(net_address) AS address FROM nets WHERE parent_net_id = $parent_net GROUP BY parent_net_id";
					$sql = $this->DB->getOneArray($sql);
					if(!$sql) $address = 1;
					else $address = $sql['address'] + 1;
					if($address > $max_level_nets){
						$this->messages[] = array('type' => 'error', 'text' => 'Створення спільноти неможливо. Перевищено ліміт!');
						return false; //ДОРОБИТИ
					}
					//$first_net = $this->net['first_net'];
				}
				else{
					$level = 1;
					$address = 1;
					//$parent_net = 'NULL';
					//$first_net = $net_id;
				}
				$full_address = pow(10, $max_level - 1);
				$sql = "INSERT INTO nodes (node_level, node_address, full_node_address, count_of_members, node_date)
									VALUES(1, 1, $full_address, 0, NOW())";
				$sql = $this->DB->getWork($sql);			
				$net_id = $this->DB->getDB('insert_id');
				$sql = "UPDATE nodes SET first_node_id = node_id WHERE node_id = $net_id";
				$sql = $this->DB->getWork($sql);
				
				//ПОДУМАТИ варіант $net = $this->net; а якщо !$parent_net, то $net[] = ...
				//а потім $level = ..., 
				//print_array($this->net);
				if($parent_net){
					//$level = $this->net['level'] + 1;
					//if($level > $max_level_nets) return false; //ДОРОБИТИ
					//$sql = "SELECT MAX(net_address) AS address FROM nets WHERE parent_net_id = $parent_net GROUP BY parent_net_id";
					//$sql = $this->DB->getOneArray($sql);
					//if(!$sql) $address = 1;
					//else $address = $sql['address'] + 1;
					////$address = $this->net['count'];
					$first_net = $this->net['first_net'];
					$full_address = $this->net['full_address'] + pow(10, $max_level_nets - $level) * $address;
					////$net = $this->net;
				}
				else{
					//$level = 1;
					//$address = 1;
					$parent_net = 'NULL';
					$first_net = $net_id;
					$full_address = pow(10, $max_level_nets - 1); 
				}
				
				$net['id'] = $net_id;
				$net['first_net'] = $first_net;
				$net['full_address'] = $full_address; 
				$net['level'] = $level;
					
				$sql = "INSERT INTO nets (net_id, net_level, net_address, parent_net_id, first_net_id, full_net_address, count_of_nets)
								VALUES($net_id, $level, $address, $parent_net, $first_net, $full_address, 0)";
				$sql = $this->DB->getWork($sql);
				//print_array($this->net);
				$this->chgNets('set_count', $net);
				//print_array($this->net);
				$this->chgNets('set_net_data', $net_id);
				//print_array($this->net);
				return $this->setNet('invite', $net_id);
			case 'set_count':
				$net = $data_value;
				$net_id = $net['id'];					
				(empty($this->command['operation'])) ? $operation = '' : $operation = $this->command['operation'];
				if($operation == 'net_create'){
					//ТУТ $data_value завжди +1
					$data_value = 1;
				}
				else{
					//ТУТ $data_value завжди -1
					$data_value = -1;					
				}
/*				
				if($data_value){
					//ТУТ $data_value завжди -1
					$net = $data_value;
					$data_value = -1;
					$net_id = $net['id'];
				}
				else{
					//ТУТ $data_value завжди +1
					$net = $this->net;
					$data_value = 1;
				}
*/
				//ВАРІАНТ 1
				//для використання даного варіанту необхідно поле full_net_address зробити індексованим
				$max_level_nets = $this->net['max_level_nets'];
				$first_net = $net['first_net'];
				$full_address = $net['full_address'];
				$level = $max_level_nets - $net['level'];
				$sql = '';				
				$or = 'full_net_address = ';
				//print_array($net['level']);
				while($level < $max_level_nets){
					$level = $level + 1;
					$sql = $sql . $or . $full_address;
					$full_address = floor($full_address / pow(10, $level)) * pow(10, $level);
					$or = ' OR full_net_address = ';
				}
				$sql = "UPDATE nets SET count_of_nets = count_of_nets + $data_value WHERE first_net_id = $first_net AND ($sql)";
				//print_array($sql);
				$sql = $this->DB->getWork($sql);
				break;
			case 'get_net_data':
				//print_array($this->net);
				$net_id = $this->net['id']; //$this->user['net'];
				$sql = "SELECT * FROM nets_data WHERE net_id = $net_id";
				$sql = $this->DB->getOneArray($sql);
				//print_array($sql);
				if($this->command['type'] == 'goal'){
					$this->net['goal'] = $sql['goal'];
					return true;
				}
				$this->net['name'] = $sql['name'];
				for($link_key = 1; $link_key <= 4; $link_key++){
					$this->net['links'][$link_key]['resource_name'] = $sql['resource_name_' . $link_key];
					$this->net['links'][$link_key]['resource_link'] = $sql['resource_link_' . $link_key];
				}
				return $sql;
			case 'set_net_data':
				//якщо при create в команду записувати links, то код нижче можна скоротити
				$data = $this->command['data'];
				$net_name = $this->DB->getDB('real_escape_string', $data['net_name']);
				$condition = 0;
				if($data_value){
					$net_id = $data_value;
					$sql = "INSERT INTO nets_data (net_id, name) VALUES ($net_id, '$net_name')";
					//print_array($sql);
				}
				else{
					$net_id = $this->net['id'];
					foreach($data['links'] as $link_key => $net_link){
						$net_links[$link_key]['resource_name'] = $this->DB->getDB('real_escape_string', $net_link['resource_name']);
						$net_links[$link_key]['resource_link'] = $this->DB->getDB('real_escape_string', $net_link['resource_link']);
					}
					$sql = "UPDATE nets_data SET
													name = '$net_name',
													resource_name_1 = '" . $net_links[1]['resource_name'] . "',
													resource_link_1 = '" . $net_links[1]['resource_link'] . "',
													resource_name_2 = '" . $net_links[2]['resource_name'] . "',
													resource_link_2 = '" . $net_links[2]['resource_link'] . "',
													resource_name_3 = '" . $net_links[3]['resource_name'] . "',
													resource_link_3 = '" . $net_links[3]['resource_link'] . "',
													resource_name_4 = '" . $net_links[4]['resource_name'] . "',
													resource_link_4 = '" . $net_links[4]['resource_link'] . "'
							WHERE net_id = $net_id";
					$condition = 1;
				}
				$sql = $this->DB->getWork($sql, $affected_rows);
				if($affected_rows == $condition){
					$this->messages[] = array('type' => 'success', 'text' => 'Дані успішно оновлено!');
					//$node['event_code'] = xx;
					//$this->setNet('notifications', $node);	
					return true;
				}
				return NULL;
			case 'set_net_goal':
				$data = $this->command['data'];
				//print_array($data);
				$net_goal = $this->DB->getDB('real_escape_string', $data['net_goal']);
				$net_id = $this->net['id'];
				$sql = "UPDATE nets_data SET goal = '$net_goal' WHERE net_id = $net_id";
				$sql = $this->DB->getWork($sql, $affected_rows);
				if($affected_rows == 1){
					$this->messages[] = array('type' => 'success', 'text' => 'Дані успішно оновлено!');	
					return true;
				}
				return NULL;
			case 'set_net_count':
				$net_id = $this->net['id'];
				$sql = "SELECT count_of_members FROM nodes WHERE node_id = $net_id";
				$sql = $this->DB->getOneArray($sql);
				$this->net['count_of_members'] = $sql['count_of_members'];
				return true;
			case 'net':
				if($data_value) $net_id = $data_value;
				else $net_id = $this->user['net'];
				//$net = array();
				//$this->net = array();
				$sql = "SELECT nets.net_id AS id, name, net_level AS level, count_of_nets AS count, first_net_id AS first_net,
								full_net_address AS full_address, IF(ISNULL(parent_net_id), 0, parent_net_id) AS parent_net
						FROM nets JOIN nets_data ON nets.net_id = nets_data.net_id
						WHERE nets.net_id = $net_id";
				$sql = $this->DB->getOneArray($sql);
				//ОПТИМІЗУВАТИ
				if($data_value) return $sql;
				if(!$sql)return array();
				$this->net['id'] = $sql['id']; //ЧИ ПОТРІБНО ДЛЯ КОМАНДИ IN?
				$this->net['name'] = $sql['name'];
				$this->net['parent_net'] = $sql['parent_net']; //ЧИ ПОТРІБНО ДЛЯ КОМАНДИ IN?
				$this->net['level'] = $sql['level']; //ЧИ ПОТРІБНО ДЛЯ КОМАНДИ IN?
				$this->net['count'] = $sql['count'];
				$this->net['first_net'] = $sql['first_net']; //ЧИ ПОТРІБНО ДЛЯ КОМАНДИ IN?
				$this->net['full_address'] = $sql['full_address']; //ЧИ ПОТРІБНО ДЛЯ КОМАНДИ IN?
				//print_array($this->net);
				return true;
			default:
				//...
		}
	}
/*-------------- NET --------------------------------------------*/
/*-------------- LIMITS -----------------------------------------*/
	public function getLimits($data_name = '', $data_value = '')
	{
		switch($data_name)
		{	
			case 'limit_on_vote':
				$net_id = $this->user['net'];
				$limit_date = time() - 3*(60*60*24); //більше ніж 3 дні тому
				if(!$this->user['net']) return false;
				if($data_value){
					$node = $data_value;
					if(is_array($node)){	//попередня перевірка
						$user_id = $node['user'];
						$node_date = $node['date'];
						if(($node_date < $limit_date) && !$user_id) return true;
						else return false;
					}
					else{					//контрольна перевірка
						$sql = "SELECT nodes.node_id
								FROM nodes WHERE node_id = $node AND count_of_members > 0 AND UNIX_TIMESTAMP(node_date) < $limit_date AND ISNULL(user_id)";
						$sql = $this->DB->getArray($sql);
						if($sql) return true;
						else return false;
					}
				}
				$sql = "SELECT nodes.node_id
						FROM nodes LEFT JOIN nodes_users ON nodes.node_id = nodes_users.node_id
						WHERE first_node_id = $net_id AND count_of_members > 0 AND UNIX_TIMESTAMP(node_date) < $limit_date AND ISNULL(user_id)";
				$sql = $this->DB->getArray($sql);
				return $sql;
				break;		
			default:
				//...
		}
		return NULL;
	}
/*-------------- LIMITS -----------------------------------------*/
	public function getData($data_name)
    {
		switch($data_name)
		{
			case 'data_name':
				//...
				break;
			default:			
		}
	}
}