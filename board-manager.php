<?php
  include("config.php");

  if (!isset($_POST['agency'])) {
    $agency = 'all';
    $order_sql = "SELECT orders.OrderID,
	  agencies.AgencyName,
    customers.FirstName, customers.LastName,
    orders.OrderDate,
    orders.OrderTotal
    FROM orders
    INNER JOIN
    customers ON orders.CustomerID = customers.CustomerID
    INNER JOIN
    agencies ON orders.AgencyID = agencies.AgencyID
    ORDER BY orders.OrderID DESC";

  }
  else if ($_POST['agency'] == 'all') {
    $agency = 'all';
    $order_sql = "SELECT orders.OrderID,
	  agencies.AgencyName,
    customers.FirstName, customers.LastName,
    orders.OrderDate,
    orders.OrderTotal
    FROM orders
    INNER JOIN
    customers ON orders.CustomerID = customers.CustomerID
    INNER JOIN
    agencies ON orders.AgencyID = agencies.AgencyID
    ORDER BY orders.OrderID DESC";
  }
  else {
    $agency = $_POST['agency'];
    $order_sql = "SELECT orders.OrderID,
	  agencies.AgencyName,
    customers.FirstName, customers.LastName,
    orders.OrderDate,
    orders.OrderTotal
    FROM orders
    INNER JOIN
    customers ON orders.CustomerID = customers.CustomerID
    INNER JOIN
    agencies ON orders.AgencyID = agencies.AgencyID
    WHERE orders.AgencyID =
    (SELECT AgencyID FROM agencies WHERE AgencyName = '$agency')
    ORDER BY orders.OrderID DESC";
  }
?>

<!DOCTYPE html>
<html>
<head>

  <title>Order List</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="css/mystyle.css" type="text/css">
  <link rel="stylesheet" href="css/table_style.css" type="text/css">
  <script>
  function submitform()
  {
    document.getElementById("Agency_form").submit();
  }
</script>
</head>

<body>

<div id="container">


  <main>
    <h1 >Order List</h1>
    <form id="Agency_form" class="MySelect" action="" method="post">
      Agency:
      <select name="agency" onchange="submitform()">
        <option value="all">All</option>
        <?php
        $sql = "SELECT AgencyName FROM agencies ORDER BY AgencyName";

        if($result = mysqli_query($db,$sql)){
          // Fetch one and one row

          while ($row = mysqli_fetch_row($result)) {
              if($row[0] == $agency){
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

    <TABLE class="MyTable">
    <TR>
      <TH>Order ID</TH>
      <TH>Agency</TH>
      <TH>Customer</TH>
      <TH>Order Date</TH>
      <TH>Order Total</TH>
      <TH>Option</TH>
    </TR>
    <?php
    if($result = mysqli_query($db,$order_sql)){
      // Fetch one and one row

      while ($row = mysqli_fetch_row($result)) {

          echo "<TR>";
          echo "<TD>" . $row[0] . "</TD>";
          echo "<TD>" . $row[1] . "</TD>";
          echo "<TD>" . $row[2] . " " . $row[3] . "</TD>";
          echo "<TD>" . $row[4] . "</TD>";
          echo "<TD>" . number_format($row[5]) . "</TD>";
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
