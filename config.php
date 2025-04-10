<?php 
date_default_timezone_set('Europe/Rome');

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);


define("CLASSES", [
    "1ATL", "1BMME", "1CMME", "1DMME", "1EIT", "1FIT", 
    "1GIT", "1HIT", "1IIT", "1LEE", "1MEE", "1NEE", 
    "2ATL", "2BMME", "2CMME", "2DIT", "2EIT", "2FIT", 
    "2GIT", "2HEE", "2IEE", "3AAT", "3AEC", "3AIA", 
    "3ALG", "3AMM", "3AEN", "3BMM", "3BIA", "3BLG", 
    "3CIA", "3DIA", "4AAT-ET", "4AEC", "4AEN", 
    "4AIA", "4ALG", "4AMM", "4BIA", "4BLG", 
    "4BMM", "4CIA", "5AAT", "5AEC-ET", "5AIA", 
    "5ALG", "5AMM", "5AEN", "5BMM" ,"5BIA", "5BLG", 
    "5CIA"]);
    

define("STATUS", ["PPY" => "PAYED", "DLV" => "DELIVERED"]);
define("STATUS_COLOR", ["PPY" => "warning", "DLV" => "success"]);

define("FORM_MIN_LENGTH", 6);

define("JWT_KEY", "<JWT_KEY> (32 bit)");
define("JWT_ALGO", "HS256");
define("JWT_EXP", 432000); // in seconds ||| 432000 => 5 days

define("NO_ORDER_DAYS", [
    "FRI" => 86400 * 2, //FRY -> SAT -> SUNDAY (2 days)
    "SAT" => 86400 //SAT -> SUNDAY (1 day)
]);

define("MAX_ADMIN_LVL", 2);

define("ADMIN_AUTH", [
    "users" => 2,
    "orders" => 1,
    "menu" => 1,
    "counter" => 2
]);

define("ADMIN_LVLS", [
    1 => "Corriere",
    2 => "Admin"
]);


//////////////////////////////////////////////////

session_start();


