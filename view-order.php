<?php
  include("config.php");

  session_start();
  if(!isset($_SESSION['login_user'])){
    header("location: index.php");
  }

  $login_user = $_SESSION['login_user'];
?>
<!DOCTYPE html>
<html>
<head>

  <title>Order List</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="css/mystyle.css" type="text/css">
  <link rel="stylesheet" href="css/table_style.css" type="text/css">
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
      <li><a href="profile.php">Profile</a></li>
      <li><a href="new-order.php">New Order</a></li>
      <li><a class="active" href="view-order.php">View Order List</a></li>
      <li><a href="product.php">Product List</a></li>
    </ul>

  </nav>

  <!---MAIN--------------------------------------->
  <main>
    <h1>Order List</h1>
    <br>
    <TABLE class="MyTable">
    <TR>
      <TH>Order ID</TH>
      <TH>Customer</TH>
      <TH>Order Date</TH>
      <TH>Order Total</TH>
      <TH>Option</TH>
    </TR>
    <?php
    $sql = "SELECT
    orders.OrderID, customers.FirstName,customers.LastName,
    orders.OrderDate, orders.OrderTotal
    FROM orders
    INNER JOIN
    customers ON orders.CustomerID = customers.CustomerID
    WHERE orders.AgencyID = '$login_user'
    ORDER BY orders.OrderID DESC";


    if($result = mysqli_query($db,$sql)){
      // Fetch one and one row

      while ($row = mysqli_fetch_row($result)) {

          echo "<TR>";
          echo "<TD>" . $row[0] . "</TD>";
          echo "<TD>" . $row[1] . " " . $row[2] . "</TD>";
          echo "<TD>" . $row[3] . "</TD>";
          echo "<TD>" . number_format($row[4]) . " VND</TD>";
          echo "<TD><a target=\"_blank\" href=\"order-detail.php/?id=$row[0]\"><button type=\"button\">View</button></a></TD>";
          echo "</TR>";
      }

      // Free result set
      mysqli_free_result($result);

    }

    mysqli_close($db);
    ?>
    </TABLE>
  </main>




</div>

</body>
</html>
