<?php
namespace Mobile\Controller;
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
        $this->display();
    }

}