<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\miniapp\controller;


use app\common\model\Order as OrderModel;
use think\Db;
use think\facade\Request;
use think\facade\Url;

class Order extends Base
{
    public function initialize()
    {
        parent::initialize();


    }

    /**
     * 订单列表
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $where = [];
        $p_name = '';
        if (Request::isGet()) {
            $_data = input('get.');
            $p_name = isset($_data['p_name']) ? $_data['p_name'] : '';
            $where = [];
            if (isset($_data['p_name'])) {
                $where[] = ['o.p_name', 'like', "%{$_data['p_name']}%"];
            }

        }
        $post['p_name'] = $p_name;
        $model = new OrderModel();
        $data = $model
            ->alias('o')
            ->field('o.p_name,o.p_price,o.p_pic,o.num,o.status,o.add_time,u.nickname,o.mem_account')
            ->leftJoin('be_miniapp_user u','u.id = o.u_id')
            ->where($where)
            ->paginate(15);
        $page = $data->render();
        $this->assign('data', $data);
        $this->assign('post', $post);
        $this->assign('page', $page);
        $this->assign('menu_title', '订单列表');
        return view('list');
    }


}