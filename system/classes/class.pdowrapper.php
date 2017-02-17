<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

class pdoWrapper
{
    private $dbConnection;
    public $rowCount;
    private $query = array();
    public $stats = array('total' => 0, 'slowest' => 0, 'fastest' => 999, 'connection' => 0);
    public $debug = false;
    public $queryCurrent = 0;
    private $queryStr = '';

    public function escape($in)
    {
        return $in;
    }
    
    function __time()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    function __construct($hostname, $username, $password, $database, $port = 3306)
    {
        /* open the connection to the database server and database */
        $s = $this->__time();
        
        try
        {
            $con = "mysql:dbname=" . $database . ";host=" . $hostname . ";port=" . (string)$port;
            $this->dbConnection = new PDO($con, 
                                          $username, 
                                          $password);
        } 
        catch(Exception $e) 
        {
            $this->pdoException($e->getMessage());
            exit;
        }
        
        $this->stats['connection'] = $this->__time() - $s;
        
        $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function query($query, $data = array())
    {
        /* run query with data */
        $s = $this->__time();
        $this->queryCurrent++;
        
        try
        {
            $this->prep = $this->dbConnection->prepare($query);
            $this->rowCount  = $this->prep->execute($data);
            
            $this->prep->setFetchMode(PDO::FETCH_ASSOC);
            
            $this->queryStr = $query;
            
            if ($this->debug == true)
            {
                $this->stats['query'][$this->queryCurrent] = array("query" => $query,
                                                                                       "data"   => $data,
                                                                                       "rows"   => $this->rows(),
                                                                                       "time"   => $this->__time() - $s);
                $this->stats['total'] += $this->stats['query'][$this->queryCurrent]['time'];
                
                $this->stats['slowest'] = ($this->stats['slowest'] < $this->stats['query'][$this->queryCurrent]['time']) ?  $this->stats['query'][$this->queryCurrent]['time'] : $this->stats['slowest'];
                $this->stats['fastest'] = ($this->stats['fastest'] > $this->stats['query'][$this->queryCurrent]['time']) ?  $this->stats['query'][$this->queryCurrent]['time'] : $this->stats['fastest'];
            }
            
            return $this->prep; // can return false
        }
        catch (Exception $e) 
        {
            $this->pdoException($e->getMessage());
            exit;
        }
    }

    function fetch($single = false)
    {
        /* fetch all data */
        if ($single == false)
        {
            return $this->prep->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            return $this->prep->fetch(PDO::FETCH_ASSOC);
        }
    }

    function rows()
    {
        /* retrieve number of found rows */
        
        return $this->prep->rowCount();
    }

    function counter($table, $field, $where = '', $data = array())
    {
        if (!is_array($data))
        {
            $data[] = $data;
        }
        
        $sql = "SELECT COUNT(`" . $field . "`) AS counter
                FROM `" . $table . "` " . 
                (($where != '') ? " WHERE " .  $where : '');
        $this->query($sql,
                     $data);
        
        if ($this->rows() > 0)
        {
            $d = $this->fetch(true);
            return $d['counter'];
        }
        else
        {
            return 0;
        }
    } 
    
    function result($truefalse = true)
    {
        /* result for used more then 0 yes or no */
        if ($truefalse == true)
        {
            return ($this->prep->rowCount() > 0) ? false : true;
        }
        else
        {
            return $this->prep->rowCount();
        
        }
    }

    function insert_id()
    {
        /* fetch last insert id */
        $lastID = $this->dbConnection->lastInsertId();
        if ($this->debug == true)
        {
            $this->stats['query'][$this->queryCurrent]['insertid'] = $lastID;
        }
        return $lastID;
    }

    function affected()
    {
        /* affected rows alternative */
        
        try
        {
            $count = $this->prep->rowCount();
            if ($this->debug == true)
            {
                $this->stats['query'][$this->queryCurrent]['affected'] = $count;
            }
            
            return $count;
        }
        catch (Exception $e) 
        {
            $this->pdoException($e->getMessage());
            exit;
        }    
    }
    
    function pdoException($message) 
    {
        $pdo_error = array('error'           => 'PDO-SQL-ERROR',
                           'PDO_error'       => $message,
                           'SQL'             => $this->queryStr,
                           'debug_backtrace' => debug_backtrace());
        
        die("Error: " . $message);
        
        // moet nog anders maar komt later wel!
        exit();
    }
}
?>