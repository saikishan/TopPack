<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../../vendor/autoload.php';
require './app.php';
require '../app/routes/reporouter.php';
require '../app/routes/packagerouter.php';

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    $ar = PDO::getAvailableDrivers();
    foreach ($ar as &$v){
        $this->logger->addInfo('this is working '.$v);
    }
    return $response;
});

$app->run();
