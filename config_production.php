<?php 
//START FILE

error_reporting(0);

//////////////////////////////////////////////////



//PAYMENTS
require_once "libs/paypal/autoload.php";
use Omnipay\Omnipay;

define("PAYMENT_URL", "https://mcmeucci.shop/orders");
define("CURRENCY", "EUR");

$gateway = Omnipay::create('PayPal_Rest');
$gateway->setClientId("<PAYPAL_API_CLIENT_ID>");
$gateway->setSecret("<PAYPAL_API_SECRET_ID>");
$gateway->setTestMode(false);



//////////////////////////////////////////////////



//DATABASE CONNECTION
$conn = new mysqli("<DATABASE_HOST> (localhost)", "<DATABASE_USERNAME>", "<DATABASE_PASSWORD>", "<DATABASE_NAME>");

if(!$conn) die("Connection Error -> " . $conn->connect_error);



//////////////////////////////////////////////////


function login_cookie($payload){
    setcookie("jwt", createJWT($payload), [
        'expires' => time() + JWT_EXP,
        'path' => '/',
        //host only
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
}

//////////////////////////////////////////////////

//Before SESSION_START() in `config.php`

ini_set('session.cookie_secure', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
?>