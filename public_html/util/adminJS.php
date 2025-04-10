<?php 
require_once "../../config.php";

outside("../");

if(getUserInfo("isAdmin") > 0){
    header('Content-Type: text/javascript');
    
    //readfile("../../private/admin.js"); //outside the `public_html` folder -> inside a "private" folder for private assets which only admin can access
    readfile("../js/admin.js");
}

else{
    header("Location: ../");
}

?>