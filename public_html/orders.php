<?php require_once "../config.php";

outside();

if(isset($_GET["paymentId"]) && isset($_GET["token"]) && isset($_GET["PayerID"])){
  if(payment_err()[0]){
      header("Location: ./index.php#error");
      exit();
  }
  
  $transaction = $gateway->completePurchase(array(
    "payer_id" => $_GET["PayerID"],
    "transactionReference" => $_GET["paymentId"],
  ));

  $resp = $transaction->send();

  if($resp->isSuccessful()){
      $d = $resp->getData();
      
      if(getTable("orders", sprintf("WHERE payID = '%s'", $d["id"]))->num_rows == 0){

        $stmt = $conn->prepare("INSERT INTO `orders` (`id`, `orderID`, `payID`, `username`, `cart`, `total`, `fee`, `status`, `timestamp`) VALUES 
        (NULL, ?, ?, ?, ?, ?, ?, 'PPY', ?)");
        //Storing Order

        
        $data = [
            getOrderID(),
        
            $d["id"],
        
            getJWT("username"),
            
            json_encode(getCart()),
            
            price((float)$d["transactions"][0]["related_resources"][0]["sale"]["amount"]["total"]),
            price((float)$d["transactions"][0]["related_resources"][0]["sale"]["transaction_fee"]["value"]),
      
            currentDate()];

        $stmt->bind_param("ssssdds", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6]);
        $stmt->execute();


        //Storing PaymentInfo
        $data = [
          $d["id"],
          
          $d['transactions'][0]['related_resources'][0]['sale']['id'],

          getJWT("username"),
          
          $d["payer"]["payer_info"]["email"],
          $d["payer"]["payer_info"]["first_name"],
          $d["payer"]["payer_info"]["last_name"],
          $d["payer"]["payer_info"]["payer_id"],
          
          $d["payer"]["payer_info"]["country_code"],
          
          $d["state"] . " - " . $d["transactions"][0]["related_resources"][0]["sale"]["state"]
        ];

        $stmt = $conn->prepare("INSERT INTO `payments_info` (`id`, `payID`, `saleID` ,`email`, `firstName`, `lastName`, `payerID`, `countryCode`, `status`) VALUES
        (NULL,?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssssss", $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7]);
        $stmt->execute();

        writeCart("{}");

      }

      else{
        header(sprintf("Location: orders#%s", $_GET["paymentId"]));
      }
  }

  else{
      header("Location: orders");
  }

}

outside();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="icon" href="./img/logo.png">
  <title>McMeucci - My Orders</title>
</head>


<body data-bs-theme="dark">
  <nav class="navbar navbar-expand-lg border-bottom border-body" id="theme">
    <div class="container-fluid">
      <img src="./img/logo.png" width="60" height="48" class="d-inline-block align-text-top">
      <a class="navbar-brand font1" href="./">Menu</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          
          <?php if(getUserInfo("isAdmin")): ?>
            <li class="nav-item">
              <a class="nav-link" href="./admin.php">Admin</a>
            </li>
          <?php endif?>

          <li class="nav-item">
            <a class="nav-link" href="./logout.php">Logout</a>
          </li>

          <li class="nav-item nav-link fw-bold" id="clock"></li>
          <li class="nav-item nav-link d-none" id="extendedClock"></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="text-center fs-2 mt-5 title font2">MY ORDERS</div>

  <?php
    $orders = getTable("orders", sprintf("WHERE username = '%s'", getJWT("username")));
    
    if($orders->num_rows == 0):
  ?>
    <h2 class="text-danger mt-5 text-center p-1">NO ORDERS ON <span class="fw-bold text-decoration-underline link-offset-2"><?= strtoupper(date("d F Y", time()))?></span></h2>

    <?php else: foreach($orders as $order):?>
      <div class="card mx-auto text-center w-75 shadow-lg rounded mt-5 mb-4" id=<?= $order["payID"] ?>>
        <div class="card-header pt-4">
          <div class="spinner-grow spinner-grow-sm me-2 text-<?= STATUS_COLOR[$order["status"]] ?>" role="status" id="statusBlinker"></div>
          <span class="fs-6 font3"><?= STATUS[$order["status"]]; ?></span>
          <div class="spinner-grow spinner-grow-sm me-2 text-<?= STATUS_COLOR[$order["status"]] ?>" role="status" id="statusBlinker"></div>

          <p class="mt-3 fs-6 fw-bold">ORDER ID : <?= $order["orderID"]; ?></p>
        </div>

        <div class="card-body">
          <h5 class="card-title">PRODUCTS</h5>
          <p class="card-text mt-4">
            <ul class="list-group w-75 mx-auto">
              <?php foreach(json_decode($order["cart"],True) as $k=>$v): $item = translateCart($k);?>
                <li class="list-group-item"><strong><?= $item["name"] ?></strong> x<?= $v ?></li>
              <?php endforeach; ?>
            </ul>

            <br>

            <span class="fw-bold fs-5">TOTAL</span> : <?= nf($order["total"]) ?> €
            <br><span class="fw-bold">( FEE</span> : <?= nf(($order["fee"])) ?> € )
          </p>
        </div>


        <div class="card-footer text-body-secondary">
          <br>Date & Time : <strong><?= $order["timestamp"]?></strong>
        </div>

      </div>

    <?php endforeach;
    
    tag();
    
    ?>

    <script src="./js/orderStatus.js"></script>
  
  <?php endif ?>

  <script src="./js/clock.js"></script>
  
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>

</body>

</html>