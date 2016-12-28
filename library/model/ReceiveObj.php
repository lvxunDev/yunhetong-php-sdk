<?php

/**
 * 接收消息的实体类
 * Created by PhpStorm.
 * User: Seanwu
 * Date: 2016/5/31
 * Time: 14:59
 */
class ReceiveObj
{
    public $content;
    public $noticeType;
    public $signDigest;
    public $noSignUsers = array();

    /**
     * ReceiveObj constructor.
     * @param $content
     */
    public function __construct($content)
    {
        $jo = json_decode($content,true);
        $this->content = $jo["content"];
        $this->noticeType = $jo["noticeType"];
        if ($this->noticeType == 2) {
            $this->signDigest = $jo["signDigest"];
        }
        print_r($jo);
        foreach ($jo["map"] as $key => $value) {
            if ($value == 0) {
                // "用户:" + __key + ", 已签署";
            } else if ($value == 1) {
                $this->noSignUsers[] = $key;
            }

        }


    }


}