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
            ->where('status','<>','1')->select();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $optionData = $option
                    ->field('id,name,q_id')
                    ->where('status','<>','1')
                    ->where('q_id',$value['id'])
                    ->select();
                $data[$key]['optionData'] = $optionData;
                // TODO::数据处理
            }
        }
        dump($data);
        echo json_encode($data);die;

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
            ->where('status','<>',1)
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
            ->where('id',$p_id)->find();
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
            ->leftJoin('be_miniapp_user mu','mu.id = u.u_id')
            ->where('u.id',$u_id)
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
            ->leftJoin('be_guess_question q','q.id = l.q_id')
            ->where('l.u_id',$u_id)
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
            ->where('u_id',$u_id)
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
            ->where('u_id',$u_id)
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
            ->where('u_id',$u_id)
            ->select();
        dump($data);
    }
}