<?php

Class Inspection
{
    
    public function __construct()
    {

    }
	
	public function doExpection(&$data_value, $data_type = '')
	{
		$data_value = trim($data_value);
		return true;
	}
	
	public function __destruct()
    {	
	
    }
}