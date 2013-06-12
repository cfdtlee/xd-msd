<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>西电美食订餐页面</title>
<script type="text/javascript"> //点击购买后弹窗提示购买成功
function Get() { 
alert('已下单，我们马上进行配送(^^)'); 
}

function isTelOrMobile(telephone){  //判断输入的号码的格式是否正确
    var teleReg = /^((0\d{2,3})-)(\d{7,8})$/;  
    var mobileReg =/^1[358]\d{9}$/;   
    if (!teleReg.test(telephone) && !mobileReg.test(telephone)){  
        return false;  
    }else{  
        return true;  
    }  
}

function check()
{
    
} 
</script> 
</head>
<body align="center">
<h1>西电美食外卖订餐页面</h1>

<?php
include('db.php');//包含db.php
$food_id = $_GET["food_id"];//获取GET传来的food_id
$row=search_from_db('food_id',$food_id);//根据food_id查询数据
$src='./img/1.jpg';
echo '<img src="./img/'.$food_id.'.jpg" /><br />';
echo $row['place'].'的'.$row['food_name'].'单价：'.$row['price'].'元';
?>

<form>
    数量：<input type="text" name="num" />
    <br />
    联系方式：<input type="text" name="contact" />
    <br />
    <input type="button" onclick="Get()" value="怒买之" />
</form>
<br />

</body>
</html>




