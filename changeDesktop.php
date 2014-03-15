<?php
include "Database.php";

//图片文件常量
define("IMG_PLUS","image/plus.png");

//验证用户
if(isset($_COOKIE['user']) && isset($_COOKIE['passwd'])){
	$name = $_COOKIE['user'];
    $passwd = $_COOKIE['passwd'];
}
else{
    $name = "customer";
    $passwd = md5("123456");
}

$desktop = $_POST["desktop"];
if($desktop == ""){
	echo "错误的桌面号";
    return;
}


$db = new Database();
$db->connect();

$desktopContent = "<div class=\"row\">";
$iconCounter = 0;
$output = $db->getDesktopContent($desktop,$name,$passwd);
$items = explode("&sep1;",$output);
foreach($items as $item){
    if($item != ""){
        //产生图标
        $iconCounter = $iconCounter + 1;
    	$args = explode("&sep0;",$item);
        $desktopContent = $desktopContent."<div class=\"grid\">
        	<a class=\"iconBox linkedIcon icon-desktop\" href=\"{$args[1]}\" id=\"{$desktop}_{$args[2]}\" target=\"_black\">
            	<image class=\"icon\" src=\"{$args[1]}/favicon.ico\"/>
            </a>
            <span class=\"text-center  label-icon\">{$args[0]}</span>
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
    <span class=\"text-center  label-icon\">添加网址</span>
</div>".'</div>';

$db->close();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>桌面内容</title>
        <script>
            $(document).ready(function(){
                btnAdd_click();
            });
        </script>
    </head>
    <body>
		<?php echo $desktopContent; ?>
    </body>
</html>
