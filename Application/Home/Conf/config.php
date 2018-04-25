<?php
return array(
	//'配置项'=>'配置值'
    'URL_MODEL'=> 2,
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__.'/Public/'.MODULE_NAME,
    ),
    //配置二级
    'VAR_PAGE' => 'pageNum',
    'PAGE_LISTROWS' => 10, //分页 每页显示多少条
    'PAGE_NUM_SHOWN' => 10, //分页 页标数字多少个
    'MAX_PAGE'  =>  100,    //设置最大分页数
    'ROOT_PATH' => substr(__FILE__,0,-33),
);