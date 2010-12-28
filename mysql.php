<?php
if (!defined('INPHP')) {die("This file cannot be accessed directly.");}

class Mysql {
	var $status = "200 OK";
	var $code = "ok";
	var $query = "";
	var $num_rows = 0;
	var $affected_rows = 0;
	var $insert_id = 0;
	var $result = array();
	var $connected = false;
	var $error = "";
	
	function Mysql() {
		global $config;
		$this->server = $config;
	}
	
	function query ($query,$fetch="object") {
		$this->code = "ok";
		$this->query = $query;
		$this->fetch = $fetch;
		$this->num_rows = 0;
		$this->affected_rows = 0;
		$this->insert_id = 0;
		$this->result = array();
		$this->connected = false;
		$this->error = "";
		
		if ($this->connect()) {
			$result = @mysql_query($this->query);
			if ($result) {
				$this->insert_id = @mysql_insert_id();
				$this->num_rows = @mysql_num_rows($result);
				$this->affected_rows = @mysql_affected_rows();
				if ($this->num_rows AND $fetch) {
					switch ($fetch) {
						case "row":
						for ($n=0;$n<$this->num_rows;$n++) {$this->result[$n] = mysql_fetch_row($result);}
						break;
						case "object":
						for ($n=0;$n<$this->num_rows;$n++) {$this->result[$n] = mysql_fetch_object($result);}
						break;
						case "assoc":
						for ($n=0;$n<$this->num_rows;$n++) {$this->result[$n] = mysql_fetch_assoc($result);}
						break;
						case "array":
						for ($n=0;$n<$this->num_rows;$n++) {$this->result[$n] = mysql_fetch_array($result);}
						break;
					}
					mysql_free_result ($result);
				}
				mysql_close();
				return true;
			}
			else {
				$this->code = "error";
				$this->error = mysql_error();
				mysql_close();
				return false;
			}
		}
	}
	
	private function get ($query, $return) {
		$this->code = "ok";
		$this->query = $query;
		$this->fetch = "row";
		$this->num_rows = 0;
		$this->affected_rows = 0;
		$this->insert_id = 0;
		$this->result = array();
		$this->connected = false;
		$this->error = "";
		
		if ($this->connect()) {
			$result = @mysql_query($this->query);
			if ($result) {
				$this->num_rows = @mysql_num_rows($result);
				if ($this->num_rows) {
					if ($return == "object") $this->result = mysql_fetch_object($result);
					if ($return == "row") $this->result = mysql_fetch_row($result);
					if ($return == "assoc") $this->result = mysql_fetch_assoc($result);
					mysql_free_result ($result);
				}
				mysql_close();
				return $this->result;
			}
			else {
				$this->code = "error";
				$this->error = mysql_error();
				mysql_close();
				return false;
			}
		}
	}
	
	function get_object ($query) {
		return $this->get ($query, "object");
	}
	function get_row ($query) {
		return $this->get ($query, "row");
	}
	function get_assoc ($query) {
		return $this->get ($query, "assoc");
	}
	
	private function connect () {
		// Connection au serveur mySQL
		// makes three attemps
		$n=0;
		while (!$this->connected = @mysql_connect($this->server["sql"]["server"], $this->server["sql"]["login"], $this->server["sql"]["password"]) AND $n<3) {
			sleep(1);$n++;
		}
		if ($this->connected) {
			if (!@mysql_select_db($this->server["sql"]["base"])) {
				$this->code = "error";
				$this->error = "FATAL ERROR: Could not connect to {$this->server["sql"]["base"]} base.";
				return false;
			}
		}
		else {
			$this->code = "error";
			$this->error = "FATAL ERROR: Could not connect to {$this->server["status"]} server.";
		}
		return $this->connected;
	}
	
	function refresh () {
		if (!empty($this->query) AND ($this->num_rows) AND !empty($this->fetch)) {
			return $this->Query($this->query,$this->fetch);
		}
	}
}

?>