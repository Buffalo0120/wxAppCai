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
use think\File;
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
        $cate_id = '';
        $status = '';
        if (Request::isGet()) {
            $_data = input('get.');
            $name = isset($_data['name']) ? $_data['name'] : '';
            $cate_id = isset($_data['cate_id']) ? $_data['cate_id'] : '';
            $status = isset($_data['status']) ? $_data['status'] : '';
            $where = [];
            if (!empty($_data['name'])) {
                $where[] = ['name', 'like', "%{$_data['name']}%"];
            }
            if (!empty($_data['cate_id'])) {
                $where[] = ['cate_id', '=', $_data['cate_id']];
            }
            if (isset($_data['status']) && $_data['status'] !== '') {
                $_data['status'] = $_data['status'] == 't' ? 1 : 0;
                $where[] = ['status', '=', $_data['status']];
            } else {
                $where[] = ['status', '<>', 99];
            }

        }
        $post['name'] = $name;
        $post['cate_id'] = $cate_id;
        $post['status'] = $status;
        $model = new ProductModel();
        $data = $model->where($where)
            ->order('order_id', 'desc')
            ->paginate(15);
        $page = $data->render();
        // 获取商品分类
        $cateData = $this->getCateList();
        // 处理分类
        foreach ($data as $key => $value) {
            $data[$key]['cate'] = '';
            foreach ($cateData as $row) {
                if ($row['id'] == $value['cate_id']) {
                    $data[$key]['cate'] = $row['name'];
                    break;
                }
            }
        }
        $this->assign('cateData', $cateData);
        $this->assign('data', $data);
        $this->assign('post', $post);
        $this->assign('page', $page);
        $this->assign('menu_title', '商品列表');
        return view('list');
    }

    public function add()
    {
        $cateData = $this->getCateList();
        $this->assign('cateData', $cateData);
        $this->assign('uploadImg', getHostDomain() . \url('uploadMediaNewsImage'));
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

    public function getCateList()
    {
        $cateData = Db::name('cate')->where('is_del', 0)->select();
        return $cateData;
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
            if (!empty($_data['pics']) && is_array($_data['pics'])) {
                $_data['pics'] = implode(',', $_data['pics']);
            } else {
                $_data['pics'] = '';
            }

            if ($id) {
                if (Db::name('product')->where(['id' => $id])->update($_data)) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败，没有做任何修改！');
                }
            } else {
                $_data['add_time'] = time();
                if ($id = Db::name('product')->insertGetId($_data)) {
                    // 将id更新到order_id上
                    Db::name('product')->where('id', $id)->update(['order_id' => $id]);
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
                if (Db::name('product')->where(['id' => $id])->update(array('status' => 99))) {
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

    public function import()
    {
        $file = Request::file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $path = ROOT_PATH . 'public' . '/' . 'uploads';
            $info = $file->move($path);
            if($info){
                // 获取文件后缀
                $ext = $info->getExtension();
                if ($ext != 'xls' && $ext != 'xlsx') {
                    ajaxMsg('1', '上传格式不对！非（xls | xlsx）');
                }
                // excel上传成功后，执行Excel导入
                $filename = $path . '/' . date('Ymd') . '/' . $info->getFilename();
                /*vendor("phpexcel.PHPExcel");
                $objReader =\PHPExcel_IOFactory::createReader('Excel2007');
                $PHPExcel = $objReader->load($filename, $encode = 'utf-8');*/
                //var_dump($PHPExcel);die;
                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                ajaxMsg('0', $url);
                echo $info->getFilename();die;
            }else{
                // 上传失败获取错误信息
                ajaxMsg('1', $file->getError());
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
                if (Db::name('product')->where(['id'=>$id])->update(array('order_id'=>$order_id))) {
                    ajaxMsg(1, '修改成功');
                } else {
                    ajaxMsg(0, '修改失败');
                }
            }
        }
    }

}