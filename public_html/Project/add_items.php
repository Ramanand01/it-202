<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}
if (isset($_POST["name"]) && isset($_POST["description"]) && isset($_POST["category"])&& isset($_POST["stock"]) && isset($_POST["visibility"])) {
    $name = se($_POST, "name", "", false);
    $desc = se($_POST, "description", "", false);
    $category = se($_POST, "category", "", false);
    $stock = se($_POST, "stock", "", false);
    $visibility = se($_POST, "visibility", "", false);
    $price = se($_POST, "price", "", false);
    if (empty($name)) {
        flash("Name is required", "warning");
    } else {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO products (name, description, category,stock,visibility,created,modified,unit_price) VALUES(:name, :desc, :category,:stock,:visibility,NOW(),NOW(),:price)");
        try {
            $stmt->execute([":name" => $name, ":desc" => $desc,':category'=>$category,':stock'=>$stock,':visibility'=>$visibility,':price'=>$price]);
            flash("Successfully created Product $name!", "success");
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                flash("A role with this name already exists, please try another", "warning");
            } else {
                flash("Unknown error occurred, please try again", "danger");
                error_log(var_export($e->errorInfo, true));
            }
        }
    }
}
?>
<h1>Create Product</h1>
<form method="POST">
    <div class="form-group">
        <label for="name">Name</label>
        <input id="name" name="name" required class="form-control" placeholder="Enter Name" />
    </div>
    <div class="form-group">
        <label for="d">Description</label>
        <textarea name="description" id="d" class="form-control" placeholder="Enter Description"></textarea>
    </div>
    <div class="form-group">
        <label for="d">Category</label>
       <input type="text" name="category" class="form-control" placeholder="Enter category">
    </div>
    <div class="form-group">
        <label for="d">stock</label>
      <input type="number" name="stock" min="1" class="form-control" placeholder="Enter stock">
    </div>
     <div class="form-group">
        <label for="d">Unit Price</label>
      <input type="number" name="price" min="1" class="form-control" placeholder="Enter Price">
    </div>
    <div class="form-group">
        <label for="d">Visbility</label>
        <select class="form-control" name="visibility">
             <option value="1">Yes</option>
             <option value="0">No</option>
        </select>
    </div>
    <div class="form-group">
    <input type="submit" value="Create Product" class="btn btn-outline-primary" />
</div>
</form>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>