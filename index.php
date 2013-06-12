<?php
include("db.php");
define("TOKEN", "weixin");//定义TOKEN，检测消息是否来自微信服务器的标识
define("PLACE", "1丁香、2竹园、3海棠、4综合楼...");//定义PLACE，若后文的关键字为PLACE的子串，表示该关键字为地址
define("KIND", "盖浇、砂锅、面食、餐点、夹馍、泡馍、主食、饮品、大餐、喝的、奶茶...");//定义PLACE，若后文的关键字为KIND的子串，表示该关键字为类型
define("ALL", "0查看全部quanbuqb");//定义ALL标识，若后文的关键字为ALL的子串，表示需要查看全部消息
$wechatObj = new wechatCallbackapiTest();	//实例化一个wechatCallbackapiTest
$wechatObj->valid();	//用checkSignature()检测消息是否来自微信服务器
$wechatObj->responseMsg();
class wechatCallbackapiTest
{

    public function valid()//用checkSignature()检测消息是否来自微信服务器
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature())
        {
            echo $echoStr.'invalid';
            exit;
        }
    }
      
    private function checkSignature()//获取$token, $timestamp, $nonce，排序后用sha1加密然后对比正确的密问，一样则表示消息来自微信服务器
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];   
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);//将$token, $timestamp, $nonce从小到大排序（微信官方的规定）
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );//将排序好的字符串用sha1方式加密（微信官方的规定）
        if( $tmpStr == $signature )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr))
        {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $all=0;
            if(strstr(PLACE, $keyword))
            {
                if($keyword==1)$keyword='竹园';
                if($keyword==2)$keyword='海棠';
                if($keyword==3)$keyword='丁香';
                if($keyword==4)$keyword='综合楼';
                $row=search_from_db('place', $keyword);
            }
            else if(strstr(KIND, $keyword))$row=search_from_db('kind', $keyword);
            else if(strstr(ALL, $keyword))$all=1;
            $contentStr = $row['place'].'的'.$row['food_name'].'挺不错的，只卖'.$row['price'].'元哦(^^)↓ ↓ 点击阅读全文进入外卖订餐';
    
            $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>123456798</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0<FuncFlag>
            </xml>";
            if(!$row && !$all)
            {
                $contentStr = "欢迎使用西电美食单\n查询规则如下\n0--查看全部美食\n1--竹园\n2--海棠\n3--丁香\n4--综合楼\n按种类请回复文字：砂锅、面食、餐点、夹馍、泡馍、主食、饮品\n更多功能敬请期待(^^)";
                $msgType = "text";
                $resultStr = sprintf($textTpl,$fromUsername,$toUsername,$msgType,$contentStr);
                echo $resultStr;
            }
            $imgTpl = "<xml>
         	<ToUserName><![CDATA[%s]]></ToUserName>
         	<FromUserName><![CDATA[%s]]></FromUserName>
         	<CreateTime>1234654789</CreateTime>
         	<MsgType><![CDATA[%s]]></MsgType>
         	<ArticleCount>1</ArticleCount>
         	<Articles>
         	<item>
         	<Title><![CDATA[%s]]></Title> 
         	<Description><![CDATA[%s]]></Description>
         	<PicUrl><![CDATA[%s]]></PicUrl>
         	<Url><![CDATA[%s]]></Url>
         	</item>
         	</Articles>
         	<FuncFlag>1</FuncFlag>
         	</xml> ";
            if($all==1)
            {
                $msgType = "news";
                $resultStr = sprintf($imgTpl,$fromUsername,$toUsername,$msgType,"西电美食单","西电美食单收录了西电三大食堂和综合楼的大部分美食，点击阅读全文查看全部美食",'http://cfdtlee.sinaapp.com/img/0.jpg','http://cfdtlee.sinaapp.com/mamain.html');
                echo $resultStr;
            }
            if(!empty( $keyword ))
            {
                $msgType = "news";
                $resultStr = sprintf($imgTpl,$fromUsername,$toUsername,$msgType,$row['place'].'的'.$row['food_name'],$contentStr,'http://cfdtlee.sinaapp.com/img/'.$row['photo'].'.jpg','http://cfdtlee.sinaapp.com/getmoreinfo.php?food_id='.$row['photo']);
                echo $resultStr;
            }
    
            else
            {
                echo "Input something...";
            }
        }
        else
        {
            echo "sss";
            exit;
        }
      }
}


?>
