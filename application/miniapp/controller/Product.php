<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\miniapp\controller;


use app\common\model\Product as ProductModel;
use think\Db;
use think\facade\Request;
use think\facade\Url;

class Product extends Base
{
    public function initialize()
    {
        parent::initialize();


    }

    /**
     * 商品列表
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $where = [];
        $name = '';
        if (Request::isGet()) {
            $_data = input('get.');
            $name = isset($_data['name']) ? $_data['name'] : '';
            $where = [];
            if (isset($_data['name'])) {
                $where[] = ['name', 'like', "%{$_data['name']}%"];
            }

        }
        $post['name'] = $name;
        $model = new ProductModel();
        $data = $model->where('status', '<>', 1)
            ->where($where)
            ->paginate(15);
        $page = $data->render();
        $this->assign('data', $data);
        $this->assign('post', $post);
        $this->assign('page', $page);
        $this->assign('menu_title', '商品列表');
        return view('list');
    }

    public function add()
    {
        $this->assign('uploadImg', getHostDomain() . \url('uploadMediaNewsImage'));
        return view('edit');
    }

    public function edit()
    {
        $this->assign('uploadImg', getHostDomain() . \url('uploadMediaNewsImage'));
        $id = input('id');
        $data = Db::name('product')->where('id',$id)->find();
        $this->assign('data',$data);
        return view('edit');
    }

    public function save()
    {
        if (Request::isPost()) {
            $_data = input('post.');
            $id = $_data['id'];
            unset($_data['id']);
            if (isset($_data['image'])) {
                unset($_data['image']);
            }
            $_data['content'] = $_data['editorValue'];
            unset($_data['editorValue']);
            if ($id) {
                if (Db::name('product')->where(['id'=>$id])->update($_data)) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败，没有做任何修改！');
                }
            } else {
                $_data['add_time'] = time();
                if (Db::name('product')->insert($_data)) {
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
                if (Db::name('product')->where(['id'=>$id])->update(array('status'=>1))) {
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