<?php
# 使用教程
#0. 部署 https 证书在服务器(若不会安装可以咨询QQ:736851786)
#1. 将大淘客上下载的index.php 重命名为 dataoke.php
#2. 将本文件放在dataoke.php同级目录
#3. 将www.zheby.com修改为你的网站地址 

$domain = 'www.zheby.com';
# 是否开启 http 永久重定向到 https
$force_https = true;
# 是否开启 非本域名跳转到本域名 , seo 相关, 如 用户访问 zheby.com, 会被跳转到 www.zheby.com
$force_domain = true;

function shutdownHandler() {
    $data = str_replace('http://', '//', ob_get_clean());
    $data = str_replace('dataoke.php', 'index.php', $data);

    if(stripos($data , '</head>')!=false){
        # 阻止动态加载图片, 因为动态加载的图片是 http 的 ,http://tj.quan007.com/_utm.gif , 这个应该是仅仅用来做统计的
        $data = str_replace('</head>' , "<script>function Image(){}</script></head>" , $data);
    }
    foreach(headers_list() as $item){
        if(strpos($item,'Location:') === 0){
            $item = str_replace('dataoke.php' , '/index.php' , $item);
            $item = str_replace('http://' , 'https://' , $item);
            header($item,true);
        }
    }
    echo $data;
}

function get_request_url(){
    global $domain;
    return "https://{$domain}/".trim($_SERVER['REQUEST_URI'],'/');
}

if (
    ($force_https && $_SERVER['HTTPS']!='on') 
    || 
    ($force_domain && $_SERVER['HTTP_HOST']!=$domain)
) {
    header("HTTP/1.1 301 Moved Permanently");
    header('Location: '.get_request_url());
    exit;
}

ob_start();
ob_implicit_flush(false);
register_shutdown_function('shutdownHandler');
$_SERVER["PHP_SELF"] = 'dataoke.php';
require($_SERVER["PHP_SELF"]);