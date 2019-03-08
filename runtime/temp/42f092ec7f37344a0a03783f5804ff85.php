<?php /*a:2:{s:60:"D:\project\erha\wxAppcai\themes/pc/miniapp/product\list.html";i:1551582232;s:59:"D:\project\erha\wxAppcai\themes/pc/miniapp/common\base.html";i:1551446749;}*/ ?>
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
        
<form action="" class="layui-form" method="get">
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">商品名称：</label>
            <div class="layui-input-block">
                <input type="text" name="name" value="<?php echo htmlentities($post['name']); ?>" placeholder="请输入商品名称" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-inline">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn layui-btn-sm layui-btn-normal">
                    <i class="layui-icon">&#xe615;</i>
                    搜索</button>
            </div>
        </div>
        <div class="layui-inline">
            <div class="layui-input-block">
                <a href="<?php echo url('product/add'); ?>" class="layui-btn layui-btn-sm layui-btn-normal"  >添加</a>
            </div>
        </div>
    </div>
</form>
<div>
    <form class="layui-form" action="" style="padding: 0px 10px 0px 10px;">
        <table class="layui-table" lay-skin="line">
            <thead>
            <tr>
                <th>商品名称</th>
                <th>图片</th>
                <th>描述</th>
                <th>原价</th>
                <!--<th>响豆价</th>-->
                <th>所需响豆数</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <tr>
                <td><?php echo htmlentities($v['name']); ?></td>
                <td>
                    <div  style="padding: 1px; border: #e6e6e6 solid 1px; width:35px; float: left; ">
                        <img class="form_logo" src="<?php echo htmlentities($v['pic']); ?>" width="35" height="35">
                    </div>
                </td>
                <td><?php echo htmlentities($v['description']); ?></td>
                <td><?php echo htmlentities($v['h_price']); ?></td>
                <!--<td><?php echo htmlentities($v['n_price']); ?></td>-->
                <td><?php echo htmlentities($v['d_price']); ?></td>
                <td>
                    <a href="<?php echo url('product/edit',['id'=>$v['id']]); ?>"
                       class="layui-btn layui-btn-sm layui-btn-normal">修改</a>
                    <a href="javascript:;"
                       class="layui-btn layui-btn-sm layui-btn-danger" onclick="aa('<?php echo htmlentities($v['id']); ?>')">删除</a>
                </td>
            </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </form>
    <?php echo $page; ?>
</div>
<script>
    layui.use('laydate', function() {
        var laydate = layui.laydate;
        laydate.render({
            elem: '#rhaphp-time1'
            ,type: 'datetime'
            ,range: '到'
            ,format: 'yyyy-M-d'
        });
    })
    layui.use('form', function(){
        var $ = layui.jquery, form = layui.form;
    });
    function aa(id)
    {
        layer.confirm('确定删除吗？',{btn:['确定','取消']},function(res){
            $.post("<?php echo url('product/del'); ?>",{id:id},function (res) {
                if(res.status=='0'){
                    layer.msg(res.msg);
                }
                if(res.status=='1'){
                    layer.msg(res.msg,{time:1000},function () {
                        location.href="<?php echo url('product/index'); ?>";
                    });
                }
            })
        })

    }
    //删除
</script>

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