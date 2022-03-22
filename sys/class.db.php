<?php
	class db{
		var $dbhost;
		var $dbuser;
		var $dbpass;
		var $dbname;
		var $connect;

		function db($dbhost, $dbuser, $dbpass, $dbname){
			$this->dbhost = $dbhost;
			$this->dbuser = $dbuser;
			$this->dbpass = $dbpass;
			$this->dbname = $dbname;
			$this->connect = 0;
		}

		function connect(){
			$connection = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);

			if(!$connection){
				return false;
			}
			else{
				$this->connect = 1;
			}
		}

		function query($SQL){
			$connection = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
			$query = mysqli_query($connection, $SQL);

			if(!$query){
				return false;
			}
			else{
				if(preg_match("/^select/i", $SQL)){
					$result = array();
					while($row = mysqli_fetch_array($query)){
						$result[] = $row;
					}
					return $result;
				}
				else{
					return true;
				}
			}
		}

		// GET SINGLE ROW FROM DATABASE
		function info($table_name, $SQL_condition = ""){
			global $dbprefix;
			$dbtable = $dbprefix . $table_name;

			$query = $this->query("SELECT * FROM {$dbtable}" . ($SQL_condition ? " WHERE {$SQL_condition}" : ""));

			if(count($query)){
				$row = $query[0];
				$data = array();

				foreach($row as $key=>$value){
					if(preg_match("/\d/i", $key)){
						continue;
					}

					$data[$key] = $value;
				}

				return $data;
			}
			else{
				return false;
			}
		}

		// GET LAST ID FROM TABLE PASSED IN PARAMETER
		function getauto($table_name){
			global $dbprefix, $dbname;
			$dbtable = $dbprefix . $table_name;

			$query = $this->query("SELECT auto_increment FROM information_schema.TABLES WHERE TABLE_NAME = '{$dbtable}' AND TABLE_SCHEMA = '{$dbname}'");

			return $query[0]["auto_increment"];
		}

		function getmaxval($field, $table_name, $SQL_condition = ""){
			global $dbprefix;
			$dbtable = $dbprefix . $table_name;

			$query = $this->query("SELECT max{$field} AS maxval FROM {$dbtable}" . ($SQL_condition ? " WHERE {$SQL_condition}" : ""));

			return $query[0]["maxval"];
		}

		// MOVE ROW UP AND DOWN
		function move($table_name, $row_id, $SQL_condition = ""){
			global $dbprefix;
			$dbtable = $dbprefix . $table_name;

			$move_id = substr($row_id, 1, strlen($row_id));
			$move = substr($row_id, 0, 1);

			$random_array = array();
			$query = $this->query("SELECT {$table_name}_id FROM {$dbtable}" . ($SQL_condition ? " WHERE {$SQL_condition}" : "") . " ORDER BY {$table_name}_order");

			foreach($query as $row){
				$random_array[] = $row[$table_name . "_id"];
			}

			if(in_array($move_id, $random_array)){
				$old_key = array_search($move_id, $random_array);

				if(($move == "u") && ($old_key != 0)){
					$random_array[$old_key] = $random_array[$old_key - 1];
					$random_array[$old_key - 1] = $move_id;
				}
				elseif(($move == "d") && ($old_key < count($random_array) - 1)){
					$random_array[$old_key] = $random_array[$old_key + 1];
					$random_array[$old_key + 1] = $move_id;
				}

				foreach($random_array as $key=>$value){
					$order = $key + 1;
					$this->query("UPDATE {$dbtable} SET {$table_name}_order = '{$order}' WHERE {$table_name}_id = '{$value}'");
				}
			}
		}

		function get_pack(){
			global $dbprefix;

			$random_array = array();
			$query = $this->query("SELECT pack_id, pack_title FROM {$dbprefix}pack ORDER BY pack_order");
			
			if(count($query)){
				foreach($query as $row){
					$random_array[$row["pack_id"]] = $row["pack_title"];
				}
			}

			return $random_array;
		}

		function get_cat($table_name, &$random_array, $nid = 0, $pid = 0, $level = 0, $SQL_condition = ""){
			global $dbprefix;
			$dbtable = $dbprefix . $table_name;

			$query = $this->query("SELECT * FROM {$dbtable} WHERE {$table_name}_pid = '{$pid}' AND {$table_name}_id <> '{$nid}' {$SQL_condition} ORDER BY {$table_name}_order");
			
			$i = 0;
			foreach($query as $row){
				$random_array[$row[$table_name . "_id"]] = array(
					"pid"=>$row[$table_name . "_pid"], 
					"title"=>$row[$table_name . "_title"], 
					"pack"=>$row[$table_name . "_pack"], 
					"tmenu"=>$row[$table_name . "_tmenu"], 
					"bmenu"=>$row[$table_name . "_bmenu"], 
					"level"=>$level, 
					"first"=>$i ? 0 : 1, 
					"last"=>($i == count($query) - 1) ? 1 : 0
				);

				$i++;
				$this->get_cat($table_name, $random_array, $nid, $row[$table_name . "_id"], $level + 1);
			}
		}

		function get_tmenu($pack, $pid = 0, $level = 0){
			global $dbprefix;

			if($level == 0){
				$attribute = "id=\"nav\"";
			}
			elseif($level == 1){
				$attribute = "class=\"nav first\"";
			}
			else{
				$attribute = "class=\"nav\"";
			}

			$str = "";

			$query = $this->query("SELECT * FROM {$dbprefix}page WHERE page_pid = '{$pid}' AND page_pack LIKE '%$pack%' AND page_tmenu = '1' ORDER BY page_order");
			
			if(count($query)){
				$str .= "<ul {$attribute}>";
				foreach($query as $row){
					$str .= "<li>";
					$str .= "<a href=\"index.php?cmd={$row["page_slug"]}\">{$row["page_title"]}</a>";
					$str .= $this->get_tmenu($pack, $row["page_id"], $level + 1);
					$str .= "</li>";
				}
				$str .= "</ul>";
			}

			return $str;
		}
		
		function prepare($SQL){
			$connection = mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
			$statement = mysqli_prepare($connection, $SQL);

			return $statement;
		}
	}
?>