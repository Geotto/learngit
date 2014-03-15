<?php
//数据库类
class Database{
  private $mysqli;

  function __construct(){
    define('SAE_MYSQL_HOST_M','localhost');
    define('SAE_MYSQL_USER','webgrid');
    define('SAE_MYSQL_PASS','F3s8ACFLZwvJc9JH');
    define('SAE_MYSQL_DB','webgrid');
    define('SAE_MYSQL_PORT','5255');

    //错误编号
    define("FAIL_NOT_LOGIN",-1);//未登录
    define("FAIL_CAN_NOT_LOGIN",-2);//无法登录
    define("FAIL_INVALID_QUERY",-3);//查询时发生错误
    define("FAIL_DENY_INSERT",-4);//无法插入数据
    define("FAIL_DENY_DELETE",-5);//无法删除数据
  }
    
    //连接到数据库
    function connect(){
      $this->mysqli = new mysqli(SAE_MYSQL_HOST_M,SAE_MYSQL_USER,SAE_MYSQL_PASS,SAE_MYSQL_DB,SAE_MYSQL_PORT);
      $this->mysqli->query("SET NAMES utf8");

      return $this->mysqli->errno;
    }
    
    //关闭数据库
    function close(){
        $this->mysqli->close();
    }
    
    //添加用户
    function addUser($name,$passwd){
        //判断用户是否已存在
        $query = "select name from user";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        while(list($nameGot) = $result->fetch_row()){
            if($name == $nameGot){
                return "用户名已存在";
            }
        }
        
        //将用户添加到数据库
        $query = "insert into user(name,passwd,isLogin,canLogin,desktopCount)
            values('$name','$passwd',0,1,4)";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        
        if($this->mysqli->errno){
            return $this->mysqli->error;
        }
        
        //创建用户表
        $query = "create table $name(id int(8) PRIMARY KEY,desktop int(1),indexs int(5))";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        if($this->mysqli->errno){
            return "创建用户数据失败";
        }
        
        //添加项目
        $query = "select id,desktop,indexs from customer";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        if($this->mysqli->errno){
            return "创建用户数据失败";
        }
        
        while(list($id,$desktop,$index) = $result->fetch_row()){
        	$query = "insert into $name(id,desktop,indexs) values($id,$desktop,$index)";
            $result2 = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
            if($this->mysqli->errno){
                return "插入用户数据失败";
            }
        }
        
        return "操作成功";
    }
    
    //是否可登录,passwd需经md5处理
    function canLogin($name,$passwd){
        $query = "select name,passwd from user where canLogin=1";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        while(list($nameGot,$passwdGot) = $result->fetch_row()){
            //判断用户是否可登录
            if($nameGot == $name && md5($passwdGot) == $passwd){
                return true;
            }
        }
        
        return false;
    }
    
    //登录
    function login($name,$passwd){
        $output = "";
        
        if(!$this->canLogin($name,$passwd)){
            return "FAIL";
        }
        else{        
            return "SUCCESS";
        }
    }
    
    //获取网站列表
    function getContent(){
    	$output = "";
        
        $query = "select name,description from site_type";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        while(list($type,$description) = $result->fetch_row()){
            $output = $output.$description."^";
            $query = "select name,site from sites where tp='$type'";
            $result2 = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
            
            while(list($name,$site) = $result2->fetch_row()){
                $output = $output."$name~$site#";
            }
            
            $output = $output."<br/>";
        }
        
        return $output;
    }
    
    //获取常用项
    function getCommen($name,$passwd){
        $output = "";

        if($this->canLogin($name,$passwd)){
	  //查询常用项
	  $query = "select id,indexs from $name where desktop=0 order by indexs";
	  $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
	  while(list($id,$indexs) = $result->fetch_row()){
	    $query = "select name,site from sites where id=$id";
	    $result2 = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
	    while(list($nameGot,$site) = $result2->fetch_row()){
	      $output = $output.$nameGot."&sep0;".$site."&sep0;".$indexs."&sep1;";
	    }
	  }
        }

        return $output;
    }
    
    //获取桌面数量
    function getDesktopCount($name,$passwd){
        if($this->canLogin($name,$passwd)){
            $query="select desktopCount from user where name='$name'";
            $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
            while(list($count) = $result->fetch_row()){
                return $count;
            }
        }
    }
    
    //获取指定桌面的内容
    function getDesktopContent($desktop,$name,$passwd){
        $output = "";
        
        if($this->canLogin($name,$passwd)){
	  $query = "select id,indexs from $name where desktop=$desktop order by indexs";
	  $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
	  while(list($id,$index) = $result->fetch_row()){
                $query = "select name,site from sites where id=$id";
            	$result2 = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
                while(list($nameGot,$site) = $result2->fetch_row()){
		  $output = $output.$nameGot
		    ."&sep0;".$site."&sep0;".$index."&sep0;".$id."&sep1;";
		}
            }
        }
        
        return $output;
    }
    
    //获取分类
    function getType(){
        $output = "";
        
        $query = "select name,description from site_type order by rank";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        while(list($name,$description) = $result->fetch_row()){
            $output = $output.$name."&sep0;".$description."&sep1;";
        }
        
        return $output;
    }
    
    //获取customer常用项
    function getSiteCommen(){
    	$output = "";
        
        $query = "select name,site,id from sites where commen=1";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        while(list($name,$site,$id) = $result->fetch_row()){
        	$output = $output.$name."&sep0;".$site."&sep0;".$id."&sep1;";
        }
        
        return $output;
        
    }
    
