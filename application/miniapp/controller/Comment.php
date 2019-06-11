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

class Comment extends Base
{
    public function initialize()
    {
        parent::initialize();


    }

    /**
     * 评论列表
     * @return \think\response\View
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $where = [];
        $id = '';
        $type = '';
        if (Request::isGet()) {
            $_data = input();
            $id = isset($_data['id']) ? $_data['id'] : '';
            $type = isset($_data['type']) ? $_data['type'] : '';
        }
        if (empty($id) || empty($type)) {
            exit('123');
        }
        $post = ['id' => $id, 'type' => $type];
        $table = $type == 'question' ? 'guess_question' : 'news';
        $where[] = ['c.type', '=', $type];
        $where[] = ['c.q_id', '=', $id];
        $where[] = ['c.is_del', '=', 0];
        $newsData = Db::name($table)->where('id', $id)->find();
        $data = Db::name('comment')
            ->alias('c')
            ->field('c.id,c.content,c.update_time,u.nickname,u.avatarurl')
            ->where($where)
            ->leftJoin('miniapp_user u', 'u.id = c.u_id')
            ->leftJoin($table . ' n', 'n.id = c.q_id')
            ->paginate(15);
        $page = $data->render();
        $data = $data->items();
        foreach ($data as $key => $row) {
            $like_num = Db::name('like')->where('m_id', $row['id'])->count('id');
            $data[$key]['like_num'] = $like_num;
        }

        $this->assign('post', $post);
        $this->assign('newsData', $newsData);
        $this->assign('data', $data);
        $this->assign('page', $page);
        $this->assign('menu_title', '评论列表');
        return view('list');
    }

    public function addLike()
    {
        if (Request::isPost()) {
            $_data = input();
            $num = $_data['num'];
            for ($i = 0; $i < $num; $i++) {
                Db::name('like')->insert(['u_id'=> $_data['u_id'], 'm_id' => $_data['m_id']]);
            }
            ajaxMsg(1, '成功');
        } else {
            $id = input('id');
            if (empty($id)) {
                exit('系统异常！');
            }
            $userData = $this->getUserList();
            $data = Db::name('comment')->where('id', $id)->find();
            $this->assign('data', $data);
            $this->assign('menu_title', '点赞');
            $this->assign('userData', $userData);
            $this->assign('data', $data);
            return view('like');
        }
    }



    public function add()
    {
        $post = input();
        if (empty($post['id']) || empty($post['type'])) {
            exit('系统异常！');
        }
        $type = $post['type'] == 'question' ? 'guess_question' : 'news';
        $userData = $this->getUserList();
        $data = Db::name($type)->where('id', $post['id'])->find();
        $this->assign('post', $post);
        $this->assign('data', $data);
        $this->assign('userData', $userData);
        return view('edit');
    }

    public function edit()
    {
        $cateData = $this->getCateList();
        $this->assign('cateData', $cateData);
        $this->assign('uploadImg', getHostDomain() . \url('uploadMediaNewsImage'));
        $id = input('id');
        $data = Db::name('product')->where('id', $id)->find();
        // 多图处理
        $data['pics'] = explode(',', $data['pics']);

        $this->assign('data', $data);
        return view('edit');
    }

    public function getUserList()
    {
        $userData = Db::name('miniapp_user')
            ->field('id,nickname,openid')
            ->select();
        return $userData;
    }

    public function save()
    {
        if (Request::isPost()) {
            $_data = input('post.');
            $id = isset($_data['id']) ? $_data['id'] : 0;
            if ($id) {
                if (Db::name('comment')->where(['id' => $id])->update($_data)) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败，没有做任何修改！');
                }
            } else {
                if ($id = Db::name('comment')->insertGetId($_data)) {
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
                if (Db::name('comment')->where(['id' => $id])->update(array('is_del' => 1))) {
                    ajaxMsg(1, '删除成功');
                } else {
                    ajaxMsg(0, '删除失败');
                }
            }
        }
    }

    public function setStatus()
    {
        if (Request::isPost()) {
            $id = input('post.id');
            $status = input('post.status');
            if ($id) {
                if (Db::name('product')->where(['id' => $id])->update(array('status' => $status))) {
                    ajaxMsg(1, ($status ? '下架' : '上架') . '成功');
                } else {
                    ajaxMsg(0, ($status ? '下架' : '上架') . '失败');
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
        Db::name('guess_question')->where('id', $q_id)->update(array('right_option' => $id));
        $this->redirect('index', array('q_id' => $q_id));
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
                if (Db::name('product')->where(['id'=>$id])->update(array('order_id'=>$order_id))) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败');
                }
            }
        }
    }

}