var selectedIcons = new Array();//被选中的图标
var selectedSites = new Array();//被选中的网址
var selectedNames = new Array();//被选中的网页名
var currentDesktop = 1;//当前桌面
var tempDesktopId = 1;//桌面号备份
var currentType = "type_commen";//当前类型
//错误编号
var FAIL_NOT_LOGIN = -1;//未登录
var FAIL_CAN_NOT_LOGIN = -2;//无法登录
var FAIL_INVALID_QUERY = -3;//查询时发生错误
var FAIL_DENY_INSERT = -4;//无法插入数据
var FAIL_DENY_DELETE = -5;//无法删除数据

//图片文件常量
var IMG_ACCEPT = "image/accept.png";
var IMG_CLOSE = "image/close.png";
var IMG_GRID = "image/grid.png";
var IMG_DESKTOP = "image/ios7-photos-outline.png";
var IMG_LOGIN = "image/log-in.png";
var IMG_PLUS = "image/plus.png";
var IMG_LOGO = "image/favico.png";

$(document).ready(function(){
    var headerHeight = 32;//标题栏高度
    var footerHeight = 48;//启动栏高度
    var mainHeight;//主体的高度
    var docHeight = $(document).height();//文档的高度
    var docWidth = $(document).width();//文档的宽度
    
    mainHeight = docHeight-headerHeight-footerHeight;
    //设置各部分高度
    $("#header").css("height",headerHeight+"px");
    $("#footer").css("height",footerHeight+"px");
    $("#main").css("height",mainHeight);
    
    //设置logo
    $("#logo").css("width",headerHeight);
    
    //设置标题
    var titleHeight = $("#title").height();
    $("#title").css("margin-top",(headerHeight-titleHeight)/2);
    
    //设置启动器
    $(".icon_group").height(footerHeight-10);
    
    //设置窗口位置
    var winListHeight = $("#win_list").height();
    var winListWidth = $("#win_list").width();
    
    $("#win_list").css("top",(docHeight-winListHeight)/2);
    $("#win_list").css("left",(docWidth-winListWidth)/2);
    
    var winLoginHeight = $("#win_login").height();
    var winLoginWidth = $("#win_login").width();
    
    $("#win_login").css("top",(docHeight-winListHeight)/2);
    $("#win_login").css("left",(docWidth-winListWidth)/2);
    
    var winRegisterHeight = $("#win_register").height();
    var winRegisterWidth = $("#win_register").width();
    
    $("#win_register").css("top",(docHeight-winRegisterHeight)/2);
    $("#win_register").css("left",(docWidth-winRegisterWidth)/2);
    
    var win_aboutWidth = $("#win_about").width();
    
    $("#win_about").css("left",docWidth - win_aboutWidth-32);
    
    //设置消息框位置
    var messageBoxWidth = $("#messagebox").width();
    $("#messagebox").css("left",(docWidth-messageBoxWidth)/2);
    
    //点击关闭按钮
    $(".closeit").click(function(){
    	$(this).parents(".win").hide();
	currentDesktop = tempDesktopId;
    });
    
    $("#launcher").click(function(){
    	$("#win_list").toggle();
    });
    
    //点击分类按钮
    $(".type").click(function(){
        var id=$(this).attr("id");
        currentType = id;
        $.post("getTypeContent.php",{request:"type",name:id},function(data,status){
        	$("#win_list_left_bottom").html(data);
            
            selectIcon();
            addToDesktop();
        });
    });
    
    //处理标签搜索
    $("#tag_search").click(function(){
    	var tag = $("#tag_name").val();
        
        if(tag != ""){
            $.post("getTypeContent.php",{request:"tag",name:tag},function(data,status){
            	$("#win_list_left_bottom").html(data);
            	selectIcon();
            	addToDesktop();
            });
        }
    });
    
    //切换桌面
    $(".desktopField").click(function(){
    	var id = $(this).attr("id");
        var args = id.split("_");
        $.post("changeDesktop.php",{desktop:args[1]},function(data,status){
            $("#main").html(data);
            //绑定菜单
            deleteIcon();
    	    gridMenu();
        });
        currentDesktop = args[1];
        tempDesktopId = currentDesktop;
        btnAdd_click();
    });
    
    //添加桌面
    $("#add_desktop").click(function(){
        $.post("addDesktop.php",{request:"add_button"},function(data,status){
            $("#messagebox").html(data);
            $("#messagebox").show();
            var t = setTimeout("$(\"#messagebox\").hide();location.reload();",5000);
        });
    });
    
    //登录按钮
    $("#btn_login").click(function(){
        var user = getCookie("user");
        if(user == "customer"){
        	$("#win_login").toggle();
        }
        else{
        	delCookie("user");
            delCookie("passwd");
            $("#messagebox").html("正在注销");
            $("#messagebox").show();
            var t = setTimeout("$(\"#messagebox\").hide()",5000);
            location.reload();
        }
    });
    
    //登录界面的注册按钮
    $("#tag_do_register").click(function(){
    	$("#win_login").hide();
        $("#win_register").show();
    });
    
    //检验用户名
    $("#user_set").keyup(function(){
	var user_set = $("#user_set").val();
	var pattern = new RegExp("^(([0-9]|_)+)");

	if(pattern.test(user_set)){
	    $("#info_confirm").text("用户名不能以数字或下划线开头");
	}
	else{
	    $("#info_confirm").text("校验结果");
	}
    });

    //校验密码
    $("#passwd_confirm").keyup(function(){
    	var passwd_set = $("#passwd_set").val();
        var passwd_confirm = $("#passwd_confirm").val();
        
        if(passwd_set == passwd_confirm){
        	$("#info_confirm").text("校验通过");
        }
        else{
        	$("#info_confirm").text("两次输入的密码不一致");     	
        }
    });
    
    //注册
    $("#btn_register").click(function(){
        var output = "";
        var alertStatus = 0;
        var user_set = $("#user_set").val();
    	var passwd_set = $("#passwd_set").val();
        var passwd_confirm = $("#passwd_confirm").val();
	var pattern = new RegExp("^(([0-9]|_)+)");
        
        if(user_set == ""){
            output = output + "用户名为空<br/>";
            alertStatus = 1;
        }
	if(pattern.test(user_set)){
	    output= output + "用户名不能以数字或下划线开始<br/>";
	    alertStatus = 1;
	}
        if(passwd_set == ""){
            output = output + "密码为空<br/>";
            alertStatus = 1;            
        }
        if(passwd_set != passwd_confirm){
            output = output + "两次输入的密码不一致<br/>";
            alertStatus = 1;
        }
        
        if(alertStatus == 1){
            $("#messagebox").html(output);
            $("#messagebox").show();
            var t = setTimeout("$(\"#messagebox\").hide()",5000);
        }
        else{
            $.post("doRegister.php",{name:user_set,passwd:passwd_set},function(data,status){
                $("#messagebox").html(data);
                $("#messagebox").show();
                var t = setTimeout("$(\"#messagebox\").hide()",5000);
                
                $("#win_register").hide();
        	});
        }
    });
    
    //登录
    $("#btn_dologin").click(function(){
        var name_input = $("#name_input").val();
        var passwd_input = $("#passwd_input").val();
        var keep_login = "false";
        
        //判断是否选中复选框
        if($("#keep_login").is(":checked") == true){
        	keep_login = "true";
        }
        
        if(name_input != "" && passwd_input != ""){
            $.post("doLogin.php",{name:name_input,passwd:passwd_input,keep_login:keep_login},function(data,status){
                if(data == "SUCCESS"){
        			$("#messagebox").html("登录成功");
                    location.reload();
                    return;
                }
                else if(data == "FAIL_INVALID_NAME_OR_PASSWD"){
                    $("#messagebox").html("用户名或密码错误");
                }
                else if(data == "FAIL_UNABLE_CONNECTED_TO_DATABASE"){
                    $("#messagebox").html("无法连接到数据库");
                }
                else if(data == "FAIL_CAN_NOT_LOGIN"){
                    $("#messagebox").html("无法登录");
                }
        	});        	
        }
        else if(name_input == ""){
            $("#messagebox").html("用户名为空");
        }
        else if(passwd_input == ""){
            $("#messagebox").html("密码为空");
        }
                
        $("#messagebox").show();
        var t = setTimeout("$(\"#messagebox\").hide()",5000);
    });
    
    //启动计时器
    timming();
    
    //显示关于信息
    $("#about").click(function(){
    	$("#win_about").toggle();
    });
    
    //删除桌面
    $('.desktopField').contextMenu('menuDelDesktop', 
     {
          bindings:
          {
            'itemDelDesktop': function(t) {
                
                //获取用户名和密码
                var userName = getCookie("user");
                var passwd = getCookie("passwd");
                var targetId = t.id;
                
                var args = targetId.split('_');
                var desktopId = args[1];
                
                $.post("del_desktop.php",{user:userName,passwd:passwd,id:desktopId},function(data,status){
                    if(data == "SUCCESS"){
                    	$("#messagebox").html("操作成功,5秒后自动刷新");
                        $("#"+targetId).hide();
                    }
                    else if(data == "FAILT_INVALID_NAME_OR_PASSWD"){
                    	$("#messagebox").html("用户名或密码有误");
                    }
                    else if(data == "FAIL_WRONG_ID"){
                    	$("#messagebox").html("错误的桌面号");
                    }
                    else if(data == "FAIL_CAN_NOT_LOGIN"){
                    	$("#messagebox").html("当前用户无法登录");
                    }
                    else if(data == "FAIL_OPERATION_FAILED"){
                    	$("#messagebox").html("操作失败");
                    }
                    else if(data == "FAIL_NOT_LOGIN"){
                    	$("#messagebox").html("您需要登录后才能执行此操作(点击右下角按钮登录)");
                    }
                    else{
                    	$("#messagebox").html(data);                    	
                    }
                    
                    $("#messagebox").show();
        			var t = setTimeout("$(\"#messagebox\").hide();location.reload();",5000);
        		});
            }
          }

    });
    
    //删除图标
    deleteIcon();
    
    //添加常用项按钮
    addCommen_clicked();
    
    //各桌面添加选项
    btnAdd_click();
    
    //自定义按钮    
    $('#self-def').click(function(){
        $('#win_list_left_bottom').load('selfDefContent.php');
        $('#current_desktop').val('0');
    });
    
    //点击图标以添加到桌面
    selectIcon();
    //点击添加按钮
    addToDesktop();
})

