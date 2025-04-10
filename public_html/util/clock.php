<?php 
require_once "../../config.php";

outside("../");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $ct = getJWT("exp") - time();

    if(date("i", $ct) < 15 && date("h", $ct) == 1 && date("d", $ct) == 1){
        echo json_encode(["time" => date("i:s", $ct)]);
    }
}

?>