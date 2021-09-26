<?php

namespace app\wangeditor\controller;

use app\BaseController;
use app\wangeditor\libs\Spider;

/**
 * 首页
 * Class Index
 * @package app\wangeditor\controller
 */
class Index extends BaseController
{

    /**
     * demo（单页面）
     * @return \think\response\Json|\think\response\View
     */
    public function demo(){
        $_action = input('_action');
        if($_action == 'submit') {
            //提交
            $text = input('text');
            cache('wangeditor_text',$text);
            return json(self::createReturn(true));
        }

        if($_action == 'details') {
            //获取详情
            return json(self::createReturn(true,[
                'text' => cache('wangeditor_text')
            ]));
        }

        if($_action == 'copy_text') {
            //复制时，批量上传图片
            $text = input('text');
            return json(self::createReturn(true,[
                'text' => (new Spider())->copyText($text)
            ]));
        }
        return view();
    }

    /**
     * demo（主键）
     * @return \think\response\View
     */
    public function demo2(){
        return view();
    }

}