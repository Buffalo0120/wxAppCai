<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller;


use app\common\model\GuessQuestion as GuessQuestionModel;
use app\common\model\GuessOption as GuessOptionModel;
use app\common\model\Product as ProductnModel;
use think\console\Table;
use think\Db;
use think\facade\Request;
use think\facade\Url;

class Api extends Base
{
    // 当前请求的用户id
    public $u_id = '';

    // 定义返回数据格式
    public $return = array(
        'retCode' => 1,
        'retMsg' => '',
        'retData' => array()
    );

    // 响豆日志中的响豆类型
    public $beanLogsType = array(
        1 => '支出',
        2 => '收入',
    );

    // 响豆日志中的类型
    public $beanLogsStatus = array(
        1 => '参与猜测',
        2 => '预测成功奖励',
        3 => '商品兑换',
        4 => '活动奖励',
        5 => '其他',
    );

    /**
     * 初始化函数
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function initialize()
    {
        if (!empty(input('u_id'))) {
            $this->u_id = input('u_id');

        }
        /*elseif (!empty(input('code'))) {
            $this->u_id = $this->getUserId(input('code'));
        }*/
    }

    /**
     * 设置返回数据
     * @param $retCode
     * @param $retMsg
     * @param array $data
     */
    protected function setReturnInfo($retCode, $retMsg, $data = array())
    {
        $this->return = array(
            'retCode' => $retCode,
            'retMsg' => $retMsg,
            'retData' => $data
        );
    }

    /**
     * 获取open_id
     * @param $code
     * @return mixed
     */
    private function getOpenId($code)
    {
        $WX_APPID = 'wx28676bd439d7943c';//appid
        $WX_SECRET = 'ba00da09293d6ccdd3aa2a177ad2bcdf';//AppSecret
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $WX_APPID . "&secret=" . $WX_SECRET . "&js_code=" . $code . "&grant_type=authorization_code";
        $infos = json_decode(file_get_contents($url), true);
        return isset($infos['openid']) ? $infos['openid'] : '';
    }

    /**
     * 根据code 或 open_id 获取用户id
     * @param $code
     * @param null $openId
     * @return int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getUserId($code, $openId = null)
    {
        if (!$openId) {
            $openId = $this->getOpenId($code);
        }
        $userData = Db::name('miniapp_user')->field('id')->where('openid', $openId)->find();
        return $userData ? $userData['id'] : 0;
    }

    /**
     * 设置响豆记录数据
     * @param $u_id
     * @param $num
     * @param int $type
     * @param int $status
     * @return array
     */
    private function setBeanData($u_id, $num, $type = 1, $status = 1)
    {
        return array(
            'u_id' => $u_id,
            'type' => $type,
            'status' => $status,
            'd_price' => $num,
            'add_time' => time()
        );
    }

    /*----------------保存数据------------------*/

    /**
     * 保存用户信息
     */
    public function saveUserInfo()
    {
        $_data = input('post.');

        if (!empty($_data['u_id'])) {
            // 判断数据中是否有id，如果id存在，则修改数据
            if (isset($_data['code'])) {
                unset($_data['code']);
            }
            $ret = Db::name('miniapp_user')->where('id', $_data['u_id'])->update($_data);
            $this->setReturnInfo($ret ? 0 : 1,$ret ? '保存成功！' : '保存失败！');

        } elseif (!empty($_data['code'])) {
            // 判断数据中是否有code
            $openId = $this->getOpenId($_data['code']);
            if (empty($openId)) {
                $this->setReturnInfo(400, '获取用户open_id失败！');
            } else {
                // 根据code去查是否存在该用户
                $id = $this->getUserId($_data['code'], $openId);
//                $id = $this->getUserId($_data['code'], $openId);
                // 去除数据中的code
                unset($_data['code']);

                if ($id) {
                    // 如果数据存在，则执行修改
                    $ret = Db::name('miniapp_user')->where('id', $id)->update($_data);
                    $this->setReturnInfo($ret ? 0 : 1,$ret ? '保存成功！' : '保存失败！', array('u_id' => $id));
                } else {
                    // 如果数据不存在，则执行添加
                    $_data['openid'] = $openId;
                    $_data['create_time'] = time();
                    $ret = Db::name('miniapp_user')->insertGetId($_data);
                    $this->setReturnInfo($ret ? 0 : 1,$ret ? '保存成功！' : '保存失败！', array('u_id' => $ret));
                }
            }

        } else {
            $this->setReturnInfo(100, '数据异常，用户id和code都为空！');
        }

        // 返回数据
        echo json_encode($this->return);die;
    }

    /**
     * 保存投票信息
     */
    public function saveVoteInfo()
    {
        $_data = input('post.');
        // 数据验证
        if (empty($_data['q_id'])) {
            $this->setReturnInfo(100, '数据异常，题目id为空！');
            // 返回数据
            echo json_encode($this->return);die;
        }
        if (empty($_data['o_id'])) {
            $this->setReturnInfo(100, '数据异常，选项id为空！');
            // 返回数据
            echo json_encode($this->return);die;
        }
        if (empty($_data['d_price'])) {
            $this->setReturnInfo(100, '数据异常，响豆数量为空！');
            // 返回数据
            echo json_encode($this->return);die;
        }
        // 获取用户id
        if (empty($_data['u_id'])) {
            $this->setReturnInfo(100, '数据异常，未获取到用户id！');
        }
        if (isset($_data['code'])) {
            unset($_data['code']);
        }

        // 根据用户id和题目id，验证该用户是否已提交过投票
        $guestData = Db::name('guess_list')
            ->field('id')
            ->where(array('u_id' => $_data['u_id'], 'q_id' => $_data['q_id']))
            ->find();
        if ($guestData) {
            $this->setReturnInfo(1, '您已对该题目投过票了！');
            // 返回数据
            echo json_encode($this->return);die;
        }

        $_data['add_time'] = time();

        // 保存数据
        $ret = Db::name('guess_list')->insert($_data);
        $this->setReturnInfo($ret ? 0 : 1,$ret ? '保存成功！' : '保存失败！');

        // 记录响豆记录
        $beanData = $this->setBeanData($_data['u_id'], $_data['d_price']);
        Db::name('bean_logs')->insert($beanData);

        // 返回数据
        echo json_encode($this->return);die;
    }

    /**
     * 保存下单信息
     */
    public function saveShopInfo()
    {
        $_data = input('post.');
        // 数据验证
        if (empty($_data['p_id'])) {
            $this->setReturnInfo(100, '数据异常，商品id为空！');
            // 返回数据
            echo json_encode($this->return);die;
        }
        // 根据产品id，获取产品信息
        $proData = Db::name('product')->where('id', $_data['p_id'])->find();
        if (!$proData) {
            $this->setReturnInfo(100, '商品不存在！');
            // 返回数据
            echo json_encode($this->return);die;
        }

        // 数量
        $_data['num'] = empty($_data['num']) ? 1 : $_data['num'];

        // 获取用户id
        if (empty($_data['u_id'])) {
            $this->setReturnInfo(100, '数据异常，未获取到用户id！');
        }
        if (isset($_data['code'])) {
            unset($_data['code']);
        }
        // 保存商品信息
        $_data['p_name'] = $proData['name'];
        $_data['p_price'] = $proData['n_price'];
        $_data['d_price'] = $proData['d_price'];
        $_data['p_pic'] = $proData['d_pic'];
        $_data['p_freight'] = $proData['freight'];
        $_data['status'] = 0; // 购物车状态
        $_data['add_time'] = time();

        $ret = Db::name('order_list')->insert($_data);

        $this->setReturnInfo($ret ? 0 : 1,
            $ret ? '保存成功！' : '保存失败！',
            $ret ? array('o_id' => $ret) : array());
        // 返回数据
        echo json_encode($this->return);die;
    }

    /**
     * 保存收货信息
     */
    public function saveAddressInfo()
    {
        $_data = input('post.');
        // 获取用户id
        if (empty($_data['u_id'])) {
            $this->setReturnInfo(100, '数据异常，未获取到用户id！');
        }
        if (isset($_data['code'])) {
            unset($_data['code']);
        }

        $a_id = '';
        if (isset($_data['a_id'])) {
            $a_id = $_data['a_id'];
            unset($_data['a_id']);
        }
        // 判断是修改还是添加
        if (empty($a_id)) {
            $_data['add_time'] = time();
            $ret = $a_id = Db::name('address')->insertGetId($_data);
            $this->setReturnInfo($ret ? 0 : 1,$ret ? '保存成功！' : '保存失败！', array('a_id' => $ret));
        } else {
            $ret = Db::name('address')->where('id', $a_id)->update($_data);
            $this->setReturnInfo($ret ? 0 : 1,$ret ? '保存成功！' : '保存失败！');
        }

        // 验证status，如果不为空，则把其他收货地址置为非默认
        if ($ret && !empty($_data['status'])) {
            Db::name('address')
                ->where('u_id', $_data['u_id'])
                ->where('id', '<>',  $a_id)
                ->update(array('status' => 0));
        }
        // 返回数据
        echo json_encode($this->return);die;
    }

    /**
     * 删除收货地址
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delAddressInfo()
    {
        $_data = input('post.');
        if (empty($_data['a_id'])) {
            $this->setReturnInfo(100, '数据异常，未获取到地址id！');
        } else {
            $ret = Db::name('address')->where('id', $_data['a_id'])->delete();
            $this->setReturnInfo($ret ? 0 : 1,$ret ? '删除成功！' : '删除失败！');
        }
        // 返回数据
        echo json_encode($this->return);die;
    }

    /**
     * 保存评论信息
     */
    public function saveComment()
    {
        $_data = input('post.');
        // 数据验证
        if (empty($_data['u_id'])) {
            $this->setReturnInfo(100, '数据异常，未获取到用户id！');
        }
        if (empty($_data['q_id'])) {
            $this->setReturnInfo(100, '数据异常，未获取到猜测题id！');
        }
        if (empty($_data['content'])) {
            $this->setReturnInfo(100, '数据异常，未获取到评论内容！');
        }
        // 组装需要获取的数据
        $data['u_id'] = $_data['u_id'];
        $data['q_id'] = $_data['q_id'];
        $data['content'] = $_data['content'];
        // 保存数据
        $ret = Db::name('comment')->insertGetId($data);
        $this->setReturnInfo($ret ? 0 : 1,$ret ? '保存成功！' : '保存失败！');
    }

    /**
     * 点赞
     */
    public function saveLike()
    {
        $_data = input('post.');
        // 数据验证
        if (empty($_data['u_id'])) {
            $this->setReturnInfo(100, '数据异常，未获取到用户id！');
        }
        if (empty($_data['m_id'])) {
            $this->setReturnInfo(100, '数据异常，未获取到评论id！');
        }
        // 验证是否已经点过赞了
        $comment = Db::name('like')
            ->where('u_id', $_data['u_id'])
            ->where('m_id', $_data['m_id'])
            ->find();
        if ($comment) {
            $this->setReturnInfo(100, '已经点过赞了！');
        }
        // 组装需要获取的数据
        $data['u_id'] = $_data['u_id'];
        $data['m_id'] = $_data['m_id'];
        // 保存数据
        $ret = Db::name('like')->insertGetId($data);
        $this->setReturnInfo($ret ? 0 : 1,$ret ? '保存成功！' : '保存失败！');
    }

    /**
     * 签到
     */
    public function checkIn()
    {
        $_data = input('post.');
        // 数据验证
        if (empty($_data['u_id'])) {
            $this->setReturnInfo(100, '数据异常，未获取到用户id！');
        }
        // 验证是否已经签到
        $checkInData = Db::name('check_in_logs')
            ->where('u_id', $_data['u_id'])
            ->whereLike('update_time', '%' .date('Y-m-d') . '%')
            ->find();
        if (!empty($checkInData)) {
            $this->setReturnInfo(100, '已签到！');
        } else {
            $systemInfo = Db::name('setting')
                ->field('value')
                ->where('name', '=', 'systemconf')
                ->find();
            $check_in = json_decode($systemInfo['value'], true)['check_in'];

            // 更新用户响豆总数
            $sql = "update be_miniapp_user set score = score + " . $check_in . " where id = ?";
            Db::name('miniapp_user')->query($sql, array($_data['u_id']));

            // 记录用户响豆记录
            $beanData = $this->setBeanData($_data['u_id'], $check_in, 2, 5);
            Db::name('bean_logs')->insert($beanData);

            // 记录签到日志
            $check_in_data = array(
                'u_id' => $_data['u_id'],
                'd_price' => $check_in
            );
            Db::name('check_in_logs')->insert($check_in_data);

            $this->setReturnInfo(0, '签到成功！');
        }

        echo json_encode($this->return);die;
    }

    /*----------------请求数据------------------*/

    /**
     * 猜测题列表页数据
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGuessQuestionList()
    {
        $u_id = $this->u_id;
        $model = new GuessQuestionModel();
        $option = new GuessOptionModel();
        // 查询时，需查询出该用户有没有进行猜测，猜测答案是多少
        $data = $model->field('id,title,pic,coin_pool,type,vote_type,description,
        from_unixtime(start_time,"%Y/%m/%d %H:%i:%s") start_time,
        from_unixtime(stop_time,"%Y/%m/%d %H:%i:%s") stop_time,
        from_unixtime(open_time,"%Y/%m/%d %H:%i:%s") open_time,
        right_option')
            ->where('status', '<>', '1')->select();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                // 获取猜测题的选项
                $optionData = $option
                    ->field('id,name,q_id')
                    ->where('status', '<>', '1')
                    ->where('q_id', $value['id'])
                    ->select();
                $data[$key]['optionData'] = $optionData;
                // 投票截至后，根据选项，获取每个选项的参与人数
                foreach ($optionData as $k => $v) {
                    if (time() > strtotime($value['stop_time'])) {
                        $guessCount = Db::name('guess_list')
                            ->where('o_id', '=', $v['id'])
                            ->where('q_id', '=', $value['id'])
                            ->count();
                        $data[$key]['optionData'][$k]['countNum'] = $guessCount;
                    } else {
                        $data[$key]['optionData'][$k]['countNum'] = 0;
                    }
                }
                // 统计参与猜测题的总响豆数+后台设置的基础响豆数
                $userCoins = Db::name('guess_list')->where('q_id', $value['id'])->sum('d_price');
                $data[$key]['coin_pool'] += $userCoins;

                // 根据用户id和猜测题id，查询猜测记录
                $guessData = Db::name('guess_list')
                    ->where('u_id', '=', $u_id)
                    ->where('q_id', '=', $value['id'])
                    ->field('o_id, d_price')
                    ->find();
                $data[$key]['guessData'] = $guessData;
            }
        }
        echo json_encode($data);die;

    }

    /** 猜测题详细数据
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGuessQuestionDetail()
    {
        $u_id = $this->u_id;
        $model = new GuessQuestionModel();
        $option = new GuessOptionModel();
        $question_id = input('id');
        // 猜测题数据
        $data = $model->field('id,title,pic,coin_pool,type,vote_type,description,
        from_unixtime(start_time,"%Y/%m/%d %H:%i:%s") start_time,
        from_unixtime(stop_time,"%Y/%m/%d %H:%i:%s") stop_time,
        from_unixtime(open_time,"%Y/%m/%d %H:%i:%s") open_time,
        right_option')
            ->where('id', $question_id)->find();
        // 统计参与猜测题的总响豆数+后台设置的基础响豆数
        $userCoins = Db::name('guess_list')->where('q_id', $question_id)->sum('d_price');

        $data['coin_pool'] = empty($data['coin_pool']) ? $userCoins : $data['coin_pool'] + $userCoins;

        // 选项数据
        $optionData = $option
            ->field('id,name,q_id')
            ->where('status', '<>', '1')
            ->where('q_id', $question_id)
            ->select();

        // 根据猜测题id，获取评论信息以及点赞信息
        $commentData = Db::name('comment')
            ->alias('c')
            ->field('c.id,c.u_id,c.q_id,c.content,c.update_time,u.avatarurl,u.nickname')
            ->where('c.q_id', $data['id'])
            ->leftJoin('miniapp_user u', 'u.id = c.u_id')
            ->select();
        foreach ($commentData as &$row) {
            $likeData = Db::name('like')
                ->field('u_id')
                ->where('m_id', $row['id'])
                ->select();
            $row['likeCount'] = count($likeData);
            $row['have_like']= false;
            if (!empty($u_id) && in_array($u_id, $likeData)) {
                $row['have_like'] = true;
            }
        }

        $data['optionData'] = $optionData;
        $data['commentData'] = $commentData;
        echo json_encode($data);die;
    }

    /**
     * 获取产品分类
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProductCate()
    {
        $data = Db::name('cate')->field('id,name,p_id')->where('is_del', 0)->select();
        echo json_encode($data);die;
    }

    /**
     * 商品列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProductList()
    {
        $_data = input('post.');

        $model = new ProductnModel();
        $where[] = array('status', '<>', 1);
        // 分类id
        if (!empty($_data['cate_id'])) {
            $where[] = array('cate_id', '=', $_data['cate_id']);
        }
        $data = $model
            ->alias('p')
            ->field('p.name,p.id,p.h_price,p.n_price,p.description,p.d_price,p.pic,p.is_overseas,p.cate_id,c.name cate_name')
            ->where($where)
            ->leftJoin('cate c', 'c.id = p.cate_id')
            ->select();
        echo json_encode($data);die;
    }

    /**
     * 商品详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProductDetail()
    {
        $p_id = input('p_id');
        $data = Db::name('product')
            ->alias('p')
            ->field('p.id,p.name,p.pic,p.pics,p.h_price,p.n_price,p.freight,p.weight,p.r_price,p.description,p.d_price,p.content,p.is_overseas,p.cate_id,c.name cate_name')
            ->where('p.id', $p_id)
            ->leftJoin('cate c', 'c.id = p.cate_id')
            ->find();
        // 多图处理
        $data['pics'] = explode(',', $data['pics']);
        echo json_encode($data);die;
    }

    /**
     * 用户详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberDetail()
    {
        $u_id = $this->u_id;
        $data = Db::name('miniapp_user')
            ->field('u.id,u.nickname,u.avatarurl,u.mobile,u.score,u.m_score')
            ->alias('u')
            ->leftJoin('be_miniapp_user mu', 'mu.id = u.u_id')
            ->where('u.id', $u_id)
            ->find();
        echo json_encode($data);die;
    }

    /**
     * 用户猜测记录
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberGuessLogs()
    {
        $u_id = $this->u_id;
        $data = Db::name('guess_list')
            ->alias('l')
            ->field('l.o_id,l.update_time,q.title,q.stop_time,q.open_time,q.right_option')
            ->leftJoin('be_guess_question q', 'q.id = l.q_id')
            ->where('l.u_id', $u_id)
            ->select();

        // 数据统计
        $count['sum'] = count($data);
        $count['win_num'] = 0;

        // 数据处理
        foreach ($data as &$row) {
            // 判断当前时间是否处于猜测中
            if ($row['stop_time'] < time()) {
                $row['state'] = '剩余预测时间 '. date('H:i:s', time() - $row['stop_time']);
            } elseif (time() < $row['open_time']) {
                $row['state'] = '开奖中，请耐心等待';
            } elseif ($row['o_id'] == $row['right_option']) {
                $row['state'] = '已结束，预言正确';
                $count['win_num'] ++;
            } else {
                $row['state'] = '已结束，预言错误';
            }
        }

        // 计算胜率
        $count['win_rate'] = $count['sum'] ? ceil($count['win_num'] / $count['sum'] * 100) . '%' : '0%';

        echo json_encode(array('guess_logs' => $data, 'guess_count' => $count));die;

    }

    /**
     * 用户响豆记录
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberBeanLogs()
    {
        $u_id = $this->u_id;
        $data = Db::name('bean_logs')
            ->where('u_id', $u_id)
            ->select();
        // 获取用户响豆总数
        $userBeanSum = Db::name('miniapp_user')->field('score')->where('id', $u_id)->find();

        echo json_encode(array('bean_logs' => $data, 'bean_sum' => $userBeanSum['score']));die;
    }

    /**
     * 用户订单列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberOrderList()
    {
        $u_id = $this->u_id;
        $data = Db::name('order_list')
            ->field('id,u_id,p_name,p_id,p_price,d_price,r_price,p_pic,update_time')
            ->where('u_id', $u_id)
            ->select();
        echo json_encode($data);die;
    }

    /**
     * 用户邀请列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberInvite()
    {
        $u_id = $this->u_id;
        $data = Db::name('miniapp_user')
            ->field('id,nickname,avatarurl,mobile')
            ->where('u_id', $u_id)
            ->select();
        echo json_encode($data);die;
    }

    /** 获取用户收货地址
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMemberAddress()
    {
        $u_id = $this->u_id;
        $data = Db::name('address')
            ->field('id,u_id,name,mobile,address')
            ->where('u_id', $u_id)
            ->select();
        echo json_encode($data);die;
    }

    /**
     * 获取系统配置信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSystemInfo()
    {
        $data = Db::name('setting')
            ->field('value')
            ->where('name', '=', 'systemconf')
            ->find();

        echo $data['value'];die;
    }

    /**
     * 微信支付
     */
    public function pay()
    {
        if (input('code')) {   //用code获取openid
            $code = input('code');
            $openid = $this->getOpenId($code);
        }
        $fee = input('fee');
        //$fee = 0.01;//举例支付0.01
        $appid = 'wx28676bd439d7943c';//appid.如果是公众号 就是公众号的appid
        $body = '标题';
        $mch_id = '1529263091'; //商户号
        $nonce_str = $this->nonce_str();//随机字符串
        $notify_url = 'https://shop.hzjudao.cn/api/api/payReturn'; //回调的url【自己填写】
        $openid = $openid;
        $out_trade_no = $this->order_number($openid);//商户订单号
        //$out_trade_no = 'test001';//商户订单号
        $spbill_create_ip = '47.99.160.245';//服务器的ip【自己填写】;
        $total_fee = $fee * 100;// 微信支付单位是分，所以这里需要*100
        $trade_type = 'JSAPI';//交易类型 默认


        //这里是按照顺序的 因为下面的签名是按照顺序 排序错误 肯定出错
        $post['appid'] = $appid;
        $post['body'] = $body;
        $post['mch_id'] = $mch_id;
        $post['nonce_str'] = $nonce_str;//随机字符串
        $post['notify_url'] = $notify_url;
        $post['openid'] = $openid;
        $post['out_trade_no'] = $out_trade_no;
        $post['spbill_create_ip'] = $spbill_create_ip;//终端的ip
        $post['total_fee'] = $total_fee;//总金额
        $post['trade_type'] = $trade_type;
        $sign = $this->sign($post);//签名
        $post_xml = '<xml>
     <appid>' . $appid . '</appid>
     <body>' . $body . '</body>
     <mch_id>' . $mch_id . '</mch_id>
     <nonce_str>' . $nonce_str . '</nonce_str>
     <notify_url>' . $notify_url . '</notify_url>
     <openid>' . $openid . '</openid>
     <out_trade_no>' . $out_trade_no . '</out_trade_no>
     <spbill_create_ip>' . $spbill_create_ip . '</spbill_create_ip>
     <total_fee>' . $total_fee . '</total_fee>
     <trade_type>' . $trade_type . '</trade_type>
     <sign>' . $sign . '</sign>
    </xml> ';


        //print_r($post_xml);die;
        //统一接口prepay_id
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $xml = $this->http_request($url, $post_xml);


        $array = $this->xml($xml);//全要大写


        //print_r($array);die;
        if ($array['RETURN_CODE'] == 'SUCCESS' && $array['RESULT_CODE'] == 'SUCCESS') {
            $time = time();
            $tmp = array();//临时数组用于签名
            $tmp['appId'] = $appid;
            $tmp['nonceStr'] = $nonce_str;
            $tmp['package'] = 'prepay_id=' . $array['PREPAY_ID'];
            $tmp['signType'] = 'MD5';
            $tmp['timeStamp'] = "$time";


            $data['state'] = 200;
            $data['time_stamp'] = "$time";//时间戳
            $data['nonce_str'] = $nonce_str;//随机字符串
            $data['sign_type'] = 'MD5';//签名算法，暂支持 MD5
            $data['package'] = 'prepay_id=' . $array['PREPAY_ID'];//统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
            $data['pay_sign'] = $this->sign($tmp);//签名,具体签名方案参见微信公众号支付帮助文档;


            // 将数据存一份
            $payData = $data;
            $payData['state'] = 1; // 待支付
            $payData['out_trade_no'] = $out_trade_no; // 订单号
            $payData['total_fee'] = $total_fee; // 费用 （分）
            Db::name('pay_logs')->insert($payData);

        } else {
            $data['state'] = 0;
            $data['text'] = "错误";
            $data['RETURN_CODE'] = $array['RETURN_CODE'];
            $data['RETURN_MSG'] = $array['RETURN_MSG'];
        }


        echo json_encode($data);
    }

    //随机32位字符串
    private function nonce_str()
    {
        $result = '';
        $str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
        for ($i = 0; $i < 32; $i++) {
            $result .= $str[rand(0, 48)];
        }
        return $result;
    }


