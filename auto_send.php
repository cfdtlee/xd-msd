<?php
// 微信公众账号
 define("Account", "cfdtlee@gmail.com");
// 微信公众号登陆密码  MD5加密
 define("PassWord", "e0c3d1e1da19d9101ae5c50f0c74c27e");

class __user_remote_opera
{
// 模拟登录
    public function login()
    {
        $snoopy = new Snoopy(); 
        $submit = "http://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN";
        $post["username"] = Account;
        $post["pwd"] = PassWord;
        $post["f"] = "json";
        $snoopy->submit($submit,$post);
        $cookie = '';
        foreach ($snoopy->headers as $key => $value) 
        {
            $value = trim($value);
            if(strpos($value,'Set-Cookie: ') || strpos($value,'Set-Cookie: ') === 0)
            {
                $tmp = str_replace("Set-Cookie: ","",$value);
                $tmp = str_replace("Path=/","",$tmp);
                $cookie .= $tmp;
            }
        }
        if(!$cookie)
        {
            $this->login();
        }
        else
        {
            $this->write("cookie.log",$cookie);
            return $cookie;
        }
    }
 
// 主动推送信息
    public function sendTextMsg($fakeId,$content)
    {
        $cookie = $this->read('cookie.log');
        $send_snoopy = new Snoopy(); 
        $send_snoopy->agent = "(Mozilla/5.0 (Windows NT 5.1; rv:19.0) Gecko/20100101 Firefox/19.0)"; //伪装浏览器  
        $send_snoopy->referer = "http://mp.weixin.qq.com/cgi-bin/singlemsgpage?token=2107899697&fromfakeid=".$fakeId."&msgid=&source=&count=20&t=wxm-singlechat&lang=zh_CN"; //伪装来源页地址 http_referer  
        $send_snoopy->rawheaders['Cookie'] = $cookie;
        $send_snoopy->rawheaders["Pragma"] = "no-cache"; //cache 的http头信息  
        $send_snoopy->rawheaders["Host"] = "mp.weixin.qq.com"; 
        $post = array();
        $post['tofakeid'] = $fakeId;
        $post['type'] = 1;
        $post['content'] = $content;
        $post['ajax'] = 1;
        $submit = "http://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response";
        $send_snoopy->submit($submit,$post);
        $result = $send_snoopy->results;
        if($result['ret'] != 0)
        {
            $cookie = $this->login();
            $this->sendTextMsg($fakeId,$content);
        }
        else
        {
            return $result;
        }
    }

    public function write($filename,$content)
    {
        $__sae = new SaeStorage();
        $fp = $__sae->write("tempfile",$filename,$content);
    }
    // 读文件
    public function read($filename)
    {
        $__sae = new SaeStorage();
        if($__sae->fileExists("tempfile",$filename))
        {
            $data = '';
            $data = $__sae->read("tempfile",$filename);
            if($data)
            {
                $send_snoopy = new Snoopy(); 
                $send_snoopy->rawheaders['Cookie'] = $data;
                $submit = "http://mp.weixin.qq.com/cgi-bin/getcontactinfo?t=ajax-getcontactinfo&lang=zh_CN&fakeid=";
                $send_snoopy->submit($submit,array());
                $result = json_decode($send_snoopy->results,1);
                if(!$result)
                {
                    return $this->login();
                }
                else
                {
                    return $data;
                }
            }
            else
            {
                return $this->login();
            }
        }
        else
        {
            return $this->login();
        }
    }
}

?>


