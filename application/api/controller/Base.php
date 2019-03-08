<?php
// +----------------------------------------------------------------------
// | [RhaPHP System] Copyright (c) 2017-2020 http://www.rhaphp.com/
// +----------------------------------------------------------------------
// | [RhaPHP] 并不是自由软件,你可免费使用,未经许可不能去掉RhaPHP相关版权
// +----------------------------------------------------------------------
// | Author: Geeson <qimengkeji@vip.qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\common\model\MiniappAddon;
use think\Db;
use think\facade\Session;

class Base extends \app\admin\controller\Base
{
    public $_mid;//小程序标识 ID
    public $miniappInfo;//小程序信息
    public function initialize()
    {

    }

}