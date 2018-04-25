<?php
/*
 * 用于打印数据,看起来比较美观易读
 * @param data array|string 数组|字符串
 * @return string;
 * 2016年8月31日09:39:17
 * create by zslin
 * */
function p($data){
	// 定义样式
	$str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
	// 如果是boolean或者null直接显示文字；否则print
	if (is_bool($data)) {
		$show_data=$data ? 'true' : 'false';
	}elseif (is_null($data)) {
		$show_data='null';
	}else{
		$show_data=print_r($data,true);
	}
	$str.=$show_data;
	$str.='</pre>';
	echo $str;
}
/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	if(strtolower(CHARSET) == 'utf-8') {
		$length = intval($length-strlen($dot)-$length/3);
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
	} else {
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen - 1;
		$current_str = '';
		$search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
		$replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
		$search_flip = array_flip($search_arr);
		for ($i = 0; $i < $maxi; $i++) {
			$current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			if (in_array($current_str, $search_arr)) {
				$key = $search_flip[$current_str];
				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
			}
			$strcut .= $current_str;
		}
	}
	return $strcut.$dot;
}
/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}
/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt='') {
	$pwd = array();
	$pwd['encrypt'] =  $encrypt ? $encrypt : create_randomstr();
	$pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
	return $encrypt ? $pwd['password'] : $pwd;
}
/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return 	TRUE or FALSE
 */
function is_password($password) {
	$strlen = strlen($password);
	if($strlen >= 6 && $strlen <= 20) return true;
	return false;
}
/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 6) {
	return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '0123456789') {
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}
function list_to_tree($list,$pk='id',$pid='pid',$child='_child',$root=0){
	// 创建Tree
	$tree=array();
	if(is_array($list)){
		// 创建基于主键的数组引用
		$refer=array();
		foreach($list as $key=>$data){
			$refer[$data[$pk]]=& $list[$key];
		}
		foreach($list as $key=>$data){
			// 判断是否存在parent
			$parentId=$data[$pid];
			if($root==$parentId){
				$tree[]=& $list[$key];
			}else{
				if(isset($refer[$parentId])){
					$parent=& $refer[$parentId];
					$parent[$child][]=& $list[$key];
				}
			}
		}
	}
	return $tree;
}
function JsJump($url,$time=0){
	$SleepTime=$time*1000;
	echo '<script language="javascript">window.setTimeout("window.location=\''.$url.'\'", '.$time.');</script>';
	exit();
}
function JsMessage($message,$URL='HISTORY',$charset='utf-8'){
	echo '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />
			<title>系统提示</title>
			</head>
		
			<body>
			<script type="text/javascript">
			alert("'.$message.'");
			'.(strtoupper($URL)=='HISTORY'?'history.back();':'location.href="'.$URL.'";').'
			</script>
			</body>
			</html>
		';
	exit();
}

//后台验证的一些正则, 只能a-zA-Z0-9_
function checkUsername($string){
	if(preg_match("/^[a-zA-z0-9_]+$/", $string)){
		return true;
	}else{
		return false;
	}
}

//获取一个代理web
function getWebProxy(){
    //清除
    //S('proxy_host', null);
    //S('proxy_port', null);
    
    //获取代理列表
    $url = 'http://www.xicidaili.com/wn';
    $snoopy = new \Lain\Snoopy;
    $snoopy->fetch($url);
    $html_code = $snoopy->results;
    //使用QueryList解析html
    $query_content = \QL\QueryList::Query($html_code, array('proxy_html' => array('#ip_list tr.odd','html')))->data;
    foreach ($query_content as $proxy){
        $proxy_data = \QL\QueryList::Query($proxy['proxy_html'], array('proxy' => array('td:nth-child(3)','html'), 'port' => array('td:nth-child(4)', 'html')))->data;
        //判断IP和端口是否可以访问
        //$proxy_data = array(0 => array('proxy' => '123.138.89.130', 'port'=> '9999'));
        //var_dump($proxy_data);
        if(checkProxy($proxy_data[0]['proxy'], $proxy_data[0]['port'])){
            //保存
            S('proxy_host', $proxy_data['proxy'], 3600*24*7);
            S('proxy_port', $proxy_data['port'], 3600*24*7);
            
            //检测通过, 则跳出
//             echo 'keyong:';
//             var_dump($proxy_data);exit;
            break;
        }
    }
    return true;
}

function checkProxy ($proxy, $port)
{
    //使用百度来检测
    $url = 'http://www.baidu.com/';
    $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh- CN; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5 FirePHP/0.2.1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_PROXYPORT, $port); //代理服务器端口
    curl_setopt($ch, CURLOPT_URL, $url);//设置要访问的IP
    //curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);//模拟用户使用的浏览器
    //@curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转
    curl_setopt($ch, CURLOPT_TIMEOUT, 3 ); //设置超时时间
    //curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    //var_dump($result);exit;
    if($result !== false && strpos($result, '百度一下') !== false)
        return true;
    else
        return false;
}

