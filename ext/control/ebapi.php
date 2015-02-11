<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
#include '../../control.php';

/* 
typedefenumEB_STATE_CODE
{
  EB_STATE_OK=0
,EB_STATE_ERROR=1
,EB_STATE_NOT_AUTH_ERROR//没有权限
,EB_STATE_ACC_PWD_ERROR//帐号或密码错误
,EB_STATE_NEED_RESEND//需要重发数据
,EB_STATE_TIMEOUT_ERROR//超时错误
,EB_STATE_EXIST_OFFLINE_MSG//存在离线消息
,EB_STATE_USER_OFFLINE//用户离线状况
,EB_STATE_USER_BUSY//用户线路忙
,EB_STATE_USER_HANGUP//用户挂断会话
,EB_STATE_OAUTH_FORWARD//OAUTH转发
,EB_STATE_UNAUTH_ERROR//未验证错误
,EB_STATE_ACCOUNT_FREEZE//帐号已经冻结
,EB_STATE_PARAMETER_ERROR=15//参数错误
,EB_STATE_DATABASE_ERROR//数据库操作错误
 ,EB_STATE_NEW_VERSION//新版本
,EB_STATE_FILE_ALREADY_EXIST//文件已经存在
,EB_STATE_ACCOUNT_NOT_EXIST=20//帐号不存在
,EB_STATE_ACCOUNT_ALREADY_EXIST//帐号已经存在
,EB_STATE_ACCOUNT_DISABLE_OFFCALL//禁止离线会话
,EB_STATE_ACCOUNT_DISABLE_EXTCALL//禁止外部会话
,EB_STATE_DISABLE_REGISTER_USER=25//禁止用户注册功能
,EB_STATE_DISABLE_REGISTER_ENT//禁止企业注册功能
,EB_STATE_ENTERPRISE_ALREADY_EXIST=30//公司名称已经存在
,EB_STATE_ENTERPRISE_NOT_EXIST//没有公司信息（企业不存在）
,EB_STATE_DEP_NOT_EXIST//不存在群组（部门）
,EB_STATE_EXIST_SUB_DEPARTMENT//存在子部门
,EB_STATE_DEP_ACC_ERROR//群组或成员不存在
,EB_STATE_ENT_ACC_ERROR//企业员工成员不存在
,EB_STATE_CS_MAX_ERROR//超过客服座席最大数量
,EB_STATE_NOT_CS_ERROR//没有客服座席
,EB_STATE_EXCESS_QUOTA_ERROR//超过最大流量配额
,EB_STATE_ENT_GROUP_ERROR//企业部门
,EB_STATE_ONLINE_KEY_ERROR=40
,EB_STATE_UM_KEY_ERROR
,EB_STATE_CM_KEY_ERROR
,EB_STATE_DEVID_KEY_ERROR
,EB_STATE_APPID_KEY_ERROR
,EB_STATE_APP_ONLINE_KEY_TIMEOUT
,EB_STATE_CALL_NOT_EXIST=50
,EB_STATE_CHAT_NOT_EXIST
,EB_STATE_MSG_NOT_EXIST
,EB_STATE_RES_NOT_EXIST
,EB_STATE_NOT_MEMBER_ERROR
,EB_STATE_ATTACHMENT_NOT_EXIST
,EB_STATE_NO_UM_SERVER=60
,EB_STATE_NO_CM_SERVER
,EB_STATE_NO_VM_SERVER
,EB_STATE_NO_AP_SERVER
,EB_STATE_ENT_BLACKLIST=70//企业黑名单用户
,EB_STATE_ANOTHER_ENT_ACCOUNT//其他企业帐号
,EB_STATE_MAX_CAPACITY_ERROR//最大容量错误
,EB_STATE_NOT_SUPPORT_VERSION_ERROR//不支持当前版本
 */

class ebApiCaller
{
    /* TODO： 这个变量目前先写死，后续修改 */
    private $ebHost;
    private $ebRestAPIPrefix;
    
    public function __construct()
    {
        //$eb_url = "192.168.1.190:8081";
        $this->ebHost = "im.1pei.com.cn:8081";
        $this->ebRestAPIPrefix = "http://$this->ebHost/" . "rest.v01.";
    }
    
