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
use app\common\model\GuessOption as GuessOptionModel;
use think\Db;
use think\facade\Request;
use think\facade\Url;

class GuessOption extends Base
{
    public function initialize()
    {
        parent::initialize();


    }

    /**
     * 猜测题选项
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $q_id = input('q_id');
        $where = [];
        $q_id = isset($q_id) ? $q_id : '';
        if (isset($q_id)) {
            $where[] = ['q_id', '=', "{$q_id}"];
        }
        $model = new GuessOptionModel();
        $data = $model->where('status', '<>', 1)
            ->where($where)
            ->paginate(15);
        foreach ($data as $key => $value) {
            $question = Db::name('guess_question')->where('id',$value['q_id'])->find();
            $data[$key]['right_option'] = $question['right_option'];
        }
        $page = $data->render();
        $this->assign('data', $data);
        $this->assign('page', $page);
        $this->assign('q_id', $q_id);
        $this->assign('menu_title', '猜测题选项');
        return view('list');
    }

    public function add()
    {
        $q_id = input('q_id');
        $this->assign('q_id', $q_id);
        return view('edit');
    }

    public function edit()
    {
        $q_id = input('q_id');
        $this->assign('q_id', $q_id);
        $id = input('id');
        $data = Db::name('guess_option')->where('id',$id)->find();
        $this->assign('data',$data);
        return view('edit');
    }

    public function save()
    {
        if (Request::isPost()) {
            $_data = input('post.');
            $id = $_data['id'];
            unset($_data['id']);
            // 去除首位空格
            if (isset($_data['name'])) {
                $_data['name'] = trim($_data['name']);
            }
            if ($id) {
                if (Db::name('guess_option')->where(['id'=>$id])->update($_data)) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败，没有做任何修改！');
                }
            } else {
                $_data['add_time'] = time();
                if ($id = Db::name('guess_option')->insertGetId($_data)) {
                    ajaxMsg(1, '增加成功', array('id' => $id));
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
                if (Db::name('guess_option')->where(['id'=>$id])->update(array('status'=>1))) {
                    ajaxMsg(1, '删除成功');
                } else {
                    ajaxMsg(0, '删除失败');
                }
            }
        }
    }

    public function set()
    {
        $id = input('id');
        $q_id = input('q_id');
        if (empty($id) || empty($q_id)) {
            $this->error('参数错误！');
        }
        Db::name('guess_question')->where('id',$q_id)->update(array('right_option'=>$id));
        $this->redirect('index',array('q_id'=>$q_id));
    }
}