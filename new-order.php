<?php
include("config.php");
session_start();
$error = "";

date_default_timezone_set('Asia/Ho_Chi_Minh');

if(!isset($_SESSION['login_user'])){
  header("location: index.php");
}
else{
  $login_user = $_SESSION['login_user'];
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
  if(isset($_POST['first_name'])){
    $f_name = $_POST['first_name'];
    $l_name = $_POST['last_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $order_date = $_POST['order_date'];
    $order_total = $_POST['order_total'];

    $search_customer_sql = "SELECT CustomerID  FROM customers WHERE
    FirstName = '$f_name' AND
    LastName = '$l_name' AND
    Email = '$email'";

    if($result = mysqli_query($db,$search_customer_sql)){
      $count = mysqli_num_rows($result);
      mysqli_free_result($result);
      if($count == 0){

        $sql = "SET @@auto_increment_increment=1";
        mysqli_query($db,$sql);
        $insert_customer_sql = "INSERT INTO customers(FirstName, LastName, Email, Address)
        VALUES ('$f_name', '$l_name', '$email', '$address')";
        mysqli_query($db,$insert_customer_sql);
      }
    }

    if($result = mysqli_query($db,$search_customer_sql)){
      $customer_id = mysqli_fetch_array($result)[0];
    }
    mysqli_free_result($result);

    $sql = "SET @@auto_increment_increment=1";
    mysqli_query($db,$sql);

    $insert_order_sql = "INSERT INTO orders(CustomerID, AgencyID, OrderDate, OrderTotal)
    VALUES ('$customer_id', '$login_user', '$order_date', '$order_total')";

    mysqli_query($db,$insert_order_sql);
    $order_id = mysqli_insert_id($db);
    $error = $error . $order_id . "\n";
    $insert_detail_sql = "";
    for($index = 0; $index < count($_POST['product']); $index++){

      $product = $_POST['product'][$index];
      $error = $error . $product . " ";
      $price = $_POST['price'][$index];
      $error = $error . $price . " ";
      $quantity = $_POST['quantity'][$index];
      $error = $error . $quantity . " ";
      $insert_detail_sql = "INSERT INTO orderdetails
      (OrderID, ProductID, SellPrice, SellQuantity)
      VALUES (
          $order_id,
          (SELECT ProductID FROM products WHERE ProductName = '$product'),
          $price,
          $quantity)";


      if(mysqli_query($db,$insert_detail_sql)){
        header("location: new-order.php");
      }
    }
    $error = mysqli_error($db);



  }

  //header("location: new-order.php");
}


?>

<!DOCTYPE html>
<html>
<head>
  <title>New Order</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="css/mystyle.css" type="text/css">
  <link rel="stylesheet" href="css/table_style.css" type="text/css">
  <script>
  var formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'VND',
  currencyDisplay: 'code',
  });
  </script>
  <script type="text/javascript">
    function AddOrderRow() {
      var pName = document.getElementById('selected-product').value;
      if(isProductInOrder(pName)){
        alert(pName + "already exists in order!");
        return;
      }

      var pPrice = parseFloat(document.getElementById('product_price').value);
      if(isNaN(pPrice) || pPrice <= 0){
        alert("Invalid sell price!");
        return;
      }

      var pQuantity = parseInt(document.getElementById('product_quantity').value);
      if(isNaN(pQuantity) || pQuantity <= 0){
        alert("Invalid sell quantity!");
        return;
      }

      var pSubTotal = pPrice * pQuantity;

      var tablebody = document.getElementById('order_table').getElementsByTagName("TBODY")[0];
      var newRow = document.createElement("TR");
      tablebody.appendChild(newRow);

      var cell1 = document.createElement("TD");
      var cell2 = document.createElement("TD");
      var cell3 = document.createElement("TD");
      var cell4 = document.createElement("TD");
      var cell5 = document.createElement("TD");

      newRow.appendChild(cell1);
      newRow.appendChild(cell2);
      newRow.appendChild(cell3);
      newRow.appendChild(cell4);
      newRow.appendChild(cell5);
    // Add some text to the new cells:

      var str_price = formatter.format(pPrice).replace("VND","") + " VND";
      var str_sub = formatter.format(pSubTotal).replace("VND","") + " VND";

      cell1.innerHTML = pName + "<input type=\"hidden\" name=\"product[]\" value=\"" + pName +"\">";
      cell2.innerHTML = str_price + "<input type=\"hidden\" name=\"price[]\" value=\"" + pPrice +"\">";
      cell3.innerHTML = pQuantity + "<input type=\"hidden\" name=\"quantity[]\" value=\"" + pQuantity +"\">";
      cell4.innerHTML = str_sub + "<input type=\"hidden\" name=\"sub_total[]\" value=\"" + pSubTotal +"\">";
      //cell4.innerHTML =pSubTotal;
      cell5.innerHTML = "<button type=\"button\" onclick=\"DeleteRow(this)\">Delete</button>";
      calculateTotal();
}

function isProductInOrder(selectedProduct){
  var order_table = document.getElementById('order_table');

//gets rows of table
  var rowLength = order_table.rows.length;
  var cells;
  var cellVal;
//loops through rows
  for (i = 1; i < rowLength; i++){
     //gets cells of current row
     cells = order_table.rows.item(i).cells;
     // get sub total cell info here
     cellVal = cells.item(0).getElementsByTagName('INPUT')[0].value;
     if(selectedProduct == cellVal)
        return true;
  }
        return false;
}

