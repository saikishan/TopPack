<?php
require_once '../../../vendor/rmccue/requests/library/Requests.php';
require_once  '../../public/config.php';
require '../../../vendor/autoload.php';
Requests::register_autoloader();
//test setup area
$db = $config['db'];
$pdo = new PDO('pgsql:host='.$db['host'].';dbname='. $db['dbname'], $db['user'], $db['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//$pdo->serAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//remove after testing


class Repo{
    public $name;
    public $url;
    public $repo_id;
    public $stars;
    public $save_status;
    public $packages;
    private static  $db;
    private static $insert_query;

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

    public static  function set_static_db_setup($db){
        self::$db = $db;
        self::$insert_query = $db->prepare("insert into repo  VALUES (?, ?, ?, ?)");
    }

    public function save(){
        $stmt = self::$insert_query->execute([$this->repo_id,$this->name,$this->url,$this->stars]);
        if($stmt){
            $this->save_status = true;
        }
    }

    public static function search_repos_from_api($q){
        $headers = array('Accept' => 'application/json');
        $response = Requests::get("https://raw.githubusercontent.com/sindresorhus/package-json/master/package.json");//'https://api.github.com/search/repositories?q=$q', $headers);
        print "hello";
        $JsonObj_Arr = json_decode($response->body, true);
        $result_array = [];
        foreach($JsonObj_Arr["items"] as $x){
            $new_repo = new Repo($x['name'], $x['url'], $x['id'], $x['stargazers_count'] );
            array_push($result_array, $new_repo);
        }
        return $result_array;
    }
}

Repo::set_static_db_setup($pdo);
$s = Repo::search_repos_from_api("youtube");


print $s;