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
            $infos = json_decode(file_get_contents($url));
            $openid = $infos->openid;
        }
        //$fee = I("post.total_fee");
        $fee = 0.01;//举例支付0.01
        $appid = 'wx28676bd439d7943c';//appid.如果是公众号 就是公众号的appid
        $body = '标题';
        $mch_id = '1529263091'; //商户号
        $nonce_str = $this->nonce_str();//随机字符串
        $notify_url = ''; //回调的url【自己填写】
        $openid = $openid;
        $out_trade_no = $this->order_number();//商户订单号
        $spbill_create_ip = '';//服务器的ip【自己填写】;
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


        //print_r($array);
        if ($array['RETURN_CODE'] == 'SUCCESS' && $array['RESULT_CODE'] == 'SUCCESS') {
            $time = time();
            $tmp = '';//临时数组用于签名
            $tmp['appId'] = $appid;
            $tmp['nonceStr'] = $nonce_str;
            $tmp['package'] = 'prepay_id=' . $array['PREPAY_ID'];
            $tmp['signType'] = 'MD5';
            $tmp['timeStamp'] = "$time";


            $data['state'] = 200;
            $data['timeStamp'] = "$time";//时间戳
            $data['nonceStr'] = $nonce_str;//随机字符串
            $data['signType'] = 'MD5';//签名算法，暂支持 MD5
            $data['package'] = 'prepay_id=' . $array['PREPAY_ID'];//统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
            $data['paySign'] = $this->sign($tmp);//签名,具体签名方案参见微信公众号支付帮助文档;
            $data['out_trade_no'] = $out_trade_no;


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


//生成订单号
    private function order_number($openid)
    {
        $openid = empty($openid) ? date('Ymd') : $openid;
        //date('Ymd',time()).time().rand(10,99);//18位
        return md5($openid . time() . rand(10, 99));//32位
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
        $wx_key = '';//申请支付后有给予一个商户账号和密码，登陆后自己设置的key
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
        $data = "";
        foreach ($index as $key => $value) {
            if ($key == 'xml' || $key == 'XML') continue;
            $tag = $vals[$value[0]]['tag'];
            $value = $vals[$value[0]]['value'];
            $data[$tag] = $value;
        }
        return $data;
    }
}