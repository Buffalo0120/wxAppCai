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
        $track_num = '';
        $status = '';
        if (Request::isGet()) {
            $_data = input('get.');
            $p_name = isset($_data['p_name']) ? $_data['p_name'] : '';
            $status = isset($_data['status']) ? $_data['status'] : '';
            $track_num = isset($_data['track_num']) ? $_data['track_num'] : '';
            $where = [];
            if (isset($_data['track_num'])) {
                $where[] = ['o.track_num', 'like', "%{$_data['track_num']}%"];
            }
            if (isset($_data['p_name'])) {
                $where[] = ['o.p_name', 'like', "%{$_data['p_name']}%"];
            }
            if (isset($_data['status']) && $_data['status'] !== '') {
                $where[] = ['status', '=', $_data['status']];
            }

        }
        $post['status'] = $status;
        $post['track_num'] = $track_num;
        $post['p_name'] = $p_name;
        $model = new OrderModel();
        $data = $model
            ->alias('o')
            ->leftJoin('be_miniapp_user u','u.id = o.u_id')
            ->where($where)
            ->order('o.id', 'desc')
            ->paginate(15);
        $page = $data->render();
        $this->assign('data', $data);
        $this->assign('post', $post);
        $this->assign('page', $page);
        $this->assign('menu_title', '订单列表');
        return view('list');
    }

    public function edit()
    {
        $id = input('id');
        $data = Db::name('order_list')->where('id', $id)->find();

        $this->assign('data', $data);
        return view('edit');
    }

    public function save()
    {
        if (Request::isPost()) {
            $_data = input('post.');
            $id = $_data['id'];
            unset($_data['id']);

            if ($id) {
                if (Db::name('order_list')->where(['id' => $id])->update($_data)) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败，没有做任何修改！');
                }
            }
            ajaxMsg(0, '提交有误！');
        }
    }
}