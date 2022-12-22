<?php
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to view this page!", "warning");
    die(header("Location:login.php"));
}
$name = se($_GET, "name", "", false);

$userId = get_user_id();
$db = getDB();
$query = "SELECT * FROM orders WHERE `user_id` = $userId";
$stmt = $db->prepare($query);
$orders = [];
$db = getDB();
//Sort and Filters
$col = se($_GET, "col", "created", false);
//allowed list
if (!in_array($col, ["created", "total_price"])) {
    $col = "total_price"; //default value, prevent sql injection
}
$order = se($_GET, "order", "asc", false);
//allowed list
if (!in_array($order, ["asc", "desc"])) {
    $order = "asc"; //default value, prevent sql injection
}
$name = se($_GET, "name", "", false);
$userId = get_user_id();
$base_query = "SELECT * FROM orders ";
$total_query = "SELECT count(1) as total FROM orders";
$query = " WHERE `user_id` = $userId";
$params = [];
if (!empty($name)) {
    $query .= " AND name like :name";
    $params[":name"] = "%$name%";
}
if (!empty($col) && !empty($order)) {
    $query .= " ORDER BY $col $order";
}
$per_page = 10;
paginate($total_query . $query, $params, $per_page);
$page = se($_GET, "page", 1, false);
$offset = ($page - 1) * $per_page;
$query .= " LIMIT :offset, :count";
$params[":offset"] = $offset;
$params[":count"] = $per_page;
//get the records
$stmt = $db->prepare($base_query . $query);
foreach ($params as $key => $value) {
    $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindValue($key, $value, $type);
}
$params = null;

try {
    $stmt->execute($params);
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $orders = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}
try {
    $stmt->execute();
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $orders = $r;
    }
} catch (PDOException $e) {
    flash("Something Broke Here");
}
?>
<h3 align="center"> Your Orders</h3>
<?php if ($orders && count($orders) == 0) : ?>
    <p>Nothing to show here!</p>
<?php else : ?>
    <form class="row row-cols-auto g-3 align-items-center">
        <div class="col">
            <div class="input-group">
                <div class="input-group-text">Sort</div>
                <select class="form-control" name="col" value="<?php se($col); ?>">
                    <option value="created">Date Range</option>
                    <option value="category">Category</option>
                    <option value="total_price">Price</option>
                </select>
                <script>
                    document.forms[0].col.value = "<?php se($col); ?>";
                </script>
                <select class="form-control" name="order" value="<?php se($order); ?>">
                    <option value="asc">Up</option>
                    <option value="desc">Down</option>
                </select>
                <script>
                    document.forms[0].order.value = "<?php se($order); ?>";
                </script>
            </div>
        </div>
        <div class="col">
            <div class="input-group">
                <input type="submit" class="btn btn-primary" value="Apply" />
            </div>
        </div>
    </form>
    <table class="userHist" style="width:50%" ;>
        <tr>
            <th>Id</th>
            <th>User ID</th>
            <th>Address</th>
            <th>Payment</th>
            <th>Date Purchased</th>
            <th>Details</th>
            <th>Order Total</th>
        </tr>
        <?php foreach ($orders as $item) : ?>
            <tr>
                <th><?php se($item, "id") ?></th>
                <th><?php se($item, "user_id") ?></th>
                <th><?php se($item, "address") ?></th>
                <th><?php se($item, "payment_method") ?></th>
                <th><?php se($item, "created"); ?></th>
                <th>
                    <a href="<?php
                                $full = (string) get_url("order_details.php") . "?id=" . (int) se($item, "id", "", false);
                                echo $full; ?>" class="orderLink">Order Details</a>

                </th>
                <th>
                    $<?php
                        $price = (int)se($item,"total_price","",false);
                        echo($price / 100);
                    ?>
                </th>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<nav aria-label="Generic Pagination">
    <ul class="pagination">
        <li class="page-item <?php echo ($page - 1) < 1 ? "disabled" : ""; ?>">
            <a class="page-link" href="?<?php se(persistQueryString($page - 1)); ?>" tabindex="-1">Previous</a>
        </li>
        <?php for ($i = 0; $i < $total_pages; $i++) : ?>
            <li class="page-item <?php echo ($page - 1) == $i ? "active" : ""; ?>"><a class="page-link" href="?<?php se(persistQueryString($i + 1)); ?>"><?php echo ($i + 1); ?></a></li>
        <?php endfor; ?>
        <li class="page-item <?php echo ($page) >= $total_pages ? "disabled" : ""; ?>">
            <a class="page-link" href="?<?php se(persistQueryString($page + 1)); ?>">Next</a>
        </li>
    </ul>
</nav>
<!-- Style here -->
<style>
    .userHist {
        margin-left: auto;
        margin-right: auto;
    }

    td,
    th {
        border: 1px solid black;
        padding: 5px 15px;
        justify-items: center;
        color: #543855;
    }
</style>