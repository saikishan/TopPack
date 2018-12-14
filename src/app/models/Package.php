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


class Package{
    public $package_id;
    public $name;
    public $save_status;
    private static  $db;
    private static $insert_query;

    public function __construct($name, $id){
        $this->name = $name;
        $this->package_id = $id;
        $this->save_status = $this->check_status($name);
    }

    private function check_status($name){
        if(self::$db->query("select count(*) from package where name = $name")->fetchColumn() > 0){
            return true;
        }
        return false;
    }

    public static  function set_static_db_setup($db){
        self::$db = $db;
        self::$insert_query = $db->prepare("insert into package(name)  VALUES (?)");
    }

    public function save(){
        $stmt = self::$insert_query->execute([$this->repo_id,$this->name,$this->url,$this->stars]);
        if($stmt){
            $this->save_status = true;
        }
    }

    public static function search_repos_from_api($q){
        $headers = array('Accept' => 'application/json');
        $response = Requests::get('https://api.github.com/search/repositories?q=$q', $headers);
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