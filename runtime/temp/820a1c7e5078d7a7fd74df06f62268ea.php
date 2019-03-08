<?php /*a:2:{s:50:"D:\project\erha\themes/pc/miniapp/index\index.html";i:1547704070;s:50:"D:\project\erha\themes/pc/miniapp/common\base.html";i:1551446749;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="keywords" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <title>猜响小程序后台</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="/public/static//admin/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="/public/static//admin/css/admin_base.css" />
    <script type="text/javascript" src="/public/static//jquery/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="/public/static//layui/layui.js"></script>
    <link rel="stylesheet" type="text/css" href="/public/static//layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="/public/static//icon/icon.css" />
    
    <style>
        <?php if($setScreen ==1): ?>
        .container_body, .wrap{width: 100%;}
        .sidebar{float:left;width: 12%;}
        .content{float: left;width: 87%;}
        .main-logo{margin-left: 5px;}
        .menu dl dt .type-ico{margin-left: 5%}
        .menu dl dd a{padding-left: 22%;}
        #addon_menu .item-icon{margin-left: 5%;}
        .addon_menu-left-nav-a .item-icon{left: 0;}
        .addon_menu-left-nav-a {padding-left: 23%;}
        <?php endif; ?>
    </style>
</head>
<body class="trade-order">
<div class="topbar" id="gotop">
    <div class="wrap">
        <ul>
            <li>你好，<a class="name" href="" id="username"><?php echo htmlentities($admin['admin_name']); ?></a>
                <?php if(!(empty($miniappInfo) || (($miniappInfo instanceof \think\Collection || $miniappInfo instanceof \think\Paginator ) && $miniappInfo->isEmpty()))): ?>
                <span class="quit">当前小程序：<a href="<?php echo url('miniapp/index/index',['mid'=>$mpInfo['id']]); ?>"><?php echo htmlentities($miniappInfo['name']); ?></a><i style="font-size: 9px; margin-left: 5px;"><?php if($miniappInfo['type']==1): ?>已认证<?php else: ?>普通<?php endif; ?></i>
                </span>
                <a class="quit" href="<?php echo url('mp/index/miniapplists'); ?>">切换小程序</a>
                <!-- <a href="<?php echo url('mp/Message/messagelist'); ?>"><i class="layui-icon">&#xe645;</i>用户消息<span class="num-feed rhaphp-msg-user show" style="display: none;">0</span></a>
                <?php endif; ?>
                <a href="javascript:;" onclick="cacheClear()"><i class="layui-icon">&#xe640;</i>清空缓存</a>
                <a href="javascript:;" onclick="setScreen()"><i style="font-size: 14px;" class="rha-icon">&#xe879;</i>宽屏</a> -->
                <a class="quit" href="<?php echo url('admin/Login/out'); ?>"><i class="rha-icon">&#xe696;</i>退出</a>
            </li>
        </ul>
    </div>
</div>
<div class="header">
    <div class="wrap">
        <div class="logo">
            <h1 class="main-logo"><a href="<?php echo url('mp/mp/index'); ?>">猜响</a></h1>
            <div class="sub-logo"></div>
        </div>
        <div class="nav">
            <ul>
                <?php if(is_array($t_menu) || $t_menu instanceof \think\Collection || $t_menu instanceof \think\Paginator): $i = 0; $__LIST__ = $t_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$t): $mod = ($i % 2 );++$i;if($t['name'] == '小程序'): ?>
                <?php endif; ?>
                <li class="<?php if($topNode == $t['url']): ?>selected<?php endif; ?>"><a href="<?php echo url($t['url']); ?>"><?php echo htmlentities($t['name']); ?></a></li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
</div>
<div class="container_body wrap">
    <div class="sidebar">
        <div class="menu">
            <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$t): $mod = ($i % 2 );++$i;?>
            <dl>
                <dt><i class="type-ico ico-trade rha-icon <?php if($t['shows'] == '1'): ?><?php endif; ?>"><?php echo $t['icon']; ?></i><?php echo htmlentities($t['name']); ?></dt>
                <?php if(is_array($t['child']) || $t['child'] instanceof \think\Collection || $t['child'] instanceof \think\Paginator): $i = 0; $__LIST__ = $t['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c): $mod = ($i % 2 );++$i;?>
                <dd class="<?php if($c['shows'] == '1'): ?>selected<?php endif; ?>"><a href="<?php echo url($c['url']); ?>"><?php echo htmlentities($c['name']); ?></a></dd>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </dl>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            
            <!-- <dl>
                <dt><i class="type-ico ico-trade rha-icon">&#xe742;</i>菜单</dt>
                <dd class="">
                    <?php if($miniappInfo['addon_name'] == ''): else: ?>
                    <a href="<?php echo url('miniapp/app/config',['name'=>$miniappInfo['addon'],'type'=>'news','_mid'=>$_mid]); ?>"><?php echo htmlentities($miniappInfo['addon_name']); ?></a>
                    <?php endif; ?>
                </dd>
            </dl> -->
        </div>
    </div>
    <div class="content" id="tradeSearchBd">
        <?php if(isset($menu_tile) OR $menu_title != ''): ?>
        <div class="content-hd">
            <h2><?php echo htmlentities($menu_title); ?></h2>
        </div>
        <?php endif; ?>
        
<div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
    <ul class="layui-tab-title">
        <li <?php if($type == 'getVisittrendTrendByDay'): ?>class="layui-this"<?php endif; ?> ><a href="<?php echo url('index',['type'=>'getVisittrendTrendByDay']); ?>">访问趋势</a></li>
        <li <?php if($type == 'getSummaryTrend'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('index',['type'=>'getSummaryTrend']); ?>">概况趋势</a></li>
        <!--<li <?php if($type == 'getVisitDistribution'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('index',['type'=>'getVisitDistribution']); ?>">访问分布</a></li>-->
        <li <?php if($type == 'getDailyRetainInfo'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('index',['type'=>'getDailyRetainInfo']); ?>">访问留存</a></li>
        <li <?php if($type == 'getVisitPage'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('index',['type'=>'getVisitPage']); ?>">访问页面</a></li>
        <!--<li <?php if($type == 'getUserPortrait'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('index',['type'=>'getUserPortrait']); ?>">用户画像</a></li>-->
    </ul>
    <div class="layui-tab-content">
        <?php switch($type): case "getVisittrendTrendByDay": ?>
        <table class="layui-table" lay-skin="line">
            <thead>
            <tr>
                <th>时间</th>
                <th>打开次数</th>
                <th>访问次数</th>
                <th>访问人数</th>
                <th>新用户数</th>
                <th>人均停留时长/秒</th>
                <th>次均停留时长/秒</th>
                <th>平均访问深度</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php if(is_array($data2['list']) || $data2['list'] instanceof \think\Collection || $data2['list'] instanceof \think\Paginator): $i = 0; $__LIST__ = $data2['list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if(is_array($v) || $v instanceof \think\Collection || $v instanceof \think\Paginator): $i = 0; $__LIST__ = $v;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key =='ref_date'): ?>
                    <td>昨日</td>
                    <?php else: ?>
                    <td><?php echo htmlentities($v); ?></td>
                    <?php endif; ?>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </tr>
            <tr>
                <?php if(is_array($data2['week']) || $data2['week'] instanceof \think\Collection || $data2['week'] instanceof \think\Paginator): $i = 0; $__LIST__ = $data2['week'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if(is_array($v) || $v instanceof \think\Collection || $v instanceof \think\Paginator): $i = 0; $__LIST__ = $v;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key =='ref_date'): ?>
                <td>本周</td>
                <?php else: ?>
                <td><?php echo htmlentities($v); ?></td>
                <?php endif; ?>

                <?php endforeach; endif; else: echo "" ;endif; ?>
                <?php endforeach; endif; else: echo "" ;endif; ?>

            </tr>
            <tr>
                <?php if(is_array($data2['month']) || $data2['month'] instanceof \think\Collection || $data2['month'] instanceof \think\Paginator): $i = 0; $__LIST__ = $data2['month'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if(is_array($v) || $v instanceof \think\Collection || $v instanceof \think\Paginator): $i = 0; $__LIST__ = $v;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key =='ref_date'): ?>
                <td>本月</td>
                <?php else: ?>
                <td><?php echo htmlentities($v); ?></td>
                <?php endif; ?>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </tr>
            </tbody>
        </table>
        <?php break; case "getSummaryTrend": ?>
        <table class="layui-table" lay-skin="line">
            <thead>
            <tr>
                <th>时间</th>
                <th>累计用户数</th>
                <th>转发次数</th>
                <th>转发人数</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if(is_array($v) || $v instanceof \think\Collection || $v instanceof \think\Paginator): $i = 0; $__LIST__ = $v;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key =='ref_date'): ?>
                <td>昨日</td>
                <?php else: ?>
                <td><?php echo htmlentities($v); ?></td>
                <?php endif; ?>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </tr>
            </tbody>
        </table>
        <?php break; case "getVisitDistribution": break; case "getDailyRetainInfo": ?>
        <table class="layui-table" lay-skin="line">
            <thead>
            <tr>
                <th>日期</th>
                <th>新增用户留存</th>
                <th>活跃用户留存</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($data2) || $data2 instanceof \think\Collection || $data2 instanceof \think\Paginator): $i = 0; $__LIST__ = $data2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <tr>
                <?php if(isset($vo['ref_date'])): ?>
                    <td><?php echo htmlentities($vo['ref_date']); ?></td>
                <?php endif; if(is_array($vo['visit_uv_new']) || $vo['visit_uv_new'] instanceof \think\Collection || $vo['visit_uv_new'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['visit_uv_new'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                    <td><?php echo htmlentities($v['value']); ?></td>
                <?php endforeach; endif; else: echo "" ;endif; if(is_array($vo['visit_uv']) || $vo['visit_uv'] instanceof \think\Collection || $vo['visit_uv'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['visit_uv'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <td><?php echo htmlentities($v['value']); ?></td>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <?php break; case "getVisitPage": ?>
        <table class="layui-table" lay-skin="line">
            <thead>
            <tr>
                <th>页面路径</th>
                <th>访问次数</th>
                <th>访问人数</th>
                <th>次均停留时长</th>
                <th>进入页次数</th>
                <th>退出页次数</th>
                <th>转发次数</th>
                <th>转发人数</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <tr>

                <?php if(is_array($v) || $v instanceof \think\Collection || $v instanceof \think\Paginator): $i = 0; $__LIST__ = $v;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($key =='ref_date'): ?>
                <td>昨日</td>
                <?php else: ?>
                <td><?php echo htmlentities($v); ?></td>
                <?php endif; ?>
                <?php endforeach; endif; else: echo "" ;endif; ?>

            </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <?php break; case "getUserPortrait": break; ?>
        <?php endswitch; ?>
    </div>
</div>




    </div>
</div>
<div class="footer">

</div>
</body>
<script>
    layui.use('element', function(){
        var element = layui.element;
    });
    function getMaterial(paramName,type){
        layer.open({
            type: 2,
            title: '选择素材',
            shadeClose: true,
            shade: 0.8,
            area: ['750px', '480px'],
            content: '<?php echo getHostDomain(); ?><?php echo url("mp/Material/getMeterial","",""); ?>/type/'+type+'/param/'+paramName //iframe的url
        });
    }
    function controllerByVal(value,paramName,type) {
        
        $('.form_'+paramName).attr('src',value);
        $("input[name="+paramName+"]").val(value);
    }
    // $(function () {
    //      setInterval(getMsgTotal,20000);
    //     function getMsgTotal() {
    //         $.get("<?php echo url('mp/Message/getMsgStatusTotal'); ?>",{},function (res) {
    //             if(res.msgTotal==0){
    //                 //TODO
    //             }else{
    //                 $('.rhaphp-msg-user').show();
    //                 $('.rhaphp-msg-user').text(res.msgTotal);
    //             }
    //
    //         })
    //     }
    // })
    var layer
    layui.use('layer', function(){
         layer = layui.layer;
    });
    function cacheClear() {
            var index =layer.load(1)
            $.post("<?php echo url('admin/system/cacheClear'); ?>",function (res) {
                layer.close(index);
                layer.alert(res.msg);
            })
    }
    function setScreen() {
        var index =layer.load(1)
        $.post("<?php echo url('admin/system/setScreen'); ?>",function (res) {
            layer.close(index);
            window.location.reload();
        })
    }
</script>
</html>