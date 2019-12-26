<?php
  include("config.php");


  session_start();
  if(isset($_SESSION['login_user']))
  header("location: profile.php");
  $error = "";
  $username = "";
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form
      if(isset($_POST['username']) || isset($_POST['password'])){
        $username = mysqli_real_escape_string($db,$_POST['username']);
        $password = mysqli_real_escape_string($db,$_POST['password']);

          $sql = "SELECT AgencyID FROM agencies WHERE AgencyUsername = '$username' and AgencyPassword = '$password'";
          if($result = mysqli_query($db,$sql)){
              $row = mysqli_fetch_array($result);
              $count = mysqli_num_rows($result);
              mysqli_free_result($result);
              if($count == 1) {

                 $_SESSION['login_user'] = $row[0];

                header("location: profile.php");
              }
              else {
                 $error = "Your Login Name or Password is invalid";
              }

           }

        }

   }

  mysqli_close($db);

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Sign In</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" >
  <link rel="stylesheet" type="text/css" href="css/mystyle.css">
  <script>
  function isEmpty(id){
    if(document.getElementById(id).value == "")
      return true;
    else
      return false;
  }

  function isFullInfo(){
    if(isEmpty('username')){
      alert("Username can not be empty!");
      return false;
    }
    else if(isEmpty('password')){
      alert("Password can not be empty!");
      return false;
    }
    else{
      return true;
    }
  }

  function SignIn(){
    if(!isFullInfo())
      return;
    else
      document.getElementById("sign_in_form").submit();
  }
  </script>
</head>
<body>
  <div class="back">

    <div class="div-center">
      <div class="content">
        <h3 style="text-align:center;">Sign in</h3>
        <hr />
        <form id="sign_in_form" action = "" method = "post">
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" id="username" value= "<?php echo $username;?>" class="form-control" id="exampleInputEmail1" placeholder="Username">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
          </div>
          <button style="width:25%; text-align:center;" type="button" class="btn btn-primary" onclick="SignIn()">Sign in</button>
          <a href="sign_up.php"><button style="float:right;" type="button" class="btn btn-link">Sign up</button></a>

        </form>

      </div>
      <br>
      <?php echo $error;?>
    </div>
  </div>
</body>
</html>
