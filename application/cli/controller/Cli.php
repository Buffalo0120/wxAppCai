<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\cli\controller;

use think\console\Table;
use think\Db;
use think\facade\Request;
use think\facade\Url;

class Cli extends Base
{
    // 猜测题类型
    const vote_type = [
        1 => '少数派',
        2 => '多数派',
        3 => '预言帝',
    ];

    public function queue()
    {
        echo '结算开始！' . date('Y-m-d H:i:s') . "\n";
        // 查询未结算且已到开奖时间的猜测题
        $guessQuestionList = Db::name('guess_question')
            ->alias('q')
            ->leftJoin('guess_list g', 'g.q_id = q.id')
            ->field('q.id, q.title, q.vote_type, q.right_option, q.coin_pool, sum(g.d_price) sum_price')
            ->where('q.status', 0)
            ->where('q.is_settlement', 0)
            ->where('q.open_time', '<=', time())
            ->group('q.id')
            ->select();
        // var_dump($guessQuestionList);exit;
        // 根据投票类型
        // 1、少数派，统计答题人数，答题少的选项为正确答案
        // 2、多数派，统计答题人数，答题多的选项为正确答案
        // 3、预言帝，后台设置的选项为正确答案
        // 将题目响豆池中所有的响豆平分给正确答案的用户
        foreach ($guessQuestionList as $row) {
            // 根据题目id，查询出对应的猜测题的用户数据
            $guessList = Db::name('guess_list')
                ->field('u_id,o_id,d_price')
                ->where('q_id', $row['id'])
                ->select();
            switch ($row['vote_type']) {
                case 1:
                    // 少数派
                    $rightOption = $this->getRightOptionUser($guessList, 1);
                    if ($rightOption) {
                        // 统计正确答案的用户数据
                        $userData = $this->getRightOptionUser($guessList, $rightOption);
                        $userSum = count($userData);
                        // 如果至少有一人答题正确，则进行结算
                        if ($userSum) {
                            // 计算每人可以得到的响豆数
                            $averageCoin = intval(($row['coin_pool'] + $row['sum_price']) / $userSum);
                            // 结算
                            $this->settlement($userData, $averageCoin);
                        }
                        // 记录日志
                        $param = [
                            'remark' => '猜测题-' . $row['title'] . '-为少数派类型，现已结算完成！',
                            'action_id' => $row['id'],
                            'model' => 'guess_question'
                        ];
                        $this->sys_log($param);
                    } else {
                        // 记录日志
                        $param = [
                            'remark' => '猜测题-' . $row['title'] . '到了开奖时间仍无人作答',
                            'action_id' => $row['id'],
                            'model' => 'guess_question'
                        ];
                        $this->sys_log($param);
                    }
                    break;
                case 2:
                    // 多少派
                    $rightOption = $this->getRightOptionUser($guessList, 2);
                    if ($rightOption) {
                        // 统计正确答案的用户数据
                        $userData = $this->getRightOptionUser($guessList, $rightOption);
                        $userSum = count($userData);
                        // 如果至少有一人答题正确，则进行结算
                        if ($userSum) {
                            // 计算每人可以得到的响豆数
                            $averageCoin = intval(($row['coin_pool'] + $row['sum_price']) / $userSum);
                            // 结算
                            $this->settlement($userData, $averageCoin);
                        }
                        // 记录日志
                        $param = [
                            'remark' => '猜测题-' . $row['title'] . '-为多数派类型，现已结算完成！',
                            'action_id' => $row['id'],
                            'model' => 'guess_question'
                        ];
                        $this->sys_log($param);
                    } else {
                        // 记录日志
                        $param = [
                            'remark' => '猜测题-' . $row['title'] . '到了开奖时间仍无人作答',
                            'action_id' => $row['id'],
                            'model' => 'guess_question'
                        ];
                        $this->sys_log($param);
                    }
                    break;
                case 3:
                    // 预言帝
                    if ($row['right_option']) {
                        // 统计正确答案的用户数据
                        $userData = $this->getRightOptionUser($guessList, $row['right_option']);
                        $userSum = count($userData);
                        // 如果至少有一人答题正确，则进行结算
                        if ($userSum) {
                            // 计算每人可以得到的响豆数
                            $averageCoin = intval(($row['coin_pool'] + $row['sum_price']) / $userSum);
                            // 结算
                            $this->settlement($userData, $averageCoin);
                        }
                        // 记录日志
                        $param = [
                            'remark' => '猜测题-' . $row['title'] . '-为预言帝类型，现已结算完成！',
                            'action_id' => $row['id'],
                            'model' => 'guess_question'
                        ];
                        $this->sys_log($param);
                    } else {
                        // 记录日志
                        $param = [
                            'remark' => '猜测题-' . $row['title'] . '到了开奖时间未设置正确答案',
                            'action_id' => $row['id'],
                            'model' => 'guess_question'
                        ];
                        $this->sys_log($param);
                    }
                    break;
                default :

            }
            // 不管结算成功与否，将题目状态改为已结算
            Db::name('guess_question')
                ->where('id', $row['id'])
                ->setField('is_settlement', 1);
            echo '猜测题-' . $row['title'] . '已结算' . "\n";
        }
        echo '结算结束！' . date('Y-m-d H:i:s') . "\n";
    }

