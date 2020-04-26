<?php

Class Session
{
    public $session = array(	'test' => '',
								
								'enter' => '',
								'next_command' => '',
								'user_status' => '',
								'user_id' => '',
								'user_net' => '',
								'user_nets' => array(), //перевірити значення за замовчуванням в інших змінних, наприклад net_parent_net
								'user_parent_nets' => array(),							
								'user_node' => '',
								'user_parent_node' => '',
								'user_level' => '',					//перевірити
								'user_node_address' => '',
								'user_full_node_address' => '',		//перевірити
								'user_count' => '',
								'user_notifications' => array(),
								'user_voice' => '',
								'invite' => '', //user_invite
								'user_net_name' => '',
								//'user_email' => '', //тимчасово до inner redirection
								'messages' => array(),
								'circle' => array(),
								'tree' => array(),
								'net_id' => '',
								'net_circle_tree' => '',
								'net_parent_net' => '',
								'net_name' => '');
    
    public function __construct()
    {
		if(session_status() != PHP_SESSION_ACTIVE) session_start();
		
		/*
		echo "<pre>";
		echo '<b><font color="blue">SESSION:</font></b>';
		echo '</br>';
		print_r($_SESSION);
		echo "</pre>";
		*/
		
		if(isset($_SESSION)){
            foreach($_SESSION as $key => $value){
                if(isset($this->session[$key]))
					$this->session[$key] = $value;
            }	
        }
    }
	
	public function __destruct()
    {
		$_SESSION = array();
		foreach($this->session as $key => $value){
			if($this->session[$key] === '') continue;
			if($this->session[$key] === false) continue;			
			if(is_null($this->session[$key])) continue;
			if(is_array($this->session[$key]) && !$this->session[$key]) continue;
			$_SESSION[$key] = $value;
        }
		
		//test
		if(isset($_SESSION['test']))
			$_SESSION['test'] = $_SESSION['test'] + 1;
		else
			$_SESSION['test'] = 1;
		//test
		
		session_write_close();
    }
}