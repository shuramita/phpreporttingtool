<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sqlserver
 *
 * @author Tam Nguyen
 */
class Sqlserver {
    //put your code here
    protected $svr;
    /* Get UID and PWD from application-specific files.  */
    protected $uid;
    protected $pwd;
    protected $dbname;
    protected $connectionInfo;  
    protected $conn;    
    protected $info;
    protected $stmt;
    
    public function __construct($database="1MyCorp",$serverName="PERSONAL-PC\SQLEXPRESS",$userid="sa",$password=""){                
            $this->dbname =  $database;
            $this->uid = $userid;
            $this->pwd = $password;
            $this->svr = $serverName;
            $this->info = array("UID"=>$this->uid,
                                    "PWD"=>$this->pwd,
                                    "Database"=>$this->dbname,"CharacterSet" => "UTF-8");
            $this->conn = sqlsrv_connect( $this->svr, $this->info);
            if( $this->conn === false )
                {
                     echo "Unable to connect.</br>";
                     die( print_r( sqlsrv_errors(), true));
                }
            
        }
    protected function connect(){
           $this->conn = sqlsrv_connect( $this->svr, $this->info);
    }
    protected function close(){
         if($this->conn) {
             sqlsrv_close($this->conn);
         }
    }
    public  function setQuery($query){
            $this->query = $query;            
    }
    public function execute(){
          return sqlsrv_query($this->conn,$this->query);          
    }
    public function loadObjectList($key = '', $class = 'stdClass'){
              //  $this->connect();
		$array = array();
                $stmt = $this->execute();
                if( $stmt === false ) { return null;}
		// Get all of the rows from the result set as objects of type $class.
                while( $obj = sqlsrv_fetch_object( $stmt)) {
                        $array[] = $obj;
                }
                //   $this->close();
                //var_dump($array);
		return $array;                
        }
    public function loadAssocList(){
                $this->connect();
		$array = array();
                $stmt = $this->execute();
                if( $stmt === false ) { return null;}
		// Get all of the rows from the result set as objects of type $class.
                while( $row = sqlsrv_fetch_array( $stmt,SQLSRV_FETCH_ASSOC)) {    
                       // remove index row
                        foreach ($row as $r=>$v) {
                            if(gettype($v) == 'object' ) {                                
                                if(get_class($v) == "DateTime") {
                                        $newValue  = $row[$r];                                        
                                        $row[$r] = $newValue->format('d-m-Y'); 
                                }
                            }
                        }
                        $array[] = $row;                        
                }
            //    $this->close();            
		return $array;  
    }
    
}
