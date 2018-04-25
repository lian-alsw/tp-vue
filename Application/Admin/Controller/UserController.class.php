<?php
/**
 * 
 * 会员管理模块
 * @author Lain
 *
 */
namespace Admin\Controller;
use Admin\Controller\AdminController;
class UserController extends AdminController{
	public function _initialize(){
		$action = array(
				'permission'=>array('profile', 'changePassword', 'ajax_checkUsername'),
				//'allow'=>array('index')
		);
		B('Admin\\Behaviors\\Authenticate', '', $action);
	}
	
	public function manage(){

		//检索条件
		if(I('post.username')){
			$this->username = $username = I('post.username');
			$map['username'] = array('like', "%$username%");
		}
		if(I('post.roleid')){
			$this->roleid = $roleid = I('post.roleid');
			$map['roleid'] = $roleid;
		}
		//分页相关
		$page['pageCurrent'] = max(1 , I('post.pageCurrent'));
		$page['pageSize']= I('post.pageSize') ? I('post.pageSize') : 30 ;
		
		$totalCount = D('user')->where($map)->count();
		$page['totalCount']=$totalCount ? $totalCount : 0;
			
		$this->page_list = D('user')->order('userid')->where($map)->page($page['pageCurrent'], $page['pageSize'])->select();
		
		$this->page = $page;
		
		$this->display();
		
	}
	
	public function edit(){
		$userid = I('get.userid','','intval');
		if(IS_POST){
			D('Admin')->where('userid='.$userid)->save($_POST['info']);
			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminManage'));
		}else{
	
			$this->Detail = D('Admin')->where('userid='.$userid)->find();
	
			//获取角色
			$this->roles = S('role') ? S('role') : D('AdminRole')->get_role_list();
			$this->display();
		}
	}
	public function add(){
		if(IS_POST){
			$info = I('post.info');
			//生成默认密码
			//$info = array_merge($info, password('1q2w3e4'));
			$info['password'] = hash_hmac('sha256', '1q2w3e4', $info['username']);
				
			D('Admin')->add($info);
			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminManage'));
		}else{
	
			//获取角色
			$this->roles = S('role') ? S('role') : D('AdminRole')->get_role_list();
			$this->display('adminEdit');
		}
	}

}