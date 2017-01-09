<?php

/**
 * 用户的实体类
 * @author 浣溪沙
 */
class LxUser
{
    /**
     * @var String 应用id
     */
    public $appId;
    /**
     * @var String 用户在第三方应用平台的唯一标识，由平台各自管理，不能为空，不能大于 200 个字符
     */
    public $appUserId;

    /**
     * @var int 用户类型,1是个人，2是企业
     */
    public $userType; //
    /**
     * @var String 电话号码，为1开头的11为数字
     */
    public $cellNum;
    /**
     * @var String 用户名称
     */
    public $userName;
    /**
     * @var int 实名认证类型1身份证2护照3军官证4营业执照5组织机构代码证
     */
    public $certifyType;
    /**
     * @var String 用户实名认证时候的证件号码，可以是对应的身份证、营业执照、组织机构代码证或者其他证件号码，原则上不能大于 30 个字符
     */
    public $certifyNumber;
    /**
     * @var int 是否自动创建签名，在导入用户并且当值为 1 时，会为导入的用户自动创建签名，0的话就不会，这个值只在用户第一次导入时有效
     */
    public $createSignature = 0;
}