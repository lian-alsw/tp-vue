<?php 
/*
 * 文章模型
 */
namespace Admin\Model;
use Think\Model;
class ArticleModel extends Model {
	
	private $setting;
	private $attachment;
 	private $data_fields = array(
	        'system' => array('catid', 'url', 'title', 'thumb', 'keywords', 'description', 'status', 'code', 'inputtime', 'updatetime','listorder','zid'),  //对内容进行分表处理,  如果不需要,都放到system
	        'data' => array('content'),
	);
	public function __construct(){
		parent::__construct();
		$this->attachment = D('Attachment');
		$this->setting = array(
			'enablesaveimage' => 0,
			'enablesavebase64image' => 0,
		);
	}
	protected $_validate = array(
			array('title','require','标题不能为空！', 1), 
			array('content','require','内容不能为空！', 1), 
 			array('catid','require','栏目不能为空',1, '', 1), 
// 			array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
// 			array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
// 			array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式 
	);
	
	public function getDetail($id){
	    $map['id'] = $id;
	    //主表
	    $detail = $this->where(array('id' => $id))->find();
	    //附表, 临时解决办法
	    $content = M('ArticleData')->where(array('article_id' => $id))->getField('content');
	    //如果content为空, 并且有code, 则跑接口获取文章内容
	    if(!$content && $detail['code']){
	        $tuicool_content = $this->getTuicoolContent($detail['code']);
	        //var_dump(S('proxy_host'));
	        if($tuicool_content){
	            //保存到数据库
	            $content = $tuicool_content['content'];
	            if(!$detail['url']){
	                $detail['url'] = $tuicool_content['url'];
	                $info['url'] = $tuicool_content['url'];
	            }
                $info['status'] = 99;
                $this->where(array('id' => $id))->save($info);
	            M('ArticleData')->where(array('article_id' => $id))->save(array('content' => $content));
	        }else{
	            $content = '当前代理IP有点堵塞, 正在排查, 您可以刷新页面重新尝试';
	        }
	    }
	    $detail['content'] = $content;
	    return $detail;
	}
	
	public function getTuicoolContent($code){
	    //如果没有代理, 则退出
 	    if(!S('proxy_host'))
 	        return '';
	    $url = 'http://www.tuicool.com/articles/'.$code;
	    
	    //使用QueryList插件
	    /* $query_request = \QL\QueryList::run('Request',[
            'target' => $url,
            'referrer'=>'http://www.tuicool.com/',
            'method' => 'GET',
            'cookiePath' => 'cookie.txt',
            //等等其它http相关参数，具体可查看Http类源码
        ]);
	    $query_article = $query_request->setQuery(array('content' => array('#nei','html'), 'url' => array('.article_meta .source a', 'href')))->data; */
	    
	    //使用snoopy
	    $snoopy = new \Lain\Snoopy;
	    $snoopy->rawheaders["COOKIE"] = "_tuicool_session=BAh7CUkiD3Nlc3Npb25faWQGOgZFRkkiJWViOTQ5NWE0NjJmNzFlYTdjZmJjZWU1ODRkMzBhNTk2BjsAVEkiDHVzZXJfaWQGOwBGaQNdygFJIhBfY3NyZl90b2tlbgY7AEZJIjFLU3ZPLzQ4Mk5RQk1Ndld5YnUrK2JuTEVhK0hNR3EyQUdtUVhBcU1LblU0PQY7AEZJIg5yZXR1cm5fdG8GOwBGSSIsaHR0cDovL3d3dy50dWljb29sLmNvbS9hcnRpY2xlcy9ySVJubWVxBjsARg%3D%3D--0169cd82b559a1b6d9ad5b9d24b7ee3c8fc6204f; domain=.tuicool.com; path=/; expires=Wed, 06-Dec-2017 05:46:41 GMT; HttpOnly";
		//代理
		$snoopy->proxy_host = S('proxy_host');
		$snoopy->proxy_port = S('proxy_port');

		$snoopy->read_timeout=4;  //读取超时时间
	    $snoopy->fetch($url);
	    $html_code = $snoopy->results;
	    //使用QueryList解析html
	    $query_article = \QL\QueryList::Query($html_code, array('content' => array('#nei','html'), 'url' => array('.article_meta .source a', 'href')))->data;
	    if(!$query_article){
	        D('Admin/Proxy')->loseConnect(S('proxy_host'), S('proxy_port'));
	        return '';
	    }else{
	        D('Admin/Proxy')->successConnect(S('proxy_host'), S('proxy_port'));
	    }
	    $content   = $query_article[0]['content'];
	    $url       = $query_article[0]['url'];
	    //下载文章中的图片
	    $content = $this->downloadTuicoolImg($content);
	    $return['content'] = $content;
	    $return['url']     = $url;

	    return $return;
	}
	
