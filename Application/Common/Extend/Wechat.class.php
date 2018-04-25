<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2016/11/1 0001
 * Time: 19:32
 */
namespace Common\Extend;
class Wechat{
    var $appid = "wx68cd87763ef5f045";
    var $appsecret = "256e195c619a3189d184caa881569a95";

    //构造函数，获取Access Token
    public function __construct($appid = NULL, $appsecret = NULL)
    {
        if($appid){
            $this->appid = $appid;
        }
        if($appsecret){
            $this->appsecret = $appsecret;
        }


        $this->lasttime = 1395049256;
        $this->access_token = "0LVdEUwomvIl8y9goMaZClm4YoMSGrX5vyL9ZDqKXIwojXHv0E1lhSuXMguyaGcLFhBnR1EyWVaLZrw7o65iLmX2zVGTjzQdav6di5nIBYgXUObAEABYS";

        if (time() > ($this->lasttime + 7200)){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
            $res = $this->https_request($url);
            $result = json_decode($res, true);

            $this->access_token = $result["access_token"];
            $this->lasttime = time();
        }
    }
//获取用户基本信息
    public function get_user_info($openid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=".$openid."&lang=zh_CN";
        $res = $this->https_request($url);
        return json_decode($res, true);
    }

//https请求
    public function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}