<?php
// order details can be reached from confirmation page
require(__DIR__ . "/../../partials/nav.php");

$userId = get_user_id();
$db = getDB();
$query = "SELECT * FROM orders WHERE user_id = $userId";
$stmt = $db->prepare($query);
$orders = [];
try {
    $stmt->execute();
    $last_id = $_GET['id'];
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $orders = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}

// ------------------------------------------------------------
$query2 = "SELECT * FROM orderItems JOIN Products ON orderItems.product_id = Products.id WHERE order_id = $last_id and quantity > 0;";
$stmt2 = $db->prepare($query2);
$orderItems = [];
try {
    $stmt2->execute();
    $r = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $orderItems = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}
flash("THANK YOU FOR YOUR ORDER");
?>


<html>
<body>
    <h2> Order Details</h2>
    <table style="width:100%">
        <tr>
            <th style="font-size: 20px; color: red;">Products</th>
            <th style="font-size: 20px; color: red;">Quantity</th>
            <th style="font-size: 20px; color: red;">Product Price</th></b>
        </tr>
        
        <?php foreach ($orderItems as $item): ?>           
           <tr>
            <th><?php se($item,"name")?></th>
            <th>x<?php se($item,"quantity")?></th>
            <th>$<?php 
                $price = (int)se($item,"unit_price","",false);
                echo($price) /100;?>
                </th>
        </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
<?php
require_once(__DIR__ . "/../../partials/footer.php");
?>