	//下载图片
	protected function downloadTuicoolImg($value, $ext = 'gif|jpg|jpeg|bmp|png'){
	    $uploader = new \Think\Upload\Driver\Local();
	    $dir = 'tuicool/'.date('Y-m-d').'/';
	    $upload_url = '/Uploads/';
	    $uploadpath = $upload_url.$dir;
	    
	    $uploaddir = './Uploads/'.$dir;

	    //检测目录可写
	    if(!$uploader->checkSavePath($uploaddir))
	        return $value;
	    
	    $string = stripslashes($value);
	    //判断是否需要下载
	    if(!preg_match_all("/(href|src)=([\"|']?)([^ \"'>]+\.($ext)(\!web)?)\\2/i", $string, $matches)) return $value;
	    //取出下载的图片
	    $remotefileurls = array();
	    foreach($matches[3] as $matche)
	    {
	        //如果是本地图片, 则跳过
	        if(strpos($matche, '://') === false) continue;
	        $remotefileurls[] = $matche;
	    }
	    unset($matches, $string);
	    $remotefileurls = array_unique($remotefileurls);
	    $oldpath = $newpath = array();
	    //开始下载
	    //import("Org.Net.Http");
	    foreach($remotefileurls as $k=>$file) {
	        //判断是否是本地图片
	        if(strpos($file, '://') === false || strpos($file, $upload_url) !== false) continue;
	        
	    
	        //获取文件扩展名
	        $filename = fileext($file);
	        $filename = str_replace('!web', '', $filename);
	        //$file_name = basename($file);
	        $filename = $this->getname($filename);
	        $newfile = $uploaddir.$filename;
	        
	        $hander = curl_init(); 
            $fp = fopen($newfile,'wb'); 
            curl_setopt($hander,CURLOPT_URL,$file); 
            curl_setopt($hander,CURLOPT_FILE,$fp); 
            curl_setopt($hander,CURLOPT_HEADER,0); 
            //curl_setopt ($hander, CURLOPT_REFERER, "http://www.tuicool.com/ ");//伪造来路   
            curl_setopt($hander,CURLOPT_FOLLOWLOCATION,1); 
            curl_setopt($hander,CURLOPT_TIMEOUT,60); 
            curl_exec($hander); 
            curl_close($hander); 
            fclose($fp); 

	        $oldpath[] = $file;
	        $newpath[] = $uploadpath.$filename;
	        
	    }
	    //替换下载后的地址
	    return str_replace($oldpath, $newpath, $value);
	}
	
	function getname($fileext){
	    return date('Ymdhis').rand(100, 999).'.'.$fileext;
	}
	
	/**
	 * 添加内容
	 *
	 * @param $datas
	 * @param $isimport 是否为外部接口导入
	 */
	public function add_content($data){
	    foreach ($this->data_fields['system'] as $field){
	        if(!$data[$field])
	            continue;
	        $systeminfo[$field] = $data[$field];
	    }
		//是否下载内容中的图片
		if($this->setting['enablesaveimage']){
			$data['content'] = $this->attachment->auto_save_image($data['content']);
		}
		//是否有创建时间
		if($data['inputtime'] && !is_numeric($data['inputtime'])) {
			$systeminfo['inputtime'] = strtotime($data['inputtime']);
		} elseif(!$data['inputtime']) {
			$systeminfo['inputtime'] = NOW_TIME;
		} else {
			$systeminfo['inputtime'] = $data['inputtime'];
		}
		
		if($data['updatetime'] && !is_numeric($data['updatetime'])) {
			$systeminfo['updatetime'] = strtotime($data['updatetime']);
		} elseif(!$data['updatetime']) {
			$systeminfo['updatetime'] = NOW_TIME;
		} else {
			$systeminfo['updatetime'] = $data['updatetime'];
		}
		
		//自动提取摘要
		if(isset($_POST['add_introduce']) && $data['description'] == '' && isset($data['content'])) {
			$content = stripslashes(html_entity_decode($data['content'], ENT_QUOTES));
			$introcude_length = intval($_POST['introcude_length']);
			$data['description'] = str_cut(str_replace(array("'","\r\n","\t",'[page]','[/page]','&ldquo;','&rdquo;','&nbsp;'), '', strip_tags($content)),$introcude_length);
			$systeminfo['description'] = addslashes($data['description']);
		}
		//自动提取缩略图
		if(isset($_POST['auto_thumb']) && $data['thumb'] == '' && isset($data['content'])) {
			$content = $content ? $content : stripslashes(html_entity_decode($data['content'], ENT_QUOTES));
			$auto_thumb_no = intval($_POST['auto_thumb_no'])-1;
			if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
				$systeminfo['thumb'] = $matches[3][$auto_thumb_no];
			}
		}
		$systeminfo['description'] = str_replace(array('/','\\','#','.',"'"),' ',$data['description']);
		//$systeminfo['keywords'] = str_replace(array('/','\\','#','.',"'"),' ',$systeminfo['keywords']);\
		//主表
		$id = $this->add($systeminfo);
		//附表
		$datainfo['article_id'] = $id;
		$datainfo['content'] = $data['content'];
		M('ArticleData')->add($datainfo);
		//$this->update($systeminfo,array('id'=>$id));
		//更新URL地址
		/* if($data['islink']==1) {
			$urls[0] = trim_script($_POST['linkurl']);
			$urls[0] = remove_xss($urls[0]);
				
			$urls[0] = str_replace(array('select ',')','\\','#',"'"),' ',$urls[0]);
		} else {
			$urls = $this->url->show($id, 0, $systeminfo['catid'], $systeminfo['inputtime'], $data['prefix'],$inputinfo,'add');
		}
		 */
		/*if($data['status']==99) {
			//更新到全站搜索
			$this->search_api($id,$data);
		}

		//更新栏目统计数据
		$this->update_category_items($data['catid'],'add',1);*/

