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

    }

    public function del()
    {

    }
}