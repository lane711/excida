<?php
 
class Dbaccess 
{

	var $q_array = array();
	var $db_id;
	var $query;
	var $counter = 0;
	var $timecounter = 0;
	var $query_res;
	var $lastid = 0;


	// Connect to the mysql server and select database
	//
	// $db->connect ("host", "login", "password", "database");

	function connect ($host, $login, $password, $db)
	{
		$this->host = $host;
		$this->login = $login;
		$this->password = $password;
		$this->db = $db;

		$this->db_id = @mysql_connect($this->host, $this->login, $this->password);

		if ($this->db_id)
		{
			$db_select = @mysql_select_db($this->db);
			
			// Default character set
			$this->query("SET NAMES 'UTF8'");

			if (!$db_select)
			{
				@mysql_close($this->db_id);
				$this->db_id = $db_select;
			}
			else
			return $this->db_id;
		}
		else

		return false;
	}

	// Close database connection and free results memory

	function close()

	{
		if($this->db_id)
		{
			if($this->query)
			{
				@mysql_free_result($this->query);
			}
			$result = @mysql_close($this->db_id);
			return $result;
		}
		else
		{
			return false;
		}

	}

	// Run sql query

	function query ($query)
	{
		unset($this->query_res);

		if($query != "")
		{

			$sql_start = explode(' ', microtime());

			$this->query_res = @mysql_query($query, $this->db_id);
			$this->lastid = @mysql_insert_id( $this->db_id );

			$sql_stop = explode(' ', microtime());
			$sql_time = $sql_stop[0] - $sql_start[0];
			$sql_time+= $sql_stop[1] - $sql_start[1];

			$this->timecounter+= round($sql_time, 5);
			$this->counter++;

		}

		if($this->query_res)

		{
			unset($this->q_array[$this->query_res]);
			return $this->query_res;
		}
		else
		{
			return false;
		}
	}

	// Return the number of rows returned by the query
	//
	// $db->numrows($resource);

	function numrows ($query_id)
	{
		if($query_id)
		{
			$result = @mysql_numrows($query_id);
			return $result;
		}
		else
		{
			return false;
		}
	}
	
	// Sanitize data
	//
	// $db->makeSafe($string)
	
	function makeSafe( $string )
	{
		$string	= mysql_real_escape_string( trim( $string ), $this->db_id );
		return $string;
	}

	// Fetch sql result returned by a query execution
	//
	// $db->fetcharray($resource);

	function fetcharray ($query_id)
	{
		if(!$query_id)
		{
			$query_id = $this->query_res;
		}

		if($query_id)

		{
			$this->q_array[$query_id] = @mysql_fetch_array($query_id);
			return $this->q_array[$query_id];
		}

		else

		{
			return false;
		}
	}

	// Fetch sql result returned by a query execution
	//
	// $db->fetchassoc($resource);

	function fetchassoc( $query_id )
	{
		if(!$query_id)
		{
			$query_id = $this->query_res;
		}

		if($query_id)
		{
			$this->q_array[$query_id] = @mysql_fetch_assoc($query_id);
			return $this->q_array[$query_id];
		}
		else
		{
			return false;
		}
	}

	// Free result memory
	//
	// $db->freeresult($resource);

	function freeresult ($query_id) {

		if ( $query_id )
		{
			unset($this->array[$query_id]);

			@mysql_free_result($query_id);

			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getLastID()
	{
		return $this->lastid;
	}

	public function error() {

		return mysql_error();
	}

}

?>