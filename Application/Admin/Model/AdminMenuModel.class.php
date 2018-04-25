<?php 
/*
 * 后台管理员模型
 */
namespace Admin\Model;
use Think\Model;
class AdminMenuModel extends Model {
	
	protected $_validate = array(
			array('name','require','菜单名不能为空！'), //默认情况下用正则进行验证
	//		array('listorder','number ','排序输入错误，请输入数字'),
	//		array('content','require','内容不能为空！'), //默认情况下用正则进行验证
	// 			array('name','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
	// 			array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
	// 			array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
	// 			array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
	);
}
