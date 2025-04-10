<?php require_once "../config.php"; 

outside();

if($_SERVER["REQUEST_METHOD"] == "POST"){
  isset_checker($_POST, ["itemID", "action"]);

  $cart = getCart();

  if($_POST["action"] == "/"){
    writeCart("{}");
  }

  else{
    $itemID = (int)$_POST["itemID"];

    $item = translateCart($itemID);

    if(($item == NULL || !$item["onSale"]) && $_POST["action"] == "+" ){ echo returnError("ITEM NOT IN SALE !"); exit(); }

    if($itemID > 0){

      if($_POST["action"] == "+"){
        if($cart[$itemID] < getCounter("maxProduct")){
          $cart[$itemID] = in_array($itemID, array_keys($cart)) ? $cart[$itemID]+1 : 1;
        }

        else{
          echo returnError(sprintf("MAX of %d for THIS ITEM !", getCounter("maxProduct"))); exit();
        }

      }

      else if($_POST["action"] == "-"){
        if(in_array($itemID, array_keys($cart))){
          
          if($cart[$itemID] == 1){
            unset($cart[$itemID]);
          }
          else{
            $cart[$itemID]-=1;
          }

        }
      }

      else if($_POST["action"] = "*"){
        unset($cart[$itemID]);
      }

      $cart = json_encode($cart);  

      writeCart($cart);
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="./img/logo.png">
    <title>McMeucci</title>
</head>
<body data-bs-theme="dark">
    <nav class="navbar navbar-expand-md border-bottom border-body" id="theme">
        <div class="container-fluid">
            <img src="./img/logo.png" width="60" height="48" class="d-inline-block align-text-top">
          <a class="navbar-brand font1">McMeucci</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="./orders">My Orders</a>
              </li>

              <?php if(getUserInfo("isAdmin") > 0):?>
              <li class="nav-item">
                <a class="nav-link" href="./admin">Admin</a>
              </li>
              <?php endif ?>
              
              <li class="nav-item">
                <a class="nav-link" aria-current="page" href="./logout">Logout</a>
              </li>

              <li class="nav-item nav-link text-danger fw-bold" id="clock"></li>
            </ul>
          </div>
        </div>
      </nav>

      
      <div class="modal fade" id="clearCartModal" tabindex="-1" aria-labelledby="cartCleared" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="fs-5 mx-auto" id="cartCleared">ARE YOU SURE ?</h1>
            </div>
            <div class="modal-footer mx-auto">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">CLOSE</button>
              <button type="button" class="btn btn-success" id="clearCart" data-bs-dismiss="modal">CLEAR CART</button>
            </div>
          </div>
        </div>
      </div>



      <?php
      $mx = getCounter("maxDiscount");
      $err = payment_err();
      
      if($mx > 0):?>
        <div class="sticky-top bg-<?= $mx >= 100 ? "danger" : "warning" ?> text-center p-2 text-black fw-bold"><?= $mx >= 100 ? "ORDERS CLOSED" : sprintf("DISCOUNT OF %d%% FOR EVERY PRODUCT", $mx) ?></div>
      <?php endif ?>

      <div class="fs-4 text-center mt-4">Welcome, <span class="fw-bold title text-info-emphasis fs-2"><?= getJWT("username")?></span></div>

      <div class="text-center fs-2 mt-5 mb-4 title font2">MENU</div>

      <div class="container-fluid mx-auto row text-center">

        <?php

        $res = getTable("menu");
        while($row = $res->fetch_assoc()):
        ?>
          <div class="col-lg-4 col-sm-6 d-flex mt-2">

            <div class="card mx-auto shadow-lg p-3 mb-5 bg-body-tertiary rounded" style="width:22rem; <?php if(!$row["onSale"]) : echo "opacity: 50%";  endif?>">
            <img src="./img/<?= $row["id"] ?>.jpeg" class="card-img-top img-fluid">
              
              <div class="card-body" style="display: flex; flex-direction: column; justify-content: space-between;" >
                <h4 class="card-title mt-2"><?= $row["name"] ?></h4>
                
                <p class="card-text fs-4 fw-bold"><?php $pr = getPrice($row["price"], $row["discount"]); if(sizeof($pr)==1 || $mx == 100): echo $pr[0]; else: echo "<span class='card-text text-decoration-line-through fs-6 fw-normal'>$pr[0] €</span><br>$pr[1]"; endif?> €</p>

                <?php if($err[0]): ?>
                  <span class="badge rounded bg-danger text-dark mb-3">NOT AVAILABLE</span>

                <?php elseif($row["discount"] > 0 || $mx > 0):?><span class="badge rounded text-dark bg-warning mb-3"><?= $mx > 0 ? $mx : $row["discount"]?>% DISCOUNT</span>
                  
                <?php endif?>
                <div class="mt-2">
                  <button id="addCart" class="d-none d-lg-block btn btn-outline-primary w-100 <?php if(!$row["onSale"]) : echo "disabled"; endif?>" item-id=<?= $row["id"] ?>>ADD TO CART</button>
                  <button id="addCart" class="d-lg-none btn btn-primary w-100 <?php if(!$row["onSale"]) : echo "disabled"; endif?>" item-id=<?= $row["id"] ?>>ADD TO CART</button>
                </div>
              </div>
            </div>
          </div>

          <?php endwhile ?>
        
      </div>

      <hr>

      <h5 class="text-center text-danger fw-bold mt-4 mb-4 p-1" id="error"><?= $err[0] ? $err[1] : "" ?></h5>

      <div class="container-fluid fs-5 mt-3">
        <?php 
        $gt = getCart();

        if(sizeof($gt) > 0): ?>
          <ul id="cartView">

            <?php foreach($gt as $k=>$v): $item = translateCart($k)?>
              <li class="mb-4 fw-bold"><?= $item["name"] ?> x<?= $v;?>
                  <button id="removeCart" class="btn btn-outline-danger btn-sm ms-2" item-id=<?= $item["id"] ?> style="border-radius: 45%">-</button>
                  <button id="addCart" class="btn btn-outline-success btn-sm ms-1" item-id=<?= $item["id"] ?> style="border-radius: 45%">+</button>
                  <button id="clearItem" class="btn btn-outline-secondary btn-sm ms-1" item-id=<?= $item["id"] ?> style="border-radius: 45%">🗑️</button>
              </li>
            <?php endforeach; ?>

          </ul>

          <h5 class="mt-4">TOTAL: <?= nf(getTotal()); ?> €</h5>

          <a class="btn btn-primary mt-3 mb-4 <?= $err[0] ? "disabled" : "" ?>" href="./payment">PAY WITH PAYPAL</a>
          <button class="btn btn-danger mt-3 mb-4 ms-1" item-id=0 id="clearCartOpen">CLEAR CART</button>
        <?php endif?>


      </div>

      <?php tag(); ?>
      
<script src="./js/cartManagement.js"></script>

<script src="./js/clock.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>