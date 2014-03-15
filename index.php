<?php
include "Database.php";

//图片文件常量
define("IMG_ACCEPT","image/accept.png");
define("IMG_CLOSE","image/close.png");
define("IMG_GRID","image/grid.png");
define("IMG_DESKTOP","image/ios7-photos-outline.png");
define("IMG_LOGIN","image/log-in.png");
define("IMG_PLUS","image/plus.png");
define("IMG_LOGO","image/favico.png");

$db = new Database();
$db->connect();

if(isset($_COOKIE['user']) && isset($_COOKIE['passwd'])){
  $name = $_COOKIE['user'];
  $passwd = $_COOKIE['passwd'];
}
else{
    $name = "customer";
    $passwd = md5("123456");
    
    //设置Cookie
    setcookie("user",$name);
    setcookie("passwd",$passwd);
}

//常用项
$commens = "";
$output = $db->getCommen($name,$passwd);
if($output != ""){
	$sites = explode("&sep1;",$output);
    foreach($sites as $siteval){
        if($siteval == "")
            continue;
    	$site = explode("&sep0;",$siteval);
        $commens = $commens."
        <a href=\"{$site[1]}\" target=\"_blank\" class=\"iconBox linkedIcon\" id=\"0_{$site[2]}\">
            <image src=\"{$site[1]}/favicon.ico\" class=\"icon\" title=\"{$site[0]}\"/>
        </a>";
    }
}
	
//添加桌面
$desktopField = "";
$desktopCount = $db->getDesktopCount($name,$passwd);
if($desktopCount){
    for($i=1;$i<=$desktopCount;$i++){
        $desktopField = $desktopField."<div class=\"iconBox desktopField\" id=\"desk_$i\">
        	<image class=\"icon\" src=\"".IMG_DESKTOP."\" title=\"桌面$i\"/>
        </div>";
    }
}	

$desktopContent = "<div class=\"row\">";
$iconCounter = 0;
$output = $db->getDesktopContent(1,$name,$passwd);
$items = explode("&sep1;",$output);
foreach($items as $item){
    if($item != ""){
        //产生图标
        $iconCounter = $iconCounter + 1;
    	$args = explode("&sep0;",$item);
        $desktopContent = $desktopContent."<div class=\"grid\">
        	<a class=\"iconBox linkedIcon  icon-desktop\" href=\"{$args[1]}\" id=\"1_{$args[2]}\" target=\"_black\">
            	<image class=\"icon\" src=\"{$args[1]}/favicon.ico\"/>
            </a>
            <span class=\"text-center label-icon\">{$args[0]}</span>
        </div>";
        
        if($iconCounter%8 == 0){
            $desktopContent = $desktopContent."</div><div class=\"row\">";
        }
    }
}
$desktopContent = $desktopContent."<div class=\"grid\" id=\"grid-last\">
	<div class=\"iconBox icon-desktop btn-add-icon\" title=\"$iconCounter\">
    	<image class=\"icon\" src=\"".IMG_PLUS."\"/>
    </div>
    <span class=\"text-center label-icon\">添加网址</span>
</div>"."</div>";


//获取分类
$classes = "";
$output = $db->getType();
if($output != ""){
	$types = explode("&sep1;",$output);
    foreach($types as $type){
        if($type == ""){
        	continue;
        }
        
        $args = explode("&sep0;",$type);
        $classes = $classes."<div class=\"type\" id=\"{$args[0]}\">
        	{$args[1]}
        </div>";
    }
}

//列表常用项
$type_commens = "<div class=\"row\">
	<span class=\"text-success text-center span4\">
    	从下面选择想要添加到桌面的网址
    </span>
	<div class=\"btn btn-primary span1\" id=\"addToDesktop\">
    	添加
    </div>
