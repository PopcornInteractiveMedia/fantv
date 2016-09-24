<?php
namespace lib;

defined( '_VALID_INCLUDE' ) or die( 'Restricted access' );
/**
 * Its a Database Class. This class use mysqli for database connection.
 *
 * @author Md.Abdullah al mamun | dev.mamun@gmail.com
 * @version 1.0
 * @copyright (c) 2014, PHSS
 * 
 */

class Database extends Functions {

    private $mysqli;
    private $isDebug = true;
    
    private $_sql = "";
    private $_limit = 0;
    private $_offset = 0;
    private $_cursor;
    public $_error = "";
    private $_log = array();
    
    private $_host = "";
    private $_user = "";
    private $_pass = "";
    private $_db   = ""; 
    private $_prefix = "";

    function __construct() {
          
    }

    public final function connect(){
        $this->_host = parent::getConfig("db_host");
        $this->_user = parent::getConfig("db_user");
        $this->_pass = parent::getConfig("db_pass");
        $this->_db = parent::getConfig("db_name");
        $this->_prefix = parent::getConfig("db_prefix");
        $this->isDebug = parent::getConfig("debug");
        if(empty($this->_prefix)){
            $this->_prefix = '#__';
        }

        $this->mysqli = new mysqli($this->_host, $this->_user, $this->_pass, $this->_db);
        if ($this->mysqli->connect_error) {
            $this->error('Unable to connect to database server', $this->mysqli->connect_error, $this->mysqli->connect_errno);
        }
        $this->mysqli->query("SET NAMES 'utf8'");
        $this->mysqli->query("SET CHARACTER SET utf8");
        $this->mysqli->query("SET CHARACTER_SET_CONNECTION=utf8");
        $this->mysqli->query("SET SQL_MODE = ''");
    }
    /*
     * $param string $sql,$offset,$limit,$prefix
     * Function setQuery() set SQL query , limit, offset, prefix 
     */

    public function setQuery($sql, $offset = 0, $limit = 0) {
        if (empty($sql)) {
            $this->error('Invalid SQL query.');
        }
        $this->connect();
        $this->_sql = $sql;
        $this->_limit = intval($limit);
        $this->_offset = intval($offset);
    }

    /**
     * @param 
     * Description its execute SQL query and return TRUE if success, FALSE otherwise. 
     * @return Boolean 
     * */
    public function query() {
        if ($this->_limit > 0 || $this->_offset > 0) {
            $this->_sql .= "\n LIMIT $this->_offset, $this->_limit";
        }
        $this->_cursor = $this->mysqli->query($this->_sql);
        $this->_log[] = $this->_sql;
        if (!$this->_cursor) {
            $this->error('Invalid SQL Query !', $this->mysqli->error, $this->mysqli->errno);
            return false;
        }
        return $this->_cursor;
    }

    /**
     * @param 
     * Description it will fetch only one row and return result as a Associative array.
     * @return Array * */
    public function loadAssoc() {
        $row = $this->_cursor->fetch_assoc();
        $this->_cursor->free();
        if(empty($row)){
            return array();
        }
        return $row;
    }

    /**
     * @param 
     * Description it will fetch only one row and return result as a stdClass Object Array.
     * * */
    public function loadObject() {
        $row = $this->_cursor->fetch_object();
        $this->_cursor->free();
        return $row;
    }

    /**
     * @param 
     * Description it will a column value.
     * * */
    public function loadResult() {
        $row = $this->loadNumeric();
        return $row[0];
    }

    /**
     * @param 
     * Description it will fetch only one row and return result as a numeric array.
     * * */
    public function loadNumeric() {
        $row = $this->_cursor->fetch_row();
        $this->_cursor->free();
        return $row;
    }

    /**
     * @param 
     * Description it will fetch all row and return result as a Associative array.
     * * */
    public function loadAssocList() {
        $rows = array();
        if (function_exists('mysqli_fetch_all')) {
            $rows = $this->_cursor->fetch_all(MYSQLI_ASSOC);
        } else {
            while ($row = $this->_cursor->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        $this->_cursor->free();
        return $rows;
    }

    /**
     * @param 
     * Description it will fetch all row and return result as a numeric array.
     * * */
    public function loadNumericList() {
        $rows = array();
        if (function_exists('mysqli_fetch_all')) {
            $rows = $this->_cursor->fetch_all(MYSQLI_NUM);
        } else {
            while ($row = $this->_cursor->fetch_row()) {
                $rows[] = $row;
            }
        }

        $this->_cursor->free();
        return $rows;
    }

    /**
     * @param 
     * Description it will fetch all row and return result as a Object array.
     * * */
    public function loadObjectList() {
        $rows = array();
        while ($row = $this->_cursor->fetch_object()) {
            $rows[] = $row;
        }
        $this->_cursor->free();
        return $rows;
    }

    public function loadResultArray($index = 0) {
        $rows = $this->loadNumericList();
        $resutArray = array();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $resutArray[$index] = $row[0];
                $index++;
            }
        }
        return $resutArray;
    }

    /**
     * @param
     * Description It Returns the total number of rows changed, deleted, or inserted by the last executed statement  
     * @return Integer
     * */
    public function getAffectedRows() {
        return $this->mysqli->affected_rows;
    }

