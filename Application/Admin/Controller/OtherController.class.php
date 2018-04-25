<?php
namespace Admin\Controller;
class OtherController extends AdminController{
	public function _initialize(){
		$action = array(
				'permission'=>array('profile', 'changePassword', 'ajax_checkUsername'),
				//'allow'=>array('index')
		);
		B('Admin\\Behaviors\\Authenticate', '', $action);
	}
	public function links(){
		$Link = D('Link');
		//初始化
		$map = array();
		$order = 'inputtime DESC';

		// 分页相关
		$page['pageCurrent'] = max(1 , I('post.pageCurrent'));
		$page['pageSize']= I('post.pageSize') ? I('post.pageSize') : 30 ;
		$totalCount = $Link->where($map)->count();
		$page ['totalCount'] = $totalCount;
		$page_list = $Link->where($map)->page($page['pageCurrent'], $page['pageSize'])->order($order)->select();

		$this->assign('page_list', $page_list);
		$this->assign('page', $page);
		$this->display();
	}
	public function linksEdit(){
		$Link = D('Link');
		$id = (int)I('get.id');
		if(empty($id)) $this->ajaxReturn(array('statusCode'=>300,'message'=>'非法操作'));
		if(IS_POST){
			//验证规则
			$info = $_POST['info'];
			if(!$Link->create($info,2)){
				$this->ajaxReturn(array('statusCode'=>300,'message'=>$Link->getError()));
			}else{
				$info['inputtime'] = time();
				$result = $Link->where(array('id'=>$id))->save($info);
			}
			if($result){
				$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功','tabid'=>'Other_link'));
			}else{
				$this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败ERROR:003'));
			}
		}
		$this->Detail = $Link->where(array('id'=>$id))->find();
		$this->display();
	}
	public function linksAdd(){
		$Link = D('Link');
		if(IS_POST){
			//验证规则
			$info = $_POST['info'];
			$one = $Link->field('id')->where(array('title'=>$info['title']))->find();
			if(!empty($one)) $this->ajaxReturn(array('statusCode'=>300,'message'=>'该链接已存在'));
			if(!$Link->create($info,1)){
				//如果不通过 ，输出错误报告
				$this->ajaxReturn(array('statusCode'=>300,'message'=>$Link->getError()));
			}else{
				$info['inputtime'] = time();
				$result = $Link->add($info);
			}
			if($result){
				$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功','tabid'=>'Other_link'));
			}else{
				$this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败ERROR:003'));
			}
		}
		$this->display('linksEdit');
	}
	public function linksDel(){
		$Link = D('Link');
		$ids = I('get.ids');  //获取ids字符串  '1130,1127'
		if(!$ids) $this->ajaxReturn(array('statusCode'=>300,'message'=>'请选择要删除的链接'));
		$idsList = explode(',', $ids);
		//循环删除数据
		foreach ($idsList as $id){
			$Link->where(array('id'=>$id))->delete();
		}
		$this->ajaxReturn(array('statusCode'=>200,'message'=>'删除成功','divid'=>'layout_Link'));
	}
	public function linksStatus(){
		$id = (int)I('get.id');
		$Link = D('Link');
		$img_info = $Link->field('status')->where(array('id'=>$id))->find();
		if(empty($img_info)) $this->ajaxReturn(array('statusCode'=>300,'message'=>'非法操作'));
		if(99==$img_info['status']){
			$result = $Link->where(array('id'=>$id))->save(array('status'=>1));
		}else{
			$result = $Link->where(array('id'=>$id))->save(array('status'=>99));
		}
		if($result){
			$this->ajaxReturn(array('statusCode'=>200,'divid'=>'Other_link'));
		}else{
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败ERROR:003'));
		}
	}
	

    
}