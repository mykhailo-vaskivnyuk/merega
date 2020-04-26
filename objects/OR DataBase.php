<?php
/*
 * Created on 06.08.2010 16:03:07 in unbp
 * By Noon-ehoS
 */

class DataBase
{
//	private $host    	= "localhost";
//	private $user    	= "root";
//	private $db     	= "atris";
//	private $db_pswd 	= "111111";
//	private $db_prefix 	= "c_";
//	private $connect_link = NULL;

	private $host    	= "localhost";
	private $user    	= "root";
	private $db     	= "merega";
	private $db_pswd 	= "";
	private $db_prefix 	= "c_";
	private $connect_link = NULL;
	
	
	
	
	private $dbTreeCLassPath = "libs/nested_sets/dbtree.class.php";
	private $dbTreeMySQLInterfacePath = "libs/nested_sets/db_mysql/db_mysql.class.php";

	public function __construct()
	{
		$this->connect_link = @mysql_connect($this->host, $this->user, $this->db_pswd) or die ('База данных недоступна!');
		mysql_select_db($this->db);

		mysql_query("SET NAMES `UTF8`");
		mysql_query("SET character_set_client = UTF8");
		mysql_query("SET character_set_connection = UTF8");
		mysql_query("SET character_set_results = UTF8");
		mysql_query("SET TIME_ZONE='".date('P')."'");
	}

	public function getWork($qs)
	{
		@$q = mysql_query($qs) or die("<pre>".mysql_error()."<br /><br />".$qs."</pre>");
		return $q;
	}

	public function getOneArray($qs)
	{
	    $res = array();
		@$q = mysql_query($qs) or die("<pre>".mysql_error()."<br /><br />".$qs."</pre>");

		if ($q)
		{
			while ($row  = mysql_fetch_array($q,MYSQL_ASSOC))
			{
			    $res=$row;
		    }
		}
		else 	return null;

		return $res;
	}

	public function getArray($qs)
	{
	    $res = array();
		$q = mysql_query($qs) or die("<pre>".mysql_error()."<br /><br />".$qs."</pre>");
		if ($q)
		{
			while ($row = mysql_fetch_array($q,MYSQL_ASSOC))
			{
			    array_push($res,$row);
		    }
		}
		else return null;

		return $res;
	}

	public function getSettings()
	{
		$sets = array();
		$sets['host'] 		= $this->host;
		$sets['user'] 		= $this->user;
		$sets['db'] 		= $this->db;
		$sets['db_pswd'] 	= $this->db_pswd;
		$sets['db_prefix'] 	= $this->db_prefix;

		return $sets;
	}

	public function newNestedTree($table_name, $prefix)
	{
		require_once ($this->dbTreeCLassPath);
		require_once ($this->dbTreeMySQLInterfacePath);

		$Nested_db = new db($this->host,  $this->user, $this->db_pswd, $this->db);
		$sql = 'SET NAMES `UTF8`'; $Nested_db->Execute($sql);
		$sql = 'SET character_set_client = UTF8'; $Nested_db->Execute($sql);
		$sql = 'SET character_set_connection = UTF8'; $Nested_db->Execute($sql);
		$sql = 'SET character_set_results = UTF8'; $Nested_db->Execute($sql);

		return new dbtree($table_name, $prefix, $Nested_db);
	}

	public function mres($var){ return mysql_real_escape_string(trim($var)); }
	public function amres($array) { return array_map('mysql_real_escape_string',array_map('trim',$array)); }

	public function __destruct()
	{
		@mysql_close($this->connect_link);
	}
}

?>
