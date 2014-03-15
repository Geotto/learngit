<?php
include "Database.php";

//图片文件常量
define("IMG_ACCEPT","http://webgrid-img.stor.sinaapp.com/accept.png");

$request = $_POST["request"];
$name = $_POST["name"];

if(!$name || !$request){
	echo "发送的数据有误";
    return;
}

//获取指定类型的内容
$result = "<div class=\"row\">
	<span class=\"text-success text-center span4\">
    	从下面选择想要添加到桌面的网址
    </span>
	<div class=\"btn btn-primary span1\" id=\"addToDesktop\">
    	添加
    </div>
</div>";

$db = new Database();
$db->connect();

if($request == "type"){
	$output = $db->getTypeContent($name);
}
if($request == "tag"){
	$output = $db->searchTag($name);
}

$sites = explode("&sep1;",$output);
foreach($sites as $site){
	if($site == "")
        continue;
    
    $args = explode("&sep0;",$site);
    $result = $result."<div class=\"span1 text-center\">  
            	<div class=\"iconBox list-icon\" id=\"icon-{$args[2]}\" title=\"not-selected\">
            		<image src=\"{$args[1]}/favicon.ico\" class=\"icon\"/>
                    <image src=\"".IMG_ACCEPT."\" class=\"tick\"/>
        		</div>
            <a class=\"label-icon\" href=\"{$args[1]}\" target=\"_blank\">{$args[0]}</a>
        </div>";
}

$db->close();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8"/>
    </head>
    <body>
        <?php echo $result;?>
    </body>
</html>
