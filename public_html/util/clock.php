<?php 
require_once "../../config.php";

outside("../");

//torre brucia di tumore, in terza dovevi morire
if(!empty($_SERVER["HTTP_REFERER"])){
    echo json_encode(["timer" => getJWT("exp") - time()]);
}

?>