<?php
require 'conn/conn.php';
require 'conn/function.php';

$action=$_GET["action"];
$S_id=intval($_GET["S_id"]);
$typex=$_GET["type"];

$_SESSION["form"]=$S_id;

$sql="select * from ".TABLE."form where F_id=".$S_id;
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
if(mysqli_num_rows($result) > 0) {
	$F_title=lang($row["F_title"]);
	$F_type=$row["F_type"];
	$F_cq=$row["F_cq"];
	$F_yzm=$row["F_yzm"];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>万能表单</title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-labelauty.js"></script>
	<link rel="stylesheet" href="css/jquery-labelauty.css">
	<script type="text/javascript" src="upload/upload.js"></script>
<style>
body{color:#666666;background:#FFFFFF; padding: 50px;box-sizing: border-box; }
p{ margin:5px;}
li { display: inline-block;}
input.labelauty + label { font: 12px "Microsoft Yahei";}

#preview{width: 100%;padding: 30px; position: absolute;box-sizing:border-box;top:0px;left: 0px;background: rgba(0,0,0,0.3);display: none;height: 100%;z-index:999;overflow: auto;}
#x{background: #ffffff;padding: 30px;border-radius: 10px;box-shadow: 0px 0px 10px;  }
</style>
<script type="text/javascript"> 
function refresh1(){ var vcode=document.getElementById('vcode'); vcode.src ="conn/code_1.php?nocache="+new Date().getTime();}
		$(document).ready(function(){
			$(':input').labelauty();
		});

function back(){
	$("#preview").hide();
}

function submitx(){
	$("#form").submit();
}

function preview(){
	$a="";
	$(".tm").each(function(){

	$a=$a+"<div style='font-size:20px;font-weight:bold;margin-top:20px;margin-bottom:10px;'>"+$(this).html()+"</div>";

	if($(this).parent().find("input").length > 0){
		$x=$(this).parent().find("input").val();
		var spl = $x.split(".");
		if(spl[spl.length-1]=="jpg" || spl[spl.length-1]=="png" || spl[spl.length-1]=="jpeg" || spl[spl.length-1]=="gif"){
			$x="<a href='media/"+$x+"' target='_blank'><img src='media/"+$x+"' style='width:100%;max-width:500px'></a>";
		}
		$a=$a+$x;
	}

	if($(this).parent().find("select").length > 0){
		$a=$a+$(this).parent().find("select").val();
	}

	if($(this).parent().find("textarea").length > 0){
		$a=$a+$(this).parent().find("textarea").val();
	}

	});

	$a=$a+"<p style='margin-top:20px;'><button class='btn btn-info' onclick='back()'>返回</button> <button class='btn btn-primary' onclick='submitx()'>确认提交</button></p>";
$("#preview").show();
$("#x").html($a);
}


	</script>
</head>
<?php

$M_id=$_SESSION["M_id"];
if($M_id==""){
$M_id=getrs("select * from ".TABLE."member where M_login='未提供'","M_id");
}
if($M_id==getrs("select * from ".TABLE."member where M_login='未提供'","M_id")){
$url_to="index.php";
}else{
$url_to="member/member_form.php";
}
if($action=="input"){
	if ((xcode($_POST["code"],'DECODE',$_SESSION["CmsCode"],0)!=$_SESSION["CmsCode"] || $_POST["code"]=="" || $_SESSION["CmsCode"]=="") && $F_yzm==1){
		echo "<div style='height:500px'></div>";
		box(lang("请拖动滑块进行验证！/l/Verification code error"),"back","error");
	}else{
		$R_time=date('Y-m-d H:i:s');
		$R_rid=gen_key(15);
		foreach ($_POST as $x=>$value) {
		if ($x>0){
			if($_POST[$x]==""){
				$y="未填写";
			}else{
				$y=$_POST[$x];
			}
			if (!IsValidStr($_POST[$x])){
			box(lang("您输入的内容有敏感字符，请重新输入！/l/The contents you have entered are sensitive characters, please re-enter!"),"back","error");
			}else{
			mysqli_query($conn,"Insert into ".TABLE."response(R_cid,R_content,R_time,R_rid,R_member) values(".$x.",'".htmlspecialchars($y)."','".$R_time."','".$R_rid."',".$M_id.")");
			}
		}
		}
		if ($F_cq>0){
		mysqli_query($conn,"Insert into ".TABLE."query(Q_code,Q_content,Q_sort) values('".$R_rid."','".date('Y-m-d H:i:s')."__用户提交表单，等待处理"."',".$F_cq.")");
		box(lang("提交成功，查询码 ".$R_rid."/l/success!code ".$R_rid.""),$C_dir.$url_to,"success");
	}else{
		box(lang("提交成功！/l/success!code ".$R_rid.""),$C_dir.$url_to,"success");
	}
	sendmail("您的网站有新的表单提交","<h2>您的网站“".lang($C_webtitle)."”有新的表单提交</h2><hr>请进入“网站后台” - “表单系统” - “查看统计”查看详情！",$C_email);
		
	}
}
?>

<body>

<div id="preview">
<div id="x"></div>
</div>

<div style="width: 100%">

<?php
if ($action == "query") {
    $Q_sort = intval($_POST["Q_sort"]);
    $Q_code = t($_POST["Q_code"]);
    if ((xcode($_POST["code"], 'DECODE', $_SESSION["CmsCode"], 0) != $_SESSION["CmsCode"] || $_POST["code"] == "" || $_SESSION["CmsCode"] == "") && $F_yzm == 1) {
        echo "<div style='height:500px'></div>";
        box(lang("验证码错误！/l/Verification code error") , "back", "error");
    } else {
        $sql = "select * from " . TABLE . "query where Q_sort=" . $Q_sort . " and Q_code='" . $Q_code . "'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) > 0) {
            mysqli_query($conn, "update " . TABLE . "query set Q_times=Q_times+1 where Q_code='" . $Q_code . "'");
            if (strpos($row["Q_content"], "__") === false || substr($row["Q_content"], 0, 2) == "__") {
                $qcode = $row["Q_content"];
            } else {
                $qcode = $qcode . "<table class=\"table table-hover\">";
                $qcode = $qcode . "<tr><th>时间</th><th>进度</th></tr>";
                $code = explode("|", $row["Q_content"]);
                for ($j = 0; $j < count($code); $j++) {
                    $Q_time = splitx($code[$j], "__", 0);
                    $Q_result = splitx($code[$j], "__", 1);
                    $qcode = $qcode . "<tr><td>" . $Q_time . "</td><td>" . $Q_result . "</td></tr>";
                }
                $qcode = $qcode . "</table>";
            }
            $sql2 = "select * from " . TABLE . "response where R_rid='" . $Q_code . "'";
            $result2 = mysqli_query($conn, $sql2);
            if (mysqli_num_rows($result2) > 0) {
                while ($row2 = mysqli_fetch_assoc($result2)) {
                    $res = $res . "<div class=\"col-md-4 col-xs-6\" style=\"font-size: 15px;padding: 5px;\"><b>" . lang(getrs("select * from " . TABLE . "content where C_id=" . $row2["R_cid"], "C_title")) . "</b>:" . $row2["R_content"] . "</div>";
                }
            }
            $Q_times = $row["Q_times"] + 1;
            if ($row["Q_first"] == "" || is_null($row["Q_first"])) {
                mysqli_query($conn, "update " . TABLE . "query set Q_first='" . date('Y-m-d H:i:s') . "' where Q_code='" . $Q_code . "'");
                $Q_first = date('Y-m-d H:i:s');
            }
            $Q_first = $row["Q_first"];
            echo "<form class=\"form-horizontal\" role=\"form\"><h2>查询系统</h2><hr><div class=\"form-group\"><label for=\"firstname\" class=\"col-sm-2 control-label\">查询码</label><div class=\"col-sm-10\" style=\"padding-top:8px\">" . $Q_code . "</div></div>";
            echo "<div class=\"form-group\"><label for=\"firstname\" class=\"col-sm-2 control-label\">查询次数</label><div class=\"col-sm-10\" style=\"padding-top:7px\">第" . $Q_times . "次查询</div></div>";
            echo "<div class=\"form-group\"><label for=\"firstname\" class=\"col-sm-2 control-label\">首次查询</label><div class=\"col-sm-10\" style=\"padding-top:7px\">" . $Q_first . "</div></div>";
            if ($res != "") {
                echo "<div class=\"form-group\"><label for=\"firstname\" class=\"col-sm-2 control-label\">提交信息</label><div class=\"col-sm-10\">" . $res . "</div></div>";
            }
            echo "<div class=\"form-group\"><label for=\"firstname\" class=\"col-sm-2 control-label\">查询结果</label><div class=\"col-sm-10\" style=\"padding-top:7px\">" . $qcode . "</div></div></form>";
            die();
        } else {
            echo "<div style='height:500px'></div>";
            box(lang("未查询到您输入的编号！/l/error") , "back", "error");
        }
    }
}
if ($typex == "query") {
    $S_id = intval($_GET["S_id"]);
    $S_title = getrs("select * from " . TABLE . "qsort where S_id=" . $S_id, "S_title");
    echo "<p style=\"margin-bottom:10px;font-size:30px;\">" . $S_title . "</p>";
    $sql = "select * from " . TABLE . "form where F_cq=" . $S_id . " order by F_id desc";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
    	if(wapstr()){
    		echo "<span>关联表单：";
    	}else{
    		echo "<span style=\"float:right;margin-top:-35px;\">关联表单：";
    	}
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<a href=\"?S_id=" . $row["F_id"] . "\">" . lang($row["F_title"]) . "</a>";
        }
        echo "</span>";
    }
    $S_content = getrs("select * from " . TABLE . "qsort where S_id=" . $S_id, "S_content");
    $SL_form = "<form action='?action=query' method='post'><input type='hidden' name='Q_sort' value='" . $S_id . "'>";
    $SL_form = $SL_form . "<div class='form-group'><h3>请输入您要查询的编号</h3><span>" . $S_content . "</span><input type='text' value='' name='Q_code' class='form-control'></div>";
    if ($F_yzm == 1) {
        $SL_form = $SL_form . "<div class='form-group'><iframe src='conn/code_1.php?name=code' scrolling='no' frameborder='0' width='100%' height='40'></iframe></div>";
    }
    $SL_form = $SL_form . "<div class='form-group'><button type='submit' class='btn btn-primary'>" . lang("提交/l/SEND") . "</button></div></form>";
    echo $SL_form;
} else {
    echo "<p style=\"margin-left:-15px;margin-bottom:10px;font-size:30px;\">" . $F_title . "</p>";
    if ($F_type == 0) {
        if ($F_cq > 0) {
            $q_title = getrs("select * from " . TABLE . "qsort where S_id=" . $F_cq, "S_title");
            if(wapstr()){
                echo "<span style=\"margin-left:-15px;margin-bottom:10px;\">关联查询：<a href=\"?type=query&S_id=" . $F_cq . "\">" . $q_title . "</a></span>";
            }else{
                echo "<span style=\"float:right;margin-top:-35px;\">关联查询：<a href=\"?type=query&S_id=" . $F_cq . "\">" . $q_title . "</a></span>";
            }
        }
        $SL_form = "<form id='form' class='form-horizontal' action='?action=input&S_id=" . $S_id . "' method='post' target='_parent'>";
        $sql = "select * from " . TABLE . "content," . TABLE . "form where C_fid=F_id and C_fid=" . $S_id . " order by C_order asc";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $F_title = lang($row["F_title"]);
                $F_id = $row["F_id"];
                $F_yz = $row["F_yz"];
                $C_required = $row["C_required"];

                if($C_required==1){
                	$r="required";
                	$x="<span style=\"color:#ff0000\">*</span>";
                }else{
                	$r="";
                	$x="";
                }

                if ($_SESSION["M_id"] == "" && $F_yz == 1) {
                    echo "<script>alert('" . lang("请先登录会员！/l/Please login to the member!") . "');top.location.href='" . $C_dir . "member/member_login.php?from=".URLEncode("//".$_SERVER["HTTP_HOST"].$C_dir."index.php?type=form&S_id=".$S_id)."';</script>";
                    die();
                }
                if ($row["C_type"] == "text") {
                    $C_content = "<input type='text' value='' name='" . $row["C_id"] . "' class='form-control' ".$r.">";
                }
                if ($row["C_type"] == "area") {
                    $C_content = "<textarea name='" . $row["C_id"] . "' cols='80' rows='3' class='form-control' ".$r."></textarea>";
                }
                if ($row["C_type"] == "radio") {
                    $content1 = explode("|", lang($row["C_content"]));
                    for ($i = 0; $i < count($content1); $i++) {
                    	if($i==0){
                    		$c="checked=\"checked\"";
                    	}else{
                    		$c="";
                    	}
                        $C_content = $C_content . " <li><input type='radio' ".$c." data-labelauty='" . $content1[$i] . "' class='to-labelauty-icon' id='" . $row["C_id"] . "_" . $i . "' name='" . $row["C_id"] . "' value='" . $content1[$i] . "'/></li>";
                    }
                }
                if ($row["C_type"] == "checkbox") {
                    $content1 = explode("|", lang($row["C_content"]));
                    for ($i = 0; $i < count($content1); $i++) {
                    	if($i==0){
                    		$c="checked=\"checked\"";
                    	}else{
                    		$c="";
                    	}
                        $C_content = $C_content . " <li><input type='checkbox' ".$c." data-labelauty='" . $content1[$i] . "' class='to-labelauty-icon' name='" . $row["C_id"] . "' value='" . $content1[$i] . "'/></li> ";
                    }
                }
                if ($row["C_type"] == "option") {
                    $content1 = explode("|", lang($row["C_content"]));
                    $C_content = "<select name='" . $row["C_id"] . "' class='form-control'>";
                    for ($i = 0; $i < count($content1); $i++) {
                        $C_content = $C_content . "<option  value='" . $content1[$i] . "'>" . $content1[$i] . "</option>";
                    }
                    $C_content = $C_content . "</select>";
                }
                if ($row["C_type"] == "date") {
                    $C_content = "<input type='date' value='' name='" . $row["C_id"] . "' class='form-control' ".$r.">";
                }
                if ($row["C_type"] == "pic") {
                    $C_content = "<div class='input-group'><input type='text' value='' name='" . $row["C_id"] . "' id='" . $row["C_id"] . "' class='form-control' ".$r."> <span class='input-group-btn'><button  onclick=\"showUpload('" . $row["C_id"] . "','../media');\" class='btn btn-info' type='button'>" . lang("上传文件/l/upload") . "</button></span></div>";
                }
                if ($row["C_bz"] !== "") {
                    if (lang($row["C_bz"]) == "未填" || lang($row["C_bz"]) == "null(en)") {
                        $C_bz = "";
                    } else {
                        $C_bz = "<p><font size='-1' color='#666666'>" . lang($row["C_bz"]) . "</font></p>";
                    }
                }
                $SL_form = $SL_form . "<div class='form-group'><label class='tm'>" . $row["C_order"] . "." . lang($row["C_title"]) ." ".$x. "</label><span>" . $C_bz . "</span>" . $C_content . "</div>";
                $C_content = "";
                $C_bz = "";
            }
        }
        if ($F_yzm == 1) {
            $SL_form = $SL_form . "<div class='form-group'><iframe src='conn/code_1.php?name=code' scrolling='no' frameborder='0' width='100%' height='40'></iframe></div>";
        }
        $SL_form = $SL_form . "<div class='form-group'><button type='button' class='btn btn-info' onclick='preview()'>" . lang("预览/l/VIEW") . "</button> <button type='submit' class='btn btn-primary'>" . lang("提交/l/SEND") . "</button></div></form>";
        echo $SL_form;
    }
    if ($F_type == 1) {
        $F_qsort = getrs("select * from " . TABLE . "form where F_id=" . $S_id, "F_qsort");
        $S_content = getrs("select * from " . TABLE . "qsort where S_id=" . $F_qsort, "S_content");
        $sql = "select * from " . TABLE . "form where F_cq=" . $F_qsort . " order by F_id desc";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            echo "<span style=\"float:right;margin-top:-35px;\">关联表单：";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<a href=\"?S_id=" . $row["F_id"] . "\">" . lang($row["F_title"]) . "</a>";
            }
            echo "</span>";
        }
        $SL_form = "<form action='?action=query&S_id=" . $S_id . "' method='post'><input type='hidden' name='Q_sort' value='" . $F_qsort . "'>";
        $SL_form = $SL_form . "<div class='form-group'><h3>请输入您要查询的编号</h3><span>" . $S_content . "</span><input type='text' value='' name='Q_code' class='form-control'></div>";
        if ($F_yzm == 1) {
            $SL_form = $SL_form . "<div class='form-group'><iframe src='conn/code_1.php?name=code' scrolling='no' frameborder='0' width='100%' height='40'></iframe></div>";
        }
        $SL_form = $SL_form . "<div class='form-group'><button type='submit' class='btn btn-primary'>" . lang("提交/l/SEND") . "</button></div></form>";
        echo $SL_form;
    }
}

?>
</div>
</body>
</html>