    //some variables for the object
    //construct an ApiCaller object, taking an
    //APP ID, APP KEY and API URL parameter
    //send the request to the API server
    //also encrypts the request, then checks
    //if the results are valid
    private function ebPostApi($apiUrl, $params)
    {
        //initialize and setup the curl handler
        $header[]="Connection:keep-alive";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookie.txt');

        //execute the request
        $result = curl_exec($ch);

        //json_decode the result
        $result = @json_decode($result);

        //if everything went great, return the data
        return $result;
    }

      private function ebCookie($apiUrl, $params)
    {
        //initialize and setup the curl handler
        $header[]="Connection:keep-alive";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_COOKIEJAR,'cookie.txt');

        //execute the request
        $result = curl_exec($ch);

        //json_decode the result
        $result = @json_decode($result);

        //if everything went great, return the data
        return $result;
    }


    /* 
     * ebweblc.authappid
     * 验证开发者APPID和APPKEY，验证成功会返回app_online_key；
     * 用户开发某些APP应用，部分接口将需要appid和验证成功app_online_key才能正常访问，具体请看接口说明，或联系恩布技术支持。
     * 输入参数   默认值（非空）  描述
     * appid                      开发者APP ID
     * apppwd                     开发者验证密码，32位小写；
     *                         apppwd=md5(appid+appkey)
     * 返回协议；
     * 参数      默认值（非空）   描述
     * code                       返回状态：见附录
     * error                      [错误]错误描述
     * app_online_key             应用ID在线KEY
     * url      可选              应用登录地址
     * appname  可选              登录应用信息
     * address  可选              登录地址信息
     * deploy_id                  服务器部署唯一编号		
     */
    public function lc_authappid($appId, $appKey)
    {
        $apppwd = md5($appId . $appKey);
        
        // create the params array, which will be the POST parameters
        $params = array(
            'appid'  => $appId,
            'apppwd' => $apppwd,
           );
        
        $ebRestApi     = "ebweblc.authappid";
        $postUrl = $this->ebRestAPIPrefix . $ebRestApi;
        $result = $this->ebPostApi($postUrl, $params);
        return $result;
    }
            
    /* 
     * ebwebum.fauth
     * 第三方应用，验证应用入口请求是否合法，验证成功返回应用入口用户信息及相关数据。
     * 输入参数   默认值（非空）描述
     * appid                    APP ID
     * app_ok                   APP online key
     * auth_id                  请求验证ID
     * from_ip                  用户IP地址
     *                          通过脚本开发语言的reqeust对象，取用户客户端IP地址，如192.168.1.200
     * 返回协议: JSON格式
     * 参数     默认值（非空）  描述
     * code                     返回状态：见附录
     * auth_id                  验证ID
     * uid                      用户ID
     * ent_id                   企业ID
     * sub_id                   订购ID
     * account                  用户帐号
     * username                 用户昵称
     * sub_level                订购等级
     * func_id                  应用功能ID
     * func_type                应用功能类型
     * extension                扩展参数：
     *                          会话参数：
     *                          gid=[群组ID]&cid=[会话ID]&fuid=[对方用户ID]&fusid=[对方标识ID]
     * post_data                HTTP POST数据
     *                          主要用于保存聊天记录数据		
     */
    public function um_fauth($appId, $appOnlineKey, $authId, $userIp)
    {
        // create the params array, which will be the POST parameters
        $params = array(
          'appid'  => $appId,
          'app_ok' => $appOnlineKey,
          'auth_id' => $authId,
          'from_ip' => $userIp,
        );

        $ebRestApi     = "ebwebum.fauth";
        $postUrl = $this->ebRestAPIPrefix . $ebRestApi;
        $result  = $this->ebPostApi($postUrl, $params);
        return $result;
    }
    
    /*
     * ebweblc.regreq
     * 说明：
     * (1) 当新注册一个个人用户或者新注册一个企业及企业管理员账户时调用。
     *    企业中新增加普通员工账户时调用ebwebum.editmember接口。 
     * (2) 需要先调用本接口获取ums url，然后在调用ebwebum.reguser接口时向该ums地址发送。
     * 
     * 输入参数 默认值（非空）  描述
     * account                 用户邮箱帐号
     *
     * 返回参数  默认值（非空） 描述   
     * code                    返回状态：见附录, 
     * error                   [错误]错误描述串
     * url                     注册UM(User Management)地址
     *    假如安装了N台服务器，当有一个用户需要注册时，先通过ebweblc.regreq接口可能获得另外一个UM的URL, 然后调用ebwebum.reguser向该UM URL发送注册请求。
     */
    public function lc_regreq($userAccount)
    {
        // create the params array, which will be the POST parameters
        $params = array(
          'account'  => $userAccount,
        );

        $ebRestApi     = "ebweblc.regreq";
        $postUrl = $this->ebRestAPIPrefix . $ebRestApi;
        $result = $this->ebPostApi($postUrl, $params);
        return $result;
    }
    
    /* 
     * ebwebum.reguser
     * 说明：
     * (1) 当新注册一个个人用户或者新注册一个企业及企业管理员账户时调用。
     *    企业中新增加普通员工账户时调用ebwebum.editmember接口。 
     * (2) 需要先调用ebweblc.regreq获取ums url，然后在调用本接口时向该ums地址发送。
     * 
     * 输入参数 默认值（非空） 描述  
     * app_id                应用ID
     * app_ok                应用在线KEY
     * account               用户帐号，邮箱格式或手机号码
     * resend_reg_mail 0     重发注册验证邮件0/1
     *                       0：普通用户注册功能
     *                       1：重发注册验证邮件
     * user_name   可选      用户名称
     * pwd         可选      用户密码
     * enc_pwd     0         密码是否已经加密0/1
     *                       0：密码未加密，需要加密
     *                       1：密码已经加密，直接保存
     * ent_name    可选      企业名称
     *                       填写：注册企业用户
     *                       不填写：注册个人用户
     * user_ext    可选      用户扩展信息
     *
     * 返回协议；
     * 参数  默认值（非空） 描述
     * code                 返回状态：0成功，错误值见附录
     * error                [错误]错误描述串
     * uid                  [成功]用户ID
     * value                [成功]注册验证码
     */
    public function um_reguser($appId, $appOnlineKey, $userAccount, $userPassword,$company)
    {
        // create the params array, which will be the POST parameters
        $params = array(
          'app_id'          => $appId,
          'app_ok'          => $appOnlineKey,
          'account'         => $userAccount,
          'pwd'             => $userPassword,
          'ent_name'        => $company
	  );
	//var_dump($params);
        $ebRestApi     = "ebwebum.reguser";
        $postUrl = $this->ebRestAPIPrefix . $ebRestApi;
        $result = $this->ebPostApi($postUrl, $params);
        return $result;
    }
    
    /*
     * ebwebum.loadorg
     * 说明：
     * (1) 该接口企业管理员账户与普通账户都可以调用。 
     *
     * 企业员工：加载企业组织结构，头像、表情等；
     * 超过2000人员工，需要分批加载企业组织结构；就是先加载部门结构，再根据部门group_code分批加载部门成员。
     * 注册用户：加载固定群组信息，头像、表情等；
     * 游客用户：加载头像，表情资源等；
     *
     * 输入参数  默认值（非空）  描述
     * uid                      用户ID
     * group_code    0          [可选]只加载某个群组（部门）信息
     *                          >0&&load_ent_dep=1&&
     *                          load_my_group=1返回群组（部门）资料
     * load_ent_dep  1          [可选]是否加载企业部门信息0/1
     * load_my_group 1          [可选]是否加载个人群组信息0/1
     * load_emp      1          [可选]是否加载成员信息0/1
     * load_image    1          [可选]是否默认表情资源，头像信息0/1
     *
     * 返回协议；
     * 参数     默认值（非空）描述
     * code                   返回状态：见附录
     * error                  [错误]错误描述
     * enterprise_info        公司信息，结构如下：
     * enterprise_code        企业代码
     * enterprise_name        企业名称
     * description            描述信息 
     */
    public function um_loadorg($uid, $groupCode="0", $loadEntDepartment="1", $loadMyGroup="1", $loadMemberInfo="1", $loadIamge="1")
    {
        // create the params array, which will be the POST parameters
        $params = array(
          'uid'         => $uid,
          'group_code'  => $groupCode,
          'load_ent_dep'=> $loadEntDepartment,
          'load_my_group'=> $loadMyGroup,
          'load_emp'    => $loadMemberInfo,
          'load_image'  => $loadIamge,
          );

        $ebRestApi     = "ebwebum.loadorg";
        $postUrl = $this->ebRestAPIPrefix . $ebRestApi;
        $result = $this->ebPostApi($postUrl, $params);
        return $result;
    }
        
    /*
     * ebwebum.editent
     * 说明：
     * (1) 该接口只能是企业管理员账户才可以调用，普通员工账户不可以调用。 
     *
     * 编辑企业资料信息
     * 输入参数  默认值（非空）  描述
     * uid                       用户ID, 这个用户ID只能是企业管理员账户
     * ent_code                  企业代码
     * phone                     电话
     * fax                       传真
     * email                     邮箱
     * url                       公司网站
     * address                   公司地址
     * description               描述备注
     * ~call_key                 呼叫来源: 根据恩布HD建议这个参数是保留参数，可以不传递。
     * ent_ext                   企业扩展数据，详见附录。
     *                           EB_ENT_EXT_DATA_NULL = 0x0,
    *                           EB_ENT_EXT_DATA_ENABLE_MODIFY_MEMBER_INFO=0x1,允许员工修改自己部门资料
     *
     * 返回协议；
     * 参数    默认值（非空）    描述
     * code                      返回状态：见附录
     * error                     [错误]错误描述 
     */
    public function um_editent($uid, $entCode, $phone, $fax, $email, $url, $address, $description)
    {
        // create the params array, which will be the POST parameters
        $params = array(
          'uid'         => $uid,
          'ent_code'    => $entCode,
          'phone'       => $phone,
          'fax'         => $fax,
          'email'       => $email,
          'url'         => $url,
          'address'     => $address,
          'description' => $description,
    //    '~call_key'   => '',
          'ent_ext'     => '0',
        );

        $ebRestApi     = "ebwebum.editent";
        $postUrl = $this->ebRestAPIPrefix . $ebRestApi;
        $result = $this->ebPostApi($postUrl, $params);
        return $result;
    }
    
    /*
     * ebwebum.editgroup
     * 说明：
     * (1) 该接口只能是企业管理员账户才可以调用，普通员工账户不可以调用。 
     *
     * 新建或编辑部门（或个人群组）资料信息；
     * 输入参数    默认值（非空）描述
     * uid                       用户ID, 这个用户ID只能是企业管理员账户
     * ent_code                  企业代码
     * group_code                部门（群组）代码，空为新建
     * group_name                部门（群组）名称，不能为空
     * parent_code               上级部门（群组）代码，空为根
     * group_type                群组类型：
     *                           0：企业部门（ent_code>0有效）
     *                           1：企业项目组（ent_code>0有效）
     *                           2：个人固定群组（ent_code=0有效）
     * phone                     电话
     * fax                       传真
     * email                     邮箱
     * url                       公司网站
     * address                   公司地址
     * description               描述备注
     *
     * 返回协议；
     * 参数       默认值（非空）描述
     * code                     返回状态：见附录
     * error                    [错误]错误描述
     * value                    [成功]部门（群组）代码
     */
    public function um_editgroup($uid, $entCode, $groupCode, $groupName, $groupType, $parentCode, $phone, $fax, $email, $url, $address, $description)
    {
        // create the params array, which will be the POST parameters
        $params = array(
          'uid'         => $uid,
          'ent_code'    => $entCode,
          'group_code'  => $groupCode,
          'group_name'  => $groupName,
          'parent_code' => $parentCode,
          'group_type'  => $groupType,
          'phone'       => $phone,
          'fax'         => $fax,
          'email'       => $email,
          'url'         => $url,
          'address'     => $address,
          'description' => $description,
        );

        $ebRestApi     = "ebwebum.editgroup";
        $postUrl = $this->ebRestAPIPrefix . $ebRestApi;
        $result = $this->ebPostApi($postUrl, $params);
        return $result;
    }
    
    /*
     * ebwebum.editmember
     * 说明：
     * (1) 该接口只能是企业管理员账户才可以调用，普通员工账户不可以调用。 
     *
     * 新建或编辑部门员工（或群组成员）资料信息；
     * 输入参数    默认值（非空）  描述
     * uid                         用户ID, 这个用户ID只能是企业管理员账户
     * group_code                  部门（群组）代码
     * member_code                 部门员工（或群组成员）代码
     *                             空为新建
     * member_account              员工（成员）帐号，邮箱格式
     * username                    用户名称
     * gender                      性别；0=未设置；1=男；2=女
     * birthday                    生日；格式：YYYYmmDD
     * job_title                   职务；如软件工程师
     * job_position                岗位；整数，默认0
     * cell_phone                  手机号码
     * fax                         传真
     * work_phone                  办公电话
     * email                       邮箱
     * address                     住址
     * description                 描述备注
     * pwd        可选             成员密码，新建帐号有效；
     *                             默认为空；使用系统配置默认密码；
     * enc_pwd     0               密码是否已经加密0/1
     *                             0：密码未加密，需要加密
     *                             1：密码已经加密，直接保存
     * app_id     0                [可选]应用ID
     *                             用于标识新建用户同步密码使用；
     * app_ok [可选]               应用在线KEY，同上；  
     *
     * 返回协议；
     * 参数     默认值（非空）    描述
     * code                       返回状态：见附录
     * error                      [错误]错误描述
     * value                      [成功]部门员工（群组成员）代码
     * uid                        [成功]用户ID
     */
    public function um_editmember($uid, $groupCode, $memberCode, $memberAccount, $userName, $pwd, $gender, $email='', $birthday='', $jobTitle='', $jobPosition='', $cellPhone='', $fax='', $workPhone='', $address='', $description='')
    {
        // create the params array, which will be the POST parameters
        $params = array(
          'uid'          => $uid,
          'group_code'   => $groupCode,
          'member_code'  => $memberCode,
          'member_account' => $memberAccount,
          'username'    => $userName,
          'gender'      => $gender,
          'birthday'    => $birthday,
          'job_title'   => $jobTitle,
          'job_position'=> $jobPosition, 
          'cell_phone'  => $cellPhone,
          'fax'         => $fax,
          'work_phone'  => $workPhone,
          'email'       => $email,
          'url'         => $url,
          'description' => $description,
          'pwd'         => $pwd,
        );

        $ebRestApi     = "ebwebum.editmember";
        $postUrl = $this->ebRestAPIPrefix . $ebRestApi;
        $result = $this->ebPostApi($postUrl, $params);
        return $result;
    }
      
    /*
     * ebwebum.deletemember
     * 说明：
     * (1) 该接口只能是企业管理员账户才可以调用，普通员工账户不可以调用。 
     *
     * 删除部门员工（或群组成员）资料信息；
     * 输入参数   默认值（非空）  描述
     * uid                        用户ID, 这个用户ID只能是企业管理员账户
     * member_code                部门员工（或群组成员）代码
     * delete_account  1          删除帐号信息0/1
     *                            0：不删除帐号信息, 原因在于一个帐号可能会属于多个部门员工
     *                            1：删除帐号信息
     * 返回协议；
     * 参数      默认值（非空）  描述
     * code                      返回状态：见附录
     * error                     [错误]错误描述  
     * 
     */
    public function um_deletemember($uid, $memberCode, $memberAccount, $deleteAccount)
    {
        // create the params array, which will be the POST parameters
        $params = array(
          'uid'            => $uid,
          'member_code'    => $memberCode,
          'delete_account' => $deleteAccount,
          );

        $ebRestApi = "ebwebum.deletemember";
        $postUrl = $this->ebRestAPIPrefix . $ebRestApi;
        $result = $this->ebPostApi($postUrl, $params);
        return $result;
    }
}

 /*
     * ebwebum.online
     * 说明：
     * (1)获取Cookie 
     *
     * 
     * 输入参数   默认值（非空）  描述
     * uid                        用户ID, 这个用户ID只能是企业管理员账户
     * online_key 默认值（非空）  online_key
     * 返回协议；
     * 参数       默认值（非空）  描述
     * code                      返回状态：见附录
     * error                     [错误]错误描述  
     * 
     */


    public function um_online($uid,$onlineKey)
    {
        $params = array(
            'uid'        =>$uid;
            'online_key' =>$onlineKey;   
        );

        $ebRestApi = "ebwebum.online";
        $postUrl = $this->ebRestAPIPrefix . $ebRestApi;
        $result = $this->ebCookie($postUrl, $params);
    }