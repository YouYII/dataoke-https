<?php

# 替换为你的域名
$domain = 'www.zheby.com';
# 是否开启 http 永久重定向到 https
$force_https = true;
# 是否开启 非本域名跳转到本域名 , seo 相关, 如 用户访问 zheby.com, 会被跳转到 www.zheby.com
$force_domain = true;

function shutdownHandler() {
    $data = str_replace('http://', '//', ob_get_clean());
    $data = str_replace('dataoke.php', 'index.php', ob_get_clean());
    # 引入的这个js 里加载了http资源
    $data = preg_replace('|node.parentNode.insertBefore[^\n]*|', '', $data);
    echo $data;
}

function getRequestUrl(){
    global $domain;
    return "https://{$domain}/".trim($_SERVER['REQUEST_URI'],'/');
}

if (
    ($force_https && $_SERVER['HTTPS']!='on') 
    || 
    ($force_domain && $_SERVER['HTTP_HOST']!=$domain)
) {
    header("HTTP/1.1 301 Moved Permanently");
    header('Location: '.getRequestUrl());
    exit;
}

ob_start();
ob_implicit_flush(false);
register_shutdown_function('shutdownHandler');
$_SERVER["PHP_SELF"] = 'dataoke.php';
require($_SERVER["PHP_SELF"]);
