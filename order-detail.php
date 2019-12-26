<?php
  include("config.php");
  if(!isset($_GET['id'])){
    die();
  }
  $order_id = $_GET['id'];

  $sql = "SELECT FirstName, LastName, Email, Address
  FROM customers
  WHERE CustomerID = (SELECT CustomerID FROM orders WHERE OrderID = '$order_id')";
  if($result = mysqli_query($db,$sql)){
    $row = mysqli_fetch_array($result);
    $f_name = $row[0];
    $l_name = $row[1];
    $email = $row[2];
    $address = $row[3];
  }
  mysqli_free_result($result);

  $sql = "SELECT OrderDate, OrderTotal FROM orders WHERE OrderID= '$order_id'";
  if($result = mysqli_query($db,$sql)){
    $row = mysqli_fetch_array($result);
    $date = $row[0];
    $total = number_format($row[1]);
  }
  mysqli_free_result($result);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Order Detail</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://gcs18026-huynhcamhung-gcs0701a.herokuapp.com/css/mystyle.css" type="text/css">
  <link rel="stylesheet" href="https://gcs18026-huynhcamhung-gcs0701a.herokuapp.com/css/table_style.css" type="text/css">
  <style>
    h3 {
      margin:0;
      color: #337AB7;
    }

    /* Customer block */
    #cus_info {
      padding: 1.5em 1em;
      border: thin solid #000;
      display:inline-block;
      width: 95%;
    }
    #cus_left{
      display:inline;
      float:left;
    }
    #cus_right{
      display:inline;
      float:right;
    }
    #cus_left input {
      float: right;
    }

    /* Product and order info block */
    #pro_ord_info {
      display: block;
    }

    #Total_area{
      float:right;
    }
  </style>
</head>

<body>

<div id="container">


  <!---MAIN--------------------------------------->
  <main>
    <h1>Order detail</h1>

    <h3>Customer information</h3>
    <div id=cus_info>
        <div id=cus_left>
          First name<input type="text" value="<?php echo $f_name; ?>" size="25" readonly>
          <br>
          <br>
          Email<input type="email" value="<?php echo $email; ?>" size="25" readonly>
          <br>
          <br>
          Address<br>
          <textarea name="customer_address" rows="8" cols="45" readonly><?php echo $address; ?></textarea>
        </div>

        <div id=cus_right>
          Last name&nbsp;&nbsp;<input type="text" value="<?php echo $l_name; ?>" readonly>
        </div>
    </div>

    <br>
    <br>
    <div id="pro_ord_info">
      <label>Order Date:<?php echo " $date";?></label>
      <div id="Total_area">
          <label>Order Total:<?php echo " $total" . " VND";?></label>
      </div>

    </div>

      <br>
      <table id="order_table" class="MyTable">
        <thead>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Sub Total</th>
        </thead>

        <tbody>
          <?php
          $sql = "SELECT products.ProductName,
          orderdetails.SellPrice,
          orderdetails.SellQuantity,
          (orderdetails.SellPrice * orderdetails.SellQuantity) AS SubTotal
          FROM orderdetails
          INNER JOIN products ON orderdetails.ProductID = products.ProductID
          WHERE OrderID = '$order_id'";


          if($result = mysqli_query($db,$sql)){
            // Fetch one and one row

            while ($row = mysqli_fetch_row($result)) {

                echo "<TR>";
                echo "<TD>" . $row[0] . "</TD>";
                echo "<TD>" . number_format($row[1]) . " VND</TD>";
                echo "<TD>" . $row[2] . "</TD>";
                echo "<TD>" . number_format($row[3]) . " VND</TD>";
                echo "</TR>";
            }

            // Free result set
            mysqli_free_result($result);

          }
          mysqli_close($db);
          ?>
        </tbody>
        </table>


  </main>




</div>

</body>
</html>
