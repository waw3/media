<?php
include 'vendor/autoload.php';
include 'classes/Route.php';
$route = new Route();
$core = new Core();
$cwd = $core->cwd();
$route->add($cwd,'home.php');
$route->add($cwd.'/movie','movies.php');
$route->add($cwd.'/show','shows.php');
$route->add($cwd.'/user','user.php');
$route->add($cwd.'/admin','admin.php');
$route->add($cwd.'/update','update.php');
$route->add($cwd.'/logout','logout.php');
$route->submit();