<?php

namespace app\miniapp\controller;

use think\Db;
use think\facade\Request;

class Category extends Base
{
    public function initialize()
    {
        parent::initialize();


    }

    public function index()
    {
        $data = Db::name('cate')->where('is_del', 0)->paginate();
        $page = $data->render();

        $this->assign('data', $data);
        $this->assign('page', $page);
        $this->assign('menu_title', '分类列表');
        return view('list');
    }

    public function add()
    {
        $cateData = Db::name('cate')
            ->where('is_del', 0)
            ->where('p_id', 0)
            ->select();

        $this->assign('cateData', $cateData);
        return view('edit');
    }

    public function edit()
    {
        $cateData = Db::name('cate')
            ->where('is_del', 0)
            ->where('p_id', 0)
            ->select();

        $id = input('id');
        $data = Db::name('cate')->where('id', $id)->find();

        $this->assign('data', $data);
        $this->assign('cateData', $cateData);
        return view('edit');

    }

    public function save()
    {
        if (Request::isPost()) {
            $_data = input('post.');
            $id = $_data['id'];
            unset($_data['id']);

            if ($id) {
                if (Db::name('cate')->where(['id' => $id])->update($_data)) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败，没有做任何修改！');
                }
            } else {
                $_data['add_time'] = time();
                if (Db::name('cate')->insert($_data)) {
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
                if (Db::name('cate')->where(['id' => $id])->update(array('is_del' => 1))) {
                    ajaxMsg(1, '删除成功');
                } else {
                    ajaxMsg(0, '删除失败');
                }
            }
        }
    }
}