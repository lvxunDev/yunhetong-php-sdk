<?php

/**
 * 用户的实体类
 * Created by PhpStorm.
 * User: SeanWu
 * Date: 2016/5/23
 * Time: 19:13
 */
class User
{
    public $appId;    // 应用Id
    public $appUserId; // 用户在第三方应用的唯一标识
    public $userType;// 用户类型
    public $cellNum; //  电话号码
    public $userName;// 用户名称
    public $certifyType; // 实名认证类型
    public $certifyNumber; // 实名认证号码
    public $createSignature = false;  // 是否由云合同平台自动创建签名,只在第一次导入的时候生效
}