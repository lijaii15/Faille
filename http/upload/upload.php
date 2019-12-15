<?php 
require '../conn/conn2.php';
require '../conn/function.php';

if($_SESSION["form"]=="" && $_SESSION["user"]==""){
  die("请勿跨站上传");
}

$S_filetype = $S_data[0]["S_filetype"] . "|xls|txt|sql|csv";
$id = $_GET["id"];
$path = $_GET["path"];
$a=$_GET["a"];
$path = str_replace("@@", "..", $path);
$processid = $_GET["processid"];
$n = "file" . str_Replace("AN", "", $processid);
$filename = date("YmdHis",time()).gen_key(2);
$kname = strtolower(substr($_FILES[$n]["name"],strrpos($_FILES[$n]["name"],'.')+1));

if($_FILES[$n]["size"]>2*1024*1024 && ($kname=="jpg" || $kname=="png" ||$kname=="jpeg" ||$kname=="gif" || $kname=="bmp")){
  die("图片文件请勿超过2M");
}

if(checklogin($_SESSION["user"],$_SESSION["pass"])){
  $file_type="|html|htm";
}else{
  $file_type="";
}

if($a=="a" && checklogin($_SESSION["user"],$_SESSION["pass"])){
  $k=iconv("UTF-8","gb2312",$_FILES[$n]["name"]);
}else{
  $k = $filename . "." . $kname;
}

if (preg_match('/asp|php|apsx|asax|ascx|cdx|cer|cgi|jsp'.$file_type.'/i', $k)) {
    die("error|上传文件格式不允许！");
} else {
    if (strpos(strtolower($S_filetype) , $kname) !== false) {
        if (strpos($path, "../") !== false) {
            $p = $path;
        } else {
            $p = $_SERVER["DOCUMENT_ROOT"] . $path;
        }
        
        move_uploaded_file($_FILES[$n]["tmp_name"], $p . "/" . $k);

        //加水印
        if ($id != "C_logo" && $id != "W_logo" && $id != "C_ico" && $id != "C_m_logo" && ($kname == "jpg" || $kname == "png" || $kname == "bmp")) {
            if ($C_mark == 1) {
                switch ($C_m_position) {
                    case 1:
                        $markPos = 1;
                        break;

                    case 2:
                        $markPos = 3;
                        break;

                    case 3:
                        $markPos = 7;
                        break;

                    case 4:
                        $markPos = 9;
                        break;

                    case 5:
                        $markPos = 5;
                        break;
                }
                @setWater($path . "/" . $k, "", $C_m_text, hex2rgb($C_m_color) , $markPos, "./simsun.ttc", "text");
            }
            if ($C_mark == 2) {
                switch ($C_m_position) {
                    case 1:
                        $markPos = 1;
                        break;

                    case 2:
                        $markPos = 3;
                        break;

                    case 3:
                        $markPos = 7;
                        break;

                    case 4:
                        $markPos = 9;
                        break;

                    case 5:
                        $markPos = 5;
                        break;
                }
                setWater($path . "/" . $k, "../" . $C_m_logo, "", "", $markPos, "", "img");
            }
        }

        echo $k;

        if($a!="a" && $C_osson==1){
          tooss("media/".$k);
        }
    }
}



