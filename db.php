<?php

define( 'SAE_MYSQL_HOST_M', 'w.rdc.sae.sina.com.cn' );//主库地址
define( 'SAE_MYSQL_HOST_S', 'r.rdc.sae.sina.com.cn' );//从库地址
define( 'SAE_MYSQL_PORT', 3307 );//数据库端口
define( 'SAE_MYSQL_USER', '2zk5oy4olx' );//数据库用户名
define( 'SAE_MYSQL_PASS', 'jkk5k13xi5ilhm5y4w1ylym3lij52wmiljjjyj1x' );//数据库密码
define( 'SAE_MYSQL_DB', 'app_' . $_SERVER['HTTP_APPNAME'] );//数据库名

function write_to_db($fromUsername)//写入数据库
{
    $db = new mysqli(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS,SAE_MYSQL_DB);
    $query="insert into test values
            ('".$fromUsername."','comeagain')";  //查询用的字符串
    $result = $db->query($query);
    $db->close();
}

function search_from_db($searchtype, $searchterm)
{
    $db = new mysqli(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS,SAE_MYSQL_DB);
    if(!$db) echo "connected failed..";
    if($searchtype == 'food_id')
    {
         $query="select * from food,restaurant where food.restaurant_id=restaurant.restaurant_id and food_id = ".$searchterm;
    }
    else
    $query="select * from food,restaurant where food.restaurant_id=restaurant.restaurant_id and ".$searchtype." like '%".$searchterm."%'"; //将查询语句赋值给$query
    $result=$db->query($query); //将查询返回的结果类保存在$result中
    if(!$result)echo 'nothing select';
    $num_results=$result->num_rows;
    $n=rand(1,$num_results);
    for($i=0; $i < $n; $i++)
    {
        $row=$result->fetch_assoc();//获取一个元组
    }
    $db->close();
    return $row;
}


function get_num($searchterm)
{
    $db = new mysqli(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS,SAE_MYSQL_DB);    
    $query="select max(".$searchterm."_id) from %".$searchterm."%'"; //将查询语句赋值给$query
    $result=$db->query($query); //将查询返回的结果类保存在$result中
    $row=$result->fetch_assoc();
    if(!$row) echo  "result is null!";
    return $row["$searchterm".'_id'];
}

?>