//生成订单号  date + rand(10,99)
    private function order_number()
    {
        return date('Ymd'). time() . rand(1000, 9999);
    }


//签名 $data要先排好顺序
    private function sign($data)
    {
        $stringA = '';
        foreach ($data as $key => $value) {
            if (!$value) continue;
            if ($stringA) $stringA .= '&' . $key . "=" . $value;
            else $stringA = $key . "=" . $value;
        }
        $wx_key = 'qazecvfdrgscbnopuejrgnnccjkh2019';//申请支付后有给予一个商户账号和密码，登陆后自己设置的key
        $stringSignTemp = $stringA . '&key=' . $wx_key;
        return strtoupper(md5($stringSignTemp));
    }


//curl请求
    public function http_request($url, $data = null, $headers = array())
    {
        $curl = curl_init();
        if (count($headers) >= 1) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_URL, $url);


        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);


        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


//获取xml
    private function xml($xml)
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);
        $data = array();
        //echo json_encode($index);die;
        foreach ($index as $key => $value) {
            if ($key == 'xml' || $key == 'XML') continue;
            $tag = $vals[$value[0]]['tag'];
            $value = $vals[$value[0]]['value'];

            $data[$tag] = $value;
        }
        //var_dump($data);die;
        return $data;
    }

    public function payReturn()
    {
        /*
         * <xml>
              <appid><![CDATA[wx24123c4370ec43b]]></appid>
              <attach><![CDATA[测试测试]]></attach>
              <bank_type><![CDATA[CFT]]></bank_type>
              <fee_type><![CDATA[CNY]]></fee_type>
              <is_subscribe><![CDATA[Y]]></is_subscribe>
              <mch_id><![CDATA[10000100]]></mch_id>
              <nonce_str><![CDATA[5d2b634f23F23da20af46e531c]]></nonce_str>
              <openid><![CDATA[oUpF8uMEb4q23FEWG4Q23R268TekukE]]></openid>
              <out_trade_no><![CDATA[140123153]]></out_trade_no>
              <result_code><![CDATA[SUCCESS]]></result_code>
              <sign><![CDATA[B552EDFW23G423G5DD0D78AB241]]></sign>
              <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
              <time_end><![CDATA[20140903131540]]></time_end>
              <total_fee>1</total_fee>
                <coupon_fee><![CDATA[10]]></coupon_fee>
                <coupon_count><![CDATA[1]]></coupon_count>
                <coupon_type><![CDATA[CASH]]></coupon_type>
                <coupon_id><![CDATA[10000]]></coupon_id>
                <coupon_fee><![CDATA[100]]></coupon_fee>
              <trade_type><![CDATA[JSAPI]]></trade_type>
              <transaction_id><![CDATA[1004400740212312353532168]]></transaction_id>
         </xml>
        */
        $data = file_get_contents('php://input');
        $arr = $this->xmlToArray($data);

        // 根据微信支付回调接口，用订单号查询签名，验证签名和金额是否一致
        $payData = Db::name('pay_logs')->where('out_trade_no', $arr['out_trade_no'])->find();
        if ($payData) {
            //判断返回状态
            if ($arr['return_code'] == 'SUCCESS' || $arr['result_code'] == 'SUCCESS') {
                //判断订单金额
                if ($payData['total_fee'] == $arr['total_fee']) {
                    //修改订单状态
                    $res = Db::name('pay_logs')->where('id',$payData['id'])->update(array('state' => 2));
                    if ($res) {
                        $prepay_id = $payData['package'];  //prepay_id要在统一下单的时候做保存
                        $money = $arr['total_fee'] / 100;
                        //$this->sendTemplateMessage($prepay_id, $arr['out_trade_no'], $money, $arr['openid']); //发送模板消息
                        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';//给微信正常相应（如果没有正常相应微信会根据自己的机制多次请求）
                    }
                }
            }
        }
    }

    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    public function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
}