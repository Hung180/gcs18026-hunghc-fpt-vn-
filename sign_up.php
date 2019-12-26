<?php
  include("config.php");


  session_start();
  if(isset($_SESSION['login_user']))
    header("location: profile.php");
  $error = "";
  $username = "";
  if($_SERVER["REQUEST_METHOD"] == "POST") {

     // username and password sent from form
     if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['re_password'])){
       $username = mysqli_real_escape_string($db,$_POST['username']);
       $password = mysqli_real_escape_string($db,$_POST['password']);
       $re_password = mysqli_real_escape_string($db,$_POST['re_password']);

         $sql = "SELECT * FROM agencies WHERE AgencyUsername = '$username'";
         if($result = mysqli_query($db,$sql)){
             $count = mysqli_num_rows($result);
             mysqli_free_result($result);
             if($count != 0) {
                $error = "This username already exist";
             }
             else {
                $sql = "SET @@auto_increment_increment=1";
                mysqli_query($db,$sql);
                $sql = "INSERT INTO agencies
                       (AgencyName, AgencyUsername, AgencyPassword, AgencyAddress)
                VALUES ('$username','$username','$password', '$username address')";
                if($result = mysqli_query($db,$sql)){

                  header("location: index.php");
                }

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
  <title>Sign Up</title>
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
    else if(isEmpty('re_password')){
      alert("Re-password can not be empty!");
      return false;
    }
    else if(document.getElementById('password').value != document.getElementById('re_password').value){
      alert("Password and Re-password are not match!");
      return false;
    }
    else{
      return true;
    }
  }

  function SignUp(){
    if(!isFullInfo())
      return;
    else
      document.getElementById("sign_up_form").submit();
  }
  </script>
</head>
<body>
  <div class="back">
    <div class="div-center">
      <div class="content">

        <h3 style="text-align:center;">Sign up</h3>
        <hr />
        <form id="sign_up_form" action="" method = "post">

          <div class="form-group">
            <label>Username</label>
            <input type="text" value= "<?php echo $username?>" id="username" name="username" class="form-control"  placeholder="Username">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" id = "password" name = "password" class="form-control"  placeholder="Password">
          </div>
          <div class="form-group">
            <label>Re-enter password</label>
            <input type="password" id = "re_password" name = "re_password" class="form-control"  placeholder="Re-enter password">
          </div>
          <button style="width:25%; text-align:center;" type="button" class="btn btn-primary" onclick="SignUp()">Sign up</button>
          <a href="index.php"><button style="float:right;" type="button" class="btn btn-link">Sign in</button></a>

        </form>

      </div>
      <br>
      <?php echo $error;?>
  </div>
</div>
</body>
</html>
