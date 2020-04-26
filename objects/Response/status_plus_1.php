<?php
//початкові дані
$circle_tree = $net['circle_tree'];
$active_node = $net['active_node'];
$notifications = $user->getIam('notifications', array('circle_tree' => 'net', 'node_key' => 'all'));
/*-- MAIN MENU --------*/
			$main_menu[1]['text'] = 'ГОЛОВНА';
			$main_menu[1]['link'] = $server;
			$main_menu[2]['text'] = $net['name'];
			$main_menu[2]['link'] = $server . 'in/';					
			$main_menu[3]['text'] = 'ВИЙТИ';
			$main_menu[3]['link'] = $server . 'exit/';
/*-- MENU -------------*/
	//menu left
			// в залежності від статусу +5 чи +1 не всі пункти меню наявні
			$menu['left'][6]['text'] = 'МЕТА';
			$menu['left'][6]['link'] = $server . 'goal/';
			$menu['left'][6]['active'] = false;			
			$menu['left'][1]['text'] = 'СТАТИСТИКА';
			$menu['left'][1]['link'] = $server . 'statistic/net/';
			$menu['left'][1]['active'] = false;
			$menu['left'][2]['text'] = 'ВІД\'ЄДНАТИСЬ';
			$menu['left'][2]['link'] = $server . 'disconnect/';
			$menu['left'][2]['active'] = false;
			$menu['left'][3]['text'] = 'ДАНІ';
			$menu['left'][3]['link'] = $server . 'data/net/';
			$menu['left'][3]['active'] = false;
			if($notifications){
				$menu['left'][4]['text'] = 'ПОВІДОМЛЕННЯ';
				$menu['left'][4]['link'] = $server . 'notification/';
				$menu['left'][4]['active'] = false;			
			}
			$menu['left'][5]['text'] = 'СТВОРИТИ';
			$menu['left'][5]['link'] = $server . 'create/';
			$menu['left'][5]['active'] = false;	
	//menu right
			// menu right
			//$this->response['menu']['right'] = array();			
			if(!$user_parent_nets = $user->getUser('parent_nets')){
				//print_array($user_nets);
				//if(count($user_parent_nets) == 1){
				//if(!$user_parent_nets){
				//if(count($user_parent_nets) > 0){
					$menu['right'][2]['text'] = 'ВИЙТИ В';
					$menu['right'][2]['link'] = $server . 'out/';
					$menu['right'][2]['active'] = false;
					$menu['right'][2]['sub_menu'] = array();
				}
				else{
					$sub_menu = &$menu['right'][2]['sub_menu'];
					foreach($user_parent_nets as $net_id => $net_name){
						$sub_menu[$net_id]['text'] = $net_name;
						$sub_menu[$net_id]['link'] = $server . 'out/' . $net_id . '/';
						$sub_menu[$net_id]['active'] = false;
					}
					$sub_menu[0]['text'] = 'АКАУНТ';
					$sub_menu[0]['link'] = $server . 'out/';
					$sub_menu[0]['active'] = false;				
					$menu['right'][2]['text'] = 'ВИЙТИ В';
					$menu['right'][2]['link'] = '#';
					$menu['right'][2]['active'] = false;
				}		
			//}

			if($user_nets = $user->getUser('nets')){
				//print_array($user_nets);
				//if(count($user_nets) == 1){
				if(count($user_nets) > 0){
				//	$menu['right'][1]['text'] = 'УВІЙТИ В';
				//	$menu['right'][1]['link'] = $server . 'in/';
				//	$menu['right'][1]['active'] = false;
				//	$menu['right'][1]['sub_menu'] = array();
				//}
				//else{
					$sub_menu = &$menu['right'][1]['sub_menu'];
					foreach($user_nets as $net_id => $net_name){
						$sub_menu[$net_id]['text'] = $net_name;
						$sub_menu[$net_id]['link'] = $server . 'in/' . $net_id . '/';
						$sub_menu[$net_id]['active'] = false;
					}
					$menu['right'][1]['text'] = 'УВІЙТИ В';
					$menu['right'][1]['link'] = '#';
					$menu['right'][1]['active'] = false;
				}
			}
