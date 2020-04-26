<?php
			// main menu		
			$this->response['main_menu'][1]['text'] = 'ГОЛОВНА';
			$this->response['main_menu'][1]['link'] = $data->getServer('HTTP_HOST');
			$this->response['main_menu'][2]['text'] = 'ЗА МЕЖАМИ СПІЛЬНОТИ';
			$this->response['main_menu'][2]['link'] = $data->getServer('HTTP_HOST') . 'enter/';					
			$this->response['main_menu'][3]['text'] = 'ВИЙТИ';
			$this->response['main_menu'][3]['link'] = $data->getServer('HTTP_HOST') . 'exit/';

			// contacts
			//contacts menu
			$this->response['contacts']['menu']['text'] = 'МІЙ АКАУНТ';
			$this->response['contacts']['menu']['link'] = $data->getServer('HTTP_HOST');
			
			$this->response['contacts']['iam']['text'] = 'Я';
			$this->response['contacts']['iam']['action'] = $data->getServer('HTTP_HOST');
			$this->response['contacts']['iam']['active'] = true;
			$this->response['contacts']['iam']['status'] = array('text' => '', 'css' => '', 'marker' => 'empty', 'dislike' => '');
			$this->response['contacts']['data']['circle'][0] = '';
			$this->response['contacts']['data']['circle'][1] = $this->response['contacts']['iam'];
			$this->response['contacts']['data']['circle'][2] = '';
			$this->response['contacts']['data']['circle'][3] = '';
			$this->response['contacts']['data']['circle'][4] = '';
			$this->response['contacts']['data']['circle'][5] = '';
			$this->response['contacts']['data']['circle'][6] = '';
			$this->response['contacts']['data']['circle'][7] = '';
			$this->response['contacts']['data']['tree'] = array();

			// menu left
			// в залежності від статусу -5 чи 0 не всі пункти меню наявні
			$this->response['menu']['left'][1]['text'] = 'ДОЛУЧИТИСЬ';
			$this->response['menu']['left'][1]['link'] = $data->getServer('HTTP_HOST');
			$this->response['menu']['left'][1]['active'] = false;

			$this->response['menu']['left'][2]['text'] = 'СТВОРИТИ';
			$this->response['menu']['left'][2]['link'] = $data->getServer('HTTP_HOST') . 'create/';
			$this->response['menu']['left'][2]['active'] = false;

			$this->response['menu']['left'][3]['text'] = 'ПОВІДОМЛЕННЯ';
			$this->response['menu']['left'][3]['link'] = $data->getServer('HTTP_HOST') . 'notification/';
			$this->response['menu']['left'][3]['active'] = false;
			
			$this->response['menu']['left'][4]['text'] = 'ВИДАЛИТИ';
			$this->response['menu']['left'][4]['link'] = $data->getServer('HTTP_HOST') . 'delete/'; //. $user->getUser('id') . '/';
			$this->response['menu']['left'][4]['active'] = false;
			
			// menu right
			if($user_nets = $user->getUser('nets')){
				//if(count($user_nets) == 1){
				if(count($user_nets) > 0){
				//	$this->response['menu']['right'][1]['text'] = 'УВІЙТИ В';
				//	$this->response['menu']['right'][1]['link'] = $data->getServer('HTTP_HOST') . 'in/';
				//	$this->response['menu']['right'][1]['active'] = false;
				//	$this->response['menu']['right'][1]['sub_menu'] = array();
				//}
				//else{
					$sub_menu = &$this->response['menu']['right'][1]['sub_menu'];
					foreach($user_nets as $net_id => $net_name){
						$sub_menu[$net_id]['text'] = $net_name;
						$sub_menu[$net_id]['link'] = $data->getServer('HTTP_HOST') . 'in/' . $net_id . '/';
						$sub_menu[$net_id]['active'] = false;
					}
					$this->response['menu']['right'][1]['text'] = 'УВІЙТИ В';
					$this->response['menu']['right'][1]['link'] = '#';
					$this->response['menu']['right'][1]['active'] = false;
				}
			}
			//$this->response['menu']['right'] = array();
			
			// content menu right					
			//$this->response['content_menu']['right'][1]['text'] = 'ПІДМЕНЮ';
			//$this->response['content_menu']['right'][1]['link'] = $data->getServer('HTTP_HOST') . '';
			//$this->response['content_menu']['right'][1]['active'] = false;
			$this->response['content_menu']['right'] = array();

			switch($command['type']){
				case 'data':
					// content menu left
					$this->response['content']['menu']['left'][1]['text'] = 'Я (' . htmlentities($user->getUser('name'), ENT_QUOTES) . ')'; //| ENT_HTML5
					$this->response['content']['menu']['left'][1]['link'] = $data->getServer('HTTP_HOST');
					$this->response['content']['menu']['left'][1]['active'] = true;
					
					// content
					$this->response['content']['data']['sub_menu']['text'] = 'МОЇ ДАНІ';
					$this->response['content']['data']['sub_menu']['link'] = $data->getServer('HTTP_HOST') . 'data/'; // 'data/edit/' ?
					$this->response['content']['data']['template'] = 'data';
					$this->response['content']['data']['operation'] = 'read';
					$this->response['content']['data']['data']['action'] = $data->getServer('HTTP_HOST') . 'data/';
					$this->response['content']['data']['data']['name'] = htmlentities($user->getUser('name'), ENT_QUOTES); //| ENT_HTML5			
					$this->response['content']['data']['data']['email'] = htmlentities($user->getUser('email'), ENT_QUOTES); //| ENT_HTML5
					$this->response['content']['data']['data']['mobile'] = htmlentities($user->getUser('mobile'), ENT_QUOTES); //| ENT_HTML5
					$this->response['content']['data']['data']['user_id'] = $user->getUser('id');
					$this->response['content']['data']['data']['password'] = $user->getUser('password'); //для перевірки наявності паролю
					if($command['operation'] == 'edit'){
						$this->response['content']['data']['sub_menu']['text'] = 'МОЇ ДАНІ (РЕДАГУВАННЯ)';
						$this->response['content']['data']['operation'] = 'edit';
						$this->response['content']['data']['cancel']['action'] = $data->getServer('HTTP_HOST');						
					}
					
					if($command['operation'] == 'write'){
						$this->response['content']['data']['sub_menu']['text'] = 'МОЇ ДАНІ (РЕДАГУВАННЯ)';
						$this->response['content']['data']['operation'] = 'edit';
						$this->response['content']['data']['cancel']['action'] = $data->getServer('HTTP_HOST');	
						$this->response['content']['data']['data']['name'] = htmlentities($command['data']['user_name'], ENT_QUOTES); //| ENT_HTML5				
						$this->response['content']['data']['data']['mobile'] = htmlentities($command['data']['user_mobile'], ENT_QUOTES); //| ENT_HTML5				
					}			
					break;
				case 'create':
					$link = $server . 'create/';
					$menu['left'][2]['active'] = true;
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
					//$form_data['net_id'] = '';
					$content_data['cancel']['action'] = $server . 'data/';		

					//if($command['operation'] == 'net_create'){
						//$content_data['operation'] = 'edit';
						$form_data['net_name'] = htmlentities($command['data']['net_name'], ENT_QUOTES); //| ENT_HTML5			
					//}
					
					break;
				case 'notification':
					// menu left
					$this->response['menu']['left'][3]['active'] = true;

					// contacts
					$this->response['contacts']['iam']['active'] = false;				
					$this->response['contacts']['data']['circle'][1] = $this->response['contacts']['iam'];
			
					// content menu left
					$this->response['content']['menu']['left'][1]['text'] = 'ПОВІДОМЛЕННЯ';
					$this->response['content']['menu']['left'][1]['link'] = $data->getServer('HTTP_HOST') . 'notification/';
					$this->response['content']['menu']['left'][1]['active'] = true;

					// content
					$this->response['content']['data']['sub_menu'] = '';
					$this->response['content']['data']['template'] = 'notification';
					$this->response['content']['data']['notifications'] = array();
					$notifications = $user->getUser('notifications'); //getNotification();
					if(!$notifications) break;
					foreach($notifications as $item){
						$notification['action'] = $data->getServer('HTTP_HOST') . 'notification/';;
						$notification['text'] = $item['notification'];
						//$notification['action'] = $link;
						//$notification['text'] = $item['notification_text'];
						$notification['id'] = $item['notification_id'];;
						$notification['close'] = $item['close'];;
						$this->response['content']['data']['notifications'][] = $notification;
					}
					break;
				case 'delete':
					// menu left
					$this->response['menu']['left'][4]['active'] = true;

					// contacts
					$this->response['contacts']['iam']['active'] = false;
					$this->response['contacts']['data']['circle'][1] = $this->response['contacts']['iam'];					
					
					// content menu left
					$this->response['content']['menu']['left'][1]['text'] = 'ВИДАЛЕННЯ АКАУНТА';
					$this->response['content']['menu']['left'][1]['link'] = $data->getServer('HTTP_HOST') . 'delete/';
					$this->response['content']['menu']['left'][1]['active'] = true;

					// content
					$this->response['content']['data']['sub_menu'] = '';
					$this->response['content']['data']['template'] = 'delete';
					$this->response['content']['data']['notification'] = '';
					$this->response['content']['data']['delete']['text'] = 'Якщо Ви бажаєте видалити свій акаунт - натисніть \'ВИДАЛИТИ АКАУНТ\'. Всі ваші дані буде стерто!';
					$this->response['content']['data']['delete']['action'] = $data->getServer('HTTP_HOST') . 'delete/';
					$this->response['content']['data']['delete']['user_id'] = $user->getUser('id');			
					$this->response['content']['data']['cancel']['action'] = $data->getServer('HTTP_HOST');								
					break;				
				default:
			}
			return $answer = true;