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

class GuessList extends Base
{
    public function initialize()
    {
        parent::initialize();


    }

    /**
     * 猜测列表
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $_data = input();
        if (empty($_data['q_id'])) {
            exit('参数错误！');
        }
        $questionData = Db::name('guess_question')
            ->where('id', '=', $_data['q_id'])
            ->find();
        $optionData = Db::name('guess_option')
            ->where('q_id', '=', $_data['q_id'])
            ->where('status', '=', 0)
            ->column('name', 'id');
        $data = Db::name('guess_list')
            ->alias('l')
            ->field('l.id,l.update_time,l.d_price,l.o_id,u.nickname,u.avatarurl')
            ->where('l.status', '=', 0)
            ->where('l.q_id', '=', $_data['q_id'])
            ->leftJoin('miniapp_user u', 'u.id = l.u_id')
            ->paginate(15);
        // 猜测列表分类汇总
        $optionGroup = $optionData;
        foreach ($data as $row) {
            if (is_array($optionGroup[$row['o_id']])) {
                $optionGroup[$row['o_id']]['u_sum'] ++;
                $optionGroup[$row['o_id']]['s_sum'] += $row['d_price'];
            } else {
                $optionGroup[$row['o_id']] = array(
                    'name' => $optionGroup[$row['o_id']],
                    'u_sum' => 1,
                    's_sum' => $row['d_price']
                );
            }
        }
        foreach ($optionGroup as &$row) {
            if (!is_array($row)) {
                $row = array(
                    'name' => $row,
                    'u_sum' => 0,
                    's_sum' => 0
                );
            }
        }
        $page = $data->render();
        $this->assign('data', $data);
        $this->assign('optionGroup', $optionGroup);
        $this->assign('questionData', $questionData);
        $this->assign('optionData', $optionData);
        $this->assign('page', $page);
        $this->assign('menu_title', '猜测列表');
        return view('list');
    }

    public function add()
    {
        $_data = input();
        if (empty($_data['q_id'])) {
            exit('参数错误!');
        }
        $questionData = Db::name('guess_question')
            ->where('id', '=', $_data['q_id'])
            ->find();
        $optionData = Db::name('guess_option')
            ->where('q_id', '=', $_data['q_id'])
            ->where('status', '=', 0)
            ->select();
        $userData = $this->getUserList();
        $this->assign('questionData', $questionData);
        $this->assign('optionData', $optionData);
        $this->assign('userData', $userData);
        $this->assign('post', $_data);
        $this->assign('menu_title', '添加答题');
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
            if (empty($_data['q_id'])) {
                ajaxMsg(0, '参数有误！');
            }
            if (empty($_data['u_id'])) {
                ajaxMsg(0, '请选择用户！');
            }
            if (empty($_data['o_id'])) {
                ajaxMsg(0, '请选择选项！');
            }
            if (empty($_data['d_price'])) {
                ajaxMsg(0, '请输入响豆数！');
            }
            $id = isset($_data['id']) ? $_data['id'] : '';
            if ($id) {
                if (Db::name('guess_list')->where(['id'=>$id])->update($_data)) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败，没有做任何修改！');
                }
            } else {
                $_data['add_time'] = time();
                if ($id = Db::name('guess_list')->insertGetId($_data)) {
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