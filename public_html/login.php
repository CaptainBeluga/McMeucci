<?php 
require_once "../config.php";
inside();

if($_SERVER["REQUEST_METHOD"] == "POST"){
  isset_checker($_POST, ["username", "password"]);

  $username = clean($_POST["username"]);
  $password = clean($_POST["password"]);

  $c = json_decode(captcha($_POST["captcha"]), true)["success"];

  if($c){
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$username);
    $stmt->execute(); 
  
    $res = $stmt->get_result();
  
    if($res->num_rows == 1){
      $row = $res->fetch_assoc();
  
      if(password_verify($password, $row["password"])){
        login_cookie([$row["username"]]);
        
        echo json_encode(["success" => true]);
      }
  
      else{
        echo returnError("USERNAME or PASSWORD Invalid");
      }
    }
  
    else{
      echo returnError("USERNAME or PASSWORD Invalid");
    }
  }
  else{
    //Captcha Failed
    echo returnError("USERNAME or PASSWORD Invalid");
  }
  
  
  exit();
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
  <title>McMeucci - Login</title>
</head>

<body data-bs-theme="dark">

  <nav class="navbar navbar-expand-lg border-bottom border-body" id="theme">
    <div class="container-fluid">
      <img src="./img/logo.png" width="60" height="48" class="d-inline-block align-text-top">
      <span class="navbar-brand fw-bold font1">Mc Meucci</span>
    </div>
  </nav>

  <div class="text-center fs-2 mt-4 mb-4 title font2">LOGIN</div>


  <div class="container-fluid w-75 mx-auto">
    <div class="card p-4 text-center">

      <input type="text" class="form-control text-center mx-auto mt-2" placeholder="Type Your Username" id="username">

      <br>

      <div id="PasswordIns">
        <input type="password" class="form-control text-center mx-auto" placeholder="Type Your Password" id="password">

        <br>
        <label for="form-check-input">See Password</label>
        <input class="form-check-input ms-1" type="checkbox" role="switch" id="seePassword">
      </div>

      <button type="submit" class="btn btn-primary w-75 mx-auto mt-3" id="login">LOGIN</button>

      <h5 class="text-danger mt-4" id="errorLabel"></h5>

      <div class="mt-4 fs-5"><a href="./register" class="text-success-emphasis text-decoration-none">STILL
          NO ACCOUNT?</a></div>
    </div>
  </div>

  <?php tag(); ?>


  <script src="https://www.google.com/recaptcha/api.js?render=<RECAPTCHA_PUBLIC_KEY>"></script>

  <script src="./js/logAction.js"></script>
  <script src="./js/login.js"></script>

  <script src="./js/seePassword.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>

  </body>

</html>