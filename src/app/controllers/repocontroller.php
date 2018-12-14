<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
require '../models/Repo';

class RepoController{
    protected $db;
    public function __construct(ContainerInterface $container)
    {
       $this->db = $container->get('db');
    
    }
    public function index(Request $request, Response $response, array $args){

        //this return a list of all the top matching repos and check and mark imported
    }
    public function process_and_save(Request $request, Response $response, array $args){
        //this to  process and add the new repo
    }
}