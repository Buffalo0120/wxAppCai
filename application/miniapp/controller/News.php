<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\miniapp\controller;


use think\Db;
use think\facade\Request;
use think\File;
use think\facade\Url;

class News extends Base
{
    public function initialize()
    {
        parent::initialize();


    }

    /**
     * 新闻列表
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $where = [];
        $title = '';
        $cate_id = '';
        if (Request::isGet()) {
            $_data = input('get.');
            $title = isset($_data['title']) ? $_data['title'] : '';
            $cate_id = isset($_data['cate_id']) ? $_data['cate_id'] : '';
            $where = [];
            if (!empty($_data['title'])) {
                $where[] = ['title', 'like', "%{$_data['title']}%"];
            }
            if (!empty($_data['cate_id'])) {
                $where[] = ['cate_id', '=', $_data['cate_id']];
            }

        }
        $post['title'] = $title;
        $post['cate_id'] = $cate_id;
        $data = Db::name('news')->where('is_del', '<>', 1)
            ->where($where)
            ->order('order_id', 'desc')
            ->paginate(15);
        $page = $data->render();

        $this->assign('data', $data);
        $this->assign('post', $post);
        $this->assign('page', $page);
        $this->assign('menu_title', '新闻列表');
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
        $data = Db::name('news')->where('id', $id)->find();

        $this->assign('data', $data);
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
            if (isset($_data['editorValue'])) {
                $_data['content'] = $_data['editorValue'];
                unset($_data['editorValue']);
            }

            if ($id) {
                if (Db::name('news')->where(['id' => $id])->update($_data)) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败，没有做任何修改！');
                }
            } else {
                $_data['add_time'] = time();
                if ($id = Db::name('news')->insertGetId($_data)) {
                    // 将id更新到order_id上
                    Db::name('news')->where('id', $id)->update(['order_id' => $id]);
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
                if (Db::name('news')->where(['id' => $id])->update(array('is_del' => 1))) {
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
                if (Db::name('news')->where(['id'=>$id])->update(array('order_id'=>$order_id))) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败');
                }
            }
        }
    }

}