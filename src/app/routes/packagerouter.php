<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/packages/find', PackageController::class. ':index');
$app->get('/packages', PackageController::class. ':show');
$app->get('/toppackages', PackageController::class. ':top_packs');
