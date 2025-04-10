<?php require_once "../config.php";
inside();

if($_SERVER["REQUEST_METHOD"] == "POST"){
  isset_checker($_POST, ["username", "class", "password", "confirmPassword", "captcha"]);

  $c = captcha($_POST["captcha"]);

  if($c){
    $username = clean($_POST["username"]);
    $class = clean($_POST["class"]);
    $password = clean($_POST["password"]);
    $confirmPassword = clean($_POST["confirmPassword"]);
  
    if(strlen($username) >= FORM_MIN_LENGTH && strlen($password) >= FORM_MIN_LENGTH){
      if(in_array($class, CLASSES)){
  
        if($password == $confirmPassword){
          $password = password_hash($password, CRYPT_SHA256);
          
          $sql = "SELECT * from users WHERE username=?";
  
          $stmt = $GLOBALS["conn"]->prepare($sql);
          $stmt->bind_param("s",$username);
  
          $stmt->execute();
  
          if($stmt->get_result()->num_rows==0){
            $sql = "INSERT INTO users (username, password, classe) VALUES (?, ?, ?)";
            $stmt->prepare($sql);
            $stmt->bind_param("sss", $username, $password, $class);
  
            if($stmt->execute()){
              echo json_encode(["success" => true]);
            }
          }
  
          else{
            echo returnError("USERNAME Already Exists");
          }
  
          $stmt->close();
  
        }
  
        else{
          echo returnError("Passwords Do NOT Match");
        }
  
      }
  
      else{
        echo returnError("Invalid Class !");
      }
    }
  
    else{
      echo returnError(sprintf("Inputs MUST be at least %d chars", FORM_MIN_LENGTH));
    }  
  }

  else{
    echo returnError("Captcha Failed !");
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
  <title>McMeucci - Register</title>
</head>

<body data-bs-theme="dark">

  <nav class="navbar navbar-expand-lg border-bottom border-body" id="theme">
    <div class="container-fluid">
      <img src="./img/logo.png" width="60" height="48" class="d-inline-block align-text-top">
      <span class="navbar-brand fw-bold font1">Mc Meucci</span>
    </div>
  </nav>

  
  <div class="text-center fs-2 mt-4 mb-4 title font2">REGISTER</div>


  <div class="container-fluid w-75 mx-auto">
    <div class="card p-4 text-center">

      <div class="row">
        <div class="col-sm-6 mt-3">
          <input type="text" class="form-control text-center mx-auto" placeholder="Type Your Username" id="username">
        </div>

        <div class="col-sm-6 mt-3">
          <select class="form-select text-center mx-auto" id="class">
            <?php foreach(CLASSES as $cl):?><option value="<?= $cl ?>"><?= className($cl)?></option><?php endforeach?>
          </select>
        </div>

        <div class="col-sm-6 mt-3">
          <input type="password" class="form-control text-center mx-auto" placeholder="Type Your Password" id="password">
        </div>

        <div class="col-sm-6 mt-3">
            <input type="password" class="form-control text-center mx-auto" placeholder="Confirm Your Password" id="confirmPassword">
  
            <br>
            <label for="form-check-input">See Password</label>
            <input class="form-check-input ms-1" type="checkbox" role="switch" id="seePassword">
        </div>


      </div>

      <br>
      
      <button type="submit" class="btn btn-primary w-75 mx-auto mt-3" id="register">SIGN IN</button>

      <h5 class="text-danger mt-4" id="errorLabel"></h5>

      <div class="mt-4 fs-5"><a href="./login" class="text-success-emphasis text-decoration-none">ALREADY GOT AN ACCOUNT?</a></div>

    </div>
  </div>

  <?php tag(); ?>

  <script src="https://www.google.com/recaptcha/api.js?render=<RECAPTCHA_PUBLIC_KEY>"></script>

  <script src="./js/logAction.js"></script>
  <script src="./js/register.js"></script>

  <script src="./js/seePassword.js"></script> 

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>
</body>

</html>