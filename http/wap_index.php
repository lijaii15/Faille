<?php
/**
 *
 *****************************************************************************************************
 *    如果您通过浏览器访问网站时看到了这个提示，那么我们很遗憾地通知您，您的空间不支持 PHP 。
 *    也就是说，您的空间可能是静态空间，或没有安装PHP，或没有为 Web 服务器打开 PHP 支持。
 *    Sorry, PHP is not installed on your web hosting if you see this prompt.
 *    Please check out the PHP configuration.
 *
 *    如您使用虚拟主机：
 *
 *        > 联系空间商，更换空间为支持 PHP 的空间。
 *        > Contact your service provider, and let them provice a new service which supports PHP.
 *
 *
 *    如您自行搭建服务器，推荐您：
 *    Configuring manually? Recommend:
 *
 *        > 访问 PHP 官方网站获取安装帮助。
 *        > Visit PHP Official Website to get the documentation of installion and configuration.
 *        > 如果您需要ASP版本，请前往 https://www.s-cms.cn/download.html?code=asp 进行下载
 *
 ******************************************************************************************************
 */

$dirx=dirname($_SERVER["SCRIPT_FILENAME"])."/";

if (file_get_contents($dirx."data/first.txt") == "1") {
    Header("Location: install.php");
    die();
}

require 'conn/conn.php';
require 'conn/function.php';


if ($_GET["action"] == "update_dir") {
    mysqli_query($conn, "update ".TABLE."config set C_dir='" . splitx( strtolower($_SERVER["PHP_SELF"]), "wap_index.php",0) . "'");
    box("更新成功！", "wap_index.php", "success");
}
if (substr($_SERVER["PHP_SELF"], -13) == "wap_index.php" && $C_dir != splitx( $_SERVER["PHP_SELF"], "wap_index.php",0)) {
    echo ("系统检测到您移动了安装目录，是否更新数据库？（<a href='?action=update_dir'>是</a>/否）" . splitx( $_SERVER["PHP_SELF"], "wap_index.php",0));
}
$S_page = $_GET["page"];

if ($_GET["type"] == "") {
    $U_type = "index";
} else {
    $U_type = $_GET["type"];
}

if(isset($_GET["S_id"])){
    $S_id = $_GET["S_id"];
}else{
	$S_id = "0";
}

if ($C_close == 1) {
    Header("Location: close.html");
    die();
}
if ($C_todomain <> "empty" && $C_todomain <> "" && $C_todomain <> $C_domain) {
    Header("Location: //" . $C_todomain);
    die();
}

switch ($U_type) {
    case "index":
        $page_info = e(j(CreateIndex(a($U_type, 1,"wap"))));
        break;

    case "contact":
        $page_info = e(j(CreateContact(a($U_type, 1,"wap"))));
        break;

    case "guestbook":
        $page_info = e(j(CreateGuestbook(a($U_type, 1,"wap"))));
        break;

    case "bbs":
        Header("location:bbs");
        break;

    case "member":
        Header("location:member");
        break;

    case "text":
        if (getrs("select * from ".TABLE."text where T_del=0 and T_id=" . intval($S_id), "T_title") == "") {
            box("菜单指向的简介已被删除，请到“菜单管理”重新编辑", "back", "error");
        } else {
            $page_info = e(j(CreateText(a($U_type, $S_id,"wap"),$S_id)));
        }
        break;

    case "form":
        if (getrs("select * from ".TABLE."form where F_del=0 and F_id=" . intval($S_id), "F_title") == "") {
            box("菜单指向的简介已被删除，请到“菜单管理”重新编辑", "back", "error");
        } else {
            $page_info = e(j(CreateForm(a($U_type, $S_id,"wap"),$S_id)));
        }
        break;

    case "news":
        if (is_numeric($S_id)) {
            if (getrs("select * from ".TABLE."nsort where  S_del=0 and S_id=" . intval($S_id), "S_title") == "" && $S_id <> 0) {
                box("菜单指向的新闻分类已被删除，请到“菜单管理”重新编辑", "back", "error");
            } else {
                if(getrs("select * from ".TABLE."nsort where S_del=0 and S_id=" . intval($S_id), "S_show")==0 && $S_id!="0"){
                    box("管理员设置该新闻分类为【隐藏】", "back", "error");
                }else{
                    $page_info = e(j(CreateNewsList(a($U_type, $S_id,"wap"), $S_id, $S_page)));
                }
            }
        } else {
            $page_info = e(j(CreateNewsList(a($U_type, $S_id,"wap"),$S_id,$S_page)));
        }
        break;

    case "newsinfo":
        if (getrs("select * from ".TABLE."news where N_del=0 and N_id=" . intval($S_id), "N_title") == "") {
            box("该新闻不存在或已被删除", "back", "error");
        } else {
            $page_info = e(j(CreateNewsInfo(a($U_type, $S_id,"wap"),$S_id)));
        }
        break;

    case "product":
        if (is_numeric($S_id)) {
            if (getrs("select * from ".TABLE."psort where S_del=0 and S_id=" . intval($S_id), "S_title") == "" && $S_id > 0) {
                box("菜单指向的产品分类已被删除，请到“菜单管理”重新编辑", "back", "error");
            } else {
                $page_info = e(j(CreateProductList(a($U_type, $S_id,"wap"),$S_id,$S_page)));
            }
        } else {
            $page_info = e(j(CreateProductList(a($U_type, $S_id,"wap"),$S_id,$S_page)));
        }
        break;

    case "productinfo":
        if (getrs("select * from ".TABLE."product where P_del=0 and P_id=" . intval($S_id), "P_title") == "") {
            box("该产品不存在或已被删除", "back", "error");
        } else {
            $page_info = e(j(CreateProductInfo(a($U_type, $S_id,"wap"),$S_id)));
        }
        break;

    default:
        $page_info = e(j(CreateIndex(a($U_type, 1,"wap"))));
}

if ($_SESSION["f"] == 1) {
    echo cnfont($page_info, "f");
} else {
    echo cnfont($page_info, "j");
}

?>