if(!isset($_SESSION["csrf"])){
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

//////////////////////////////////////////////////

//JWT AUTH

require_once "libs/jwt/autoload.php";


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use \Firebase\JWT\ExpiredException;


function createJWT($payload) {
    $payload = [
        'iat' => time(),
        'exp' => time() + JWT_EXP,
        'username' => $payload[0]
    ];

    return JWT::encode($payload, JWT_KEY, JWT_ALGO);
}

function decodeJWT($j) {
    try {
        return JWT::decode($j, new Key(JWT_KEY, JWT_ALGO));
    }
    catch (ExpiredException $e) {
        return false;
    }

    catch(Exception $e){
        return false;
    }
}


function getJWT($value){ return decodeJWT($_COOKIE["jwt"])->$value; }



function logout(){
    session_regenerate_id(true);
    session_destroy();
    $_SESSION = [];

    setcookie("jwt","", time()-JWT_EXP);

    header("Location: login");
}


//////////////////////////////////////////////////


function isset_checker($post, $data){
    foreach($data as $d){
        if(!isset($post[$d])){
            echo returnError("bro wtf are u doing ?!?!");
            exit();
        }
    }
}

function outside($path=""){
    if(!isset($_COOKIE["jwt"])){
        header(sprintf("Location: %slogin", $path));
    }
    else{
        if(!decodeJWT($_COOKIE["jwt"]) || getUserInfo("id") == null){
            logout();
        }
    }
}


function inside(){
    if(isset($_COOKIE["jwt"])){
        header("Location: index");
    }
}



//////////////////////////////////////////////////



//PAYMENTS
require_once "libs/paypal/autoload.php";

use Omnipay\Omnipay;

define("PAYMENT_URL", "http://localhost/McMeucci/public_html/orders");
define("CURRENCY", "EUR");

$gateway = Omnipay::create('PayPal_Rest');
$gateway->setClientId("<PAYPAL_API_CLIENT_ID>");
$gateway->setSecret("<PAYPAL_API_SECRET_ID>");
$gateway->setTestMode(true);

//////////////////////////////////////////////////



//DATABASE CONNECTION (xampp configuration)
$conn = new mysqli("localhost", "root", "", "<DATABASE_NAME>");

if(!$conn) die("Connection Error -> " . $conn->connect_error);


//////////////////////////////////////////////////


function login_cookie($payload){
    setcookie("jwt", createJWT($payload), time() + JWT_EXP);
}


//////////////////////////////////////////////////


function payment_err(){
    $msg = "";
    $hour = (int) date("H",time());

    $cd = strtoupper(date("D", time()));

    if(!isOrder()){
        $msg.= "- MAX ORDERS REACHED<br><br>";
    }

    if(getCounter("maxDiscount") >= 100){
        $msg.= "- ORDERS CLOSED BY ADMINS<br><br>";
    }

    if(array_key_exists($cd, NO_ORDER_DAYS)){
        $t = time()+NO_ORDER_DAYS[$cd];
        $msg.= sprintf("- ORDERS CLOSED ON %s -> COME BACK %d %s", strtoupper(date("l", time())), date("d", $t), strtoupper(date("F (l)", $t)));
    }

    else if($hour < getCounter("openTime") || $hour >= getCounter("closeTime")){
        $msg.= sprintf("- ORDERS CLOSED, COME BACK %d %s (%d - %d)<br><br>", $hour >= getCounter("closeTime") ? date("d",time()+86400) : date("d",time()),  strtoupper(date("F", time())), getCounter("openTime"), getCounter("closeTime"));         
    }

    return [$msg != "", $msg];
}

function getCounter($value){
    return $GLOBALS["conn"]->query("SELECT * FROM counter")->fetch_assoc()[$value];
}


function getOrderID(){
    $id = getCounter("orderNumber")+1;

    $orderID = "MCM#";

    for($i=0;$i<4-strlen($id);$i++){
        $orderID.="0";
    }

    $orderID.=$id;

    $GLOBALS["conn"]->query(sprintf("UPDATE `counter` SET `orderNumber` = %d WHERE `counter`.`id` = 0", $id));

    return $orderID;
}

function isOrder(){
    return getCounter("orderNumber") < getCounter("maxOrdini");
}

function getTable($tableName, $filter=""){
    return $GLOBALS["conn"]->query("SELECT * FROM $tableName $filter");
}

function getUserInfo($info, $table="users"){
    return json_decode(getTable($table, sprintf("WHERE username = '%s'", getJWT("username")))->fetch_assoc()[$info], true);
}

function getCart($table="users"){
    return json_decode(getTable($table, sprintf("WHERE username = '%s'", getJWT("username")))->fetch_assoc()["cart"], true);
}

function translateCart($k){
    return getTable("menu", sprintf("WHERE id=%d", $k))->fetch_assoc();
}

function getTotal(){
    $total = 0;
    $cart = getCart();

    foreach($cart as $c=>$v){
        $row = translateCart($c);
        $pr = getPrice($row["price"], $row["discount"]);
        
        $total+= (float) (sizeof($pr) > 1 && getCounter("maxDiscount") != 100 ? $pr[1] : $pr[0])*$v;
    }

    return $total;
}


function writeCart($cart){  
    $sql = "UPDATE users SET cart=? WHERE username = ?";
    $stmt = $GLOBALS["conn"]->prepare($sql);
    $stmt->bind_param("ss", $cart, getJWT("username"));
    $stmt->execute();
  
    $stmt->close();
}


function returnError($err){
    return json_encode(["success" => false, "error" => $err]);
}

function clean($v){
    return htmlspecialchars(str_replace(" ","",trim($v)));
}


function className($class){
    return substr($class,0,1) . " " .substr($class,1);
}

//*.xx -> just graphical
function nf($value) { return number_format($value,2); }

//*.xx (from USD to EUR style)
function price($value){ return str_replace(",", "", nf($value)); }

function getPrice($price,$ds){
    $mx = getCounter("maxDiscount");

    if($ds==0 && $mx==0){
        return [nf($price)]; 
    }

    else{
        return [nf($price), nf($price-($price * (($mx > 0 ? $mx : $ds)/100)))];
    }

}

function currentDate(){
    return date("l | d/M/Y | d/m/y | H:i:s");
}

function captcha($token=""){
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => "<RECAPTCHA_PRIVATE_KEY>",
        'response' => $token
    );

    $options = array(
        'http' => array (
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result);

    /*
    * google response score = 0.0 -> 1.0
    
    + >= 0.5 -> human (recommend by google)
    - < 0-5 -> bot

    */

    return json_encode(["success" => $response->success && $response->score >= 0.5]);
}



function tag(){
    echo "
<!-- 

    ~ 3arbi malamour production ~


   __________...----..____..-'``-..___
    ,'.                                  ```--.._
   :      CB                                      ``._
   |                           --                    ``.
   |                   -.-      -.     -   -.        `.  \
   :                     __           --            .     \
    `._____________     (  `.   -.-      --  -   .   `     \
       `-----------------\   \_.--------..__..--.._ `. `.   :
                          `--'                     `-._ .   |
                                                       `.`  |
                                                         \` |
                                                          \ |
                                                          / \`.
                                                         /  _\-'
                                                        /_,'
              _,-''''-..__
         |`,-'_. `  ` ``  `--''''.
         ;  ,'  | ``  ` `  ` ```  `.
       ,-'   ..-' ` ` `` `  `` `  ` |==.
     ,'    ^    `  `    `` `  ` `.  ;   \
    `}_,-^-   _ .  ` \ `  ` __ `     ;   #
       `'---'' `-`. ` \---''''`.`.  `;
                  \\` ;       ; `. `,
                   ||`;      / / | |
      JB           //_;`    ,_;' ,_;'                                                       



-->";
}
?>