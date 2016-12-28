<?php

/**
 * 合同基本信息实体类
 * Created by PhpStorm.
 * User: Seanwu
 * Date: 2016/5/30
 * Time: 11:25
 */
class Contract
{
    public $title;    // 合同标题
    public $appId;    // 应用Id
    public $overtime;
    public $defContractNo;  // 自定义合同号
    public $templateId;     // 合同模板Id
    public $params;      // 合同模板相关占位符，array类型
}