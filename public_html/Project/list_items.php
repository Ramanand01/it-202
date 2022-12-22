<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("home.php")));
}
//handle the toggle first so select pulls fresh data
if (isset($_POST["role_id"])) {
    $role_id = se($_POST, "role_id", "", false);
    if (!empty($role_id)) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Roles SET is_active = !is_active WHERE id = :rid");
        try {
            $stmt->execute([":rid" => $role_id]);
            flash("Updated Role", "success");
        } catch (PDOException $e) {
            flash(var_export($e->errorInfo, true), "danger");
        }
    }
}
$query = "SELECT * from products";
$params = null;
// if (isset($_POST["role"])) {
//     $search = se($_POST, "role", "", false);
//     $query .= " WHERE name LIKE :role";
//     $params =  [":role" => "%$search%"];
// }
$query .= " ORDER BY modified desc LIMIT 10";
$db = getDB();
$stmt = $db->prepare($query);
$roles = [];
try {
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $roles = $results;
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

?>
<h1>List Products</h1>

<table class="table">
    <thead>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Category</th>
        <th>Stock</th>
        <th>Unit Price</th>
        <th>Visibility</th>
        <th>Action</th>
    </thead>
    <tbody>
        <?php if (empty($roles)) : ?>
            <tr>
                <td colspan="100%">No roles</td>
            </tr>
        <?php else : ?>
            <?php foreach ($roles as $role) : ?>
                <tr>
                    <td><?php se($role, "id"); ?></td>
                    <td><?php se($role, "name"); ?></td>
                    <td><?php se($role, "description"); ?></td>
                    <td><?php se($role, "category"); ?></td>
                    <td><?php se($role, "stock"); ?></td>
                    <td><?php se($role, "unit_price"); ?></td>
                    <td><?php echo (se($role, "visibility", 0, false) ? "yes" : "no"); ?></td>
                    <td>
                        <a href="edit_items.php?id=<?php se($role, "id");  ?>">Edit</a>
                        <a href="?delete=<?php se($role, "id");  ?>" onclick='return confirm("Are you sure to delete Product;")'>Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db = getDB();
 $stmt = $db->prepare("DELETE FROM products WHERE id='$id'");
 $stmt->execute();
 echo "
 <script>
window.location.href='list_items.php';
 </script>";

}
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>