<?php

Class Response
{   
	private $response = array();
	private $service = false;
	private $html = true;
	
    public function __construct()
    {

    }
	
	public function getResponse()
	{
		$response = &$this->response;
			$main_menu = &$response['main_menu'];
			$menu = &$response['menu'];
				//$menu_left = &$menu['left'];
				//$menu_right = &$menu['right'];
			$contacts = &$response['contacts'];
				//$contacts_menu = &$contacts['menu'];
				//$contacts_data = &$contacts['data'];
			$content = &$response['content'];
				$content_menu = &$content['menu'];
					$content_menu_right = &$content_menu['right'];
			$content_data = &$content['data']; 
		
		require_once './html/html.php';
	}
	
	public function setResponse($data_name = '', $data_value = '', &$command = '', &$user = '', &$data = '', &$circle = '', &$tree = '', &$net = '')
	{
		switch($data_name){
			case 'data_name':
				//...
				break;			
			default:
			$this->response[$data_name] = $data_value;
		}

		if($data_name) return true;
		
		$this->response['content']['messages'] = $data->messages;
		
		//початковы дані
		$response = &$this->response;
			$main_menu = &$response['main_menu'];
			$menu = &$response['menu'];
				$menu_left = &$menu['left'];
				$menu_right = &$menu['right']; $menu_right = array();
			$contacts = &$response['contacts'];
				$contacts_menu = &$contacts['menu'];
				$contacts_data = &$contacts['data'];
			$content = &$response['content'];
				$content_menu = &$content['menu'];
					$content_menu_left = &$content_menu['left'];
					$content_menu_right = &$content_menu['right']; $content_menu_right = array();
				$content_data = &$content['data'];
					$content_data['sub_menu'] = '';

		$server = $data->getServer('HTTP_HOST');
		//початкові дані

		if($user->getUser('status') == -1)
			require 'Response/status_minus_1.php';
		elseif($user->getUser('status') <= 0)
			require 'Response/status_0.php';		
		elseif($user->getUser('status') > 0)
			require 'Response/status_plus_1.php';
		
		return $answer;
	}			

	public function __destruct()
    {	

    }
}