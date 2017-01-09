<?php

/**
 * 合同基本信息实体类
 * @author 浣溪沙
 */
class Contract
{
    /**
     * @var String 合同标题
     */
    public $title;    // 合同标题
    /**
     * @var String 应用 Id
     */
    public $appId;    // 应用Id
    /**
     * @var DateTime  合同过期时间，目前并不生效
     */
    public $overtime;
    /**
     * @var String 自定义合同标号
     */
    public $defContractNo;  // 自定义合同号
    /**
     * @var int 合同模板id
     */
    public $templateId;     // 合同模板Id

    /**
     * @var array 合同占位符参数
     */
    public $params;      // 合同模板相关占位符，array类型
}