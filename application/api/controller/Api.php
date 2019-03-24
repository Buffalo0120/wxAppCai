<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller;


use app\common\model\GuessQuestion as GuessQuestionModel;
use app\common\model\GuessOption as GuessOptionModel;
use app\common\model\Product as ProductnModel;
use think\Db;
use think\facade\Request;
use think\facade\Url;

class Api extends Base
{
    // 当前请求的用户id
    public $u_id = '';

    public function initialize()
    {
        $this->u_id = input('u_id');
    }

    /**
     * 猜测题列表页数据
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGuessQuestionList()
    {
        $u_id = $this->u_id;
        $model = new GuessQuestionModel();
        $option = new GuessOptionModel();
        // TODO::查询时，需查询出该用户有没有进行猜测，猜测答案是多少
        $data = $model->field('id,title,pic,coin_pool,
        from_unixtime(start_time,"%Y-%m-%d %H:%i:%s") start_time,
        from_unixtime(stop_time,"%Y-%m-%d %H:%i:%s") stop_time,
        right_option')
            ->where('status', '<>', '1')->select();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $optionData = $option
                    ->field('id,name,q_id')
                    ->where('status', '<>', '1')
                    ->where('q_id', $value['id'])
                    ->select();
                $data[$key]['optionData'] = $optionData;
                // TODO::数据处理
            }
        }
        dump($data);
        echo json_encode($data);
        die;

    }

    /**
     * 商品列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProductList()
    {
        $model = new ProductnModel();
        $data = $model
            ->field('name,id,h_price,d_price,pic')
            ->where('status', '<>', 1)
            ->select();
        dump($data);
    }

    /**
     * 商品详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProductDetail()
    {
        $p_id = input('p_id');
        $data = Db::name('product')
            ->field('id,name,pic,h_price,d_price,content')
            ->where('id', $p_id)->find();
        dump($data);
    }

    /**
     * 用户详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberDetail()
    {
        $u_id = $this->u_id;
        $data = Db::name('miniapp_user')
            ->field('u.id,u.nickname,u.avatarurl')
            ->alias('u')
            ->leftJoin('be_miniapp_user mu', 'mu.id = u.u_id')
            ->where('u.id', $u_id)
            ->find();
        dump($data);
    }

    /**
     * 用户猜测记录
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberGuessLogs()
    {
        $u_id = $this->u_id;
        $data = Db::name('guess_list')
            ->alias('l')
            ->leftJoin('be_guess_question q', 'q.id = l.q_id')
            ->where('l.u_id', $u_id)
            ->select();
        dump($data);

    }

    /**
     * 用户响豆记录
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberBeanLogs()
    {
        $u_id = $this->u_id;
        $data = Db::name('bean_logs')
            ->where('u_id', $u_id)
            ->select();
        dump($data);
    }

    /**
     * 用户订单列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberOrderList()
    {
        $u_id = $this->u_id;
        $data = Db::name('order_list')
            ->field('id,u_id,p_name,p_id,p_price,d_price,r_price,p_pic,update_time')
            ->where('u_id', $u_id)
            ->select();
        dump($data);
    }

    /**
     * 用户邀请列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberInvite()
    {
        $u_id = $this->u_id;
        $data = Db::name('miniapp_user')
            ->field('id,nickname,avatarurl,mobile')
            ->where('u_id', $u_id)
            ->select();
        dump($data);
    }

    public function pay()
    {
        if (input('code')) {   //用code获取openid
            $code = input('code');
            $WX_APPID = 'wx28676bd439d7943c';//appid
            $WX_SECRET = 'ba00da09293d6ccdd3aa2a177ad2bcdf';//AppSecret
            $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $WX_APPID . "&secret=" . $WX_SECRET . "&js_code=" . $code . "&grant_type=authorization_code";
            $infos = json_decode(file_get_contents($url), true);
            $openid = $infos['openid'];
        }
        $fee = input('fee');
        //$fee = 0.01;//举例支付0.01
        $appid = 'wx28676bd439d7943c';//appid.如果是公众号 就是公众号的appid
        $body = '标题';
        $mch_id = '1529263091'; //商户号
        $nonce_str = $this->nonce_str();//随机字符串
        $notify_url = 'https://shop.hzjudao.cn/api/api/payReturn'; //回调的url【自己填写】
        $openid = $openid;
        $out_trade_no = $this->order_number($openid);//商户订单号
        //$out_trade_no = 'test001';//商户订单号
        $spbill_create_ip = '47.99.160.245';//服务器的ip【自己填写】;
        $total_fee = $fee * 100;// 微信支付单位是分，所以这里需要*100
        $trade_type = 'JSAPI';//交易类型 默认


        //这里是按照顺序的 因为下面的签名是按照顺序 排序错误 肯定出错
        $post['appid'] = $appid;
        $post['body'] = $body;
        $post['mch_id'] = $mch_id;
        $post['nonce_str'] = $nonce_str;//随机字符串
        $post['notify_url'] = $notify_url;
        $post['openid'] = $openid;
        $post['out_trade_no'] = $out_trade_no;
        $post['spbill_create_ip'] = $spbill_create_ip;//终端的ip
        $post['total_fee'] = $total_fee;//总金额
        $post['trade_type'] = $trade_type;
        $sign = $this->sign($post);//签名
        $post_xml = '<xml>
     <appid>' . $appid . '</appid>
     <body>' . $body . '</body>
     <mch_id>' . $mch_id . '</mch_id>
     <nonce_str>' . $nonce_str . '</nonce_str>
     <notify_url>' . $notify_url . '</notify_url>
     <openid>' . $openid . '</openid>
     <out_trade_no>' . $out_trade_no . '</out_trade_no>
     <spbill_create_ip>' . $spbill_create_ip . '</spbill_create_ip>
     <total_fee>' . $total_fee . '</total_fee>
     <trade_type>' . $trade_type . '</trade_type>
     <sign>' . $sign . '</sign>
    </xml> ';


        //print_r($post_xml);die;
        //统一接口prepay_id
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $xml = $this->http_request($url, $post_xml);


        $array = $this->xml($xml);//全要大写


        //print_r($array);die;
        if ($array['RETURN_CODE'] == 'SUCCESS' && $array['RESULT_CODE'] == 'SUCCESS') {
            $time = time();
            $tmp = array();//临时数组用于签名
            $tmp['appId'] = $appid;
            $tmp['nonceStr'] = $nonce_str;
            $tmp['package'] = 'prepay_id=' . $array['PREPAY_ID'];
            $tmp['signType'] = 'MD5';
            $tmp['timeStamp'] = "$time";


            $data['state'] = 200;
            $data['time_stamp'] = "$time";//时间戳
            $data['nonce_str'] = $nonce_str;//随机字符串
            $data['sign_type'] = 'MD5';//签名算法，暂支持 MD5
            $data['package'] = 'prepay_id=' . $array['PREPAY_ID'];//统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
            $data['pay_sign'] = $this->sign($tmp);//签名,具体签名方案参见微信公众号支付帮助文档;


            // 将数据存一份
            $payData = $data;
            $payData['state'] = 1; // 待支付
            $payData['out_trade_no'] = $out_trade_no; // 订单号
            $payData['total_fee'] = $total_fee; // 费用 （分）
            Db::name('pay_logs')->insert($payData);

        } else {
            $data['state'] = 0;
            $data['text'] = "错误";
            $data['RETURN_CODE'] = $array['RETURN_CODE'];
            $data['RETURN_MSG'] = $array['RETURN_MSG'];
        }


        echo json_encode($data);
    }

    //随机32位字符串
    private function nonce_str()
    {
        $result = '';
        $str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
        for ($i = 0; $i < 32; $i++) {
            $result .= $str[rand(0, 48)];
        }
        return $result;
    }


//生成订单号  date + rand(10,99)
    private function order_number()
    {
        return date('Ymd') . rand(1000, 9999);
    }


//签名 $data要先排好顺序
    private function sign($data)
    {
        $stringA = '';
        foreach ($data as $key => $value) {
            if (!$value) continue;
            if ($stringA) $stringA .= '&' . $key . "=" . $value;
            else $stringA = $key . "=" . $value;
        }
        $wx_key = 'qazecvfdrgscbnopuejrgnnccjkh2019';//申请支付后有给予一个商户账号和密码，登陆后自己设置的key
        $stringSignTemp = $stringA . '&key=' . $wx_key;
        return strtoupper(md5($stringSignTemp));
    }


//curl请求
    public function http_request($url, $data = null, $headers = array())
    {
        $curl = curl_init();
        if (count($headers) >= 1) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_URL, $url);


        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);


        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


//获取xml
    private function xml($xml)
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);
        $data = array();
        //echo json_encode($index);die;
        foreach ($index as $key => $value) {
            if ($key == 'xml' || $key == 'XML') continue;
            $tag = $vals[$value[0]]['tag'];
            $value = $vals[$value[0]]['value'];

            $data[$tag] = $value;
        }
        //var_dump($data);die;
        return $data;
    }

    public function payReturn()
    {
        /*
         * <xml>
              <appid><![CDATA[wx24123c4370ec43b]]></appid>
              <attach><![CDATA[测试测试]]></attach>
              <bank_type><![CDATA[CFT]]></bank_type>
              <fee_type><![CDATA[CNY]]></fee_type>
              <is_subscribe><![CDATA[Y]]></is_subscribe>
              <mch_id><![CDATA[10000100]]></mch_id>
              <nonce_str><![CDATA[5d2b634f23F23da20af46e531c]]></nonce_str>
              <openid><![CDATA[oUpF8uMEb4q23FEWG4Q23R268TekukE]]></openid>
              <out_trade_no><![CDATA[140123153]]></out_trade_no>
              <result_code><![CDATA[SUCCESS]]></result_code>
              <sign><![CDATA[B552EDFW23G423G5DD0D78AB241]]></sign>
              <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
              <time_end><![CDATA[20140903131540]]></time_end>
              <total_fee>1</total_fee>
                <coupon_fee><![CDATA[10]]></coupon_fee>
                <coupon_count><![CDATA[1]]></coupon_count>
                <coupon_type><![CDATA[CASH]]></coupon_type>
                <coupon_id><![CDATA[10000]]></coupon_id>
                <coupon_fee><![CDATA[100]]></coupon_fee>
              <trade_type><![CDATA[JSAPI]]></trade_type>
              <transaction_id><![CDATA[1004400740212312353532168]]></transaction_id>
         </xml>
        */
        $data = file_get_contents('php://input');
        $arr = $this->xmlToArray($data);

