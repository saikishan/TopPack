<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/repo', RepoController::class. ':index');
$app->post('/repo',RepoController::class. ':process_and_save');
$app->get('/repo', PackageController::class. ':index');