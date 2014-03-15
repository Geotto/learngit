<?php
include "Database.php";

$db = new Database();
$db->connect();

$types_for_selection = "";
$output = $db->getType();
if($output != ""){
	$types = explode("&sep1;",$output);
    
    foreach($types as $type){
        if($type == ""){
        	continue;
        }
        
        //自定义框中分类选择器
        $args = explode("&sep0;",$type);
        $types_for_selection = $types_for_selection."<li class=\"type_selector text-center\" id=\"type_{$args[0]}\">
        	{$args[1]}
        </li>";
    }
}

$db->close();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <style type="text/css">
            .type_selector:hover{
                cursor:pointer;
                background-color:lightblue;
            }
            .type_selector{
                color:red;
            }            
        </style>
        <script src="js/selfDefContent.js" type="text/javascript"></script>
    </head>
    <body>         
        <div class="row" style="margin-top:50px">
            <div class="span1 text-info">名称</div>
            <input class="span5" id="win_selfdef_name" type="text" name="selfdef_name"/>
        </div>
        <div class="row">
            <div class="span1 text-info">网址</div>
            <input class="span5" id="win_selfdef_site" type="text" name="selfdef_site" value="http://"/>
        </div>
      <input type="hidden" name="selected-type" id="selected-type" value="type"/>
        <div class="row">
            <div class="btn-group span1">
                <div class="btn btn-warning btn-small dropdown-toggle " id="selected_type" data-toggle="dropdown">
                    选择分类<span class="caret"></span>
                </div>
                <ul class="dropdown-menu">
                    <?php echo $types_for_selection;?>
                </ul>
            </div>
            <div class="text-center text-success span3">添加标签（多个标签用;分开）</div>
            <input class="span2" id="win_selfdef_tags" type="text" name="selfdef_tags"/>
        </div>
        <div class="row">
            <div class="btn btn-primary offset4 span1" id="btn_commit_seldefcontent">
                确定
            </div>
        </div>
    </body>
</html>