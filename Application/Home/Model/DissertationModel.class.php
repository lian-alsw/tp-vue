<?php 
/*
 * 文章模型
 */
namespace Home\Model;
use Common\Extend\FormPage;
use Think\Model;
class DissertationModel extends Model {
	/*
     * 获取公交新闻资讯分页
     * @param where array|varchar
     * @param url varchar url规则 必须是xxx-[PAGE](PAGE必须是大写)
     * @param pagenum int
     * @field varchar
     * @return list array 数据列表
     * @return page varchar 分页样式 调用格式必须是<div class="digg">{$page}</div> 样式存放在 layout.css样式表中
     * @return count int 总量
     * 2015年3月19日15:59:46
     * create by zslin
     * */
	public function getPageNews($where,$url='',$pagenum=40,$field='id,title,thumb,pinyin'){
		if(empty($pagenum)) return false;
		$count      = $this->where($where)->count();
		$Page       = new FormPage($count,$pagenum);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$res = $this->field($field)->where($where)->order('inputtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		if($url) $Page->setConfig('link',$url);
		return array('list'=>$res,'page'=>$Page->show(),'count'=>$count);
	}

}
