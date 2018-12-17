<?php



$app->get('/repo', RepoController::class. ':index');
$app->post('/repo',RepoController::class. ':process_and_save');
