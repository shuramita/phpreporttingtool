<?php
/*------------------------------------------------------------------------
# reportool.php - Reporting
# ------------------------------------------------------------------------
# author    Tam Nguyễn
# copyright Copyright (C) 2014. All Rights Reserved
# license   GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
# website   www.haiausolution.com
-------------------------------------------------------------------------*/
//If you are using sqlserver, you need uncomment line bellow
//require_once 'database/sqlserver.php';
/**
 * Clients component helper.
 */
class ReportsHelper
{
        public $headerArray = array();
        public $sideArray = array();
        protected $vdata;
        protected $db;
        public $dataSet;
        public $totalRecord;
        public $totalVariable;
        public  $collCell;
        public $typeCell;
        public $map = array();
        public $rowTotal = true;
        public $collTotal = true;
        public $collAverage = false;
        public $rowAverage = false;
        public $rowPercent = false;
        protected $number_of_decimal = 2;
        protected $colMedian = false;
        public $number_of_statictis_column = 0;        
        public $dbType;
        public $chartProps;
        public $where;
        public $reset_number_of_variable_column;
        public function __construct($databaseType='mysql'){            
            $this->dbType = $databaseType;
            if($this->dbType == 'sqlserver') {
                $this->db = new Sqlserver();
            }else{
                $this->db = JFactory::getDbo();            
            }
            
        }
        
        
        public function setMedian($flag){
            $this->colMedian = $flag;
        }
        public function setDecimal($number_of_decimal){
            $this->number_of_decimal = $number_of_decimal;
        }
        public function setNumberFormat($number, $thousands_sep = ',', $dec_point  = '.'){            
            return number_format($number,$this->number_of_decimal,$dec_point ,$thousands_sep);
        }
        public function setCollAverage($flag){
            $this->resetHeader();
            $this->collAverage = $flag;
        }
        public function setRowPercentage($flag){
            $this->rowPercent = $flag;
        }
        public function setRowAverage($flag){
            $this->resetSide();
            $this->rowAverage = $flag;
        }
        protected function resetSide(){
            $this->sideArray = array();
            $this->buildSide();
        }

        public function resetHeader(){
            $this->headerArray = array();
            $this->buildHeader();
        }
        