/*-- CONTACTS ---------*/
	//contacts menu
			if($circle_tree == 'circle'){
				$contacts_menu['text'] = 'МОЄ КОЛО';
				$contacts_menu['link'] = $server . 'circle/';
			}
			else{
				$contacts_menu['text'] = 'МОЄ ДЕРЕВО';
				$contacts_menu['link'] = $server . 'tree/';
			}
	//contacts data
			//$iam['text'] = 'Я' . ' (' . $user->getUser('name') . ')';
			$contacts_data['circle'] = array();		
			$contacts_data['tree'] = array();
			$resp_circle = &$contacts_data['circle'];
			$resp_tree = &$contacts_data['tree'];
			$circle_members = $circle->getCircle();
			$tree_members = $tree->getTree();
			$member_key = $active_node['key']; // $command['data']['member']; можливо не потрібно
		//circle		
			if($circle_tree == 'circle'){
				$nodes_status = $circle->getCircle('nodes_status');			
				foreach($circle_members as $key => &$member){
					//echo $active_node['node_status']; exit;
					if($key > 1 && $user->getUser('status') == 5){
						//$item['active'] = 'disabled';
						$resp_circle[] = ''; //$item;
						continue;
					}
					//if($key == 1) $item['text'] = 'Я';
					//else $item['text'] = $member['user_name'];
					$item['text'] = htmlentities($member['user_name'], ENT_QUOTES); //| ENT_HTML5
					if($nodes_status[$key]['css'] == 'empty blocked') $item['action'] = '#';
					else $item['action'] = $server . $circle_tree . '/' . $key . '/';
					$item['active'] = false;
					$item['status'] = $nodes_status[$key];
					//$item['marker'] = $nodes_status[$key]['marker'];
					$resp_circle[] = $item;
				}
				if($tree_members){
					$resp_circle[7]['action'] = $server . 'tree/';
					$resp_circle[7]['status'] = $nodes_status[7];
				}
				else $resp_circle[7] = '';
				//$resp_circle[$member_key]['active'] = true;
				//$member = $circle_members[$member_key];
			}
		//tree			
			if($net['circle_tree'] == 'tree'){
				$nodes_status = $tree->getTree('nodes_status');
				if($circle_members){
					$resp_tree[0]['action'] = $server . 'circle/';
					$resp_tree[0]['status'] = $nodes_status[0];
				}
				else $resp_tree[0] = '';
				foreach($tree_members as $key => &$member){
					if($key == 1) $item['text'] = 'Я';
					else $item['text'] = $member['user_name'];					
					$item['text'] = htmlentities($item['text'], ENT_QUOTES); //| ENT_HTML5
					
					$item['action'] = $server . $circle_tree . '/' . $key . '/';
					$item['active'] = false;
					$item['status'] = $nodes_status[$key];
					//$item['marker'] = $nodes_status[$key]['marker'];					
					$resp_tree[] = $item;
				}
				//$resp_tree[$member_key]['active'] = true;
				//$member = $tree_members[$member_key];
				//echo '<pre>'; print_r($resp_tree); echo '</pre>'; exit;
			}		
