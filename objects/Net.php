<?php
class Circle
{
    private $site;
	private $data;
	private $user;
	private $circle = array();
	private $circle_tmp = array();
	private $tree = array();
	private $parent;
	private $command = array();

    public function __construct(&$site, &$parent)
    {
		$this->site = $site;
		$this->data = &$site->data;
		$this->user = &$site->user;
		$this->circle = &$site->data->circle;
		$this->parent = $parent;
		$this->command = &$site->data->command;
		$this->circle_tmp = &$site->data->circle_tmp;
	}
	
	public function setCircle($data_name = '')
	{
		if(!$data_name){
			$this->data->setCircle('first');
			//print_array($this->circle);
			if(!$this->circle) return NULL;
			$this->setCircle('voices');
			//print_array($this->circle, 1);
			//print_array($this->user->getUser());
			return true;
		}
		
		if($data_name == 'voices'){
			//$voices = $this->data->getCircle('voices');
			$this->data->getCircle('voices');
			foreach($this->circle_tmp['voices'] as $member_voices)
				$this->circle[$member_voices['node_key']]['voices'] = $member_voices['voices'];
			return true; //$voices;
		}
		
		if($data_name == 'iam'){
			$iam = $this->user->getUser();
			$this->circle[1] = array('node' => $iam['node'],
										'user' => $iam['id'],
										'user_name' => $iam['name'],
										'node_status' => $iam['status'],
										'count' => $iam['count'],
										'dislike' => 0, //перевірити
										'voices' => 0,
										'invite' => $iam['invite']);
			return true;
		}
	}
	
	public function getCircle($data_name = '', $data_value = '')
	{
		if(!$data_name) return $this->circle;
		
		if($data_name == 'nodes_status'){
			$status[0] = array('text' => 'координатор', 'css' => '');
			$status[1] = array('text' => '', 'css' => '');
			$status[2] = $status[1];
			$status[3] = $status[1];
			$status[4] = $status[1];
			$status[5] = $status[1];
			$status[6] = $status[1];
			$status[7] = $status[1];
			$notifications = $this->user->getIam('notifications', array('circle_tree' => 'circle', 'node_key' => 'all'));
			//print_array($this->circle);
			foreach($this->circle as $key => $member){
				//print_array($member);

				if(!$member['node_status'])
					$status[$key] = array('text' => '', 'css' => 'empty'); //array('text' => '', 'css' => 'empty blocked');
				elseif($member['node_status'] == 5)
					$status[1] = array('text' => '', 'css' => '');
				elseif($member['node_status'] == -1)
					$status[$key] = array('text' => 'обрання', 'css' => 'empty');
				elseif($this->user->getUser('status') == 5)
					$status[$key] = array('text' => 'ідентифікація', 'css' => 'empty');
				else{			
					(!$member['voices']) or $status[$key]['text'] = 'голосів: ' . $member['voices'];
				}	
					
				if(isset($notifications[$key]))
					if($notifications[$key]['not_shown']) $status[$key]['marker'] = 'new';
					else $status[$key]['marker'] = 'old';
				else{
					$status[$key]['marker'] = 'empty';
					//if($status[$key]['css'] == 'empty' && !$member['node_status']) $status[$key]['css'] = 'empty blocked';
					if(!$member['node_status']) $status[$key]['css'] = 'empty blocked';
				}
/*				
				$name = $member['user_name'];
				if($key == 1) {$name = 'Я';}
				elseif(!$name){
					if($key == 0 ) {$name = 'Координатор';}				
					elseif($member['user']){
						$key_in_circle = $key - 1;
						$name = 'Учасник ' . $key_in_circle;
					}
				}
				$status[$key]['name'] = htmlentities($name, ENT_QUOTES | ENT_HTML5);
*/
				if($member['dislike']) $status[$key]['dislike'] = 'dislike';
				else $status[$key]['dislike'] = '';
				
				
			}
			

			//print_array($status);
			$notifications = $this->user->getIam('notifications', array('circle_tree' => 'tree', 'node_key' => ''));
			if($notifications === false)
				$status[7]['marker'] = 'empty';
			else
				if($notifications > 0) $status[7]['marker'] = 'new';
				else $status[7]['marker'] = 'old';		
			
			return $status;
		}
/*		
		if($data_name == 'sub_menu'){
			// 0 - МОЇ ДАНІ/ДАНІ
			// 2 - ІДЕНТИФІКАЦІЯ
			// 3 - СТАТИСТИКА
			// 4 - ПОВІДОМЛЕННЯ
			
			//вважаємо, що меню для node_status == 0 не запитується ???
			//все-таки може запитуватись
			$key = $data_value;
			$member = $this->circle[$key];
			$node_status = $member['node_status'];

			if($node_status > 0)
				($key == 1) ? $sub_menu[0] = array('text' => 'МОЇ ДАНІ') : $sub_menu[] = array('text' => 'ДАНІ');				
			
			if($node_status == 5) $sub_menu[2] = array('text' => 'ІДЕНТИФІКАЦІЯ');
			else $sub_menu[3] = array('text' => 'СТАТИСТИКА');
			
			if($key <> 1) $sub_menu[4] = array('text' => 'ПОВІДОМЛЕННЯ');
			
			return $sub_menu;
		}
*/		
		if($data_name == 'node'){
			if(array_key_exists($data_value, $this->circle))
				return $this->circle[$data_value];
			return false;
		}
		elseif($data_name == 'voices'){
			return $this->circle_tmp['voices'];
		}
		elseif($data_name == 'changes'){
			//print_array($this->circle);
			//при створенні спільноти та approve в колі є Iam
			if($this->circle && (count($this->circle) > 1)) return $this->data->getCircle('changes');
			return false;
		}
		else
			return $this->circle[$data_name];
	}
}

