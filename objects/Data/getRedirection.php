<?php
//public function getRedirection()
//{
		$server = $this->server['HTTP_HOST'];
		
		switch($this->command['type']){
			case 'authorize':
				$link = $server . 'authorize/';
				break;
			case 'enter':
				$link = $server . 'enter/';
				break;
			case 'in':
				$link = $server . 'in/';
				break;
			case 'connect':
				$link = $server . 'connect/';
				break;
			case 'statistic':
				$link = $server . 'statistic/';
				if(	isset($this->command['data']['circle_tree']) && 
					$this->command['data']['circle_tree'])
					$link = $link . $this->command['data']['circle_tree'] . '/' . $this->command['data']['member'] . '/';
				break;
			case 'notification':
				$link = $server . 'notification/';
				if(	isset($this->command['data']['circle_tree']) && 
					$this->command['data']['circle_tree'])
					$link = $link . $this->command['data']['circle_tree'] . '/' . $this->command['data']['member'] . '/';				
				break;
			case 'registration':
				//тимчасово, перенаправлення з first
				$link = $server . 'registration/';
				break;	
			case 'data':
				$link = $server . 'data/';
				if(	isset($this->command['data']['circle_tree']) && 
					$this->command['data']['circle_tree'])
					$link = $link . $this->command['data']['circle_tree'] . '/' . $this->command['data']['member'] . '/';
				//echo $link; exit;
				break;
			case 'circle':
				$link = $server . 'circle/';
				if(	isset($this->command['data']['circle_tree']) && 
					$this->command['data']['circle_tree'])
					$link = $link . $this->command['data']['member'] . '/';
				//echo $link; exit;
				break;
			case 'tree':
				$link = $server . 'tree/';
				if(	isset($this->command['data']['circle_tree']) && 
					$this->command['data']['circle_tree'])
					$link = $link . $this->command['data']['member'] . '/';
				//echo $link; exit;
				break;				
			case 'invitation':
				$link = $server . 'invitation/';
				//if(	isset($this->command['data']['circle_tree']) && 
				//	$this->command['data']['circle_tree'])
					$link = $link . $this->command['data']['circle_tree'] . '/' . $this->command['data']['member'] . '/';
				//echo $link; exit;
				break;
			case 'invite':
				$link = $server . 'invite/' . $this->command['data']['link'] . '/';
				break;
			case 'delete':
				$link = $server . 'delete/';
				break;
			case 'vote':
				$link = $server . 'vote/';
				//if(	isset($this->command['data']['circle_tree']) && 
				//	$this->command['data']['circle_tree'])
					$link = $link . $this->command['data']['circle_tree'] . '/' . $this->command['data']['member'] . '/';
				//print_array($link);
				break;				
			default:
				$link = $server;
		}
		$this->SESSION->session['messages'] = $this->messages;
		$_SESSION['messages'] = $this->messages;
//}
?>