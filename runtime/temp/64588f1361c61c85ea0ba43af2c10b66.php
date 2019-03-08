<?php /*a:2:{s:54:"D:\project\erha\themes/pc/miniapp/miniapp\setting.html";i:1551529817;s:50:"D:\project\erha\themes/pc/miniapp/common\base.html";i:1551446749;}*/ ?>
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
        <li <?php if($type == 'systemconf'): ?>class="layui-this"<?php endif; ?> ><a href="<?php echo url('setting',['type'=>'systemconf']); ?>">系统配置</a></li>
        <li <?php if($type == 'wxpay'): ?>class="layui-this"<?php endif; ?> ><a href="<?php echo url('setting',['type'=>'wxpay']); ?>">微信支付</a></li>
        <!--<li <?php if($type == 'sms'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('setting',['type'=>'sms']); ?>">短信配置</a></li>
        <li <?php if($type == 'uploadjsfile'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('setting',['type'=>'uploadjsfile']); ?>">上传验证文件</a></li>
        <li <?php if($type == 'cloud'): ?>class="layui-this"<?php endif; ?>><a href="<?php echo url('setting',['type'=>'cloud']); ?>">云存储</a></li>-->
    </ul>
    <div class="layui-tab-content">

        <?php switch($type): case "systemconf": ?>
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">响豆汇率</label>
                <div class="layui-input-block">
                    <input type="text" name="rate" value="<?php echo htmlentities($config['rate']); ?>" autocomplete="off" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="setting_name" value="systemconf">
                    <button class="layui-btn" lay-submit lay-filter="setting">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>

        <?php break; case "wxpay": ?>
        <form class="layui-form" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">AppId</label>
                    <div class="layui-input-block">
                        <input type="text"   name="appid" value="<?php echo htmlentities($config['appid']); ?>" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">AppSecret</label>
                    <div class="layui-input-block">
                        <input type="text"  name="appsecret" value="<?php echo htmlentities($config['appsecret']); ?>" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">商户ID</label>
                    <div class="layui-input-block">
                        <input type="text" name="mchid" value="<?php echo htmlentities($config['mchid']); ?>"  autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">支付密钥</label>
                    <div class="layui-input-block">
                        <input type="text" name="paysignkey" value="<?php echo htmlentities($config['paysignkey']); ?>"  autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">支付证书cert</label>
                    <div class="layui-input-block">
                        <textarea name="apiclient_cert" class="layui-textarea"><?php echo htmlentities($config['apiclient_cert']); ?></textarea>
                        <p class="tip_for_p">请在微信商户后台下载支付证书，用记事本打开<span style="color: red">apiclient_cert.pem</span>，并复制里面的内容粘贴到这里。</p>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">支付证书key</label>
                    <div class="layui-input-block">
                        <textarea name="apiclient_key"   class="layui-textarea"><?php echo htmlentities($config['apiclient_key']); ?></textarea>
                        <p class="tip_for_p">请在微信商户后台下载支付证书，使用记事本打开<span style="color: red">apiclient_key.pem</span>，并复制里面的内容粘贴到这里。</p>
                    </div>

                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="hidden" name="setting_name" value="wxpay">
                        <button class="layui-btn" lay-submit lay-filter="setting">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
                </form>

            <?php break; case "sms": ?>
        <form class="layui-form" action="">
            <blockquote class="layui-elem-quote">
                腾讯云短信
            </blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label">AppId</label>
                <div class="layui-input-block">
                    <input type="text"   name="txsms[appid]" value="<?php echo htmlentities($config['txsms']['appid']); ?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">AppSecret</label>
                <div class="layui-input-block">
                    <input type="text"  name="txsms[appsecret]" value="<?php echo htmlentities($config['txsms']['appsecret']); ?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <blockquote class="layui-elem-quote">
                阿里大鱼短信
            </blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label">AppId</label>
                <div class="layui-input-block">
                    <input type="text"   name="alisms[appid]" value="<?php echo htmlentities($config['alisms']['appid']); ?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">AppSecret</label>
                <div class="layui-input-block">
                    <input type="text"  name="alisms[appsecret]" value="<?php echo htmlentities($config['alisms']['appsecret']); ?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="setting_name" value="sms">
                    <button class="layui-btn" lay-submit lay-filter="setting">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>

        <?php break; case "cloud": ?>
        <form class="layui-form" action="">

            <blockquote class="layui-elem-quote">
                七牛云存储
            </blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label">AccessKey</label>
                <div class="layui-input-block">
                    <input type="text"   name="qiniu[accessKey]" value="<?php echo htmlentities($config['qiniu']['accessKey']); ?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">SecretKey</label>
                <div class="layui-input-block">
                    <input type="text"  name="qiniu[secretKey]" value="<?php echo htmlentities($config['qiniu']['secretKey']); ?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">Bucke空间名称</label>
                <div class="layui-input-block">
                    <input type="text"  name="qiniu[bucke]" value="<?php echo htmlentities($config['qiniu']['bucke']); ?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">空间域名</label>
                <div class="layui-input-block">
                    <input type="text"  name="qiniu[domain]" value="<?php echo htmlentities($config['qiniu']['domain']); ?>" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">是否开启</label>
                <div class="layui-input-block">
                    <?php if($config['qiniu']['status'] == ''): ?>
                    <input type="radio" name="qiniu[status]" value="1" title="开启">
                    <input type="radio" name="qiniu[status]" value="0" title="关闭" checked>
                    <?php else: ?>
                    <input type="radio" name="qiniu[status]" value="1" title="开启" <?php if($config['qiniu']['status'] == '1'): ?> checked <?php endif; ?>>
                    <input type="radio" name="qiniu[status]" value="0" title="关闭" <?php if($config['qiniu']['status'] == '0'): ?> checked <?php endif; ?>>
                    <?php endif; ?>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="setting_name" value="cloud">
                    <button class="layui-btn" lay-submit lay-filter="setting">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>

        <?php break; case "uploadjsfile": ?>
        <form class="layui-form" action="">

            <div class="layui-form-item">
                <label class="layui-form-label">验证文件</label>
                <div class="layui-input-block">
                    <?php echo hook('Upload',['type'=>'file_mp','name'=>'mp_verify','bt_title'=>'选择文件']); ?>
                    <p class="tip_for_p">上传需要验证域名的文件。上传后小程序平台后台或者微信支付后台确定验证即可。 </p>
                </div>
            </div>

        </form>
        <?php break; ?>
        <?php endswitch; ?>
    </div>
</div>
<script>
    layui.use('form', function(){
        var form = layui.form;
        form.on('submit(setting)', function(data){
            $.post('',data.field,function (res) {
                if(res.status=='0'){
                    layer.msg(res.msg);
                }
                if(res.status=='1'){
                    layer.msg(res.msg,{time:1000},function () {

                    });
                }
            })
            return false;
        });

    });
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