    /**
     * 获取正确答案
     * @param $data 猜测列表
     * @param string $type 类型
     * @return int
     */
    public function getRightOption($data = [], $type = '1')
    {
        if (empty($data)) {
            return 0;
        }
        // 对所有的选项进行分组统计答题人数
        // 根据data中的o_id进行分组并统计
        $optionArr = [];
        foreach ($data as $row) {
            if (!isset($optionArr[$row['o_id']])) {
                $optionArr[$row['o_id']] = 1;
            } else {
                $optionArr[$row['o_id']] ++;
            }
        }
        // 1：少数派，按升序排列，取第一个键；2：多数派，按降序排列，取第一个键
        $type == 1 ? krsort($optionArr) : ksort($optionArr);

        return $optionArr ? array_keys($optionArr)[0] : 0;
    }

    /**
     * 结算
     * @param $data
     * @param $coin
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function settlement($data, $coin)
    {
        if (!empty($data) && is_array($data) && !empty($coin)) {
            foreach ($data as $row) {
                // 在用户响豆基础上增加答题正确所得的响豆
                Db::name('miniapp_user')
                    ->where('id', $row['u_id'])
                    ->setInc('score', $coin);
                // 记录日志
                $param = [
                    'remark' => '用户响豆数增加',
                    'model' => 'miniapp_user',
                    'action_id' => $row['u_id'],
                    'data' => ['score' => $coin]
                ];
                $this->sys_log($param);
                // 记录用户响豆日志
                $param = [
                    'u_id' => $row['u_id'],
                    'type' => '2', // 收入
                    'd_price' => $coin,
                    'status' => 2 // 预测成功奖励
                ];
                Db::name('bean_logs')->insert($param);
            }
        }
    }

    /**
     * 统计正确答案的用户数据
     * @param $data
     * @param $rightOption
     * @return array
     */
    private function getRightOptionUser($data, $rightOption)
    {
        $userData = array();
        if (empty($data) || !is_array($data) || empty($rightOption)) {
            return $userData;
        }

        foreach ($data as $row) {
            if (isset($row['o_id']) && $row['o_id'] == $rightOption) {
                $userData[] = $row;
            }
        }
        return $userData;
    }

    /**
     * 记录日志
     * @param $data = [remark:'备注',action_id:'数据id',model:'表名',data:'数据（json/array）']
     */
    private function sys_log($data)
    {
        if (isset($data['data']) && is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }
        Db::name('logs')->insert($data);
    }

}