function setWater($imgSrc,$markImg,$markText,$TextColor,$markPos,$fontType,$markType)
{
 
  $srcInfo = @getimagesize($imgSrc);
  $srcImg_w  = $srcInfo[0];
  $srcImg_h  = $srcInfo[1];
     

  switch ($srcInfo[2]) 
  { 
    case 1: 
      $srcim =imagecreatefromgif($imgSrc); 
      break; 
    case 2: 
      $srcim =imagecreatefromjpeg($imgSrc); 
      break; 
    case 3: 
      $srcim =imagecreatefrompng($imgSrc); 
      break; 
    default: 
      die("不支持的图片文件类型"); 
      exit; 
  }
     

  if(!strcmp($markType,"img"))
  {
    if(!file_exists($markImg) || empty($markImg))
    {
      return;
    }
       
    $markImgInfo = @getimagesize($markImg);
    $markImg_w  = $markImgInfo[0];
    $markImg_h  = $markImgInfo[1];
       
    if($srcImg_w < $markImg_w || $srcImg_h < $markImg_h)
    {
      return;
    }
       
    switch ($markImgInfo[2]) 
    { 
      case 1: 
        $markim =imagecreatefromgif($markImg); 
        break; 
      case 2: 
        $markim =imagecreatefromjpeg($markImg); 
        break; 
      case 3: 
        $markim =imagecreatefrompng($markImg); 
        break; 
      default: 
        die("不支持的水印图片文件类型"); 
        exit; 
    }
       
    $logow = $markImg_w;
    $logoh = $markImg_h;
  }
     
  if(!strcmp($markType,"text"))
  {


    $fontSize = 16;

    $box = @imagettfbbox($fontSize, 0, $fontType,$markText);
    $logow = max($box[2], $box[4]) - min($box[0], $box[6]);
    $logoh = max($box[1], $box[3]) - min($box[5], $box[7]);
  }
     
  if($markPos == 0)
  {
    $markPos = rand(1, 9);
  }

  switch($markPos)
  {
    case 1:
      $x = +5;
      $y = +20;
      break;
    case 2:
      $x = ($srcImg_w - $logow) / 2;
      $y = +5;
      break;
    case 3:
      $x = $srcImg_w - $logow - 10;
      $y = +20;
      break;
    case 4:
      $x = +5;
      $y = ($srcImg_h - $logoh) / 2;
      break;
    case 5:
      $x = ($srcImg_w - $logow) / 2;
      $y = ($srcImg_h - $logoh) / 2;
      break;
    case 6:
      $x = $srcImg_w - $logow - 5;
      $y = ($srcImg_h - $logoh) / 2;
      break;
    case 7:
      $x = +5;
      $y = $srcImg_h - $logoh ;
      break;
    case 8:
      $x = ($srcImg_w - $logow) / 2;
      $y = $srcImg_h - $logoh - 5;
      break;
    case 9:
      $x = $srcImg_w - $logow - 10;
      $y = $srcImg_h - $logoh ;
      break;
    default: 
      die("此位置不支持"); 
      exit;
  }
     
  $dst_img = @imagecreatetruecolor($srcImg_w, $srcImg_h);
     
  imagecopy ( $dst_img, $srcim, 0, 0, 0, 0, $srcImg_w, $srcImg_h);
     
  if(!strcmp($markType,"img"))
  {
    imagecopy($dst_img, $markim, $x, $y, 0, 0, $logow, $logoh);
    imagedestroy($markim);
  }
     
  if(!strcmp($markType,"text"))
  {
    $rgb = explode(',', $TextColor);
       
    $color = imagecolorallocate($dst_img, $rgb[0], $rgb[1], $rgb[2]);
    imagettftext($dst_img, $fontSize, 0, $x, $y, $color, $fontType,$markText);
  }
     
  switch ($srcInfo[2]) 
  { 
    case 1:
      imagegif($dst_img, $imgSrc); 
      break; 
    case 2: 
      imagejpeg($dst_img, $imgSrc); 
      break; 
    case 3: 
      imagepng($dst_img, $imgSrc); 
      break;
    default: 
      die("不支持的水印图片文件类型"); 
      exit; 
  }
     
  imagedestroy($dst_img);
  imagedestroy($srcim);
}

//16进制转RGB颜色
function hex2rgb($hexColor) {
        $color = str_replace('#', '', $hexColor);
        if (strlen($color) > 3) {
            $rgb=hexdec(substr($color, 0, 2)).",".hexdec(substr($color, 2, 2)).",".hexdec(substr($color, 4, 2));
            
        } else {
            $color = $hexColor;
            $r = substr($color, 0, 1) . substr($color, 0, 1);
            $g = substr($color, 1, 1) . substr($color, 1, 1);
            $b = substr($color, 2, 1) . substr($color, 2, 1);
            $rgb=hexdec($r).",".hexdec($g).",".hexdec($b);
        }
        return $rgb;
    }
?>