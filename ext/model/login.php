<?php
	public function login($account)
	{
		$user = $this->identify($account);
        if(!$user) return false;

        $user->rights = $this->authorize($user);
        $this->session->set('user', $user);
        $this->app->user = $this->session->user;

    return true;
}


/**
 * Get user by his account.
 * 
 * @param mixed $account
 * @access public
 * @return object           the user.
 */
public function getByAccount($account)
{
    return $this->dao->select('*')->from(TABLE_USER)
//        ->beginIF(validater::checkEmail($account))->where('email')->eq($account)->fi()
//        ->beginIF(!validater::checkEmail($account))->where('account')->eq($account)->fi()
        ->beginIF(validater::checkEmail($account))->where('account')->eq($account)->fi()
        ->fetch('', false);
}
