
<body>
    <?php
    require_once(__DIR__ . "/../../partials/nav.php");
    if (!is_logged_in()) {
        flash("You must be logged in to see this!");
        die(header("Location: login.php"));
    }
    $name = $_POST['fullname'];
    $address = $_POST['address'] . ", " . $_POST['city'] . ", " . $_POST['state'] . ", " . $_POST['zip'];
    $payment = $_POST['pay'];
    $db = getDB();
    $user_id = get_user_id();
    $query3 = "SELECT * FROM Products JOIN cart on cart.product_id = Products.id and user_id = $user_id and desired_quantity > 0";
    $products = [];
    $stmt3 = $db->prepare($query3);
    $status = true;
    try {
        $stmt3->execute();
        $p = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        if ($p) {
            $products = $p;
        }
    } catch (PDOException $e) {
    }
    foreach ($products as $p) {
        if (se($p, "unit_cost", "", false) == se($p, "unit_price", "", false)) {
            $status = true;
        } else {
            $status = false;
            break;
        }
        // check cart desired_quanity < stock
        if (se($p, "desired_quantity", "", false) > se($p, "stock", "", false)) {
            $prodName = (string) se($p, "name", "", false);
            $quantity = (string) se($p, "desired_quantity", "", false);
            $quantity2 = (string) se($p, "stock", "", false);
            $errMSG = "You are trying to order '" . $prodName . "' but we currently do not have enough stock for the desired quantity of " . $quantity . " We only have " . $quantity2;
            flash("$errMSG");
            flash("Please go back to your cart and try again");

            die(header("Location: cart.php"));
            $status = false;
        }
    }

    if ($status) {
        $db = getDB();
        $user_id = get_user_id();
        $query = "SELECT * FROM cart WHERE user_id = $user_id";
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
        //CALC TOTAL

        $total = 0;
        foreach ($results as $item) {
            $x = $item['unit_cost'];
            $y = $item['desired_quantity'];
            $subtotal = $x * $y;
            $total = $total + $subtotal;
        }
        $total2 = (int)$total;

        //INSERT INTO orders TABLE
        $query2 = "INSERT INTO orders (`user_id`, `total_price`, `address`, `payment_method`) VALUES ('$user_id', '$total2', '$address', '$payment')";
        $stmt2 = $db->prepare($query2);
        $res = [];
        $lastID;
        try {
            $stmt2->execute();
            $lastID = $db->lastinsertid(); // Gets Last Order ID
        } catch (PDOException $e) {
        }
        // copy cart details into the orderItems tables with the ORDER ID
        foreach ($results as $item) {
            $prodID = (int) se($item, "product_id", "", false);
            $cartQuant = (int) se($item, "desired_quantity", "", false);
            $price = (int) se($item, "unit_cost", "", false);
            $query4 = "INSERT INTO orderItems (order_id, product_id, quantity, unit_cost) VALUES ('$lastID','$prodID','$cartQuant','$price')";

            $stmt4 = $db->prepare($query4);
            try {
                $stmt4->execute();
            } catch (PDOException $e) {
            }
        }

        $query5 = "SELECT * FROM orderItems WHERE order_id = $lastID";
        $stmt5 = $db->prepare($query5);
        $orderItems = [];
        try {
            $stmt5->execute();
            $r = $stmt5->fetchAll(PDO::FETCH_ASSOC);
            if ($r) {
                $orderItems = $r;
            }
        } catch (PDOException $e) {
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
        foreach ($orderItems as $oitem) {
            $prodID3 = (int) se($oitem, "product_id", "", false);
            $cartQuant3 = (int) se($oitem, "quantity", "", false);
            $query5 = "UPDATE Products SET stock = stock-$cartQuant3 WHERE id = $prodID3 && visibility > 0";
            $stmt5 = $db->prepare($query5);
            try {
                $stmt5->execute();
            } catch (PDOException $e) {
            }
        }
    
        // clear user's cart 
        $userid = get_user_id();
        $query6 = "DELETE FROM cart WHERE user_id = $userid";
        $stmt6 = $db->prepare($query6);
        try {
            $stmt6->execute();
        } catch (PDOException $e) {
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
        flash("THANK YOU FOR YOUR PURCHASE!");
    }

    die(header("Location: order_details.php?id=$lastID"));
    ?>


<?php  ?>
</body>

<?php require(__DIR__ . "/../../partials/footer.php"); ?>