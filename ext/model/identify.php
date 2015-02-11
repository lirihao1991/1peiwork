<?php
	public function identify($account)
	{
		if(!$account) return false;

        /* First get the user from database by account or email. */
        $user = $this->dao->select('*')->from(TABLE_USER)
            ->where('deleted')->eq('0')
            ->beginIF(validater::checkEmail($account))->andWhere('account')->eq($account)->fi()
            ->fetch();

        /* Then check the password hash. */
        if(!$user) return false;

        /* Can not login before ten minutes when user is locked. */
        if($user->locked != '0000-00-00 00:00:00')
        {
            $dateDiff = (strtotime($user->locked) - time()) / 60;

            /* Check the type of lock and show it. */
            if($dateDiff > 0 && $dateDiff <= 0)
            {
                $this->lang->user->loginFailed = sprintf($this->lang->user->locked, '10' . $this->lang->date->minute);
                return false;
            }
            elseif($dateDiff > 10)
            {
                $dateDiff = ceil($dateDiff / 60 / 24);
                $this->lang->user->loginFailed = $dateDiff <= 30 ? sprintf($this->lang->user->locked, $dateDiff . $this->lang->date->day) : $this->lang->user->lockedForEver;
                return false;
            }
            else
            {
                $user->fails  = 0;
                $user->locked = '0000-00-00 00:00:00';
            }
        }

        /* The password can be the plain or the password after md5. */

        /* Update user data. */
        $user->ip     = $this->server->remote_addr;
        $user->last   = helper::now();
        $user->fails  = 0;
        $user->visits ++;

        /* Update password when create password by oldCreatePassword function. */
        $this->dao->update(TABLE_USER)->data($user)->where('account')->eq($account)->exec();

        $user->realname  = empty($user->realname) ? $account : $user->realname;
        $user->shortLast = substr($user->last, 5, -3);
        $user->shortJoin = substr($user->join, 5, -3);
        unset($_SESSION['random']);

        /* Return him.*/
        return $user;

	}
	

?>