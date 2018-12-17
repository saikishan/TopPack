<?php
//require_once '../../../vendor/rmccue/requests/library/Requests.php';
//require_once  '../../public/config.php';
//require '../../../vendor/autoload.php';
//Requests::register_autoloader();
//test setup area
//$db = $config['db'];
//$pdo = new PDO('pgsql:host='.$db['host'].';dbname='. $db['dbname'], $db['user'], $db['password']);
//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//$pdo->serAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//remove after testing


class Package{
    public $package_id;
    public $name;
    public $save_status;
    public $usage_count;
    private static  $db,$logger;
    private static $insert_query;

    public function __construct($name,$id=null,$count=null, $save_status=null){
        $this->package_id = $id;
        $this->name = $name;
        $this->usage_count = $count;
        if ($save_status == null){
            $this->save_status = $this->check_status($name);
        }
        else{
            $this->save_status = $save_status;
        }
    }
    private function  log_line($str){
        self::$logger->addInfo(" from  logger in PackageModel $str");
    }

    private function find_package_id(){
        $stmt = self::$db->query("select package_id from package where name = '$this->name'");
        return $stmt->fetch()["package_id"];

    }
    private function check_status($name){
        if(self::$db->query("select count(package_id) from package where name = '$name'")->fetchColumn() > 0){
            return true;
        }
        return false;
    }

    public static  function set_static_db_setup($db,$logger){
        self::$db = $db;
        self::$logger =$logger;
        self::$insert_query = $db->prepare("insert into package(name)  VALUES (?)");
    }

    public function save(){
        $stmt = self::$insert_query->execute([$this->name]);
        if($stmt){
            $this->save_status = true;
            $this->package_id = $this->find_package_id();
            return true;
        }
        return false;
    }

    public static function fetch_top_packages($limit){
        $stmt = self::$db->query("select package.package_id,package.name,count(repo_id) from repo_packages right join package on package.package_id = repo_packages.package_id group by package.package_id order by count(repo_id) Desc limit $limit");
        $result_array = [];
        while (($row = $stmt->fetch())){
            self::$logger->addInfo($row);
            array_push($result_array, (new Package($row["name"], $row["package_id"], $row["count"]))->to_Dict());
        }
        return $result_array;
    }

    public static function search_packages_from_db($q, $limit){
        $stmt = self::$db->query("select package_id,name from package where name like '$q%' order by name limit $limit");
        $result_array = [];
        while (($row = $stmt->fetch()) && sizeof($result_array) < 3){
            array_push($result_array, (new Package($row["name"], $row["package_id"],null,true))->to_Dict());
        }
        return $result_array;
    }
    public function to_Dict(){
        $obj_dict  = array("package_id"=> $this->package_id, "name" => $this->name);
        if ($this->usage_count){
            $obj_dict["count"] = $this->usage_count;
        }
        return $obj_dict;
    }
    public static function search_packages_from_repo($name){
        $headers = array('Accept' => 'application/json');
        $response = Requests::get("https://raw.githubusercontent.com/$name/master/package.json");//
        if ($response->status_code != 200){
            return null;
        }

        $JsonObj_Arr = json_decode($response->body, true);
        $result_array = [];
        foreach($JsonObj_Arr["dependencies"] as $x => $v){
            $new_package = new Package($x);
            array_push($result_array, $new_package);
        }
        return $result_array;
    }
}
//select * from repo
//left join repo_packages on repo.repo_id = repo_packages.repo_id
//where repo_packages.package_id= 9
//order by repo.stars desc limit 3
//Package::set_static_db_setup($pdo);
//$s = Package::search_packages_from_repo("rg3/youtube-dl");
//$s2 =  Package::search_packages_from_repo("jhen0409/react-native-debugger");

//print $s;