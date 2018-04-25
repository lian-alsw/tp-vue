<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {

    function __construct()
    {
        parent::__construct();
        $this->webUrl = 'http://www.dy.com';
    }

}