/**
 * 转义 javascript 代码标记
 *
 * @param $str
 * @return mixed
 */
function trim_script($str) {
    if(is_array($str)){
        foreach ($str as $key => $val){
            $str[$key] = trim_script($val);
        }
    }else{
        $str = preg_replace ( '/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str );
        $str = preg_replace ( '/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str );
        $str = preg_replace ( '/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str );
        $str = str_replace ( 'javascript:', 'javascript：', $str );
    }
    return $str;
}
/**
 * 及时显示提示信息
 * @param  string $msg 提示信息
 */
function show_msg($msg, $class = '')
{
	echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
	ob_flush();
	flush();
}
/*
 * 短信发送接口
 * @param to 手机号码
 * @param datas array 传过去的参数
 * */
function l_send_sms($to,$datas,$tempId){
	require_once '/Public/Plugins/smsapi/CCPRestSmsSDK.php';
	// 初始化REST SDK
	$sms_config = C('SMS_CONFIG');
	$rest = new REST($sms_config['serverIP'],$sms_config['serverPort'],$sms_config['softVersion']);
	$rest->setAccount($sms_config['accountSid'],$sms_config['accountToken']);
	$rest->setAppId($sms_config['appId']);

	// 发送模板短信
	/*echo "Sending TemplateSMS to $to <br/>";*/
	$result = $rest->sendTemplateSMS($to,$datas,$tempId);
	if($result == NULL ) {
		//return false;
		echo "result error!";
		exit();
	}
	if($result->statusCode!=0) {
		//return false;
		echo "error code :" . $result->statusCode . "<br>";
		echo "error msg :" . $result->statusMsg . "<br>";
	}else{
		//return true;
		// 获取返回信息
		$smsmessage = $result->TemplateSMS;
		return true;
		/*echo "dateCreated:".$smsmessage->dateCreated."<br/>";
        echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";*/
	}
}
/*
 * 随机取几位数
 * @param num int 定义几位数
 * @return res int
 * 2015年5月27日17:30:26
 * create by zslin
 * */
function getRandNum($num=4){
	if(empty($num)) return false;
	$string = '';
	for($i=1;$i<=$num;$i++){
		$string.= rand(0,9);
	}
	return $string;
}
/*
 * 验证是否是邮箱
 * @parame string 要验证的值
 * @return boolean
 * 2015年5月27日10:26:32
 * create by zslin
 * */
function __isEmail($string){
	return strlen($string)>6&&preg_match("/^[\w\-\.]+@[\w\-]+(\.\w+)+$/",$string);
}
/*
 * 验证是手机号码
 * @parame string 要验证的值
 * @return boolean
 * 2015年5月27日10:26:32
 * create by zslin
 * */
function __isMobileNum($string){
	return strlen($string)==11&&preg_match('/^(1(([35][0-9])|(47)|[8][0126789]))\d{8}$/',$string);
}
/*
 * 短信发送函数
 * @param mobile string
 * @param content string
 * @return boolean
 * */
function sendSMS($mobile, $content){
	if(empty($mobile) || empty($content)) return false;
	return file_get_contents('http://www.jpsw99.com/SendSms.asp?Account=yinweijiabo&Password=123456&Phones='.trim($mobile).'&Content='.$content.'&Channel=1&SendTime=');
}
function check_verify($code, $id = ''){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}
/*
 * 404跳转函数 404.html放在项目根目录
 * param @
 * */
function _404(){
	header('HTTP/1.1 404 Not Found');
	header("Status: 404 Not Found");
	exit('404');
}
/*
 * 获取完成的网址
 * */
function allUrl(){
	return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

/*
 * 判断是不是微信登录
 * */
function is_weixin(){
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
		return true;
	}
	return false;
}
function _substr($string,$length,$dot='',$ClearHtml=true,$charset='utf-8'){
	if(mb_strlen($string)<=$length){
		return $string;
	}
	if($ClearHtml){
		$string=str_replace(array('&amp;','&quot;','&lt;','&gt;','&nbsp;'),array('&','"','<','>',' '),$string);
		$string=strip_tags(clear_kongge($string));
	}
	$string=preg_replace('/([\s]{2,})/','',$string);
	$strcut=mb_substr($string,0,$length,$charset);
	return $strcut.(strlen($string)>strlen($strcut)?$dot:'');
}
function clear_kongge($str){
	return str_replace(array('&nbsp;',' ',"　","\t","\r","\n"),'',$str);
}

function __img_onerror($type){
	if ($type=="lvyou") {
		return ' onerror="javascript:this.src=\''.__STATIC__.'/images/none.gif\';" ';
	}else{
		return ' onerror="javascript:this.src=\''.__STATIC__.'/images/none.gif\';" ';
	}
}
function download_img($url){
	if(empty($url)) return false;
	$http = new \Org\Net\Http();
	$image = new \Think\Image();
	$img_arr = explode('.',$url);
	$img_type = $img_arr[count($img_arr)-1];
	$path = '/Uploads/'.date('Ymd').'/';
	if(!is_dir(C(ROOT_PATH).$path)) mkdir(C(ROOT_PATH).$path,0777,true);
	$newfile = $path.md5($url).'.'.$img_type;
	$http->curlDownload($url,C(ROOT_PATH).$newfile);
	if(file_exists(C(ROOT_PATH).$newfile)) {
		$image->open(C(ROOT_PATH).$newfile);
		//$image->water(C(ROOT_PATH).'/public/images/bigwater.png')->save(C(ROOT_PATH).$newfile);
		return $newfile;
	}
}
function img_thumb($img){
	if(empty($img)) return false;
	$image = new \Think\Image();
	$img_arr = explode('.',$img);
	$img_type = $img_arr[count($img_arr)-1];
	$path = '/Uploads/thumb/'.date('Ymd').'/';
	if(!is_dir(C(ROOT_PATH).$path)) mkdir(C(ROOT_PATH).$path,0777,true);
	$newfile = $path.md5($img).'.thumb.'.$img_type;
	$image->open(C(ROOT_PATH).$img);
	//将图片裁剪为440x440并保存为corp.jpg
	$image->thumb(180, 120,3)->save(C(ROOT_PATH).$newfile);
	//$http->curlDownload($url,C(ROOT_PATH).$newfile);
	if(file_exists(C(ROOT_PATH).$newfile)) return $newfile;
}
/*
**	下载文章中所有的图片,并替换成本地图片后返回内容
**	date:2014年10月28日11:45:39
**	create by zslin
** 	返回值：array
*/
function ContentDownImg($content, $title) {
	$content = stripslashes ($content );
	if (preg_match_all ( "/<img(.*?)src=([^h]{0,1})(http[^\s|\'|\"|>]+)/is", $content, $matches, PREG_PATTERN_ORDER )) {
		$images = $matches [3];
		foreach ( $images as $imageid => $image ) {
			//	var_dump($image);
			if (substr ( $image, 0, 2 ) == "\\\"" || substr ( $image, 0, 2 ) == "\\'") {
				$image = substr ( $image, 2 );
			}
			if (substr ( $image, - 2 ) == "\\\"" || substr ( $image, - 2 ) == "\\'") {
				$image = substr ( $image, 0, - 2 );
			}
			if (substr ( $image, 0, 1 ) == "\"" || substr ( $image, 0, 1 ) == "'") {
				$image = substr ( $image, 1 );
			}
			if (substr ( $image, - 1 ) == "\"" || substr ( $image, - 1 ) == "'") {
				$image = substr ( $image, 0, - 1 );
			}
			$download_images [$image] = $image;
		}
		$key = 1;
		foreach ( $download_images as $image ) {
			if (strtolower ( substr ( $image, 0, 7 ) ) == 'http://') {
				$savepath = '/Uploads/article/' . date ( "Y-m-d" );
				if(!is_dir(C(ROOT_PATH).$savepath)) mkdir(C(ROOT_PATH).$savepath,0777,true);
				$image_info = pathinfo ( $image );
				$newfile = $savepath . '/' . md5( $image ) . '.' . strtolower ( $image_info ['extension'] );
				if (copy ( $image, C(ROOT_PATH) . $newfile )) {
					if (getimagesize ( C(ROOT_PATH) . $newfile )) {
						$downloaded_images [] = $newfile;
						$content = preg_replace ( "/<img([^>]+)" . str_replace ( array ('/', '(', ')' ), array ('\/', '\(', '\)' ), $image ) . "([^>]{0,})>/is", "<img alt=\"{$title}" . $key . "\" src=\"{$newfile}\">", $content );
						$key ++;
					} else {
						$content = preg_replace ( "/<img([^>]+)" . str_replace ( array ('/', '(', ')' ), array ('\/', '\(', '\)' ), $image ) . "([^>]{0,})>/is", "", $content );
						@unlink ( C(ROOT_PATH) . $newfile );
					}
				}
			}
		}
	}

	$content = preg_replace ( array ("/<a([^>]+)>/ies", '/<\/a>/ies' ), array ('', '' ), $content );
	$content = preg_replace ( "/\sstyle=\"(.*?)\"/is", '', $content );
	$content = preg_replace ( "/\sstyle=\'(.*?)\'/is", '', $content );

	return array ("data" => $content, "imglist" => $downloaded_images );
}