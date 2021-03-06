<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\miniapp\controller;


use app\common\model\GuessQuestion as GuessQuestionModel;
use think\Db;
use think\facade\Request;
use think\facade\Url;

class GuessQuestion extends Base
{
    public function initialize()
    {
        parent::initialize();


    }

    /**
     * 猜测题列表
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $where = [];
        $title = '';
        if (Request::isGet()) {
            $_data = input('get.');
            $title = isset($_data['title']) ? $_data['title'] : '';
            $where = [];
            if (isset($_data['title'])) {
                $where[] = ['title', 'like', "%{$_data['title']}%"];
            }
            if (!empty($_data['vote_type'])) {
                $where[] = ['vote_type', '=', $_data['vote_type']];
            }
            if (!empty($_data['vote_status'])) {
                switch ($_data['vote_status']) {
                    case 5:
                        $where[] = ['is_settlement', '=', 1];
                        break;
                    case 1 :
                        $where[] = ['start_time', '>', time()];
                        break;
                    case 2 :
                        $where[] = ['start_time', '<=', time()];
                        $where[] = ['stop_time', '>', time()];
                        break;
                    case 3 :
                        $where[] = ['stop_time', '<=', time()];
                        $where[] = ['open_time', '>', time()];
                        break;
                    case 4 :
                        $where[] = ['open_time', '<', time()];
                        break;
                }
            }

        }
        $post['title'] = $title;
        $post['vote_type'] = isset($_data['vote_type']) ? $_data['vote_type'] : '';
        $post['vote_status'] = isset($_data['vote_status']) ? $_data['vote_status'] : '';
        $model = new GuessQuestionModel();
        $data = $model->where('status', '<>', 1)
            ->where($where)
            ->order('order_id', 'desc')
            ->paginate(15);
        $page = $data->render();
        foreach ($data as &$row) {
            // 处理猜测题状态
            $time = time();
            if ($row['is_settlement']) {
                $row['is_settlement_desc'] = '已结算';
            } elseif ($row['start_time'] > $time) {
                $row['is_settlement_desc'] = '未开始';
            } elseif ($row['stop_time'] > $time) {
                $row['is_settlement_desc'] = '进行中';
            } elseif ($row['open_time'] > $time) {
                $row['is_settlement_desc'] = '待开奖';
            } else {
                $row['is_settlement_desc'] = '待结算';
            }

            $row['vote_type_desc'] = $row['vote_type'] == 1 ? '少数派' : ($row['vote_type'] == 2 ? '多数派' : ($row['vote_type'] == 3 ? '预言帝' : ''));
        }
        $this->assign('data', $data);
        $this->assign('post', $post);
        $this->assign('page', $page);
        $this->assign('menu_title', '猜测题');
        return view('list');
    }

    public function add()
    {
        return view('edit');
    }

    public function edit()
    {
        $id = input('id');
        $data = Db::name('guess_question')->where('id',$id)->find();
        $this->assign('data',$data);
        return view('edit');
    }

    public function save()
    {
        if (Request::isPost()) {
            $_data = input('post.');
            if (!empty($_data['start_time'])) {
                $_data['start_time'] = strtotime($_data['start_time']);
            }
            if (!empty($_data['stop_time'])) {
                $_data['stop_time'] = strtotime($_data['stop_time']);
            }
            if (!empty($_data['open_time'])) {
                $_data['open_time'] = strtotime($_data['open_time']);
            }
            // 时间验证
            if ($_data['stop_time'] < $_data['start_time']) {
                ajaxMsg(0, '结束时间需大于开始时间！');
            }
            if ($_data['open_time'] < $_data['stop_time']) {
                ajaxMsg(0, '开奖时间需大于结束时间！');
            }
            $id = $_data['id'];
            unset($_data['id']);
            if (isset($_data['image'])) {
                unset($_data['image']);
            }
            $_data['ad_content'] = isset($_data['editorValue']) ? $_data['editorValue'] : '';
            unset($_data['editorValue']);
            if ($id) {
                if (Db::name('guess_question')->where(['id'=>$id])->update($_data)) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败，没有做任何修改！');
                }
            } else {
                $_data['add_time'] = time();
                if ($id = Db::name('guess_question')->insertGetId($_data)) {
                    // 将id更新到order_id上
                    Db::name('guess_question')->where('id', $id)->update(['order_id' => $id]);
                    ajaxMsg(1, '增加成功');
                } else {
                    ajaxMsg(0, '增加失败');
                }
            }
        }
    }

    public function del()
    {
        if (Request::isPost()) {
            $id = input('post.id');
            if ($id) {
                if (Db::name('guess_question')->where(['id'=>$id])->update(array('status'=>1))) {
                    ajaxMsg(1, '删除成功');
                } else {
                    ajaxMsg(0, '删除失败');
                }
            }
        }
    }

    /**
     * 修改排序
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function saveOrderId()
    {
        if (Request::isPost()) {
            $id = input('post.id');
            $order_id = input('post.order_id');
            if ($id) {
                if (Db::name('guess_question')->where(['id'=>$id])->update(array('order_id'=>$order_id))) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败');
                }
            }
        }
    }
}