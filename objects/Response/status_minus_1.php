<?php
			// main menu		
			$main_menu[1]['text'] = 'ГОЛОВНА';
			$main_menu[1]['link'] = $server;
			
			$main_menu[2]['text'] = 'You & World';
			$main_menu[2]['link'] = $server;
			
			$main_menu[3] = '';	
			
			// menu left
			$menu_left[1]['text'] = 'АВТОРИЗАЦІЯ';
			$menu_left[1]['link'] = $server . 'authorize/';
			$menu_left[1]['active'] = false;

			$menu_left[2]['text'] = 'РЕЄСТРАЦІЯ';
			$menu_left[2]['link'] = $server . 'registration/';
			$menu_left[2]['active'] = false;
			
			$menu_left[3]['text'] = 'ДОЛУЧЕННЯ';
			$menu_left[3]['link'] = $server . 'connect/';
			$menu_left[3]['active'] = false;
			
			$menu_left[4]['text'] = 'СТВОРЕННЯ';
			$menu_left[4]['link'] = $server . 'create/';
			$menu_left[4]['active'] = false;
			
			$menu_left[5]['text'] = 'ВПЕРШЕ';
			$menu_left[5]['link'] = $server . 'first/';
			$menu_left[5]['active'] = false;
			
			// menu right			
			
			// contacts
			$this->response['contacts'] = '';
			
			// content
			$content_data['sub_menu'] = '';		

			switch($command['type']){
				case 'authorize':
					// menu left
					$menu_left[1]['active'] = true;
					// content menu left
					$content_menu_left[1]['text'] = 'АВТОРИЗАЦІЯ';
					$content_menu_left[1]['link'] = $server . 'authorize/';
					$content_menu_left[1]['active'] = true;					
					// content menu right					
					// content
					$operation = $command['operation'];
					if($operation == 'forbid') $operation = 'allow';
					$content_data['operation'] = $operation;
					$content_data['template'] = 'authorize';
					$content_data['authorize']['action'] = $server . 'authorize/';
					$content_data['authorize']['email'] = htmlentities($command['data']['email'], ENT_QUOTES); //| ENT_HTML5
					
					if($operation == 'allow'){
						$content_data['restore']['action'] = $server . 'restore/';
						$content_data['first']['action'] = $server . 'first/';
					}
					else{
						$content_data['cancel']['action'] = $server . 'invite/';
					}

					break;
				case 'registration':
					// menu left
					$menu_left[2]['active'] = true;					
					// content menu left
					$content_menu_left[1]['text'] = 'РЕЄСТРАЦІЯ';
					$content_menu_left[1]['link'] = $server . 'registration/';
					$content_menu_left[1]['active'] = true;				
					// content menu right					
					// content
					$content_data['sub_menu'] = '';
					if($command['operation'] == 'forbid'){
						$content_data['template'] = 'default';
						$content_data['default']['action'] = $server;
						$content_data['default']['text'] = 'Реєстрація можлива лише по запрошенню від учасника спільноти!';
						break;
					}
					
					$content_data['operation'] = $command['operation'];
					$content_data['template'] = 'registration';					
					$content_data['registration']['action'] = $server . 'registration/';
					$content_data['registration']['name'] = htmlentities($command['data']['name'], ENT_QUOTES); //| ENT_HTML5
					$content_data['registration']['email'] = htmlentities($command['data']['email'], ENT_QUOTES); //| ENT_HTML5
					//$content_data['registration']['mobile'] = htmlentities($command['data']['mobile'], ENT_QUOTES); //| ENT_HTML5			
					//$content_data['registration']['password'] = htmlentities($command['data']['password'], ENT_QUOTES); //| ENT_HTML5
					$content_data['cancel']['action'] = $server . 'authorize/';
					
					if($command['operation'] == 'allow_registration')
						$content_data['cancel']['action'] = $server . 'invite/';
					
					break;
				case 'connect':
					//print_array($command);
					// menu left
					$menu_left[3]['active'] = true;
					// content menu left
					$content_menu_left[1]['text'] = 'ДОЛУЧЕННЯ ДО СПІЛЬНОТИ';
					$content_menu_left[1]['link'] = $server . 'connect/';
					$content_menu_left[1]['active'] = true;
					// content menu right			
					// content				
					$content_data['template'] = 'connect';
					$content_data['operation'] = $command['operation'];
					if($command['operation'] == 'allow'){					
						$content_data['connect']['text'] = 'Ви отримали запрошення від учасника спільноти! 
															Для долучення до спільноти авторизуйтесь або зареєстрйтесь!';
						$content_data['authorize']['action'] = $server . 'authorize/';
						$content_data['registration']['action'] = $server . 'registration/';
						$content_data['cancel']['action'] = $server . 'invite/';
						break;
					}
					elseif($command['operation'] == 'allow_authorize'){					
						$content_data['connect']['text'] = 'Ви отримали запрошення від учасника спільноти! 
															Для долучення до спільноти необхідно авторизуватись!';
						$content_data['authorize']['action'] = $server . 'authorize/';
						$content_data['cancel']['action'] = $server . 'invite/';
						break;					
					}
					elseif($command['operation'] == 'allow_registration'){					
						$content_data['connect']['text'] = 'Ви отримали запрошення від учасника спільноти! 
															Для долучення до спільноти необхідно зареєструватись!';
						$content_data['registration']['action'] = $server . 'registration/';
						$content_data['cancel']['action'] = $server . 'invite/';
						break;					
					}				
					
					$content_data['connect']['text'] = 'Долучення до спільноти можливе лише по запрошенню від її учасника!';
					$content_data['connect']['action'] = $server;
					break;
					
				case 'create':					
					$link = $server . 'create/';
					$menu['left'][4]['active'] = true;
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
					$content_data['operation'] = $command['operation'];
					$form_data['action'] = $link;
					$content_data['cancel']['action'] = $link;
					//print_array($command);
					if($command['operation'] == 'allow'){					
						$content_data['create']['text'] = 'Для створення спільноти авторизуйтесь або зареєстрйтесь!';
						$content_data['authorize']['action'] = $server . 'authorize/';
						$content_data['registration']['action'] = $server . 'registration/';
						//$content_data['cancel']['action'] = $server . 'invite/';
						break;
					}
					elseif($command['operation'] == 'allow_authorize'){					
						$content_data['create']['text'] = 'Для створення спільноти необхідно авторизуватись!';
						$content_data['authorize']['action'] = $server . 'authorize/';
						//$content_data['cancel']['action'] = $server . 'invite/';
						break;				
					}
					elseif($command['operation'] == 'allow_registration'){					
						$content_data['create']['text'] = 'Для створення спільноти необхідно зареєструватись!';
						$content_data['registration']['action'] = $server . 'registration/';
						//$content_data['cancel']['action'] = $server . 'invite/';
						break;					
					}				
					elseif($command['operation'] == 'forbid'){
						$content_data['create']['text'] = 'Створення спільноти не можливе!';
						$content_data['create']['action'] = $server;
						break;					
					}
					
					$content_data['operation'] = 'edit';
					$form_data['text'] = 'Для створення спільноти вкажіть її назву!';
					//$form_data['net_name'] = htmlentities($user->getUser('net_name'), ENT_QUOTES | ENT_HTML5);
					//$form_data['user_id'] = $user->getUser('id');
					//$form_data['net_id'] = '';
					$content_data['cancel']['action'] = $server;

					//if($command['operation'] == 'net_create'){
						//$content_data['operation'] = 'edit';
						$form_data['net_name'] = htmlentities($command['data']['net_name'], ENT_QUOTES); //| ENT_HTML5				
					//}				
					break;
				case 'first':
					// menu left
					$menu_left[5]['active'] = true;
					// content menu left
					$content_menu_left[1]['text'] = 'Я ТУТ ВПЕРШЕ';
					$content_menu_left[1]['link'] = $server . 'first/';
					$content_menu_left[1]['active'] = true;
					// content menu right			
					// content
					$content_data['sub_menu'] = '';					
					$content_data['template'] = 'first';
					$content_data['sub_menu'] = '';					
					$content_data['first']['text'] = 'Ви можете долучитись до існуючої спільноти або створити нову!';
					$content_data['connect']['action'] = $server . 'connect/';
					$content_data['create']['action'] = $server . 'create/';						
					$content_data['authorize']['action'] = $server . 'authorize/';
					break;
				case 'create':
					// menu left
					$menu_left[4]['active'] = true;					
					// content menu left
					$content_menu_left[1]['text'] = 'СТВОРЕННЯ СПІЛЬНОТИ';
					$content_menu_left[1]['link'] = $server . 'create/';
					$content_menu_left[1]['active'] = true;				
					// content menu right				
					// content
					$this->response['content']['data']['sub_menu'] = '';
					$content_data['template'] = 'default';
					$content_data['default']['action'] = $server;
					$content_data['default']['text'] = 'Створити нову спільноту неможливо!';
					break;			
				default:
			}
			return $answer = true;