<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Common\Extend;
class UnLimit{
    //无限级分类
    static public function cate($cate,$html='&nbsp;└',$parentid=0,$level=0){
        $arr = array();
        foreach ($cate as $v){
            if ($v['parentid'] == $parentid){
                $v['level'] = $level+1;
                $v['html'] = str_repeat('&nbsp;&nbsp;', $level).'└';
                $arr[] = $v;
                $arr = array_merge($arr,self::cate($cate,$html,$v['catid'],$level+1));
            }
        }
        return $arr;
    }
}
