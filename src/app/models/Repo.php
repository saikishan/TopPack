<?php

//require_once  '../../public/config.php';
//require_once '../../../vendor/autoload.php';
//

//test setup area
//$db = $config['db'];
//$pdo = new PDO('pgsql:host='.$db['host'].';dbname='. $db['dbname'], $db['user'], $db['password']);
//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//$pdo->serAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//remove after testing


class Repo{
    public $name;
    public $url;
    public $repo_id;
    public $stars;
    public $save_status;
    public $packages;
    private static  $db,$logger;
    private static $insert_query, $insert_link_query;

    public function __construct($name, $url, $id, $stars){
        $this->name = $name;
        $this->url = $url;
        $this->repo_id = $id;
        $this->stars = $stars;
        $this->save_status = $this->check_status($id);
    }

    private function check_status($id){
        //add support for packages
        if(self::$db->query("select count(*) from repo where repo_id = $id")->fetchColumn() > 0){
            return true;
        }
        return false;
    }

    public static  function set_static_db_setup($db, $logger){
        self::$logger = $logger;
        self::$db = $db;
        self::$insert_query = $db->prepare("insert into repo  VALUES (?, ?, ?, ?)");
        self::$insert_link_query = $db->prepare("insert into repo_packages VALUES (?, ?)");
    }

    public function save(){
        if ($this->save_status)
            return false;
        $stmt = self::$insert_query->execute([$this->repo_id,$this->name,$this->url,$this->stars]);
        if($stmt){
            $this->save_status = true;
            $this->get_packages();
            if ($this->packages){
                foreach ($this->packages as $package){
                    $package->save();
                    $this->log_line('saved $package->name');
                    $this->repo_package_linker($package);
                }
            }
            return true;
        }
    }

    private function log_line($str){
        self::$logger->addInfo(" from  logger in RepoModel $str");
    }

    public function get_packages(){
        if($this->packages){
            return $this->packages;
        }
        $this->packages = Package::search_packages_from_repo($this->name);
        return $this->packages;
    }

    public static function search_repos_from_api($q){
        $headers = array('Accept' => 'application/json');
        $response = Requests::get("https://api.github.com/search/repositories?q=$q", $headers);
        print "hello";
        $JsonObj_Arr = json_decode($response->body, true);
        $result_array = [];
        foreach($JsonObj_Arr["items"] as $x){
            $new_repo = new Repo($x['full_name'], $x['url'], $x['id'], $x['stargazers_count']);
            array_push($result_array, $new_repo);
        }
        return $result_array;
    }

    private function  repo_package_linker($package){
        $stmt = self::$insert_link_query->execute([$this->repo_id, $package->package_id]);
        if($stmt){
            return true;
        }
        return false;
    }
    public function to_Dict(){
        $obj_dict  = array("id"=> $this->repo_id, "name"=> $this->name ,"url" => $this->url , "stars" => $this->stars, "status" => $this->save_status);
        return $obj_dict;
    }
}

//Repo::set_static_db_setup($pdo);
//$s = Repo::search_repos_from_api("youtube");


//print $s;