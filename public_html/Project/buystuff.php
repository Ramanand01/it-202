<?php
require_once(__DIR__ . "/../../partials/nav.php");
?>

<html>
<!-- remember to style this later -->

<body>
    <?php
    $db = getDB();
    $userId = get_user_id();
    $query = "SELECT * FROM Products JOIN cart on cart.product_id = Products.id and user_id = $userId and desired_quantity > 0";
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

    <h1 style="text-align: center;">Checkout</h1>
    <div class="row">
        <div class="col-75">
            <div class="container">
                <form action="confirmed.php" method="post">
                    <div class="row">
                        <div class="col-50">
                            <h3>Billing Address</h3>
                            <label for="fname"><i class="fa fa-user"></i> Full Name</label>
                            <input type="text" id="fullname" name="fullname" placeholder="John M. Doe">
                            <label for="email"><i class="fa fa-envelope"></i> Email</label>
                            <input type="text" id="email" name="email" placeholder="john@example.com">
                            <label for="adr"><i class="fa fa-address-card-o"></i> Address</label>
                            <input type="text" id="address" name="address" placeholder="542 W. 15th Street">
                            <label for="city"><i class="fa fa-institution"></i> City</label>
                            <input type="text" id="city" name="city" placeholder="New York">

                            <div class="row">
                                <div class="col-50">
                                    <label for="state">State</label>
                                    <input type="text" id="state" name="state" placeholder="NY">
                                </div>
                                <div class="col-50">
                                    <label for="zip">Zip</label>
                                    <input type="text" id="zip" name="zip" placeholder="10001">
                                </div>
                            </div>
                        </div>

                        <div class="col-50">
                            <h3>Payment</h3>
                            <label for="fname">Accepted Cards</label>
                            <div class="icon-container">
                                <i class="fa fa-cc-visa" style="color:navy;"></i>
                                <i class="fa fa-cc-amex" style="color:blue;"></i>
                                <i class="fa fa-cc-mastercard" style="color:red;"></i>
                                <i class="fa fa-cc-discover" style="color:orange;"></i>
                            </div>
                            <label for="pay">Form of Payment</label>
                            <select class = "form-control" name="pay" id="pay">
                                <option value="1">VISA</option>
                                <option value="2">AMEX</option>
                                <option value="3">MASTERCARD</option>
                                <option value="4">CASH</option>
                                
                            </select>
                                <div class="row">
                                    <div class="col-50">
                                    </div>
                                    <div class="col-50">
                                    </div>
                                </div>
                        </div>

                    </div>
                    <a></a> 
                    <input type="submit" value="Place Order" class="place_order">
                </form>
            </div>
        </div>
        <div class="col-25">
            <div class="container" style="padding: 2px;">
                <h4>Cart <span class="price" style="color:black"><i class="fa fa-shopping-cart"></i></span></h4>
                <?php
                
                $total = 0;
                
                foreach ($results as $item) :
                    $x = $item['unit_price'];
                    $y = $item['desired_quantity'];
                    $subtotal = $x * $y;
                ?>

                <p><a href="product_details.php?id=<?php se($item, "product_id"); ?>"><?php se($item, "name"); ?></a> <span class="price">$<?php echo deci($subtotal); ?> </span></p>
                <?php $total = $total + $subtotal;
                endforeach ?>
                <p>Total <span class="price" style="color:black"><b>$<?php echo deci($total); ?></b></span></p>

            </div>
        </div>
    </div>

</body>
</html>