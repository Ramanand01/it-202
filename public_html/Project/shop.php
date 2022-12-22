<?php
require(__DIR__ . "/../../partials/nav.php");
$name = se($_GET, "name", "", false);
$results = [];
$db = getDB();
//Sort and Filters
$col = se($_GET, "col", "cost", false);
//allowed list
if (!in_array($col, ["unit_price", "category", "name","rating"])) {
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
$query = " WHERE visibility > 0";
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<form class="row row-cols-auto g-3 align-items-center">
    <div class="col">
        <div class="input-group">
            <div class="input-group-text">Search</div>
            <input class="form-control" name="name" value="<?php se($name); ?>" />
        </div>
    </div>
    <div class="col">
        <div class="input-group">
            <div class="input-group-text">Sort</div>
            <!-- make sure these match the in_array filter above-->
            <select class="form-control" name="col" value="<?php se($col); ?>">
                <option value="unit_price">Price</option>
                <option value="category">Category</option>
                <option value="rating">Rating</option>
            </select>
            <script>
                //quick fix to ensure proper value is selected since
                //value setting only works after the options are defined and php has the value set prior
                document.forms[0].col.value = "<?php se($col); ?>";
            </script>
            <select class="form-control" name="order" value="<?php se($order); ?>">
                <option value="asc">Up</option>
                <option value="desc">Down</option>
            </select>
            <script>
                //quick fix to ensure proper value is selected since
                //value setting only works after the options are defined and php has the value set prior
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


<div class="container-fluid">
    <div class="row row-cols-1 row-cols-md-5 g-4">
        <?php foreach ($results as $item) : ?>
            <div class="col">
                <div class="card 2 bg-light">
                    <div class="card-header">
                        <?php if (has_role("Admin")) : ?>
                            <a href="admin/edit_item.php?id=<?php se($item, "id"); ?>"> EDIT: <?php se($item, "name"); ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="body">
                        <h5 class="title"><a href="product_details.php?id=<?php se($item, "id"); ?>"><?php se($item, "name"); ?></a></h5>
                        <p class="desc"><?php se($item, "description"); ?></p>
                    </div>
                    <div class="footer">
                        Cost: $<?php
                                $item_cost = (int)se($item, "unit_price", "", false);
                                echo $item_cost / 100;
                                ?>
                        <br>
                        <button class="btn-primary"> <a href="cart.php?action=add&id=<?php se($item, "id"); ?>"> ADD TO CART </a></button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

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
<?php
require(__DIR__ . "/../../partials/footer.php");
?>