    //获取分类内容
    function getTypeContent($type){
        $output = "";
        
        //处理常用请求
        if($type == "type_commen"){
        	$query = "select name,site from sites where commen=1";
        }
        else{
    		$query = "select name,site,id from sites where tp='$type'";
        }
        
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        while(list($name,$site,$id) = $result->fetch_row()){
        	$output = $output.$name."&sep0;".$site."&sep0;".$id."&sep1;";
        }
    
    	return $output;
    }
    
    //搜索标签
    function searchTag($tag){
        $output = "";
    		
        $query = "select name,site,id from sites where tag like '%$tag%'";
    	$result = mysqli_query($this->mysqli,$query);
        while($row = $result->fetch_row()){
        	$output = $output.$row[0]."&sep0;".$row[1]."&sep0;".$row[2]."&sep1;";
        }
    
    	return $output;
    }
    
    //添加桌面
    function addDesktop($name,$passwd){
        
        if($name == "customer"){
        	return "您需要登录后才能进行该操作（点击右下角按钮登录）";
        }
        
        if(!$this->canLogin($name,$passwd)){
        	return "当前用户无法登录";
        }
        
        $query = "select desktopCount from user where name='$name'";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        while(list($desktopCount) = $result->fetch_row()){	
            $desktopCount = $desktopCount + 1;
        	$query = "update user set desktopCount=$desktopCount where name='$name'";
        	$result2 = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
            if($this->mysqli->errno){
                return "更新数据库失败";
            }
        }
        
        return "操作成功";
    }
    
    //删除桌面
    function delDesktop($name,$passwd,$id){
        if($name == "customer"){
        	return "FAIL_NOT_LOGIN";
        }
        
        if(!$this->canLogin($name,$passwd)){
        	return "FAIL_CAN_NOT_LOGIN";
        }
        
        $query = "DELETE FROM $name WHERE desktop = $id";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        if($this->mysqli->errno){
            return "FAIL_OPERATION_FAILED";
        }
        
        $query = "SELECT desktopCount FROM user WHERE name='$name'";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        while(list($desktopCount) = $result->fetch_row()){
            $desktopCount = $desktopCount - 1;
            
	    $query = "UPDATE user SET desktopCount = $desktopCount WHERE name = '$name'";
            $result2 = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        }
        if($this->mysqli->errno){
            return "FAIL_OPERATION_FAILED";
        }
        
        return "SUCCESS";
    }
    
    //删除图标
    function delIcon($name,$passwd,$desktop,$indexs){
        if($name == "customer"){
        	return "FAIL_NOT_LOGIN";
        }
        
        if(!$this->canLogin($name,$passwd)){
        	return "FAIL_CAN_NOT_LOGIN";
        }
        
        $query = "DELETE FROM $name WHERE desktop=$desktop AND indexs=$indexs";
        $result = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        if($this->mysqli->errno){
	  return "FAIL_OPERATION_FAILED";
        }
        
        return "SUCCESS";
    }
    
    //添加图标到桌面
    function addIcon($name,$passwd,$id,$desktop){        
        if($name == "customer"){
        	return FAIL_NOT_LOGIN;
        }
        
        if(!$this->canLogin($name,$passwd)){
        	return FAIL_CAN_NOT_LOGIN;
        }
        
        //将图标添加到桌面
	$maxIndex = 0;
        $query = "SELECT MAX(indexs) FROM $name WHERE desktop='$desktop'";
        $result5 = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
        while(list($indexGot) = $result5->fetch_row()){
	  $maxIndex = $indexGot;
	  $newIndex = $indexGot + 1;
	  $query = "INSERT INTO $name(id,desktop,indexs) VALUES($id,$desktop,$newIndex)";
	  $result6 = $this->mysqli->query($query,MYSQLI_STORE_RESULT);
	  if($this->mysqli->errno){
	    return FAIL_DENY_INSERT;
	  }
        }
        
        return $maxIndex;
    }
    
    //添加自定义图标
    function addSelfDefContent($name,$passwd,$desktop,$siteName,$siteUrl,$siteType,$tags){
      if($name == "customer"){
	return FAIL_NOT_LOGIN;
      }

      if(!$this->canLogin($name,$passwd)){
	return FAIL_CAN_NOT_LOGIN;
      }
        
      //添加到列表
      $query = "INSERT INTO sites(name,site,tp,tag) VALUES('$siteName','$siteUrl','$siteType','$tags')";
      $result3 = mysqli_query($this->mysqli,$query);
      if($this->mysqli->errno){
	return FAIL_DENY_INSERT;
      }
                    
      //获取添加网址的id
      $maxIndex = 0;
      $query = "SELECT id FROM sites WHERE site='$siteUrl'";
      $result4 = mysqli_query($this->mysqli,$query);
      $row4 = $result4->fetch_row();

      //将图标添加到桌面
      $query = "SELECT MAX(indexs) FROM $name WHERE desktop='$desktop'";
      $result5 = mysqli_query($this->mysqli,$query);
      $row5 = $result5->fetch_row();

      $maxIndex = $row5[0];
      $newIndex = $row5[0] + 1;
      $query = "INSERT INTO $name(id,desktop,indexs) VALUES({$row4[0]},$desktop,$newIndex)";
      $result6 = mysqli_query($this->mysqli,$query);
      if($this->mysqli->errno){
		return FAIL_DENY_INSERT;
      }
        
      return $maxIndex;
    }
}
?>