//设置cookie
function setCookie(c_name,value,expiredays)
{
var exdate=new Date()
exdate.setDate(exdate.getDate()+expiredays)
document.cookie=c_name+ "=" +escape(value)+
((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}

//获取cookie
function getCookie(c_name)
{
if (document.cookie.length>0)
  {
  c_start=document.cookie.indexOf(c_name + "=")
  if (c_start!=-1)
    { 
    c_start=c_start + c_name.length+1 
    c_end=document.cookie.indexOf(";",c_start)
    if (c_end==-1) c_end=document.cookie.length
    return unescape(document.cookie.substring(c_start,c_end))
    } 
  }
return ""
}

//删除cookie
function delCookie(name)
{
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval=getCookie(name);
    if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
}

//删除数组指定元素
function delElem(array,elem){
    var index = -1;
    
    for(i=0;i<array.length;i++){
        if(array[i] == elem){
        	index = i;
            break;
        }
    }
    
    if(index != -1){
    	array.splice(index,1);
    }
    
    return index;
}

//时钟
function timming(){
	var today = new Date();
    var hours = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();
    
    minutes = checkTime(minutes);
    seconds = checkTime(seconds);
    
    $("#timezone").html(hours+":"+minutes+":"+seconds);
    
    setTimeout('timming()',1000);
}

//格式化时间表示
function checkTime(i)
{
if (i<10) 
  {i="0" + i}
  return i
}

//显示提示信息
function showTips(tips){
	$("#messagebox").html(tips);
    $("#messagebox").show();
    setTimeout("$(\"#messagebox\").hide()",5000);    
}

//删除图标
function deleteIcon(){
    $(".linkedIcon").contextMenu('menuDelIcon',{
        bindings:{
            'itemDelIcon':function(t){
            	//获取用户名和密码
                var userName = getCookie("user");
                var passwd = getCookie("passwd");
                
                //获取桌面号和链接编码
                var targetId = t.id;
                var args = targetId.split('_');
                
                $.post("del_icon.php",{user:userName,passwd:passwd,desktop:args[0],indexs:args[1]},function(data,status){
                    
                    if(data == "SUCCESS"){
                    	$("#messagebox").html("操作成功");

                        //删除常用项
                        if(args[0] == 0){
                            $('#'+targetId).remove();
                        }
                        //重排桌面
                        else{
							var content = "<div class=\"row\">";//更新后的内容
							var gridIndex = 0;//网格编号
							var grids = new Array();

							$(".grid").not("#grid-last").each(function(){
								var icon = $(this).children(".icon-desktop");
								if(icon.attr("id") != targetId){
									grids[gridIndex] = $(this);
									gridIndex++;
								}
							});

							j = 0;
							for(i=0;i<grids.length;i++){
							if(j == 8){
								content += "</div><div class=\"row\">";
								j = 0;
							}
							content += "<div class=\"grid\">"
								+grids[i].html()
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

					$("#main").html(content);
					deleteIcon();
					btnAdd_click();
                    }
				}
                    else if(data == "FAILT_INVALID_NAME_OR_PASSWD"){
                    	$("#messagebox").html("用户名或密码有误");
                    }
                    else if(data == "FAIL_WRONG_ID_OR_DESKTOP"){
                    	$("#messagebox").html("错误的桌面号或编号");
                    }
                    else if(data == "FAIL_NOT_LOGIN"){
                    	$("#messagebox").html("您需要登录后才能执行此操作(点击右下角按钮登录)");
                    }
                    else if(data == "FAIL_CAN_NOT_LOGIN"){
                    	$("#messagebox").html("当前用户无法登录");
                    }
                    else if(data == "FAIL_OPERATION_FAILED"){
                    	$("#messagebox").html("操作失败");
                    }
                    else{
                    	$("#messagebox").html(data);
                    }
                    
                    $("#messagebox").show();
        			setTimeout("$(\"#messagebox\").hide()",5000);
                });
            }
        }
    });	
}

//处理选中图标
function selectIcon(){	
    //点击图标以添加到桌面
    $('.list-icon').click(function(){
        var iconId = $(this).attr('id');
        var args = iconId.split('-');
        
        var isSelected = $(this).attr('title');
        if(isSelected == 'not-selected' || isSelected == ''){
            //未被选中
            $(this).find('.tick').show();
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
}

//各桌面添加选项
function btnAdd_click(){
    $(".btn-add-icon").click(function(){
    	$("#win_list").show();
        currentDesktop = tempDesktopId;
    });
}

//点击添加按钮
function addToDesktop(){
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
                    	    + "<a href=\""+selectedSites[i]+"\" target=\"_blank\" class=\"iconBox linkedIcon icon-desktop\" id=\""+currentDesktop+"_"+maxIndex+"\">"
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
}

function addCommen_clicked(){
    //添加常用项按钮
    $('#add-commen').click(function(){
    	$('#win_list').show();
        tempDesktopId = currentDesktop;
        currentDesktop = 0;
    });
}
