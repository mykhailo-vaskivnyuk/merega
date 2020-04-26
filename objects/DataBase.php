<?php


class DataBase
{
	private $db_host    	= 'grey00.mysql.tools';
	private $db_user    	= 'grey00_mike'; //'root';
	private $db_name     	= 'grey00_mike'; //merega';
	private $db_password 	= 'mM82L1kJX5zc';
	private $db;

	public function __construct()
	{
		$this->db = new mysqli;
		$this->db->init();
		@$this->db->real_connect($this->db_host, $this->db_user, $this->db_password, $this->db_name) or die ('База даних недоступна!');
		$this->db->set_charset('utf8');
	}
	
	public function getDB($data_name, $data_value = '')
	{
		if($data_name == 'insert_id')
			return $this->db->insert_id;
		elseif($data_name == 'real_escape_string')
			return $this->db->real_escape_string($data_value);			
		return false;
	}	
	
	public function getWork($sql, &$affected_rows = 0)
	{		
		$result = $this->db->query($sql) or die('<pre>' . $this->db->error . '<br/><br/>' . $sql . '</pre>');
		$affected_rows = $this->db->affected_rows;
		return $result;
	}

	public function getOneArray($sql)
	{
		$result = $this->db->query($sql) or die('<pre>' . $this->db->error . '<br/><br/>' . $sql . '</pre>');
		if(!$result) return NULL;
		return $result->fetch_assoc();
	}

	public function getArray($sql)
	{
		$result = $this->db->query($sql) or die('<pre>' . $this->db->error . '<br/><br/>' . $sql . '</pre>');
		if(!$result) return NULL;
		return $result->fetch_all(MYSQLI_ASSOC);
	}

	public function __destruct()
	{
		@$this->db->close();
	}
}

?>
