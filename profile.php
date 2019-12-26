<?php
  include("config.php");

  session_start();
  if(!isset($_SESSION['login_user'])){
    header("location: index.php");
  }

  $login_user = $_SESSION['login_user'];
  $error = "";

  $sql = "SELECT AgencyUsername, AgencyPassword FROM agencies WHERE AgencyID = '$login_user'";
  if($result = mysqli_query($db,$sql)){
    $row = mysqli_fetch_array($result);
    $username = $row[0];
    $password = $row[1];
    mysqli_free_result($result);
  }

  if($_SERVER["REQUEST_METHOD"] == "POST") {
     // username and password sent from form
       if(isset($_POST['username']) || isset($_POST['password'])){
         $user = mysqli_real_escape_string($db,$_POST['username']);
         $pass = mysqli_real_escape_string($db,$_POST['password']);

         $sql = "SELECT * FROM agencies WHERE AgencyUsername = '$user'";
         if($result = mysqli_query($db,$sql)){
             $count = mysqli_num_rows($result);
             mysqli_free_result($result);
             if($count != 0 && $username != $user) {
                $error = $user . " username already exists!";
             }
             else{

               $sql = "UPDATE agencies SET AgencyUsername='$user',AgencyPassword='$pass' WHERE AgencyID = '$login_user'";
               if($result = mysqli_query($db,$sql)){
                     header("location: profile.php");
               }
               else {
                      $error = "Error occur!";
               }
             }
          }



       }


    }



  mysqli_close($db);
?>

<!DOCTYPE html>
<html>
<head>

  <title>Profile</title>
  <meta charset="utf-8">

  <link rel="stylesheet" href="css/mystyle.css" type="text/css">
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

  function EditProfile(){
    if(!isFullInfo())
      return;
    else if(confirm("Are you sure want to edit your profile?"))
      document.getElementById("profile_form").submit();
  }
  </script>

  <style>
    main {
      width: 40%;
      margin-left: 30%;
      border: thin solid #000;
    }
    #profile_content {
      width: 80%;
      margin-left: 10%;
    }
    button, input {
      float:right;

    }
  </style>

</head>

<body>

<div id="container">

  <!---HEADER--------------------------------------->
  <header>
  <!--LEFT IMAGE------------------>
     <a href="sign-out.php"><img id="LeftImage" src="https://i.ibb.co/cb0pyxL/weblogo.png" alt="It's a logo"></a>

  </header>

  <!---NAVIGATOR--------------------------------------->
  <nav>

    <ul>



      <li><a class="active" href="profile.php">Profile</a></li>
      <li><a href="new-order.php">New Order</a></li>
      <li><a href="view-order.php">View Order List</a></li>
      <li><a href="product.php">Product List</a></li>


    </ul>

  </nav>

  <!---MAIN--------------------------------------->
  <main>
    <div id="profile_content">
      <h1>Profile</h1>
      <form id="profile_form" action="" method="post">
        <label>Username &nbsp;</label>
        <input type="text" value="<?php echo $username;?>" id="username" name="username" size="22">
        <br>
        <br>
        <label>Password &nbsp; </label>
        <input type="password" value="<?php echo $password;?>" id="password" name="password" size="22">
        <br>
        <br>
        <br>
        <button class="btn btn-primary" type="button" onclick="EditProfile()">Edit profile</button>
        <br>
        <?php echo $error;?>
      </form>
    </div>


  </main>




</div>

</body>
</html>
