<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2015/3/9
 * Time: 10:22
 */
// 配置类型模型
namespace Home\Model;
use Think\Model;
class UserModel extends Model {
    //protected $trueTableName = 'db_user';
    private $key = '_auto_key__';   //cookie存放字段名

    public function __construct(){
        parent::__construct();
    }

    /*
     * 用户登录
     * @param touxiang varchar 用户名
     * @param password 密码 明文
     * @return int  返回用户id
     * 2015年5月25日11:15:41
     * create by zslin
     * */
    public function login($user,$password){
        if(empty($user)||empty($password)) return false;
        if(__isMobileNum($user)) $where['mobile'] = $user;
        $res = $this->field('password,id')->where($where)->find();
        $password = md5(md5($password));
        if($res){
            if($password == $res['password']){
                $this->where($where)->save(array('ctime'=>time()));
                $this->clearCookie();
                $this->setCookie($res['id'],$this->key);
                //cookie($this->key,$this->autoCode($res['id'],'ENCODE'),time()+3600*24);
                return $res['id'];
            }
        }
        return false;
    }
    /*
     * 获取用户信息
     * @param field varchar|*
     * @return array
     * 2015年5月25日11:19:51
     * create by zslin
     * */
    public function getUser($field='id,nickname,headimgurl,name,mobile'){
        $where['id'] = $this->enCode();
        if(empty($where['id'])) return false;
        return $this->field($field)->where($where)->find();
    }
    /*
     * 获取解密后的明文
     * @param keys varchar 要解密的字段
     * @return varchar 解密后的明文
     * 2015年5月25日11:39:33
     * create by zslin
     * */
    public function enCode($keys=''){
        $keys = $keys?$keys:$this->key;
        $string = cookie($keys);
        if($string==false) return false;
        return $this->autoCode($string);
    }
    /*
     * 清空cookie
     * 2015年5月25日15:49:33
     * create by zslin
     * */
    public function clearCookie(){
        cookie(null,C('COOKIE_PREFIX')); //  清空指定前缀的所有cookie值
    }
    /*
     * 添加cookie并加密
     * @param string 要加密的明文
     * @param string cookie 字段名
     * @param time 存储时间 默认24小时
     * 2015年5月27日10:50:33
     * create by zslin
     * */
    public function setCookie($string,$keys,$time='31104000'){
        $keys = $keys?$keys:$this->key;
        return cookie($keys,$this->autoCode($string,'ENCODE'),time()+$time);
    }
    /*
     * 秘钥加密
     * @param string： 明文 或 密文
     * @param operation：DECODE表示解密,其它表示加密
     * @param key： 密匙
     * @param expiry：密文有效期
     * */
    public function autoCode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $ckey_length = 4;
        // 密匙
        $key = md5($key ? $key : C('AU_KEY'));
        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
    /*
     * 添加会员信息
     * @param data array 添加的数据
     * @param is false返回布尔值 true返回插入数据的id
     * @return boolean
     * 2015年5月27日11:42:02
     * */
    function addUserInfo($data,$is=true){
        if(empty($data)) return false;
        $res = $this->add($data);
        if($is==true) return $this->getLastInsID();
        return $res;
    }
    /*
     * 更新会员信息
     * @param where array|varchar
     * @param data array 添加的数据
     * @return boolean
     * 2015年5月27日11:42:02
     * */
    function updateUserInfo($where,$data){
        if(empty($where)) return false;
        return $this->where($where)->save($data);
    }
    /*
     * 获取会员信息
     * @param where array
     * @param field varchar
     * @return array
     * create by zslin
     * 2015年5月27日13:41:21
     * */
    public function getUserInfo($where,$field='id,mobile,password'){
        if(empty($where)) return false;
        return $this->field($field)->where($where)->find();
    }
}
?>