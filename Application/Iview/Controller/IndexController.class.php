<?php
namespace Iview\Controller;
use Think\Controller;
class IndexController extends CommonController {

    function __construct()
    {
        parent::__construct();
        $this->categorys = F('category_content');
        $this->seoTitle = 'seo标题';
        $this->seoKeywords = 'seo关键词';
        $this->seoDescription = 'seo描述';
    }

    public function index(){
        /*游戏资讯end*/
        $this->seoTitle = 'pp游戏-单机游戏攻略秘籍_手机游戏攻略大全_微信游戏攻略';
        $this->seoKeywords = 'pp游戏,vr游戏攻略,单机游戏攻略,手机游戏攻略,微信游戏攻略,psp游戏攻略,网页游戏攻略,安卓游戏攻略';
        $this->seoDescription = 'pp游戏中心为游戏玩家提供vr游戏攻略，单机游戏攻略，手机游戏攻略，微信游戏攻略，网页游戏攻略，安卓游戏攻略，PSP攻略，PSV攻略，3DS攻略，NDS攻略，WIIU攻略，XBOX360攻略等，给各位玩家提供最新游戏攻略资讯。';
        $this->display();
    }

}