function DeleteRow(o) {
     //no clue what to put here?
     var p=o.parentNode.parentNode;
     p.parentNode.removeChild(p);
     calculateTotal();
    }

    function calculateTotal(){
         var order_table = document.getElementById('order_table');

      //gets rows of table
         var rowLength = order_table.rows.length;
         var total = 0;
         var cellVal;
         var cells;
      //loops through rows
         for (i = 1; i < rowLength; i++){
            //gets cells of current row
            cells = order_table.rows.item(i).cells;
            // get sub total cell info here
            cellVal = cells.item(3).getElementsByTagName('INPUT')[0].value;


            total += parseFloat(cellVal);
         }
         document.getElementById('ot').value = total;

         total = formatter.format(total).replace("VND","");
         document.getElementById('order_total').innerHTML = total;

    }

    function isEmpty(id){
      if(document.getElementById(id).value == "")
        return true;
      else
        return false;
    }

    function isTableEmpty(id){
      if(document.getElementById(id).rows.length == 1)
        return true;
      else
        return false;
    }

    function isFullInfo(){
       if(isEmpty('first_name') || isEmpty('last_name') || isEmpty('email') || isEmpty('address')){
         alert("Customer information can not be empty!");
         return false;
       }
       else if (isTableEmpty('order_table')) {
         alert("Order detail is empty!");
         return false;
       }
       else{
         return true;
       }
    }
    function SubmitOrder(){
      if(!isFullInfo())
        return;
      else if(confirm("Are you sure want to submit the order?"))
        document.getElementById("order_form").submit();
    }

 </script>
 <script>
 var formatter = new Intl.NumberFormat('en-US', {
   style: 'currency',
   currency: 'VND',
   currencyDisplay: 'code',
   });
</script>
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

  #button_area{
    float:right;
    text-align: right;
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

      <li><a href="profile.php">Profile</a></li>
      <li><a class="active" href="new-order.php">New Order</a></li>
      <li><a href="view-order.php">View Order List</a></li>
      <li><a href="product.php">Product List</a></li>

    </ul>

  </nav>

  <!---MAIN--------------------------------------->
  <main>
    <h1>Create new order</h1>
    <form action="new-order.php" method="post" id="order_form">
      <h3>Customer information</h3>
      <div id=cus_info>
        <div id=cus_left>
          First name <input type="text" name="first_name" id="first_name" size="25" >
            <br>
            <br>
             Email <input type="email" name="email" id="email" size="25">
             <br>
             <br>
             Address<br>
             <textarea name="address" rows="8" cols="45" id="address"></textarea>
        </div>
        <div id=cus_right>
        Last name &nbsp;&nbsp;
        <input type="text" style="float:right;" name="last_name" id="last_name">

        </div>


      </div>

      <br>
      <br>
      <div id="pro_ord_info">
        Product &nbsp;
        <select id="selected-product">
          <?php
          $category_sql = "SELECT CategoryName FROM categories ORDER BY CategoryName";

          if($category_result = mysqli_query($db,$category_sql)){
            // Fetch one and one row
            while ($row = mysqli_fetch_row($category_result)) {
                echo "<option disabled>------" . $row[0] . "------</option>";

                  $product_sql = "SELECT ProductName, ProductPrice FROM products
                  WHERE CategoryID =
                  (SELECT CategoryID FROM categories WHERE CategoryName = '$row[0]')
                  ORDER BY ProductName" ;
                  if($product_result = mysqli_query($db,$product_sql)){
                    // Fetch one and one row

                    while ($product = mysqli_fetch_row($product_result)) {
                        echo "<option value=\"$product[0]\">"  ;
                        echo "<column>" . $product[0] . "</column>";
                        echo "<column> | " . number_format($product[1]) . " VND</column>";
                        echo "</option>";
                      }
                      mysqli_free_result($product_result);
                    }

            }

            // Free result set
            mysqli_free_result($category_result);
          }

          mysqli_close($db);
            ?>
        </select>

        &nbsp;Unit Price&nbsp;  <input type="text" id="product_price" value="" size="9">
        &nbsp;Quantiy &nbsp; <input type="number" id="product_quantity" min="1" value="1" max="99">

        <br>
        <br>
        Order Date:<label><?php echo " " . date("Y-m-d"); ?></label>
        <input type="hidden" name="order_date" value="<?php echo date("Y-m-d"); ?>">
        <div id="button_area">
          <button id="button_add" type="button" onclick="AddOrderRow()">Add Record</button>
          <button type="button" onclick="SubmitOrder()">Submit Order</button>
          <br>
          <br>

          Order Total:<label id="order_total">&nbsp;0</label>&nbsp;VND
          <input type="hidden" name="order_total" id="ot" value="">
        </div>


      </div>

      <br>

      <br>

      <table id="order_table" name="order_table" class="MyTable">
        <thead>
          <th>Product</th>
          <th>Price</th>
          <th>Quantiy</th>
          <th>Sub Total</th>
          <th>Option</th>
        </thead>

        <tbody>
        </tbody>
        </table>
          <?php echo $error; ?>
    </form>

  </main>
</div>

</body>
</html>