    /**
     * @param
     * Description It Return the number of rows in statements result set.An integer representing the number of rows in result set.  
     * @return Integer 
     * */
    public function getNumRows() {
        return $this->_cursor->num_rows;
    }

    /**
     * @param 
     * Description It Return the auto generated id used in the last query.The value of the AUTO_INCREMENT field that was updated by the previous query.Returns zero if there was no previous query on the connection or if the query did not update an AUTO_INCREMENT value. 
     * @return Integer
     * */
    public function getInsertId() {
        return $this->mysqli->insert_id;
    }

    /**
     * @param String $name 
     * Description Input table name in paramater and it will delete all data to this table
     * @return Boolean  
     * */
    public function emptyTable($name) {
        if (empty($name)) {
            $this->error('Invalid SQL query!', $this->mysqli->error, $this->mysqli->errno);
            return false;
        }
        $sql = "TRUNCATE " . $name . "; ";
        $this->setQuery($sql);
        if ($this->query()) {
            return true;
        }
        $this->error("Invalid SQL query!", $this->mysqli->error, $this->mysqli->errno);
        return false;
    }

    /**
     * @param Array $names 
     * Description Input table name as array and it will delete all data to this table
     * @return Boolean  
     * */
    public function emptyMultipleTable($names) {
        if (empty($names)) {
            $this->error('Invalid SQL query!', $this->mysqli->error, $this->mysqli->errno);
            return false;
        }
        foreach ($names as $name) {
            $isEmpty = $this->emptyTable($name);
            if (!$isEmpty) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param String $name 
     * Description Input table name in paramater and it will drop this table
     * @return Boolean  
     * */
    public function dropTable($name) {
        if (empty($name)) {
            $this->error('Invalid SQL query!', $this->mysqli->error, $this->mysqli->errno);
            return false;
        }
        $sql = "DROP TABLE " . $name . ";";
        $this->setQuery($sql);
        if ($this->query()) {
            return true;
        }
        $this->error("Invalid SQL query!", $this->mysqli->error, $this->mysqli->errno);
        return false;
    }

    /**
     * @param Array $names 
     * Description Input table name as array and it will drop all given table
     * @return Boolean  
     * */
    public function dropMultipleTable($names) {
        if (empty($names)) {
            $this->error('Invalid SQL query!', $this->mysqli->error, $this->mysqli->errno);
            return false;
        }
        foreach ($names as $name) {
            $isEmpty = $this->dropTable($name);
            if (!$isEmpty) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $value
     * This function Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
     * */
    public function escape($value) {
        return $this->mysqli->real_escape_string($value);
    }

    /**
     * @param string $errormsg 
     * Description User define error message
     * @param string $mysqlError 
     * Description Mysql error message
     * @param int $mysqlErrorno 
     * Description Mysql error number
     * */
    private final function error($errormsg, $mysqlError, $mysqlErrorno) {
        $this->_error = json_encode(array('error' => $errormsg, 'sql' => $this->_sql, 'sql_error' => $mysqlError, 'sql_error_no' => $mysqlErrorno));
        if ($this->isDebug) {
            //$this->debug($this->mysqli);    
            $errormsg = '<b>Database error:</b> ' . $errormsg . "<br />\n";
            $errormsg .= '<b>MySQL query:</b> ' . $this->_sql . "<br />\n";
            $errormsg .= '<b>MySQL error:</b> ' . $mysqlError . "<br />\n";
            $errormsg .= '<b>MySQL error number:</b> ' . $mysqlErrorno . "<br />\n";
            $errormsg .= '<b>MySQL Version:</b> ' . mysqli_get_client_info() . "<br />\n";
            if (isset($this->mysqli->host_info)) {
                $errormsg .= '<b>MySQL Host:</b> ' . $this->mysqli->host_info . "<br />\n";
            }
            if (isset($this->mysqli->host_info)) {
                $errormsg .= '<b>MySQL Stat:</b> ' . $this->mysqli->stat . "<br />\n";
            }
            $errormsg .= '<b>PHP Version:</b> ' . phpversion() . "<br />\n";
            $errormsg .= '<b>Date:</b> ' . gmdate("r") . "<br />\n";
            $errormsg .= '<b>Ip:</b> ' . getenv('REMOTE_ADDR') . "<br />\n";
            $errormsg .= '<b>Script:</b> ' . getenv("REQUEST_URI") . "<br />\n";
            $errormsg .= '<b>Referer:</b> ' . getenv("HTTP_REFERER") . "<br />\n<br />\n";


            die('<span style="font-family:Courier New;font-size:12px;"><h2>SQL-DATABASE ERROR</h2>' . $errormsg . '</span>');
        }
    }

    public function __destruct() {
        if(is_object($this->mysqli)){     
            $close = $this->mysqli->close();
        }
        unset($this->mysqli);
        unset($this->isDebug);
        unset($this->_prefix);
        unset($this->_sql);
        unset($this->_limit);
        unset($this->_offset);
        unset($this->_cursor);
        unset($this->_error);
        unset($this->_log);
    }

}
