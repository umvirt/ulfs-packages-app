<?php

class db_connection{
	/**
	* Database server 
	*
	* IP-address or hostname
	* @access private
	* @var string 
	*/
	private $sqlserver;
	/**
	* Database user
	* @access private
	* @var string
	*/
	private $sqluser;
	/**
	* Database user password
	* @access private
	* @var string user
	*/
	private $sqlpassword;
	/**
	* Database name
	* @access private
	* @var string
	*/
	private $sqldb;
	/**
	* Last executed dataset
	* @access public
	* @var array 
	*/
	var $dataset;
	/**
	*Error strings 
	* @access public
	* @var array 
	*/	
	var $errors;
	var $queries;
	/**
	* Datasets
	*
	* Results of executed queries
	* @access public
	* @var array 
	*/
	var $datasets;
	/**
	* Constructor sets up db_connection
	* @param $yaps yaps application object
	*/
	function __construct($db_config){
		///Store parameters	
		$this->sqlserver=$db_config["server"];
		$this->sqluser=$db_config["user"];
		$this->sqlpassword=$db_config["password"];
		$this->sqldb=$db_config["database"];
	}
	/**
	* Executing queries
	*
	* Executing one query or sequence of queries in transaction. Injecting queries in transaction is perfoming automatically
	* @param mixed $query string or array of strings which contain queries
	* @param string $db define specific database name. If not defined used default database defined in constructor
	* @return boolean
	*/
	function execute($query, $db=""){
		if(!$db){
		    $db=$this->sqldb;
		}
//		global $yaps;
//		$this->yaps->dbqueries++;
//		echo $this->server->dbqueries;
		unset($this->datasets);
		unset($this->dataset);
		unset($this->errors);
		unset($this->queries);
		//var_dump($query);
		$this->connection=new mysqli($this->sqlserver,$this->sqluser,$this->sqlpassword);
		//var_dump($c);
		if(!$this->connection){
			throw new Exception("Database server connection failed");
		}
		$e=$this->connection->select_db($db);
		if(!$e){
			throw new Exception("Database connection failed");
		}

		$this->connection->query("set names utf8");
		if (is_array($query)){
			//Transaction
			$this->connection->query("begin");
			foreach ($query as $k=>$v){$this->makeresult($v, $k);}
			$this->connection->query("commit");
		}else{
			//Single query
			$this->makeresult($query);
			}
		$this->connection->close();
		return true;
	}
	/**
	* Executing single query
	*
	* Executing one query with specified id. Inserting results in datasets array, errors in errors array
	* @param string $query string SQL-query
	* @param mixed $id key of element in arrays
	*/	
	function makeresult($query, $id=0){
		//execting query
		$x=$this->connection->query($query);
		$this->queries[$id]=$query;
		$this->errors[$id]=$this->connection->error;
		$this->error=$this->connection->error;
		//Making dataset
		if (@$x->num_rows>0){
			while($row = $x->fetch_assoc()){$res[]=$row;}
			$this->datasets[$id]=$res;
			$this->dataset=$res;
		}else{$this->dataset=array();}
	}
}