</div>";
$output = $db->getSiteCommen();
if($output != ""){
	$commen_sites = explode("&sep1;",$output);
    foreach($commen_sites as $commen_site){
        if($commen_site == "")
            continue;
    	$args = explode("&sep0;",$commen_site);
        $type_commens = $type_commens."
        	<div class=\"span1 text-center\">
                <div class=\"iconBox list-icon\" id=\"icon-{$args[2]}\" title=\"not-selected\">
                    <image src=\"{$args[1]}/favicon.ico\" class=\"icon\"/>
                    <image src=\"".IMG_ACCEPT."\" class=\"tick\"/>
                </div>
                <a class=\"label-icon\" href=\"{$args[1]}\" target=\"_blank\">{$args[0]}</a>
        	</div>";
    }
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8"/>
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>书页</title>
        
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/index.css" type="text/css" rel="stylesheet">
        
        <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="js/index.js" type="text/javascript"></script>
        <script src="js/jquery.contextmenu.r2.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="header">
            <image src="<?php echo IMG_LOGO;?>" id="logo"/>
            <div id="title">书页桌面</div>
            <div id="timezone"></div>
            <div id="about">
                关于
            </div>
        </div>
        <!-- 主体部分 -->
        <div id="main" class="container">
            <?php echo $desktopContent;?>
        </div>
        <div id="footer">
            <div id="launcher" class="icon_group">
                <div class="iconBox">
                    <image class="icon" src="<?php echo IMG_GRID; ?>" title="网页列表"/>
                </div>
            </div>
            <div id="commen" class="icon_group">
                <?php echo $commens;?>
            </div>
            <div id="bar-add" class="icon_group">
                <div class="iconBox"  id="add-commen">
                    <image src="<?php echo IMG_PLUS; ?>" class="icon" title="添加常用项"/>
                </div>
            </div>
            <div id="login" class="icon_group">
                <div class="iconBox">
                	<image src="<?php echo IMG_LOGIN; ?>" class="icon" id="btn_login" title="登录/注销"/>
                </div>
        	</div>
            <div id="desktop" class="icon_group">
                <?php echo $desktopField;?>
                
                <div class="iconBox">
                    <image src="<?php echo IMG_PLUS;?>" class="icon" id="add_desktop" title="添加桌面"/>
                </div>
            </div>
        </div>
        
        <!--网页列表-->
        <div class="win" id="win_list">
            <div class="title_bar">
                <div class="title_field">
                    网页列表
                </div>
            	<image class="closeit" src="<?php echo IMG_CLOSE; ?>"/>
            </div>
            <div class="win_body">
                <div id="win_list_left">
                    <div id="win_list_left_top">
                            <div class="float-left text-center">搜索标签：</div>
                            <input class="float-left" type="text" id="tag_name"/>
                            <div class="float-left btn btn-success" id="tag_search">搜索</div>                        
                    </div>
                    <div class="container" id="win_list_left_bottom">
                        <?php  echo $type_commens?>
                    </div>
                </div>
                <div id="win_list_right">
                    <div class="type" id="type_commen">
                        常用
                    </div>
                    <?php echo $classes?>
                    <div class="type" id="self-def">
                        自定义
                    </div>
                </div>
            </div>
        </div>
        
        <!--登录框 -->
        <div class="win" id="win_login">
            <div class="title_bar">
                <div class="title_field">
                    登录
                </div>
                <image class="closeit" src="<?php echo IMG_CLOSE; ?>"/>
            </div>
            <div class="win_body container" id="body_login">
                <div class="row">
                    <div class="span4 offset2 text-center">还没有帐号？马上<span id="tag_do_register">注册</span></div>
                </div>
                <div class="row">
                    <div class="span1 offset2 text-success">用户名：</div><input class="span3" id="name_input" type="text" name="user_name"/>
                </div>
                <div class="row">
                    <div class="span1 offset2 text-success">密码：</div><input class="span3" id="passwd_input" type="password" name="passwd"/>
                </div>
                <div class="row">
                    <div class="offset2 span2 text-info"><input id="keep_login" type="checkbox" name="keep_login" value="true"/>&nbsp;保持登录状态</div><div class="btn btn-success span1" id="btn_dologin">登录</div>
                </div>
            </div>
        </div>
        
        <!--注册框-->
        <div class="win" id="win_register">
            <div class="title_bar">
                <div class="title_field">
                    注册
                </div>
                <image class="closeit" src="<?php echo IMG_CLOSE;?>"/>
            </div>
            <div class="win_body container" id="body_register">
                <div class="row">
                    <div class="span1 offset2 text-success">用户名：</div><input class="span3" id="user_set" type="text" name="user_name"/>
                </div>
                <div class="row">
                    <div class="span1 offset2 text-success">密码：</div><input class="span3" id="passwd_set" type="password" name="passwd"/>
                </div>
                <div class="row">
                    <div class="span1 offset2 text-success">确认密码：</div><input class="span3"  id="passwd_confirm" type="password" name="passwd"/>
                </div>
                <div class="row">
                    <div class="text-info offset2 span1" id="info_confirm">校验结果</div><div class="btn btn-success span1 offset1" id="btn_register">注册</div>
                </div>
            </div>
        </div>
        
        <!--消息框-->
        <div id="messagebox" class="text-center">
            This is a message
        </div>
        
        <!--关于-->
        <div class="container" id="win_about">
            <h4 class="text-success">关于作者</h4>
            本网页由<a href="http://weibo.com/u/2131611243?from=profile&wvr=5&loc=infdomain" target="_blank">唯子与卿</a>设计制作<br/>
            如果您有什么意见或者建议，可以发送邮件到<a href="mailto://wuxiaotu2012@gmail.com">1107211156@qq.com</a>或直接<a href="http://weibo.com/u/2131611243?from=profile&wvr=5&loc=infdomain" target="_blank">@我</a><br/>
            <h4 class="text-success">致谢</h4>
            本网站中使用了bootstrap来优化显示效果，您可以通过<a href="http://getbootstrap.com/" target="_blank">这里</a>访问它<br/>
            本网站中的菜单实现使用的是Chris
            Domigan制作，Dan G. Switzer, II参与制作
            的ContextMenu，以<a href="http://www.opensource.org/licenses/mit-license.php" 
            target="_blank">MIT</a>许可证和<a 
            href="http://www.gnu.org/licenses/gpl.html" target="_blank">GPL</a> 许
            可证发布，您可以从这里<a href="http://www.trendskitchens.co.nz/jquery/contextmenu/" target="_blank">访问它
            </a><br/>
            <h4 class="text-success">网站中的部分图标来源</h4>
            Ionicons，由Drifty以<a href="LICENSE/LICENSE_IONICONS" target="_blank">MIT</a>许可证发布，您可以通过<a href="http://ionicframework.com/" target="_blank">这里</a>访问它<br/>
            silk&nbsp;icons,由Mark James以<a href="http://creativecommons.org/licenses/by/2.5/" target="_blank">Creative Commons Attribution 2.5 License</a>发布，您可以通过<a href="http://www.famfamfam.com/lab/icons/silk/" target="_blank">这里</a>访问它<br/>
        </div>
        
        <!--删除桌面菜单-->
        <div class="contextMenu" id="menuDelDesktop">
            <ul>
            <li class="menuItem" id="itemDelDesktop">
                删除桌面
            </li>
            </ul>
        </div>
        
        <!--删除图标菜单-->
        <div class="contextMenu" id="menuDelIcon">
            <ul>
            	<li class="menuItem" id="itemDelIcon">
                    删除图标
                </li>
            </ul>
        </div>
        
        <!--网格菜单-->
        <div class="contextMenu" id="gridMenu">
            <ul>
            	<li class="menuItem" id="itemSelfDef">
                    自定义图标
                </li>
                <li class="menuItem" id="itemAdd">
                    从列表添加
                </li>
            </ul>
        </div>
        
        <!--当前桌面-->
        <input type="hidden" name="desktop_id" id="current_desktop" value="1"/>
        <!-- Bootstrap -->
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
<?php
$db->close();
?>
