<?php
  include("config.php");

  session_start();
  if(!isset($_SESSION['login_user'])){
    header("location: index.php");
  }
  if (!isset($_POST['category'])) {
    $category = 'all';
    $product_sql = "SELECT products.ProductName,
    suppliers.SupplierName,
    products.ProductPrice,
    products.ProductQuantity
    FROM products INNER JOIN suppliers ON products.SupplierID = suppliers.SupplierID
    ORDER BY products.ProductName";
  }
  else if ($_POST['category'] == 'all') {
    $category = 'all';
    $product_sql = "SELECT products.ProductName,
    suppliers.SupplierName,
    products.ProductPrice,
    products.ProductQuantity
    FROM products INNER JOIN suppliers ON products.SupplierID = suppliers.SupplierID
    ORDER BY products.ProductName";
  }
  else {
    $category = $_POST['category'];
    $product_sql = "SELECT products.ProductName,
    suppliers.SupplierName,
    products.ProductPrice,
    products.ProductQuantity
    FROM products INNER JOIN suppliers ON products.SupplierID = suppliers.SupplierID
    WHERE products.CategoryID =
    (SELECT CategoryID FROM categories WHERE CategoryName = '$category')
    ORDER BY products.ProductName" ;
  }
?>

<!DOCTYPE html>
<html>
<head>

  <title>Product List</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="css/mystyle.css" type="text/css">
  <link rel="stylesheet" href="css/table_style.css" type="text/css">
  <script>
  function submitform()
  {

    document.getElementById("Category_form").submit();

  }
</script>
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
      <li><a href="view-order.php">View Order List</a></li>
      <li><a class="active" href="product.php">Product List</a></li>

    </ul>

  </nav>

  <!---MAIN--------------------------------------->
  <main >
    <h1>Product</h1>
    <form id="Category_form" action="" method="post" class="MySelect">
      Category:
      <select name="category" onchange="return submitform()">
        <option value="all">All</option>
        <?php
        $sql = "SELECT CategoryName FROM categories ORDER BY CategoryName";

        if($result = mysqli_query($db,$sql)){
          // Fetch one and one row

          while ($row = mysqli_fetch_row($result)) {
              if($row[0] == $category){
                $select = "selected=\"selected\"";
              }
              else{
                $select = "";
              }

              echo "<option value = \"$row[0]\" $select>" . $row[0] . "</option>";
          }

          // Free result set
          mysqli_free_result($result);
        }
        ?>
      </select>
    </form>
    <br>
    <TABLE class="MyTable">
    <TR>
      <TH>Product</TH>
      <TH>Supplier Name</TH>
      <TH>Price</TH>
      <TH>Quantity</TH>
    </TR>
    <?php
    if($result = mysqli_query($db,$product_sql)){
      // Fetch one and one row

      while ($row = mysqli_fetch_row($result)) {

          echo "<TR>";
          echo "<TD>" . $row[0] . "</TD>";
          echo "<TD>" . $row[1] . "</TD>";
          echo "<TD>" . number_format($row[2]) . " VND</TD>";
          echo "<TD>" . $row[3] . "</TD>";
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