class Tree
{
    private $site;
	private $data;
	private $user;
	private $tree = array();
	private $parent;
	private $command;

    public function __construct(&$site, &$parent)
    {
		$this->site = $site;
		$this->data = &$site->data;
		$this->user = &$site->user;
		$this->tree = &$site->data->tree;
		$this->parent = $parent;
		$this->command = &$site->data->command;
	}
	
	public function setTree($data_name = '', $data_value = '')
	{
		return $this->data->setTree('first', $data_value);
	}

	public function getTree($data_name = '', $data_value = '')
	{
		if(!$data_name) return $this->tree;

		if($data_name == 'nodes_status'){
			$status[0] = array('text' => '', 'css' => '');
			$status[1] = array('text' => '', 'css' => '');
			$status[2] = $status[1];
			$status[3] = $status[1];
			$status[4] = $status[1];
			$status[5] = $status[1];
			$status[6] = $status[1];		
			$status[7] = $status[1];
			$notifications = $this->user->getIam('notifications', array('circle_tree' => 'tree', 'node_key' => 'all'));
			//echo '<pre>'; print_r($this->tree); exit; echo '</pre>';
			foreach($this->tree as $key => $member){
				if(!$member['node_status'])
					$status[$key] = array('text' => 'запросити', 'css' => 'empty');
				elseif($member['node_status'] == 5)
					$status[$key] = array('text' => 'ідентифікація', 'css' => 'empty');
				elseif($member['node_status'] == -1)
					$status[$key] = array('text' => 'обрання', 'css' => 'empty');
				elseif($member['node_status'] == -5)
					$status[$key] = array('text' => 'запрошений', 'css' => 'empty');

				if(isset($notifications[$key]))
					if($notifications[$key]['not_shown']) $status[$key]['marker'] = 'new';
					else $status[$key]['marker'] = 'old';
				else $status[$key]['marker'] = 'empty';		
			
				if($member['dislike']) $status[$key]['dislike'] = 'dislike';
				else $status[$key]['dislike'] = '';
			}
			
			$notifications = $this->user->getIam('notifications', array('circle_tree' => 'circle', 'node_key' => ''));
			
			if($notifications === false)
				$status[0]['marker'] = 'empty';
			else
				if($notifications > 0) $status[0]['marker'] = 'new';
				else $status[0]['marker'] = 'old';				
			
			//echo '<pre>'; print_r($status); exit; echo '</pre>';
			return $status;
		}
/*		
		if($data_name == 'sub_menu'){
			// 0 - МОЇ ДАНІ/ДАНІ
			// 1 - ЗАПРОСИТИ
			// 2 - ІДЕНТИФІКАЦІЯ
			// 3 - СТАТИСТИКА
			// 4 - ПОВІДОМЛЕННЯ
			
			$key = $data_value;
			$member = $this->tree[$key];
			$node_status = $member['node_status'];

			if($node_status > 0)
				($key == 1) ? $sub_menu[0] = array('text' => 'МОЇ ДАНІ') : $sub_menu[] = array('text' => 'ДАНІ');				
			
			if(!$node_status || $node_status == -5) $sub_menu[1] = array('text' => 'ЗАПРОШЕННЯ');
			elseif($node_status == 5) $sub_menu[2] = array('text' => 'ІДЕНТИФІКАЦІЯ');
			else $sub_menu[3] = array('text' => 'СТАТИСТИКА');
			
			if($key <> 1) $sub_menu[4] = array('text' => 'ПОВІДОМЛЕННЯ');
			
			return $sub_menu;
		}
*/		
		
		if($data_name == 'node'){
			if(array_key_exists($data_value, $this->tree))
				return $this->tree[$data_value];
			return false;
		}
	}
}