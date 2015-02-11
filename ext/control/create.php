<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../control.php';	
include 'ebapi.php';


class myUser extends user
{
	public function create()
    {                          
        $ebApi = new ebApiCaller();
        if(!empty($_POST))     
        {    
           

        
            //a("app_id:".$this->config->entboost->appId ."app_ok:".$this->session->onlineKey . "account:" .$_POST['account'] .  "pwd:".$_POST['password1']);exit;
            
            if($this->user->create()); 

            $registResult=$ebApi->um_editmember($this->config->entboost->appId,$this->session->onlineKey,$_POST['account'],$_POST['password1'],$this->config->company->name);
            a($registResult);exit;
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError())); 
            /* Go to the referer. */    
            $this->send( array('result' => 'success', 'locate'=>inlink('admin')) );
        }                      

        $this->view->treeMenu = $this->loadModel('tree')->getTreeMenu('dept', 0, array('treeModel', 'createDeptAdminLink'));
        $this->view->depts    = $this->tree->getOptionMenu('dept');
        $this->display();      
        $orgResult=$ebApi->um_loadorg($this->session->uid);
        a($orgResult);exit;
    }
 }