        public function prepareData(){
            $this->db->setQuery('select * from '.$this->vdata.$this->where);
            $this->dataSet = $this->db->loadAssocList();
            $this->totalRecord = count($this->dataSet);
        }
        protected function caculateCell($colx,$coly,$x,$y){
            switch ($this->typeCell) {
                case 'sum':
                    $result = $this->sum($colx,$coly,$x,$y,$this->collCell);
                    break;
                case 'count':
                    $result = $this->count($colx,$coly,$x,$y,$this->collCell);
                    break;
                default:
                    break;
            }
            return $result;           
                    
        }
        protected function count($colx,$coly,$x,$y,$colSum){
            $countvalue = 0;
            $j=0;            
            for($j=0;$j < $this->totalRecord; $j++){                 
                if($this->dataSet[$j][$colx] == $x && $this->dataSet[$j][$coly]==$y && $this->dataSet[$j][$colSum] !== '') {                    
                    $countvalue = $countvalue + 1;
                }
            }
            return $countvalue;
        }
        protected function sum($colx,$coly,$x,$y,$colSum){
            $sumValue = 0;
            $j=0;            
            for($j=0;$j < $this->totalRecord; $j++){                 
                if($this->dataSet[$j][$colx] == $x && $this->dataSet[$j][$coly]==$y) {
                    
                    $sumValue = $sumValue + $this->dataSet[$j][$colSum];
                }
            }
            return $sumValue;
        }
        public function mmmr($array, $output = 'mean'){ 
            if(!is_array($array)){ 
                return FALSE; 
            }else{ 
                switch($output){ 
                    case 'mean': 
                        $count = count($array); 
                        $sum = array_sum($array); 
                        $total = $sum / $count; 
                    break; 
                    case 'median': 
                        rsort($array); 
                        $middle = round(count($array) / 2); 
                        $total = $array[$middle-1]; 
                    break; 
                    case 'mode': 
                        $v = array_count_values($array); 
                        arsort($v); 
                        foreach($v as $k => $v){$total = $k; break;} 
                    break; 
                    case 'range': 
                        sort($array); 
                        $sml = $array[0]; 
                        rsort($array); 
                        $lrg = $array[0]; 
                        $total = $lrg - $sml; 
                    break; 
                } 
                return $total; 
            } 
        } 
        public function setTable($tableName){
            $this->vdata = $tableName;
        }
        public function setHeader($hString){
            $variables = explode("+", str_replace(" ", "", $hString));
            if (count($variables) > 0 ) {
                foreach($variables as $value){
                      $sql = 'select distinct '.$value.' from '.$this->vdata.$this->where.' and '.$value.' is not null' ;
                      $this->db->setQuery($sql);                      
                      $this->headerArray = array_merge($this->headerArray,$this->db->loadAssocList());
                      if($this->collTotal) {
                          $this->headerArray = array_merge($this->headerArray,array(array("col-total"=>"TOTAL")));
                          $this->number_of_statictis_column = $this->number_of_statictis_column + 1;
                      }
                      //Kiểm tra nếu tính giá trị trung bình, set trung bình và header
                      if ($this->collAverage){                          
                          $this->headerArray = array_merge($this->headerArray,array(array("col-avg"=>"Average")));
                          $this->number_of_statictis_column = $this->number_of_statictis_column + 1;
                      }
                      if($this->colMedian) {
                          $this->headerArray = array_merge($this->headerArray,array(array("col-median"=>"Median")));
                          $this->number_of_statictis_column = $this->number_of_statictis_column + 1;
                      }
                      if($this->rowPercent) {
                          $arrayPercentage = array();
                          foreach($this->db->loadAssocList() as $key=>$val) {
                              foreach ($val as $k=>$v) {
                                  $arrayPercentage[][$k.'-percent']= $v;
                              }                              
                          } 
                          
                        $this->headerArray = array_merge($this->headerArray,$arrayPercentage);
                         
                      }
                      
                      
                      
                      
                }
            }else{
                      $sql = 'select distinct '.$variables.'from '.$this->vdata;
                      $this->db->setQuery($sql);
                      $this->headerArray = $this->db->loadAssocList();
            }            
            
        }
        
