<?php /*a:2:{s:60:"D:\project\erha\wxAppcai\themes/pc/miniapp/product\edit.html";i:1551582221;s:59:"D:\project\erha\wxAppcai\themes/pc/miniapp/common\base.html";i:1551446749;}*/ ?>
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
    
<link rel="stylesheet" type="text/css" href="/public/static//admin/css/media_news.css" />
<script type="text/javascript" src="/public/static//admin/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/public/static//admin/ueditor/ueditor.all.js"></script>
<script type="text/javascript" src="/public/static//js/ajax_file_upload.js"></script>
<style>
    #edui1_iframeholder img{ width: 100%;}
    #edui1_iframeholder{
        height: 450px !important;}
</style>

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
        
<form class="layui-form" action="" style="padding-right: 10px;">
    <input type="hidden" name="id" value="<?php if(isset($data)): ?><?php echo htmlentities($data['id']); ?><?php endif; ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">商品名称</label>
        <div class="layui-input-block">
            <input type="text"
                   name="name"
                   required
                   lay-verify="required"
                   placeholder="请输入商品名称"
                   autocomplete="off"
                   value="<?php if(isset($data)): ?><?php echo htmlentities($data['name']); ?><?php endif; ?>"
                   class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">描述</label>
        <div class="layui-input-block">
            <textarea
                   name="description"
                   placeholder="请输入商品描述"
                   autocomplete="off"
                      class="layui-input"><?php if(isset($data)): ?><?php echo htmlentities($data['description']); ?><?php endif; ?></textarea>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">封面图片</label>
        <div class="layui-input-block">
            <?php echo hook('Upload',['type'=>'image','name'=>'pic','value'=>isset($data['pic']) ? $data['pic'] : '']); ?>
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">原价</label>
        <div class="layui-input-inline">
            <input name="h_price"
                   min="0"
                   required
                   lay-verify="required"
                   placeholder="请输入原价"
                   autocomplete="off"
                   class="layui-input"
                   value="<?php if(isset($data)): ?><?php echo htmlentities($data['h_price']); ?><?php endif; ?>"
                   type="number">
        </div>
    </div>
    <!--<div class="layui-form-item">
        <label class="layui-form-label">响豆价</label>
        <div class="layui-input-inline">
            <input name="n_price"
                   min="0"
                   required
                   lay-verify="required"
                   placeholder="请输入响豆价"
                   autocomplete="off"
                   class="layui-input"
                   value="<?php if(isset($data)): ?><?php echo htmlentities($data['n_price']); ?><?php endif; ?>"
                   type="number">
        </div>
    </div>-->
    <div class="layui-form-item">
        <label class="layui-form-label">所需响豆数</label>
        <div class="layui-input-inline">
            <input name="d_price"
                   min="0"
                   required
                   lay-verify="required"
                   placeholder="请输入所需响豆数"
                   autocomplete="off"
                   class="layui-input"
                   value="<?php if(isset($data)): ?><?php echo htmlentities($data['d_price']); ?><?php endif; ?>"
                   type="number">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">内容</label>
        <div class="layui-input-inline" style="width:380px;">
            <div class="controls" id="discripContainer">
                <textarea id="tareaProductDiscrip" name="content" style=" width: 100%; display: none;"><?php if(isset($data)): ?><?php echo $data['content']; ?><?php endif; ?></textarea>
                <script id="editor1" type="text/plain"><?php if(isset($data)): ?><?php echo $data['content']; ?><?php endif; ?></script>
            </div>

        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="ADM">立即提交</button>
            <a href="<?php echo url('product/index'); ?>" class="layui-btn layui-btn-primary">返回</a>
        </div>
    </div>
</form>


<script>
    UE.Editor.prototype._bkGetActionUrl = UE.Editor.prototype.getActionUrl;
    UE.getEditor('editor1').addListener("selectionchange", function () {
        changeElement('content');
    });
    function changeElement(name) {
        if (name == 'content') {
            var content = UE.getEditor('editor1').getContent();
            $("#" + name).val(content);
        }
    }
    layui.use('laydate', function() {
        var laydate = layui.laydate;
        laydate.render({
            elem: '#time1'
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm:ss'
        });
        laydate.render({
            elem: '#time2'
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm:ss'
        });
    })
    layui.use('form', function(){
        var form = layui.form;
        var $ = layui.jquery;
        form.on('submit(ADM)', function(data){
            $.post("<?php echo url('product/save'); ?>",data.field,function (res) {
                if(res.status=='0'){
                    layer.msg(res.msg);
                }
                if(res.status=='1'){
                    layer.msg(res.msg,{time:1000},function () {
                        location.href="<?php echo url('product/index'); ?>";
                    });
                }
            })
            return false;
        });
    });
    function addOption()
    {
        layer.prompt(function(value, index, elem){
            var input = "<div class='option'><input type='radio' name='right_option' value='"+value+"' title='"+value+"'><input type='hidden' name='option[]' value='"+value+"'><i class='layui-icon-close layui-icon' style='cursor:pointer;' onclick='del(this)'></i></div>"
            $('#last').before(input);
            renderForm();
            layer.close(index);
        });
    }
    function renderForm(){
        layui.use('form', function(){
            var form = layui.form;//高版本建议把括号去掉，有的低版本，需要加()
            form.render();
        });
    }
    function del(el)
    {
        $(el).parent().remove();
    }
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