<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ALL);
ini_set("display_errors","On");



header('Content-Type: text/html;charset=utf-8');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin');


$ret['errcode'] = 1;
$ret['errmsg'] = 'unkown';
$ret['code'] = '';

$base64_image_string = isset($_POST['img']) ? $_POST['img'] : false;

if(!$base64_image_string) {
    $ret['errcode'] = 2;
    $ret['errmsg'] = '错误的请求：参数错误';
    output($ret);
}


if( substr( $base64_image_string, 0, 5 ) === "data:" ) {
    $filename = save_base64_image($base64_image_string, createUnique(),  getcwd()."/files/");
    
    $shell_output = shell_exec("python ./main.py ./files/{$filename}");
    
    
    
    $ret['errcode'] = 0;
    $ret['errmsg'] = 'ok';
    $ret['code'] = $shell_output;
    output($ret);
    
} else {
    $ret['errcode'] = 3;
    $ret['errmsg'] = '错误的请求：参数错误';
    output($ret);
}


function save_base64_image($base64_image_string, $output_file_without_extension, $path_with_end_slash='') {

    $splited = explode(',', substr( $base64_image_string , 5 ) , 2);
    //var_dump($splited);exit;
    $mime=$splited[0];
    $data=$splited[1];
    
    $mime_split_without_base64=explode(';', $mime,2);
    $mime_split=explode('/', $mime_split_without_base64[0],2);
    
    if(count($mime_split)==2) {
        $extension=$mime_split[1];
        
        if($extension=='jpeg') {
            $extension='jpg';
        }

        $output_file_with_extension=$output_file_without_extension.'.'.$extension;
        //exit($output_file_with_extension);
    }
    file_put_contents( $path_with_end_slash . $output_file_with_extension,  base64_decode($data) );
    return $output_file_with_extension;
}

function output($data, $format='json') {
    
    if($format == 'json') {
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($data);
        exit;
        
    } else if($format == 'jsonp') {
        header("Content-Type: application/javascript; charset=UTF-8");
        $callback = $_GET['callback'];
        echo $callback.'('.json_encode($data).')';
        exit;
    }
}

function createUnique() {
    
    $data = time().$_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . microtime(true) . random_int(0, 999999999);
    
    return sha1($data);
}