/*-- CONTENT ----------*/
			if($active_node && $command['data']['circle_tree'] != 'net'){
			$contacts_data[$circle_tree][$member_key]['active'] = true;
	//content menu
		//content menu left		
					$content_menu['left'][1]['text'] = htmlentities($active_node['content_menu_left_text'], ENT_QUOTES); //| ENT_HTML5
					$content_menu['left'][1]['link'] = $server . $circle_tree . '/' . $active_node['key'] . '/';
					$content_menu['left'][1]['active'] = true;				
		//content menu right
					$sub_menu = &$active_node['sub_menu'];
					if(count($active_node['sub_menu']) > 1){
						//$sub_menu = &$active_node['sub_menu'];
						switch($command['type']){
							case 'data':
								$sub_menu[0]['active'] = true;
								break;
							case 'invitation':
								if($command['operation'] == 'connected')
									$sub_menu[2]['active'] = true;
								else
									$sub_menu[1]['active'] = true;
								break;								
							case 'statistic':
								$sub_menu[3]['active'] = true;
								break;
							case 'notification':
								//if($member_key != 1)
									$sub_menu[4]['active'] = true;
								break;
							case 'vote':
								$sub_menu[5]['active'] = true; //перевірити
								break;						
							default:
						}						
						$content_menu_right[1]['text'] = 'МЕНЮ';
						$content_menu_right[1]['link'] = '#';
						$content_menu_right[1]['active'] = false;
			//content sub_menu
						$content_menu_right[1]['sub_menu'] = $sub_menu;
					}
			}
			switch($command['type']){			
				case 'data':
					if ($command['data']['circle_tree'] == 'net'){
						$link = $server . 'data/net/';
						$menu['left'][3]['active'] = true;
	//content data
		//content menu left		
					$content_menu['left'][1]['text'] = 'ДАНІ СПІЛЬНОТИ';
					$content_menu['left'][1]['link'] = $link;
					$content_menu['left'][1]['active'] = true;				
		//content menu right
					//...
		//content data sub_menu

		//content data forms				
						$content_data['template'] = 'data_net';
						$content_data['operation'] = 'read';
						$form_data = &$content_data['data'];
						if($active_node['level'] == 1) $form_data['action'] = $link;
						else $form_data['action'] = '';
						//ТУТ може бути глюк, при write невірних полів, якщо пропустити get_net_data в StatusPlus1 (аналогічно в goal)
						$form_data['name'] = htmlentities($net['name'], ENT_QUOTES); //| ENT_HTML5
						foreach($net['links'] as $link_key => $net_link){
							$form_data['links'][$link_key]['resource_name'] = htmlentities($net_link['resource_name'], ENT_QUOTES); //| ENT_HTML5
							$form_data['links'][$link_key]['resource_link'] = htmlentities($net_link['resource_link'], ENT_QUOTES); //| ENT_HTML5
						}
						
						//ПЕРЕВІРИТИ
						$form_data['user_id'] = $user->getUser('id'); //перевірити, можливо винести в загальну зону
						$form_data['net_id'] = $net['id'];
						
						if($command['operation'] == 'edit'){
							$content_data['sub_menu']['text'] = 'ДАНІ (РЕДАГУВАННЯ)';
							$content_data['sub_menu']['link'] = '#';
							$content_data['operation'] = 'edit';
							$content_data['cancel']['action'] = $link;
						}
					
						if($command['operation'] == 'write'){ //ДОРОБИТИ А ТАКОЖ В ІНШИХ КОМАНДАХ
							$content_data['sub_menu']['text'] = 'ДАНІ (РЕДАГУВАННЯ)';
							$content_data['sub_menu']['link'] = '#';
							$content_data['operation'] = 'edit';
							$form_data['name'] = htmlentities($command['data']['net_name'], ENT_QUOTES); //| ENT_HTML5
							for($link_key = 1; $link_key <=4; $link_key++){
								//$form_data['links'][$link_key]['resource_name'] = htmlentities($command['data']['link_name_' . $link_key], ENT_QUOTES | ENT_HTML5);
								//$form_data['links'][$link_key]['resource_link'] = htmlentities($command['data']['link_value_' . $link_key], ENT_QUOTES | ENT_HTML5);			
								$form_data['links'][$link_key]['resource_name'] = htmlentities($command['data']['links'][$link_key]['resource_name'], ENT_QUOTES); //| ENT_HTML5
								$form_data['links'][$link_key]['resource_link'] = htmlentities($command['data']['links'][$link_key]['resource_link'], ENT_QUOTES); //| ENT_HTML5			
							}
							$content_data['cancel']['action'] = $link;
						}		
						break;
					}
					//$content_menu_right[1]['sub_menu'][0]['active'] = true;
	//content data
					$link = $server . 'data/' . $circle_tree . '/' . $member_key . '/';
		//content data sub_menu
					$content_data['sub_menu']['text'] = $active_node['sub_menu'][0]['text'];
					$content_data['sub_menu']['link'] = $link;
		//content data forms
					if($member_key == 1)
						$content_data['template'] = 'data_iam';
					else
						$content_data['template'] = 'data_circle_tree';
					$content_data['operation'] = 'read';
					$form_data = &$content_data['data'];
					//$form_data['action'] = '';
					//if($member_key != 1){
						$form_data['action'] = $link;
						$form_data['list_name'] = htmlentities($active_node['list_name'], ENT_QUOTES); //| ENT_HTML5
					//}
					$name = $active_node['user_name'];
					$email = $active_node['email'];
					$mobile = $active_node['mobile'];
					if(is_null($name)) $name = 'приховано';
					if(is_null($email)) $email = 'приховано';
					if(is_null($mobile)) $mobile = 'приховано';
					$form_data['name'] = htmlentities($name, ENT_QUOTES); //| ENT_HTML5
					$form_data['email'] = htmlentities($email, ENT_QUOTES); //| ENT_HTML5
					$form_data['mobile'] = htmlentities($mobile, ENT_QUOTES); //| ENT_HTML5
					$form_data['list_name'] = htmlentities($active_node['list_name'], ENT_QUOTES); //| ENT_HTML5
					$form_data['note'] = htmlentities($active_node['note'], ENT_QUOTES); //| ENT_HTML5
					
					$form_data['user_id'] = $user->getUser('id'); //перевірити, можливо винести в загальну зону
					$form_data['member_id'] = $active_node['user'];
					$form_data['member_node'] = $active_node['node'];
					//node_id
					//member_id
					if($member_key == 1){
						($active_node['name_show']) ? $form_data['name_show'] = 'checked' : $form_data['name_show'] = '';
						($active_node['email_show']) ? $form_data['email_show'] = 'checked' : $form_data['email_show'] = '';
						($active_node['mobile_show']) ? $form_data['mobile_show'] = 'checked' : $form_data['mobile_show'] = '';
						if($circle_tree == 'tree')
							$form_data['action'] = '';
					}		
					
					if($command['operation'] == 'edit'){
						$content_data['sub_menu']['text'] = $sub_menu[0]['text'] . ' (РЕДАГУВАННЯ)';
						$content_data['operation'] = 'edit';
						$content_data['cancel']['action'] = $link;
					}
					
					if($command['operation'] == 'write'){ //ДОРОБИТИ А ТАКОЖ В ІНШИХ КОМАНДАХ
						$content_data['sub_menu']['text'] = $sub_menu[0]['text'] . ' (РЕДАГУВАННЯ)';
						$content_data['operation'] = 'edit';
						$form_data['list_name'] = htmlentities($command['data']['list_name'], ENT_QUOTES); //| ENT_HTML5
						$form_data['note'] = htmlentities($command['data']['note'], ENT_QUOTES); //| ENT_HTML5		
						$content_data['cancel']['action'] = $link;
					}		
					break;
				case 'goal':
					$link = $server . 'goal/';
					$menu['left'][6]['active'] = true;
	//content menu
		//content menu left		
					$content_menu['left'][1]['text'] = 'МЕТА СПІЛЬНОТИ';
					$content_menu['left'][1]['link'] = $link;
					$content_menu['left'][1]['active'] = true;				
		//content menu right
					//...
	//content data
		//content data sub_menu
					//...
		//content data forms
					$content_data['template'] = 'goal';
					$content_data['operation'] = 'read';
					$form_data = &$content_data['goal'];
					if($active_node['level'] == 1) $form_data['action'] = $link;
					else $form_data['action'] = '';
					//$form_data['net_goal'] = htmlentities($net['goal'], ENT_QUOTES | ENT_HTML5, 'utf-8');
					//$form_data['net_goal'] = htmlentities($net['goal']);
					$form_data['net_goal'] = htmlspecialchars($net['goal'], ENT_QUOTES); //| ENT_HTML5
					$form_data['user_id'] = $active_node['user'];
					//net_id	
					
					if($command['operation'] == 'edit'){
						$content_data['sub_menu']['text'] = 'МЕТА СПІЛЬНОТИ (РЕДАГУВАННЯ)';
						$content_data['sub_menu']['link'] = '#';
						$content_data['operation'] = 'edit';
						$content_data['cancel']['action'] = $link;
					}
					
					if($command['operation'] == 'write'){ //ДОРОБИТИ А ТАКОЖ В ІНШИХ КОМАНДАХ
						$content_data['sub_menu']['text'] = 'МЕТА СПІЛЬНОТИ (РЕДАГУВАННЯ)';
						$content_data['sub_menu']['link'] = '#';
						$content_data['operation'] = 'edit';
						//$form_data['net_goal'] = htmlentities($command['data']['net_goal'], ENT_QUOTES | ENT_HTML5);
						$form_data['net_goal'] = htmlspecialchars($command['data']['net_goal'], ENT_QUOTES); //| ENT_HTML5
						$content_data['cancel']['action'] = $link;
					}
					//print_array($content_data);
					break;
				case 'vote':
	//content data
					$link = $server . 'vote/' . 'circle/';
		//content data sub_menu
					$content_data['sub_menu']['text'] = $active_node['sub_menu'][5]['text'];
					$content_data['sub_menu']['link'] = $link . '0/';
		//content data forms
					$content_data['template'] = 'vote';
					$form_data = &$content_data['vote'];
					$voices = $circle->getCircle('voices'); //$circle->setCircle('voices');
					//foreach($vote as $member)
					//$vote[] = array('key' => 1, 'vote' => '', 'vote_count' => 5, 'name' => 'Чиєсь імя'); //$net['vote'];
					//$vote[] = array('key' => 2, 'vote' => '', 'vote_count' => 2, 'name' => 'Інше імя'); //$net['vote'];
					//$vote[] = array('key' => 3, 'vote' => '', 'vote_count' => 3, 'name' => 'Ще імя'); //$net['vote'];
					//$vote[] = array('key' => 4, 'vote' => '', 'vote_count' => 4, 'name' => 'Також імя'); //$net['vote'];					
					//$vote[] = array('key' => 5, 'vote' => 'checked', 'vote_count' => 5, 'name' => 'Моє імя'); //$net['vote'];
					//$vote[] = array('key' => 6, 'vote' => '', 'vote_count' => 0, 'name' => 'Просто імя'); //$net['vote'];					
					//print_array($vote);
					//print_array($user->getUser());
					$user_voice = $user->getUser('voice_for_key');
					foreach($voices as $member_voices){
						$key = $member_voices['node_key'];
						$form_data['members'][$key]['action'] = $link . $key . '/';
						if($user_voice == $key)
							$form_data['members'][$key]['voice'] = 'checked';
						else
							$form_data['members'][$key]['voice'] = '';
						if($member_voices['voices'])
							$form_data['members'][$key]['voices'] = 'голосів [ ' . $member_voices['voices'] . ' ]';
						else
							$form_data['members'][$key]['voices'] = '';
						//if($key == 1)
						$form_data['members'][$key]['name'] = htmlentities($circle_members[$key]['user_name'], ENT_QUOTES); //| ENT_HTML5
					}
					break;
				case 'disconnect':
					$link = $server . 'disconnect/';
					$menu['left'][2]['active'] = true;
	//content menu
		//content menu left		
					$content_menu['left'][1]['text'] = 'ВІД\'ЄДНАННЯ ВІД СПІЛЬНОТИ';
					$content_menu['left'][1]['link'] = $link;
					$content_menu['left'][1]['active'] = true;				
		//content menu right
					//...
	//content data
		//content data sub_menu
					//$content_data['sub_menu']['text'] = 'ВІД\'ЄДНАННЯ ВІД СПІЛЬНОТИ'; //це здається лишнє
					//$content_data['sub_menu']['link'] = $link;
		//content data forms					
					$content_data['template'] = 'disconnect';
					$content_data['operation'] = 'ready';
					$form_data = &$content_data['disconnect'];
					$form_data['action'] = $link;
					$form_data['text'] = 'Якщо Ви готові від\'єднатись від спільноти - натисніть [ від\'єднатись ] !';			
					$form_data['user_id'] = $user->getUser('id');
					$form_data['net_id'] = $user->getUser('net');				
					$content_data['cancel']['action'] = $server . 'data/';
					break;
				case 'statistic':
					if ($command['data']['circle_tree'] == 'net'){
						$link = $server . 'data/';
						$menu['left'][1]['active'] = true;
	//content data
		//content menu left		
					$content_menu['left'][1]['text'] = 'СТАТИСТИКА СПІЛЬНОТИ';
					$content_menu['left'][1]['link'] = $link . 'net/';
					$content_menu['left'][1]['active'] = true;				
		//content menu right
					//...
		//content data sub_menu

		//content data forms
						$count = $net['count_of_members'];
						$content_data['template'] = 'statistic';
						$form_data = &$content_data['statistic'];
						$form_data['text'] = 'В спільнті ' . $count . ' учасників!';
						$form_data['action'] = $link;
						$content_data['dislike'] = '';
						break;
					}

					//$content_menu_right[1]['sub_menu'][3]['active'] = true;
	//content data
					$count = $active_node['count'];
					$link = $server . 'data/';
					
					if($active_node['node_status'] == 1){
						$link = $link . $circle_tree . '/' . $member_key . '/';
						$count = $count - 1;
					}

					($active_node['key'] == 1) ?	$text = 'В моєму ДЕРЕВІ: ' :
													$text = 'В ДЕРЕВІ ' . $active_node['user_name'] . ': ';
		//content data sub_menu
					$content_data['sub_menu']['text'] = $active_node['sub_menu'][3]['text'];
					$content_data['sub_menu']['link'] = $link;
		//content data forms				
					$content_data['template'] = 'statistic';
					$form_data = &$content_data['statistic'];
					$form_data['action'] = $link;
					$form_data['text'] = $text . $count . ' учасників!';
					$form_dislike = &$content_data['dislike'];
					if($active_node['dislike']){
						$form_dislike['dislike'] = 'off';
						$form_dislike['button_text'] = 'Вже подобається';
					}
					else{
						$form_dislike['dislike'] = 'on';
						$form_dislike['button_text'] = 'Не подобається';
					}
					if($member_key <> 1 && $active_node['node_status'] == 1){
						$form_dislike['action'] = $server . 'statistic/' . $circle_tree . '/' . $member_key . '/';
					}
					else{
						$form_dislike = '';
					}
					break;
				case 'notification':
					
					if($member_key == 1){
						$link = $server . 'notification/';
						$menu['left'][4]['active'] = true;
	//content menu
		//content menu left		
						$content_menu['left'][1]['text'] = 'ПОВІДОМЛЕННЯ';
						$content_menu['left'][1]['link'] = $link;
						$content_menu['left'][1]['active'] = true;				
		//content menu right
					//...
					}
					else{
	//content data
						$link = $server . 'notification/' . $circle_tree . '/' . $member_key . '/';
		//content data sub_menu
						$content_data['sub_menu']['text'] = $active_node['sub_menu'][4]['text'];
						$content_data['sub_menu']['link'] = $link;
					}
		//content data forms
					$content_data['template'] = 'notification';
					$form_data = &$content_data['notifications'];
					$notifications = $user->getIam('notifications', array('circle_tree' => 'response'));
					//print_array($notifications);
					foreach($notifications as $item){
						$notification['action'] = $link;
						$notification['text'] = $item['notification_text'];
						$notification['id'] = $item['event_id'];
						$notification['close'] = $item['notification_close'];
						$form_data[] = $notification;
					}
					//print_array($form_data);
					break;
				case 'create':
					$link = $server . 'create/';
					$menu['left'][5]['active'] = true;
	//content menu
		//content menu left		
					$content_menu['left'][1]['text'] = 'СТВОРЕННЯ СПІЛЬНОТИ';
					$content_menu['left'][1]['link'] = $link;
					$content_menu['left'][1]['active'] = true;				
		//content menu right
					//...
	//content data
		//content data sub_menu

		//content data forms
					$form_data = &$content_data['create'];
					$content_data['template'] = 'create';
					$content_data['operation'] = 'edit';
					$form_data['action'] = $link;
					$form_data['text'] = 'Для створення спільноти вкажіть її назву!';
					//$form_data['net_name'] = htmlentities($user->getUser('net_name'), ENT_QUOTES | ENT_HTML5);
					$form_data['user_id'] = $user->getUser('id');
					$form_data['net_id'] = $user->getUser('net');
					$content_data['cancel']['action'] = $server . 'data/';	

					//if($command['operation'] == 'net_create'){
						//$content_data['operation'] = 'edit';
						$form_data['net_name'] = htmlentities($command['data']['net_name'], ENT_QUOTES); //| ENT_HTML5				
					//}
					
					break;
				case 'invitation':
					//$content_menu_right[1]['sub_menu'][1]['active'] = true;
	//content data
					$link = $server . 'invitation/' . $circle_tree . '/' . $member_key . '/';
		//content data sub_menu
					//$content_data['sub_menu']['text'] = $active_node['sub_menu'][1]['text'];
					$content_data['sub_menu']['link'] = $link;
		//content data forms
					//print_array($active_node, 0);
					$form_data = &$content_data['invitation'];
					$name = $active_node['user_name'];
					$email = $active_node['email'];
					$mobile = $active_node['mobile'];
					if(is_null($name)) $name = 'приховано';
					if(is_null($email)) $email = 'приховано';
					if(is_null($mobile)) $mobile = 'приховано';
					$form_data['email'] = htmlentities($email, ENT_QUOTES); //| ENT_HTML5
					$form_data['name'] = htmlentities($name, ENT_QUOTES); //| ENT_HTML5			
					$form_data['mobile'] = htmlentities($mobile, ENT_QUOTES); //| ENT_HTML5
					if($command['operation'] == 'ready'){
						$content_data['sub_menu']['text'] = $active_node['sub_menu'][1]['text'];
						$content_data['template'] = 'invitation';
						$content_data['operation'] = 'ready';
						//$form_data = &$content_data['invitation'];
						$form_data['action'] = $link;
						$form_data['text'] = 'Щоб ваш знайомий чи знайома могли долучитись до спільноти - сформуйте лінк-запрошення.
												Запрошення може бути надіслано на вказаний Вами email.';			
						$form_data['user_id'] = $user->getUser('id');
						$form_data['member_node'] = $active_node['node'];				
						$content_data['cancel']['action'] = $server . 'data/';				
					}
					elseif($command['operation'] == 'waiting'){
						$content_data['sub_menu']['text'] = $active_node['sub_menu'][1]['text'];
						$content_data['template'] = 'invitation';
						$content_data['operation'] = 'waiting';
						//$form_data = &$content_data['invitation'];
						$form_data['action'] = $link;
						if($active_node['email'])
							$text = "Лінк-запрошення надіслано на вказаний Вами email.";
						else{
							$text = "Надішліть лінк-запрошення вашому знайомому чи знайомій, щоб вони могли долучитись до спільноти.";
						}
						$form_data['text'] = "$text Для скасування запрошення натисність [скасувати] !";
						$form_data['user_id'] = $user->getUser('id');
						$form_data['member_node'] = $active_node['node'];
						$content_data['cancel']['action'] = $server . 'data/';

						$form_data['invite'] = $server . 'invite/' . $active_node['invite'] . '/';
						//$form_data['name'] = htmlentities($active_node['user_name'], ENT_QUOTES | ENT_HTML5);							
						//$form_data['email'] = htmlentities($active_node['email'], ENT_QUOTES | ENT_HTML5);
						//$form_data['mobile'] = htmlentities($active_node['mobile'], ENT_QUOTES | ENT_HTML5);
						$form_data['list_name'] = htmlentities($active_node['list_name'], ENT_QUOTES); //| ENT_HTML5
						$form_data['note'] = htmlentities($active_node['note'], ENT_QUOTES); //| ENT_HTML5
					}
					elseif($command['operation'] == 'edit'){
						$content_data['sub_menu']['text'] = $active_node['sub_menu'][1]['text'] . ' (РЕДАГУВАННЯ)';
						$content_data['template'] = 'invitation';
						$content_data['operation'] = 'edit';
						//$form_data = &$content_data['invitation'];
						$form_data['action'] = $link;
						//$form_data['text'] = "Надішліть лінк-запрошення вашому знайомому чи знайомій, щоб вони могли долучитись до спільноти!
						//						Для скасування запрошення натисність [скасувати] !";		
						$form_data['user_id'] = $user->getUser('id');
						$form_data['member_node'] = $active_node['node'];				
						$content_data['cancel']['action'] = $link;

						$form_data['invite'] = $server . 'invite/' . $active_node['invite'] . '/';
						//$form_data['name'] = htmlentities($active_node['user_name'], ENT_QUOTES | ENT_HTML5);				
						//$form_data['email'] = htmlentities($active_node['email'], ENT_QUOTES | ENT_HTML5);
						//$form_data['mobile'] = htmlentities($active_node['mobile'], ENT_QUOTES | ENT_HTML5);
						$form_data['list_name'] = htmlentities($active_node['list_name'], ENT_QUOTES); //| ENT_HTML5
						$form_data['note'] = htmlentities($active_node['note'], ENT_QUOTES); //| ENT_HTML5					
					}
					elseif($command['operation'] == 'write'){
						$content_data['sub_menu']['text'] = $active_node['sub_menu'][1]['text'] . ' (РЕДАГУВАННЯ)';
						$content_data['template'] = 'invitation';
						$content_data['operation'] = 'edit';
						//$form_data = &$content_data['invitation'];
						$form_data['action'] = $link;
						//$form_data['text'] = "Надішліть лінк-запрошення вашому знайомому чи знайомій, щоб вони могли долучитись до спільноти!
						//						Для скасування запрошення натисність [скасувати] !";		
						$form_data['user_id'] = $user->getUser('id');
						$form_data['member_node'] = $active_node['node'];				
						$content_data['cancel']['action'] = $link;

						$form_data['invite'] = $server . 'invite/' . $active_node['invite'] . '/';
						//$form_data['name'] = htmlentities($active_node['user_name'], ENT_QUOTES | ENT_HTML5);				
						//$form_data['email'] = htmlentities($active_node['email'], ENT_QUOTES | ENT_HTML5);
						//$form_data['mobile'] = htmlentities($active_node['mobile'], ENT_QUOTES | ENT_HTML5);
						$form_data['list_name'] = htmlentities($command['data']['list_name'], ENT_QUOTES); //| ENT_HTML5
						$form_data['note'] = htmlentities($command['data']['note'], ENT_QUOTES); //| ENT_HTML5
					}
					elseif($command['operation'] == 'connected'){
						$content_data['sub_menu']['text'] = $active_node['sub_menu'][2]['text'];
						$content_data['template'] = 'invitation';
						$content_data['operation'] = 'connected';
						//$form_data = &$content_data['invitation'];
						$form_data['action'] = $link;
						$form_data['text'] = 'На Ваше запрошення відповіли. Якщо Ви впевнені, що це саме та людина, яку Ви запрошували - натисніть [ідентифікувати] .
												Щоб відмовити у долученні до спільноти - натисніть [відмовити] .';			
						$form_data['user_id'] = $user->getUser('id');
						$form_data['member_node'] = $active_node['node'];
						$form_data['member_id'] = $active_node['user'];
						$content_data['cancel']['action'] = $server . 'data/';	

						//$form_data['invite'] = $server . 'invite/' . $active_node['invite'] . '/';
						//$form_data['name'] = htmlentities($active_node['user_name'], ENT_QUOTES | ENT_HTML5);				
						//$form_data['email'] = htmlentities($active_node['email'], ENT_QUOTES | ENT_HTML5);
						//$form_data['mobile'] = htmlentities($active_node['mobile'], ENT_QUOTES | ENT_HTML5);
						$form_data['list_name'] = htmlentities($active_node['list_name'], ENT_QUOTES); //| ENT_HTML5
						$form_data['note'] = htmlentities('', ENT_QUOTES); //| ENT_HTML5
					}				
					break;
				default:
			}
		return $answer = true;