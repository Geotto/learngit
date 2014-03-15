$(document).ready(function(){
    //点击图标以添加到桌面
    $('.list-icon').click(function(){
        var iconId = $(this).attr('id');
        var args = iconId.split('-');
        
        var isSelected = $(this).attr('title');
        if(isSelected == 'not-selected' || isSelected == ''){
	    //未被选中
	    var tick = $(this).children('.tick');
	    tick.attr("css",'display:block');
	    $(this).attr('title','selected');
	    selectedIcons.push(args[1]);
	    
	    //将网址和网页名称添加到数组
	    var hrefs = $(this).next('a');
	    var href = hrefs.attr('href');
	    var name = hrefs.text();
	    selectedSites.push(href);
	    selectedNames.push(name);
	    showTips("OK");
        }
        else if(isSelected == 'selected'){
	    //已被选中
	    $(this).find('.tick').hide();
	    $(this).attr('title','not-selected');
	    
	    //删除数组中的对象
	    var index = delElem(selectedIcons,args[1]);
	    if(index != -1){
                selectedSites.splice(index,1);
                selectedNames.splice(index,1);
	    }
        }
    });

    //添加到桌面
    $("#addToDesktop").click(function(){
        var strIds = selectedIcons.join("&sep0;");
        $.post("addIcons.php",{desktop:currentDesktop,sites:strIds},function(data,status){
            var content = "";//添加的内容
	    var maxIndex = parseInt(data);//最大索引
            var i=0;

	    //解析返回值
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
                    for(i=0;i<selectedIcons.length;i++){
			maxIndex++;
                    	content = content + "<a href=\""+selectedSites[i]+"\" target=\"_blank\" class=\"iconBox linkedIcon\" id=\"0_"+maxIndex+"\">"
                            +"<image src=\""+selectedSites[i]+"/favicon.ico\" class=\"icon\" title=\""+selectedNames[i]+"\"/>"
                            +"</a>";
                    }

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

		    for(i=0;i<selectedIcons.length;i++){
			maxIndex++;
			if(j == 8){
			    j = 0;
			    content += "</div><div class=\"row\">";
			}
			content += "<div class=\"grid\">"
                    	    + "<a href=\""+selectedSites[i]+"\" target=\"_blank\" class=\"iconBox linkedIcon icon-desktop\" id=\"0_"+maxIndex+"\">"
                            +"<image src=\""+selectedSites[i]+"/favicon.ico\" class=\"icon\" title=\""+selectedNames[i]+"\"/>"
                            +"</a>"
			    +"<span class=\"text-center label-icon\">"+selectedNames[i]+"</span>"
			    +"</div>";
			j++;
		    }

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
                
                selectedSites = new Array();
                selectedNames = new Array();
                selectedIcons = new Array();
                
                $(".list-icon").attr("title","not-selected");
                $(".tick").hide();
		deleteIcon();
            }
        });
    });
});