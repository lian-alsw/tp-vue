<?php
/**
 * 主面板
 * @author Lain
 *
 */
namespace Admin\Controller;

use Admin\Controller\AdminController;
/**
 * 后台界面初始化
 */
class AdminCPController extends AdminController{
	
	public function _initialize(){

		//B('Admin\\Behaviors\\Authenticate', '', $action);
	}
	
	//加载主面板
    public function index(){
    	
    	//获取菜单
    	$map['display'] = 1;

    	//检查权限,如果是超级管理员 ，则显示所有菜单
    	$roleid = session('roleid');
    	if($roleid != 1){
    		//取出权限中的menu_id
    		$priv_list = M('admin_role_priv')->where('roleid='.$roleid)->field('menuid')->select();

    		$menu_ids = '';
    		if($priv_list){
    			foreach ($priv_list as $v){
    				$menu_ids .= $menu_ids ? ','.$v['menuid'] : $v['menuid'];
    			}
    			$map['id'] = array('in', $menu_ids);
    		}
    	}
    	$result = D('admin_menu')->where($map)->order('listorder,id')->select();
    	
    	//这里可以做缓存
    	$menu=list_to_tree($result,'id','parentid','_child');
    	$this->assign('menu', $menu);
    	$this->display();
    }
    
    //欢迎界面
    public function welcome(){
    	//获取git更新日志
    	$this->display();
    }
}