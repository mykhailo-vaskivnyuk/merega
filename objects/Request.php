<?php

Class Request
{
    public $command = array();
    
    public function __construct()
    {
		//print_array($_REQUEST);
		
        $this->command = array(
			//name=>user_name, та інші аналогічні змінні
			'link_1' => '',
			'link_2' => '',
			'link_3' => '',
			'email' => '',
			'password' => '',
			'name' => '',
			'mobile' => '',
			'user_id' => '',
			'operation' => '',
			'net_id' => '',
			'member_node' => '',
			'member_id' => '',
			'notification_id' =>'',
			'list_name' => '',
			'note' => '',
			'dislike' => '',
			'voice' => '',
			'link_name_1' => '',
			'link_value_1' => '',
			'link_name_2' => '',
			'link_value_2' => '',
			'link_name_3' => '',
			'link_value_3' => '',
			'link_name_4' => '',
			'link_value_4' => '',
			'text' => '');
		
		if(isset($_GET)){
            foreach($_GET as $key => $value){
                $this->command[$key] = trim($value);
            }
        }
		
        if(isset($_POST)){
            foreach($_POST as $key => $value){
                $this->command[$key] = trim($value);
            }
        }		
    }
}