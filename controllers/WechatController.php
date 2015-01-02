<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class WechatController extends Controller
{
    public function behaviors()
    {
        return [
        ];
    }

    public function actions()
    {
        return [
        ];
    }

    /*
    public function actionMessage()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = 'ilovemaomao';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            echo $_GET["echostr"];
        }else{
            echo ''; 
        }   
    }*/

    public function actionGetMenu()
    {
        $appid="";//填写appid
        $secret="";//填写secret

        $url = "https://.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $a = curl_exec($ch);
        $strjson=json_decode($a);
        $token = $strjson->access_token;
        
        $url = "https://.weixin.qq.com/cgi-bin/menu/get?access_token={$token}"; //查询地址 
        $ch = curl_init();//新建curl
        curl_setopt($ch, CURLOPT_URL, $url);//url  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $b = curl_exec($ch); //输出   
        curl_close($ch); 

        echo $b;
    }

    public function actionUpdateMenu()
    {
        $appid="";//填写appid
        $secret="";//填写secret

        $url = "https://.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $a = curl_exec($ch);


        $strjson=json_decode($a);
        $token = $strjson->access_token;
        $post="{
            \"button\":[
                {
                \"type\":\"click\",
                \"name\":\"法律咨询\",
                \"sub_button\":[
                    {
                    \"type\":\"view\",
                    \"name\":\"提问\",
                    \"url\":\"http://lawyer-help.cn\"
                    },
                    {
                    \"type\":\"click\",
                    \"name\":\"电话\",
                    \"key\":\"key_tele\"
                    }
                    {
                    \"type\":\"view\",
                    \"name\":\"投稿\",
                    \"url\":\"http://lawyer-help.cn/site/contact\"
                    },
                ]
                },
                {
                \"type\":\"click\",
                \"name\":\"热点事件\",
                \"sub_button\":[
                    {
                    \"type\":\"click\",
                    \"name\":\"我要离婚\",
                    \"key\":\"key_wylh\"
                    },
                    {
                    \"type\":\"click\",
                    \"name\":\"我要创业\",
                    \"key\":\"key_wycy\"
                    }
                    {
                    \"type\":\"click\",
                    \"name\":\"我闯祸了\",
                    \"key\":\"key_wchl\"
                    }
                    {
                    \"type\":\"click\",
                    \"name\":\"我被骗了\",
                    \"key\":\"key_wbpl\"
                    }
                ]
                },

                {
                \"type\":\"click\",
                \"name\":\"关于我们\",
                \"sub_button\":[
                    {
                    \"type\":\"view\",
                    \"name\":\"我们应用\",
                    \"url\":\"http://lawyer-help.cn\"
                    },
                    {
                    \"type\":\"view\",
                    \"name\":\"我们团队\",
                    \"url\":\"http://lawyer-help.cn/site/about\"
                    },
                ]
                }
        ]
    }";  //提交内容
    $url = "https://.weixin.qq.com/cgi-bin/menu/create?access_token={$token}"; //查询地址 
    $ch = curl_init();//新建curl
    curl_setopt($ch, CURLOPT_URL, $url);//url  
    curl_setopt($ch, CURLOPT_POST, 1);  //post
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);//post内容  
    curl_exec($ch); //输出   
    curl_close($ch); 
    }

    public function actionMessage()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch ($RX_TYPE) {
            case 'text':
                $resultStr = $this->receiveText($postObj);
                break;
            case 'event':
                $resultStr = $this->receiveEvent($postObj);
                break;

            default:
                break;
            }

            echo $resultStr;
        }else {
            echo '';
            exit;
        }
    }

    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        if(!empty( $keyword )) {
            switch ($keyword) {
            case '感谢有你':
                $resultStr = $this->transmitText($object, '我爱周泓');
                break;
            /*
            case '1':
                $a = Util::loadConfig('wechat_mxwy');
                $resultStr = $this->transmitNews($object, $a);
                break;
            case '2':
                $a = Util::loadConfig('wechat_cwjt');
                $resultStr = $this->transmitNews($object, $a);
                break;
            case '3':
                $a = Util::loadConfig('wechat_wqhd');
                $resultStr = $this->transmitNews($object, $a);
                break;
            case '4':
                $a = Util::loadConfig('wechat_gxtp');
                $resultStr = $this->transmitNews($object, $a);
                break;*/
            default:
                break;
            }
        }
        if (isset($resultStr)) {
            return $resultStr;
        } else {
            echo '';
            exit;
        }
    }

    private function receiveEvent($object)
    {
        switch ($object->Event)
        {
        case "subscribe":
            $contentStr = "律师帮，帮天下";
            $resultStr = $this->transmitText($object, $contentStr);
            break;
        case "CLICK":
            $resultStr = $this->receiveClick($object);
            break;    
        default:
            break;
        }
        if (isset($resultStr)) {
            return $resultStr;
        } else {
            echo '';
            exit;
        }
    }

    private function receiveClick($object)
    {
        switch ($object->EventKey) {
            default:
                // code...
                break;
        }
        if (isset($resultStr)) {
            return $resultStr;
        } else {
            echo '';
            exit;
        }
    }

    private function transmitText($object, $content) 
    { 
        $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0<FuncFlag>
            </xml>";
        $msgType = "text";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $msgType, $content); 

        return $resultStr; 
    }

    private function transmitNews($object, $arr_item) 
    { 
        if(!is_array($arr_item)) return;

        $itemTpl = "
            <item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
            </item>
            ";
        $item_str = ""; 
        foreach ($arr_item as $item) 
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml> 
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <ArticleCount>%s</ArticleCount>
            <Articles>
            $item_str
            </Articles>
            <FuncFlag>0<FuncFlag>
            </xml>";
        $resultStr = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item)); 

        return $resultStr; 
    } 
}
