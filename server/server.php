<?php

$file = $_REQUEST['filed'];
$data = base64_upload($file);
echo json_encode($data);

//base64位图片上传
function base64_upload($base64_img, $config = array())
{
    $default_up_dir = dirname(dirname(__FILE__));
    $default_up_dir = str_replace("\\", "/", $default_up_dir)."/upload/";
    if (!file_exists($default_up_dir)) {
        mkdir($default_up_dir, 0775);
    }
    //$default_file_url = "http://" . $_SERVER['HTTP_HOST'] . "/upload/";.
    $default_file_url="upload/";
    $default_file_type = array('pjpeg', 'jpeg', 'jpg', 'gif', 'bmp', 'png');

    $default = array(
        'dir' => $default_up_dir,
        'picurl'=> $default_file_url,
        'filetype' => $default_file_type
    );
    $default = $config + $default;
    $base64_img = trim($base64_img);
    $data=array();
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)) {

        $type = $result[2];
        $nfilename = date('YmdHis_') . mt_rand(1, 999) . '.' . $type;
        $picurl=$default['picurl'].$nfilename;
        $tmpfile = base64_decode(str_replace($result[1], '', $base64_img));
        if (file_put_contents($default['dir'] . $nfilename, $tmpfile)) {

            $data = array(
                'code' => 100,
                'summary' => "上传成功",
                'data' => array('url' =>$picurl)
            );

        }else{
            $data = array(
                'code' =>101 ,
                'summary' => "上传失败",
            );

        }

    }else
    {
        $data = array(
            'code' => 102,
            'summary' => "上传文件类型不支持",

        );
    }
    return $data;

}