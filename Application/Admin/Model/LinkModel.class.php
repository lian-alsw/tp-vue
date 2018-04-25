<?php 
/*
 * 文章模型
 */
namespace Home\Model;
use Think\Model;
class LinkModel extends Model {

	public function __construct(){
		parent::__construct();
	}
	protected $_validate = array(
			array('title','require','标题不能为空！', 1),
			array('url','require','网址不得为空', 1)
	);
}
