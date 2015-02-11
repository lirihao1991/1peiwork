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

    /**
     * Entboost IM Login.
     * 
     * @param string $referer 
     * @access public
     * @return void
     */
    public function eblogin($referer = '')
    {
        $this->setReferer($referer);

        /*  Get parameters from URL get: authid, fk, color, ums */
        $authid = $this->get->authid;
        $fk     = $this->get->fk;
        $color  = $this->get->color;
        $ums    = $this->get->ums;

        //if (!$this->session->authid)
        {
            $this->session->set('authid', $authid);
        }

        /*  Get user IP */
        $userip = commonModel::getIP();
        //if (!$this->session->userIp)
        {
            $this->session->set('userip', $userip);
        }

        // validate fk
        $md5_fk = md5("[eb_fk_v1]$authid;$userip;" . $this->config->entboost->appId);
        if ($fk == $md5_fk)
        {
            $ebApi = new ebApiCaller();


            /* Get app online key */
            $ebResult = $ebApi->lc_authappid($this->config->entboost->appId, $this->config->entboost->appKey);
            if ($ebResult->code != 0)
            {
                a("Error when call authappid: $ebResult->code $ebResult->error\n"); 
                exit;
            }
            
            //if (!$this->session->onlineKey)
            {
                $this->session->set('onlineKey', $ebResult->app_online_key);
            }

            /* Get user account */
            $ebResult = $ebApi->um_fauth($this->config->entboost->appId, $this->session->onlineKey, $this->session->authid, $userip);
   

            if ($ebResult->code != 0)
            {
                a("Error when call authappid: $ebResult->error\n");
                exit;
            }
            
            //if (!$this->session->uid)
            {
                $this->session->set('uid', $ebResult->uid);
            }
            //if (!$this->session->ent_id)
            {
                $this->session->set('ent_id', $ebResult->ent_id);
            }
            //if (!$this->session->account)
            {
                $this->session->set('account', $ebResult->account);
            }
            
            $ebCookie = $ebApi->um_online($ebResult->uid,$this->session->onlineKey);

            $this->user->login($ebResult->account);


            //relocation when authorize completed
            echo ("<script> location.href = $this->createLink('index', 'index')</script>");

            if (!$this->session->random)
                $this->session->set('random', md5(time() . mt_rand()));

            $this->view->title = $this->lang->user->login->common;
            $this->view->referer = "/www/ranzhi/sys/";
            $this->display();
        }
        // Forbidden message for wrong login
        else
        {
            echo ("Access Forbidden       " . $md5_fk . "!=" . $fk );
            exit;
        }
    }
    
}
