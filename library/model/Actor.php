<?php

/**
 * <p>合同参与者类</p>
 * <p>合同参与者我们才不管甲方还是乙方呢，反正有几个参与者就传几个 Actor 就好了</p>
 *
 * @author 浣溪沙
 * @version 0.0.1
 *
 */
class Actor
{
    /**
     * @var LxUser 用户实体类
     */
    public  $user;
    /**
     * @var String 是否自动签署：1 是；0 或空不是
     */
    public $autoSign; // s是否自动签名
    /**
     * @var String 签名位置Id
     */
    public $locationName; // 签名位置信息

}