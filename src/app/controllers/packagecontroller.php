<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;
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

    public function index(Request $request, Response $response, array $args)
    {
        $q = $request->getQueryParams("q",$default = null);
        if($q){
            $packages = Package::search_packages_from_db($q['q'],3);
            return $response->withJson($packages, 200);
        }
//this return a list of all the top matching repos and check and mark imported
    }

    public function top_packs(Request $request, Response $response, array $args){
        $packages = Package::fetch_top_packages(10);
        return $response->withJson($packages,200);
    }

    private function list_to_array($arry)
    {
        $reslt_arr = [];
        foreach ($arry as $s) {
            array_push($reslt_arr, $s->to_Dict());
        }
        return $reslt_arr;
    }

    public function show(Request $request, Response $response, array $args){
        $q = $request->getQueryParams("q",$default = null);
        if($q){
            $repos = Repo::get_repos_from_db_linked_to($q["id"]);
            return $response->withJson($repos, 200);
        }
    }

}