        public function setSide($hString){
            $variables = explode("+", str_replace(" ", "", $hString));
            if (count($variables) > 0 ) {
                foreach($variables as $value){
                      $sql = 'select distinct '.$value.' from '.$this->vdata.$this->where;
                      $this->db->setQuery($sql);
                      $this->sideArray = array_merge($this->sideArray,$this->db->loadAssocList());
                      if($this->rowTotal) {
                          $this->sideArray = array_merge($this->sideArray,array(array("row-total"=>"TOTAL")));
                      }
                      if($this->rowAverage) {
                          $this->sideArray = array_merge($this->sideArray,array(array("row-avg"=>"AVERAGE")));
                      }
                }
            }else{
                      $sql = 'select distinct '.$variables.'from '.$this->vdata;
                      $this->db->setQuery($sql);
                      $this->sideArray = $this->db->loadAssocList();
            }
            
            
        }        
        public function setMap($key,$value,$sig=''){            
            $this->db->setQuery('select distinct '.$key.' as code, '.$value.' as label from  '.$this->vdata);
            $results = $this->db->loadObjectList();
            foreach($results as $v) {
                $this->map[$key][$v->code] = $v->label;
                if($this->rowPercent) {
                    $this->map[$key.'-percent'][$v->code] = $v->label.'<br /> ('.$sig.')';
                }
            }
            
        }
        public function buildHeaderTable(){
            echo "<thead>";
            echo '<tr class="row-header">';
                    echo '<th id="lineDescription">/</th>';
                    $i=0;
                    foreach($this->headerArray as $hk => $hv ) {      
                            foreach ($hv as $key=>$val) {
                                echo  '<th class="column col-'.$i.'" id="lineValues'.$i.'">';                                
                                echo $this->setLabel($val,$key);//.'<br />'.$key;                                
                                echo '</th>';
                            }
                            $i++;
                    }                            
                    echo  '</tr>';
            echo '</thead>';
        }
        public function setLabel($val,$variable){
            if( isset($this->map[$variable][$val])) { 
                return $this->map[$variable][$val];
                
            } else { 
                return $val;
                };
        }
        public function setChartProperty(){
                $this->chartProps = 'id="AttendancePercentages"
    		summary="pieDescription" 
    		data-attc-createChart="true"
    		data-attc-colDescription="lineDescription" 
    		data-attc-colValues="lineValues3" 
    		data-attc-location="AttendancePercentagesPie" 
    		data-attc-hideTable="true" 
    		data-attc-type="line"';
    		$this->chartProps .='data-attc-googleOptions=\'{"is3D":true}\'';
    		$this->chartProps .= 'data-attc-controls=\'{"showHide":true,"create":true,"chartType":true}\'';
        }
        public function setWhere( $where ) {
            $this->where = $where;
        }
        public function drawChart(){
            $doc = JFactory::getDocument();
            $doc->addScript('//www.google.com/jsapi');
            $doc->addScript( JURI::base().'components/'.JRequest::getVar('option').'/helpers/chart/attc.googleCharts.js');
            $doc->addStyleSheet(JURI::base().'components/'.JRequest::getVar('option').'/helpers/chart/attc.css');
            $doc->addScriptDeclaration("jQuery(document).ready(function ($){
                        $('[data-attc-createChart]').attc();
                });");
            echo '<div id="AttendancePercentagesPie"></div>';
        }
        public function drawTable(){
            //echo count($this->headerArray);
            
            echo '<table class="report" border=1 '.$this->chartProps.'>';
            $this->buildHeaderTable();
            echo '<tbody>';
            $cvalue = array();
            $cvalue[] = array();
            $i=0;
            foreach($this->sideArray as $sk => $sv) {                    
                        echo '<tr class="row-data">';                    
                        $coly = ""; // reset side lable every time
                        $y = ""; // reset side value every time
                        $resetValue = false;
                        foreach($sv as $key=>$val){
                            if($key=='row-total') {
                                echo '<td class="side row-total">';
                            }elseif($key=='row-avg'){
                                echo '<td class="side row-average">';
                            }else{
                                echo '<td class="side">';
                            }
                            echo $this->setLabel($val,$key);
                            
                            echo'</td>';
                            $coly = $key;
                            $y = $val;
                        }
                        if($coly == 'row-total') {                      
                            $number_of_variable_column = 0;
                            for($j=0; $j<count($this->headerArray); $j++) {
                                echo '<td class="row-total">';          
                                $key = array_keys($this->headerArray[$j]);                                
                                $temTotal = array_sum(array_column($cvalue, $j));                                
                                if(strpos($key[0], 'percent')){ // check collum header has keyword percent
                                    // process percent for total
                                    
                                    // var_dump(array_column($cvalue, $j-2-$number_of_variable_column));
                                    //echo $number_of_variable_column.'--';
                                    //echo array_sum(array_column($cvalue, $j-2-$number_of_variable_column)).'=';
                                    //echo array_sum(array_column($cvalue, $number_of_variable_column));
                                   
                                    echo round( array_sum(array_column($cvalue, $j-$this->number_of_statictis_column-$number_of_variable_column)) *100 / array_sum(array_column($cvalue, $number_of_variable_column)) , $this->number_of_decimal) ;//,2)*100;    
                                    //echo '#NA';          
                                    $this->reset_number_of_variable_column = true;
                                   
                                }elseif ($key[0] == 'col-avg') {
                                        echo $this->setNumberFormat($temTotal);
                                        
                                }elseif($key[0] == 'col-total'){
                                        echo $this->setNumberFormat($temTotal);
                                        
                                }elseif ($key[0] == 'col-median') {
                                        //echo $temTotal;
                                        
                                }else{
                                        if( $this->reset_number_of_variable_column ) {
                                            $number_of_variable_column = 0;
                                            $this->reset_number_of_variable_column = false;
                                        }else{
                                                    $this->reset_number_of_variable_column = false;
                                            }
                                        $number_of_variable_column = $number_of_variable_column+1;
                                        echo $this->setNumberFormat($temTotal);
                                        
                                }
                                echo '</td>';                                
                            }
                            $resetValue = true;
                        }elseif($coly == 'row-avg'){
                            for($j=0; $j<count($this->headerArray); $j++) {
                                echo '<td class="row-average">';
                                $arrayValue = array_column($cvalue, $j);
                               
                                $key = array_keys($this->headerArray[$j]);
                                if(strpos($key[0], 'percent')){
                                    // process percent for average
                                    //echo '#NA';
                                    echo round( array_sum(array_column($cvalue, $j-$this->number_of_statictis_column-$number_of_variable_column)) *100 / array_sum(array_column($cvalue, $number_of_variable_column)) , $this->number_of_decimal) ;//,2)*100;    
                                }elseif ($key[0] == 'col-median') {
                                        //echo $temTotal;
                                        
                                }else{
                                    if(count($arrayValue) > 0) {                                                                     
                                         $temAvg =  round((array_sum($arrayValue)/count($arrayValue)),$this->number_of_decimal);
                                         echo $this->setNumberFormat($temAvg);      
                                    }       
                                }
                                //var_dump($cvalue[$j]);
                                echo '</td>';                                
                            }   
                           $resetValue = true;
                            
                        }else{
                           if($resetValue){
                               $cvalue = array();
                               $cvalue[] = array();
                               $resetValue = false;
                           }else{
                               $resetValue = false;
                           }
                            $cTotal = 0;                            
                            $collumnCount = 0;
                            foreach($this->headerArray as $hk => $hv ) {
                                $colx = "";                            
                                foreach($hv as $key=>$val) {
                                    $colx = $key;
                                    $x = $val;
                                    if($colx == 'col-total') { 
                                        echo '<td class="cell total">'; 
                                        echo $this->setNumberFormat($cTotal);
                                        $cvalue[$i][] = $cTotal;    
                                        //echo array_sum($cvalue[$i]);
                                        echo '</td>';
                                    }elseif($colx == 'col-avg'){
                                      echo '<td class="cell avg">'; 
                                        $average = round($cTotal/$collumnCount,$this->number_of_decimal);
                                        echo $this->setNumberFormat($average);
                                        $cvalue[$i][] = $average;                                         
                                      echo '</td>';                                        

                                    }elseif($colx == 'col-median'){
                                        echo '<td class="cell median">';
                                        $row_value =  $cvalue[$i];
                                        
                                        for($k=0;$k<$this->number_of_statictis_column-1;$k++){
                                            unset($row_value[ count($row_value)-1 ]);                                            
                                        }
                                        echo $this->mmmr($row_value,'median');
                                        echo '</td>';
                                        //$this->mmmr(, 'median')
                                    }elseif(strpos($colx, 'percent')){
                                        echo '<td class="cell percent">';
                                           // echo 'h'.$cTotal;
                                            if($cTotal > 0){
                                                if($colx == 'col-total') { echo "col percent";}// process value for total row
                                                elseif($colx == 'col-avg') {echo "avg percent";} // process value for avg row
                                                else{
                                                    $temPercent =  round((($this->caculateCell(str_replace('-percent', "",$colx), $coly, $x, $y))/$cTotal)*100,$this->number_of_decimal);
                                                    $cvalue[$i][] = $temPercent;
                                                    echo $this->setNumberFormat($temPercent);
                                                }
                                            }else{
                                                echo 0;
                                            }
                                            
                                        echo '</td>';
                                        $resetValue = true;
                                    }else{
                                        if($resetValue){
                                            //reset total
                                            $cTotal=0;
                                            //reset count 
                                            $collumnCount = 0;
                                            $resetValue = false;
                                        }else{
                                            $resetValue = false;
                                        }
                                        echo '<td class="cell">'; 
                                        $cvalue[$i][] =  $this->caculateCell($colx, $coly, $x, $y);
                                        echo $this->setNumberFormat(end($cvalue[$i]));
                                        $cTotal = $cTotal + end($cvalue[$i]);                                             
                                        $collumnCount = $collumnCount+1;                                        
                                        echo '</td>';
                                    }

                                    
                                }

                               
                            }                            
                            $i++;
                            
                        }
                        
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            //var_dump($cvalue);
        }
        
        public function addCellContents($collCellName,$typeCell){
            $this->collCell = $collCellName;
            $this->typeCell = $typeCell;
        }
        
        protected function buildHeader(){
            
        }
        
        protected function buildSide(){
            
        }
        
}

if (!function_exists('array_column')) {
    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }
        if (!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }
        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }
        return $resultArray;
    }
}
?>