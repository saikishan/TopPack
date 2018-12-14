
<?php
require './config.php';
$app = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();
$container['logger'] = function($c){
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('./logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function($c){
    $db = $c['settings']['db'];
    $pdo = new PDO('pgsql:host='.$db['host'].';dbname='. $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->serAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
