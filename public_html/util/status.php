<?php 
require_once "../../config.php";

outside("../");

if(!empty($_SERVER["HTTP_REFERER"])){
    $status = [];
    $i = 0;

    $res = getTable("orders", sprintf("WHERE username = '%s'", getJWT("username")));

    while($row = $res->fetch_assoc()){
        $status[$i] = sprintf("%s-%s", STATUS[$row["status"]], STATUS_COLOR[$row["status"]]);
        $i++;
    }

    echo json_encode($status);
}

?>