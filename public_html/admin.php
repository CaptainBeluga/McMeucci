<?php 

require_once "../config.php";

outside();

if(getUserInfo("isAdmin") < 1){
    header("Location: ./");
}

function checkLevel($table){
  if(getUserInfo("isAdmin") < ADMIN_AUTH[$table]){
    echo returnError("You Can't Perform This Action !");
    exit();
  }
}

if($_SERVER["REQUEST_METHOD"] == "POST" && getUserInfo("isAdmin") > 0 && $_POST["csrf"] == $_SESSION["csrf"]){
    if(isset($_POST["table"]) && isset($_POST["action"])){

      $table = clean($_POST["table"]);
      $action = clean($_POST["action"]);
      
      if(in_array(clean($_POST["id"]), [1]) && $table == "users") { header("Location: admin"); exit();}

      checkLevel($table);

      if($action == "edit"){
        switch($table){

          case "users":

            $stmt = $conn->prepare("UPDATE `users` SET `isAdmin` = ? WHERE `id` = ?");
            
            $t = (int) clean($_POST["isAdmin"]) > MAX_ADMIN_LVL ? MAX_ADMIN_LVL : clean($_POST["isAdmin"]);

            $stmt->bind_param("dd", $t, clean($_POST["id"]));
            
            break;
  
          case "orders":

            $stmt = $conn->prepare("UPDATE `orders` SET `status` = ? WHERE `id` = ?");
            $stmt->bind_param("sd", clean($_POST["status"]), clean($_POST["id"]));
  
            break;
  
          case "menu":

            $stmt = $conn->prepare("UPDATE `menu` SET `name` = ?, `price` = ?,  discount = ?, `onSale` = ? WHERE `id` = ?");
            
            $t = [clean($_POST["onSale"]) == "true"  ? 1 : 0];
  
            $stmt->bind_param("sdddd", $_POST["name"], clean($_POST["price"]), clean($_POST["discount"]), $t[0], clean($_POST["id"]));
  
            break;   
  
          case "counter":

            $stmt = $conn->prepare("UPDATE `counter` SET `maxOrdini` = ?, `orderNumber` = ?, `maxProduct` = ?, `maxDiscount` = ?, `openTime` = ?, `closeTime` = ? WHERE `id` = 0");
  
            $stmt->bind_param("dddddd", clean($_POST["maxOrdini"]), clean($_POST["orderNumber"]), clean($_POST["maxProduct"]), clean($_POST["maxDiscount"]), clean($_POST["openTime"]), clean($_POST["closeTime"]));
  
            break;
  
          default:
            break;
        }
  
        $stmt->execute();
      }


      else if($action == "delete"){

        $deletable = ["users", "orders", "menu"];

        if(in_array($table, $deletable)){
          
          $stmt = $conn->prepare(sprintf("DELETE FROM `%s` WHERE `id` = ?", $table));
          $stmt->bind_param("d", clean($_POST["id"]));

          $stmt->execute();
        }
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="icon" href="./img/logo.png">
  <title>McMeucci - Admin</title>
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

          <li class="nav-item">
            <a class="nav-link" href="./orders">My Orders</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="./logout">Logout</a>
          </li>

          <li class="nav-item">
            <a class="nav-link disabled fw-bold">Admin Level: <?= getUserInfo("isAdmin") ?> | <?= ADMIN_LVLS[getUserInfo("isAdmin")] ?></a>
          </li>

          <li class="nav-item nav-link text-danger fw-bold" id="clock"></li>

        </ul>
      </div>
    </div>
  </nav>


  <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="cartCleared" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
            <h1 class="fs-5 mx-auto" id="cartCleared">ARE YOU SURE ?</h1>
          </div>
          
          <div class="modal-footer mx-auto">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">CLOSE</button>
            <button type="button" class="btn btn-success" id="deleteItem" data-bs-dismiss="modal">DELETE ITEM</button>
          </div>
      </div>
    </div>
  </div>


  <div class="text-info-emphasis text-center fs-2 mt-5 fw-bold title">ADMIN DASHBOARD</div>

  <input type="hidden" value="<?= $_SESSION['csrf']; ?>">

  <?php if(getUserInfo("isAdmin") >= ADMIN_AUTH["users"]): ?>
  <div class="accordion mt-5 mx-auto p-3">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
          USERS
        </button>
      </h2>
      
      <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse">
        <div class="accordion-body">

          <div class="table-responsive text-center">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Username</th>
                  <th scope="col">Class</th>
                  <th scope="col">Admin LvL</th>
                </tr>
              </thead>

              <tbody class="table-group-divider" table-id="users">
                <?php 
                    $table = getTable("users");
                    while($row = $table->fetch_assoc()):
                ?>
                <tr dbID=<?= $row["id"] ?>>
                  <td scope="row"><?= $row["id"]; ?></td>
                  <td><?= $row["username"]; ?></td>
                  <td><?= $row["classe"]; ?></td>
                  <td edit-field="number" dbName="isAdmin"><?= $row["isAdmin"] > 0 ? $row["isAdmin"] : "❌"; ?></td>

                  <td>
                    <button type="button" class="btn btn-success" disabled id="saveItem">SAVE</button>
                  </td>
  
                  <td>
                    <button type="button" class="btn btn-warning" id="editItem">EDIT</button>
                  </td>
  
                  <td>
                    <button type="button" class="btn btn-danger" id="deleteItem">DELETE</button>
                  </td>
                </tr>

                <?php endwhile ?>
                
              </tbody>
              
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

  <?php endif ?>


  <?php if(getUserInfo("isAdmin") >= ADMIN_AUTH["orders"]): ?>
  <div class="accordion mt-5 mx-auto p-3">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
          ORDERS
        </button>
      </h2>
      <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse">
        <div class="accordion-body">

          <div class="table-responsive text-center">
            <table class="table table-bordered">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Order ID</th>
                <th scope="col" class="d-none d-sm-block">PAY ID</th>
                <th scope="col">Username</th>
                <th scope="col">Products</th>
                <th scope="col">Price</th>
                <th scope="col">PayPal Fee</th>
                <th scope="col">Payment Status</th>
                <th scope="col">Timestamp</th>
              </tr>
            </thead>
            <tbody class="table-group-divider" table-id="orders">
              <?php 
                    $table = getTable("orders");
                    while($row = $table->fetch_assoc()):
              ?>
              <tr dbID=<?= $row["id"] ?>>
                <th scope="row"><?= $row["id"]; ?></th>
                <td><?= $row["orderID"]; ?></td>
                <td class="d-none d-sm-block"><?= $row["payID"]; ?></td>
                <td><?= $row["username"]; ?></td>

                <td>
                   
                    <?php foreach(json_decode($row["cart"],True) as $k=>$v): $item = translateCart($k);?>
                        <span class="list-group-item"><strong><?= $item["name"] ?></strong> x<?= $v ?></span><hr>
                    <?php endforeach; ?>
                </td>

                <td><?= $row["total"]; ?> €</td>
                <td><?= $row["fee"]; ?> €</td>
                <td class="text-<?= STATUS_COLOR[$row["status"]]?> fw-bold fs-5" edit-field="select" dbName="status"><?= STATUS[$row["status"]]; ?></td>
                <td><?= $row["timestamp"]; ?></td>

                <td>
                  <button type="button" class="btn btn-success" disabled id="saveItem">SAVE</button>
                </td>

                <td>
                  <button type="button" class="btn btn-warning" id="editItem">EDIT</button>
                </td>

                <td>
                  <button type="button" class="btn btn-danger" id="deleteItem">DELETE</button>
                </td>

              </tr>

              <?php endwhile ?>
            </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

  <?php endif ?>



  <?php if(getUserInfo("isAdmin") >= ADMIN_AUTH["menu"]): ?>
  <div class="accordion mt-5 mx-auto p-3">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false"
          aria-controls="panelsStayOpen-collapseThree">
          MENU
        </button>
      </h2>
      <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
        <div class="accordion-body">
          
          <!-- <button class="btn btn-success mt-3 mb-4">ADD RECORD</button> -->

          <div class="table-responsive text-center">
            <table class="table table-bordered">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Price</th>
                <th scope="col">Discount</th>
                <th scope="col">Sale</th>
              </tr>
            </thead>
            <tbody class="table-group-divider" table-id="menu">
              <?php 
                    $table = getTable("menu");
                    while($row = $table->fetch_assoc()):
                ?>
              <tr dbID=<?= $row["id"] ?>>
                <th scope="row"><?= $row["id"]; ?></th>
                <td edit-field="text" dbName="name"><?= $row["name"]; ?></td>
                <td edit-field="number" dbName="price"><?= $row["price"]; ?></td>
                <td edit-field="number" dbName="discount"><?= $row["discount"] != 0 ? sprintf("%d %%",$row["discount"]) : "❌"; ?></td>
                <td edit-field="checkbox" dbName="onSale"><?= $row["onSale"] ? "✔️" : "❌"; ?></td>

                <td>
                  <button type="button" class="btn btn-success" disabled id="saveItem">SAVE</button>
                </td>

                <td>
                  <button type="button" class="btn btn-warning" id="editItem">EDIT</button>
                </td>

                <td>
                  <button type="button" class="btn btn-danger" id="deleteItem">DELETE</button>
                </td>
              </tr>

              <?php endwhile ?>

            </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

  <?php endif; ?>



  <?php if(getUserInfo("isAdmin") >= ADMIN_AUTH["counter"]): ?>
  <div class="accordion mt-5 mx-auto p-3">
    <div class="accordion-item">
      <h2 class="accordion-header">
        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false"
          aria-controls="panelsStayOpen-collapseFour">
          COUNTER
        </button>
      </h2>
      <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse">
        <div class="accordion-body">

          <div class="table-responsive text-center">
            <table class="table table-bordered">
            <thead>
              <tr>
                <th scope="col">Max Ordini</th>
                <th scope="col">Order Number</th>
                <th scope="col">Max Product</th>
                <th scope="col">Max Discount</th>
                <th scope="col">Open Time</th>
                <th scope="col">Close Time</th>
              </tr>
            </thead>
            <tbody class="table-group-divider" table-id="counter">
              <?php 
                  $table = getTable("counter");
                  while($row = $table->fetch_assoc()):
              ?>
              <tr>
                <td edit-field="number" dbName="maxOrdini"><?= $row["maxOrdini"]; ?></td>
                <td edit-field="number" dbName="orderNumber"><?= $row["orderNumber"]; ?></td>
                <td edit-field="number" dbName="maxProduct"><?= $row["maxProduct"]; ?></td>
                <td edit-field="number" dbName="maxDiscount"><?= $row["maxDiscount"] != 0 ? sprintf("%d %%", $row["maxDiscount"]) : "❌"; ?></td>
                <td edit-field="number" dbName="openTime"><?= $row["openTime"]; ?></td>
                <td edit-field="number" dbName="closeTime"><?= $row["closeTime"]; ?></td>

                <td>
                  <button type="button" class="btn btn-success" disabled id="saveItem">SAVE</button>
                </td>

                <td>
                  <button type="button" class="btn btn-warning" id="editItem">EDIT</button>
                </td>

                <td>
                  <div type="button" class="btn btn-danger" id="deleteItem">DELETE</button>
                </td>
              </tr>

              <?php endwhile ?>

            </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

  <?php endif ?>

  <?php tag(); ?>


  <script src="./util/adminJS"></script>

  
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>
</body>

</html>