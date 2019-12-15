<?php
require '../conn/conn2.php';
require '../conn/function.php';

$dirx=dirname($_SERVER["SCRIPT_FILENAME"])."/";
if(checklogin($_SESSION["user"],$_SESSION["pass"])){
    $DownName=$_GET["DownName"];
    $DownName=str_replace("@@","..",$DownName);

    if(strpos(strtolower($DownName),".php")!==false){
        die("禁止下载PHP格式文件！");
    }
    if(strtolower(getExt($DownName))=="txt" || strtolower(getExt($DownName))=="zip" || strtolower(getExt($DownName))=="rar"){
        downtemplateAction($dirx.$DownName);
    }
    
}

function downtemplateAction($f){
    header("Content-type:text/html;charset=utf-8");
    $file_name = $f;
    $file_name = iconv("utf-8","gb2312",$file_name);
    $file_path=$file_name;
    if(!file_exists($file_path))
    {
        echo "下载文件不存在！";
        exit;
    }
 
    $fp=fopen($file_path,"r");
    $file_size=filesize($file_path);
    Header("Content-type: application/octet-stream");
    Header("Accept-Ranges: bytes");
    Header("Accept-Length:".$file_size);
    Header("Content-Disposition: attachment; filename=".$file_name);
    $buffer=1024;
    $file_count=0;
    while(!feof($fp) && $file_count<$file_size)
    {
        $file_con=fread($fp,$buffer);
        $file_count+=$buffer;
        echo $file_con;
    }
    fclose($fp);
}
?>