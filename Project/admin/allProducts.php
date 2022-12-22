<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!is_logged_in()) {
    flash("You must be logged in to view this page!", "warning");
    die(header("Location: $BASE_PATH" . "login.php"));
}
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "home.php"));
}


$results = [];
$db = getDB();
//Sort and Filters
$col = se($_GET, "col", "created", false);
//allowed list
if (!in_array($col, ["stock", "created", "unit_price"])) {
    $col = "unit_price"; //default value, prevent sql injection
}
$order = se($_GET, "order", "asc", false);
//allowed list
if (!in_array($order, ["asc", "desc"])) {
    $order = "asc"; //default value, prevent sql injection
}
$name = se($_GET, "name", "", false);
$base_query = "SELECT * FROM Products ";
$total_query = "SELECT count(1) as total FROM Products";
if (isset($_GET['quantity'])) {
    $quant = $_GET['quantity'];
    $query = " WHERE stock <= $quant";
} else {
    $query = " WHERE 1=1";
}

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
        $results = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}


?>

<html>

<body>
    <h3 align="center">All Products</h3>
    <?php if ($results && count($results) == 0) : ?>
        <p>Nothing To Display Here</p>
    <?php else : ?>
        <form class="row row-cols-auto g-3 align-items-center">
            <div class="col">
                <div class="input-group">
                    <div class="input-group-text">Search</div>
                    <input class="form-control" name="name" value="<?php se($name); ?>" />
                </div>
                <div class="input-group">
                    <div class="input-group-text">Quantity</div>
                    <input class="form-control" name="quantity" value="<?php se($name); ?>" />
                </div>
            </div>
            <div class="col">
                <div class="input-group">
                    <div class="input-group-text">Sort</div>
                    <select class="form-control" name="col" value="<?php se($col); ?>">
                        <option value="stock">Stock</option>
                        <option value="category">Category</option>
                        <option value="unit_price">Price</option>
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
        <br>
        <table class="t1" style="width:50%" ;>
            <tr className="headings">
                <th className="heading" style="text-align: center;">Id</th>
                <th className="heading" style="text-align: center;">Name</th>
                <th className="heading" style="text-align: center;">Category</th></b>
                <th className="heading" style="text-align: center;">Stock</th></b>
                <th className="heading" style="text-align: center;">Unit Price</th></b>
                <th className="heading" style="text-align: center;">Date Created</th></b>
                <th className="heading" style="text-align: center;">Visibility</th></b>
                <th className="heading" style="text-align: center;">Edit Product</th></b>
            </tr> <?php foreach ($results as $item) : ?>
                <tr className="policy">
                    <th><?php se($item, "id") ?></th>
                    <th><?php se($item, "name") ?></th>
                    <th><?php se($item, "category") ?></th>
                    <th><?php se($item, "stock"); ?></th>
                    <th>$<?php echo deci(se($item, "unit_price", "", false)) ?></th>
                    <th><?php se($item, "created") ?></th>
                    <th><?php se($item, "visibility") ?></th>

                    <th>
                        <a href="edit_item.php?id=<?php se($item, "id"); ?>" class="orderLink"> EDIT: <?php se($item, "name"); ?></a>

                    </th>

                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>


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

</html>