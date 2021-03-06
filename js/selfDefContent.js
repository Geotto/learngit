$(document).ready(function(){
    $('.type_selector').click(function(){
	$('#selected_type').html($(this).text()+'<span class="caret"></span>');
	var type_id = $(this).attr('id');
	var args = type_id.split("_");
	$('#selected-type').val(args[1]);
	currentType = args[1];
    });

    //提交自定义内容
    $('#btn_commit_seldefcontent').click(function(){
	var val_name = $('#win_selfdef_name').val();
	var val_site = $('#win_selfdef_site').val();
	var val_type = $('#selected-type').val();
	var val_tags = $('#win_selfdef_tags').val();

	if(val_name == ""){
	    $("#messagebox").html('请输入网站名称');
	    $("#messagebox").show();
	    var t = setTimeout("$(\"#messagebox\").hide()",5000);
	}
	else if(val_site == "http://"){
	    $("#messagebox").html('请输入网址');
	    $("#messagebox").show();
	    var t = setTimeout("$(\"#messagebox\").hide()",5000);
	}
	else if(val_type == "type"){
	    $("#messagebox").html('请选择网站类型');
	    $("#messagebox").show();
	    var t = setTimeout("$(\"#messagebox\").hide()",5000);
	}
	else{
	    $.post("addSelfDefContent.php",{
		desktop:currentDesktop,
		siteName:val_name,
		siteUrl:val_site,
		siteType:val_type,
		tags:val_tags
	    },function(data,status){
			var maxIndex = parseInt(data);
			var content = "";
				
			switch(maxIndex){
				case FAIL_NOT_LOGIN:
					showTips("您尚未登录，点击右下角按钮登录");
					break;
				case FAIL_CAN_NOT_LOGIN:
					showTips("您无法登录");
					break;
				case FAIL_DENY_INSERT:
					showTips("执行操作时发生错误");
					break;
			}
			
			if(maxIndex >= 0){
				if(currentDesktop == 0){
					//创建图标
					maxIndex++;
					content = content + "<a href=\""+val_site+"\" target=\"_blank\" class=\"iconBox linkedIcon\" id=\"0_"+maxIndex+"\">"
						+"<image src=\""+val_site+"/favicon.ico\" class=\"icon\" title=\""+val_name+"\"/>"
						+"</a>";
					content = $("#commen").html() + content;
					$("#commen").html(content);
					showTips("操作成功");
				}
				else{
					var gridIndex = 0;//网格位置索引

					//重排桌面
					j = 0;
					content = "<div class=\"row\">";
					$(".grid").not("#grid-last").each(function(){
						if(j == 8){
						j = 0;
						content += "</div><div class=\"row\">";
						}
						content += "<div class=\"grid\">" + $(this).html() + "</div>";
						j++;
					});

					maxIndex++;
					if(j == 8){
						j = 0;
						content += "</div><div class=\"row\">";
					}
					content += "<div class=\"grid\">"
						+ "<a href=\""+val_site+"\" target=\"_blank\" class=\"iconBox linkedIcon icon-desktop\" id=\""+currentDesktop+"_"+maxIndex+"\">"
						+"<image src=\""+val_site+"/favicon.ico\" class=\"icon\" title=\""+val_name+"\"/>"
						+"</a>"
						+"<span class=\"text-center label-icon\">"+val_name+"</span>"
						+"</div>";
					j++;

					//加入添加按钮
					if(j == 8)
						content += "</div><div class=\"row\">";
					content += "<div class=\"grid\" id=\"grid-last\">"
						+$("#grid-last").html()
						+"</div>";

					content += "</div>";

					//替换桌面内容
					$("#main").html(content);
					btnAdd_click();
					deleteIcon();

					showTips("操作成功");
				}
			}
		});
	}
});
});
