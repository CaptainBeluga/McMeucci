<?php 
require_once "../config.php";
outside();

$total = getTotal();

if($total > 0 && !payment_err()[0]){
    $resp = $gateway->purchase(array(
        'amount' => getTotal(),
        'currency' => CURRENCY,
        'returnUrl' => PAYMENT_URL,
        'cancelUrl' => PAYMENT_URL,
    ))->send();
    
    if($resp->isRedirect()){
        $resp->redirect();
    }
    
    // else{
    //     echo $resp->getMessage();
    // }    
}

else{
    header("Location: ./index#error");
}



?>