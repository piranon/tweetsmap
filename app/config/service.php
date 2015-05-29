<?php
use Phalcon\Mvc\View\Simple as SimpleView;
use Phalcon\Http\Response\Cookies as Cookies;
use Phalcon\Mvc\Router;

$di->set('config', function() {
    include '../app/config/config.php';
    $configObject = new \Phalcon\Config($config);
    return $configObject;
}, true);

$di->set('view', function() {
    $view = new SimpleView();
    $view->setViewsDir('../app/views/');
    return $view;
}, true);

$di->set('cookies', function() {
    $cookies = new Cookies();
    $cookies->useEncryption(false);
    return $cookies;
}, true);

$di->set('router', function() use ($di) {
    include '../app/config/routes.php';
    return $router;
}, true);

$di->set('mongo', function() use ($di) {
    $config = $di->get('config')->mongo;
    $client = new MongoClient($config->server, (array) $config->options);
    $db = $client->{$config->options->db};
    return $db;
}, true);