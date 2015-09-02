<?php
public function createHaiAuReport(){
               // Display the template
                $rp = new ReportsHelper('mysql');                
                $rp->setTable('haiau_timesheet');
                $filter = new JObject();
                $filter->dateFrom = JRequest::getVar('from',date("Y-m-d",strtotime("-1 month")));
                $filter->dateTo = JRequest::getVar('to',date("Y-m-d"));
                $this->filter = &$filter;
                $rp->setWhere(" where date_spent >= '".$this->filter->dateFrom."' and date_spent <= '".$this->filter->dateTo."'");
                $rp->prepareData();
                //var_dump($rp->totalRecord);                
                $rp->setCollAverage(true);
                //$rp->setRowAverage(true);
                //$rp->setRowPercentage(true);
                //$rp->setMedian(true);
                $rp->setHeader('user_regist_time');
               // var_dump($rp->headerArray);     
                $rp->setSide('client_id');         
                
                $rp->addCellContents('tvalue', 'sum');
                //$rp->addCellContents('tvalue', 'count');
                $rp->setMap('client_id', 'client_name','%');
                $rp->setMap('date_spent','date_spent','%');
                $rp->setMap('user_regist_time','agent_name');
                $rp->setDecimal(3);
                return $rp;
        }
		
?>