        // 根据微信支付回调接口，用订单号查询签名，验证签名和金额是否一致
        $payData = Db::name('pay_logs')->where('out_trade_no', $arr['out_trade_no'])->find();
        if ($payData && $payData['sign'] == $arr['sign']) {
            //判断返回状态
            if ($arr['return_code'] == 'SUCCESS' || $arr['result_code'] == 'SUCCESS') {
                //判断订单金额
                if ($payData['total_fee'] == $arr['total_fee']) {
                    //修改订单状态
                    $res = Db::name('pay_logs')->where('id',$payData['id'])->update(array('state' => 2));
                    if ($res) {
                        $prepay_id = $payData['package'];  //prepay_id要在统一下单的时候做保存
                        $money = $arr['total_fee'] / 100;
                        //$this->sendTemplateMessage($prepay_id, $arr['out_trade_no'], $money, $arr['openid']); //发送模板消息
                        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';//给微信正常相应（如果没有正常相应微信会根据自己的机制多次请求）
                    }else {
                        Db::name('test')->insert(array('content'=>json_encode($arr),'status' => 1));
                    }
                }else {
                    Db::name('test')->insert(array('content'=>json_encode($arr),'status' => 2));
                }
            }
        }else {
            Db::name('test')->insert(array('content'=>json_encode($arr),'status' => 3));
        }
    }

    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
}