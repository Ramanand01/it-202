<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    flash("You must be logged in to see this!");
    die(header("Location: login.php"));
}
?>
<?php
$product_ids = array();

//global variables
$user = get_user_id();
$db = getDB();

//checking state of the URL
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "add":
            $item_id = $_GET['id'];
            $results = add_to_cart_func();
            foreach ($results as $item) :
                if (se($item, "id", "", false) == $item_id) {
                    $cost = se($item, "unit_price", "", false);
                    $query2 = "INSERT INTO cart (product_id, user_id, desired_quantity, unit_cost) VALUES ('$item_id', '$user', '1','$cost') ON duplicate KEY UPDATE desired_quantity = desired_quantity + 1";
                    $stmt2 = $db->prepare($query2);
                    try {
                        $stmt2->execute();
                    } catch (PDOException $e) {
                        //flash("<pre>" . var_export($e, true) . "</pre>");
                    }
                    $co = se($item, "desired_quantity");
                    flash($co);
                }
            endforeach;
            flash("Successfully Added To Cart");
            break;
        case "remove":
            $item_id = $_GET['id'];
            $query = "UPDATE cart SET desired_quantity = desired_quantity - 1 WHERE id= $item_id and desired_quantity > 0";
            $stmt = $db->prepare($query);
            try {
                $stmt->execute();
            } catch (PDOException $e) {
                flash("<pre>" . var_export($e, true) . "</pre>");
            }
            //flash("Item Removed");
            break;
        case "empty":
            $user = get_user_id();
            $query = "DELETE FROM cart WHERE user_id = $user";
            $stmt = $db->prepare($query);
            //$results = [];
            try {
                $stmt->execute();
            } catch (PDOException $e) {
                flash("<pre>" . var_export($e, true) . "</pre>");
            }
            //flash("Successfully Emptied Cart");
            break;
    }
}

$query = "SELECT * FROM Products JOIN cart on cart.product_id = Products.id and user_id = $user and desired_quantity > 0";
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute();
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart :: Checkout</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/vendors/@fortawesome/fontawesome-free/css/all.min.css">

</head>
</body>

<div class="container-fluid md-5">
    <h4>My Cart</h4>
    <?php foreach ($results as $item) : ?>
        <div class="row px-5 py-2">
            <div class="col-md-7">
                <div class="card px-3 mb-5">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                            </div>
                            <div class="col-md-4">
                                <h4><?php se($item, "name"); ?></h4>
                                <h6><b>Description: </b><?php se($item, "description"); ?></h6>
                                <p></p>
                                <span class="price"> <b>$<?php $num = (int)se($item, "unit_price", "", false);
                                                            echo deci($num) ?></b></span>
                                </h5>
                            </div>

                            <div class="col-md-4 py-5 px-5">
                                <a href="cart.php?action=remove&id=<?php se($item, "id"); ?>">
                                    <div class="btn btn-danger">Remove</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div><?php endforeach ?>
        <div class="col-md-5 py-5 px5">

            <div class="card">

                <div class="card-body">
                    <b>Price Details</b>
                    <hr>
                    <?php
                    $user = get_user_id();
                    $results = [];
                    $db = getDB();
                    $query = "SELECT * FROM Products JOIN cart on cart.product_id = Products.id and user_id = $user and desired_quantity > 0";
                    $stmt = $db->prepare($query);
                    $results = [];
                    try {
                        $stmt->execute();
                        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($r) {
                            $results = $r;
                        }
                    } catch (PDOException $e) {
                        flash("<pre>" . var_export($e, true) . "</pre>");
                    }
                    $total = 0; ?>
                    <?php foreach ($results as $item) : ?>
                        <tr>
                            <?php
                            $x = $item['unit_cost'];
                            $y = $item['desired_quantity'];
                            $subtotal = $x * $y;
                            ?>
                            <td> Name: <?php se($item, 'name'); ?></td>
                            <td> Quantity: <?php se($item, 'desired_quantity'); ?></td><br></br>
                            <!-- <td>$<?php # echo deci($subtotal); ?></td> -->
                            <?php
                            $total = $total + $subtotal;
                            ?>
                        </tr>

                    <?php endforeach; ?>
                    <div class="row">
                        
                        <div class="col-md-5">
                            <!-- <h5 class="float-right">Subtotal: 
                                <?php #
                                #if ($total == 0) {
                                #    echo "0";
                                #} else {
                                #    echo deci($subtotal);
                                #?>
                            </h5> -->
                        </div>
                        <!--- Price section --->
                        <!--- Total price section --->

                        <div class="row">
                            <div class="col-md-5 py-4">
                                <h5><b>Amount Payable</b></h5>
                            </div>
                            <div class="col-md-5 py-4">
                                <h5 class="float-right"><b>$<?php echo $total / 100; ?></b></h5>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!--<a href="sales_order.php"> -->
        <div class="col-md-4 py-5 px-5">
            <a href="cart.php?action=empty">
                <div class="btn btn-danger btn-block px-5">Empty Cart</div>
            </a>
        </div>
        <a class="btn btn-warning btn-block px-5" href = "buystuff.php">Continue Checkout</a>
        </div>
        <!--- Total Price section ends here --->
</div>
</div>
</body>

</html>
<?php
    require_once(__DIR__."/../../partials/footer.php");
?>