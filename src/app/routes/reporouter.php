<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/repo', function( Request $request, Response $response, array $args){
    $data = ($request->getQueryParams())['q'];
    $response->getBody()->write("Hello, $data");
    return $response;
});