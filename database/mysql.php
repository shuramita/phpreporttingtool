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
class Mysql {
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
          
            
        }
    protected function connect(){
          
    }
    protected function close(){
        
    }
    public  function setQuery($query){
                      
    }
    public function execute(){
               
    }
    public function loadObjectList($key = '', $class = 'stdClass'){
                    
        }
    public function loadAssocList(){
       
    }
    
}
