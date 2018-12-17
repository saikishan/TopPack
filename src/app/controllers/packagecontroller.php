<?php

class PackageController
{
    private $db;
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('db');
        $this->logger = $container->get('logger');
        Repo::set_static_db_setup($this->db, $this->logger);
        Package::set_static_db_setup($this->db, $this->logger);

    }

    private function log_line($str)
    {
        self::$logger->addInfo(" from  logger in package controller $str");
    }

    public function index(Request $request, Response $response, array $args)
    {
        $q = $request->getQueryParams("q", $default = null);

//this return a list of all the top matching repos and check and mark imported
    }

    private function list_to_array($arry)
    {
        $reslt_arr = [];
        foreach ($arry as $s) {
            array_push($reslt_arr, $s->to_Dict());
        }
        return $reslt_arr;
    }

    public function process_and_save(Request $request, Response $response, array $args)
    {
        $parsed_Body = $request->getParsedBody();
        $new_repo = new Repo($parsed_Body["name"], $parsed_Body["url"], $parsed_Body["id"], $parsed_Body["stars"]);


        if (!$new_repo->save_status) {
            $new_repo->save();
            $response->withStatus(200);
        }
        $response->withStatus(202);

        return $response;
    }
}