		return $id;
	}
	
	/**
	 * 修改内容
	 *
	 * @param $datas
	 */
	public function edit_content($data,$id) {
	    foreach ($this->data_fields['system'] as $field){
	        if(!$data[$field])
	            continue;
	        $systeminfo[$field] = $data[$field];
	    }
		//是否下载内容中的图片
		if($this->setting['enablesaveimage']){
			$data['content'] = $this->attachment->download('content', $data['content']);
		}
		
		//是否有创建时间
		if($data['updatetime'] && !is_numeric($data['updatetime'])) {
			$systeminfo['updatetime'] = strtotime($data['updatetime']);
		} elseif(!$data['updatetime']) {
			$systeminfo['updatetime'] = NOW_TIME;
		} else {
			$systeminfo['updatetime'] = $data['updatetime'];
		}
		//自动提取摘要
		if(isset($_POST['add_introduce']) && $data['description'] == '' && isset($data['content'])) {
			//$data是通过I方法传入的,　所以需要html_entity_decode反转义html标签
			$content = stripslashes(html_entity_decode($data['content'], ENT_QUOTES));
			$introcude_length = intval($_POST['introcude_length']);
			$data['description'] = str_cut(str_replace(array("'","\r\n","\t",'[page]','[/page]','&ldquo;','&rdquo;','&nbsp;', ' '), '', strip_tags($content)),$introcude_length);
			$systeminfo['description'] = addslashes($data['description']);
		}
		//自动提取缩略图
		if(isset($_POST['auto_thumb']) && $data['thumb'] == '' && isset($data['content'])) {
			//$data是通过I方法传入的,　所以需要html_entity_decode反转义html标签
			$content = $content ? $content : stripslashes(html_entity_decode($data['content'], ENT_QUOTES));
			$auto_thumb_no = intval($_POST['auto_thumb_no'])-1;
			if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
				$systeminfo['thumb'] = $matches[3][$auto_thumb_no];
			}
		}
		$systeminfo['description'] = str_replace(array('/','\\','#','.',"'"),' ',$data['description']);
		//保存信息
		$this->where('id='.$id)->save($systeminfo);
        
		M('ArticleData')->where(array('article_id' => $id))->save(array('content' => $data['content']));
		
		return true;
		
	}
	/**
	 * 删除内容
	 * @param $id 内容id
	 * @param $file 文件路径
	 * @param $catid 栏目id
	 */
	public function delete_content($id, $catid = null) {
		//删除主表数据
		//$this->delete(array('id'=>$id));
		$this->where('id='.$id)->delete();
		//删除从表数据
		//$this->table_name = $this->table_name.'_data';
		//$this->delete(array('id'=>$id));
		//重置默认表
		//$this->table_name = $this->db_tablepre.$this->model_tablename;
		//更新栏目统计
		//$this->update_category_items($catid,'delete');
	}
	
	//栏目统计
	private function update_category_items($catid,$action = 'add',$cache = 0) {
		/* $this->category_db = pc_base::load_model('category_model');
		if($action=='add') {
			$this->category_db->update(array('items'=>'+=1'),array('catid'=>$catid));
		}  else {
			$this->category_db->update(array('items'=>'-=1'),array('catid'=>$catid));
		}
		if($cache) $this->cache_items(); */
	}
	
	public function search_api($id = 0, $data = array(), $action = 'update') {
		$model = 'article';
		if($action == 'update') {
			//要搜索的字段
			$fulltext_array = array('title', 'description');
			foreach($fulltext_array AS $key){
				$fulltextcontent .= $data[$key];
			}
			D('Search')->update_search($model, $id, $fulltextcontent,addslashes($data['title']));
		} elseif($action == 'delete') {
			D('Search')->delete_search($model, $id);
		}
	}
}
