<?php
session_start();
error_reporting(-1);
date_default_timezone_set("Asia/Dhaka");
define('_VALID_INCLUDE', true);

$cfg = array();
$cfg['host'] = $_SERVER['HTTP_HOST'];
$cfg['log_path'] = dirname(__FILE__) . "/logs/";
$cfg['site_url'] = "http://" . $_SERVER['HTTP_HOST'] . "/";
$cfg['db_host'] = "localhost";
$cfg['db_name'] = "onlingl6_fantv";
$cfg['db_user'] = "onlingl6_fantv";
$cfg['db_pass'] = "onlingl6_fantv";
$cfg['db_prefix'] = "onlingl6_fantv";
$cfg['log'] = true;
$cfg['debug'] = true;
$cfg['fantv'] = [
    'url' => 'https://api.fan.tv/1.0/',
    'consumer_key'=>'personal_C_bbdcb814dc6f43aedd77bb6053dc99aa228ebee6814dda6ac8b8ff62e88e97fa',
    'consumer_secret'=>'1fea219e163505858d52a1552e90609e33c07f04544591806f44b1ead16e007e',
    'bearer_token'=>'personal_C_9b88a7e002e99b869c653fc08d145cfe455c50ac7b9176c62b91edbcb27ee0cc',
];


define('SITE', $cfg['site_url']);
define('LOGS', $cfg['log_path']);
