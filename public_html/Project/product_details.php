<?php
require(__DIR__ . "/../../partials/nav.php");

if (isset($_POST["submit"])) {
  $comment = $_POST['comment'];
  $rating = $_POST['rating'];
  $db = getDB();
$item_id = isset($_GET['id'])?$_GET['id']:"0";
  $user = get_user_id();
  $params = [":comment" => $comment, ":rating" => $rating, ":user_id" => get_user_id(), ":prod_id" => $item_id];

  $query2 = "INSERT INTO ratings (product_id, user_id, rating, comment) VALUES (:prod_id, :user_id, :rating, :comment)";
  $stmt2 = $db->prepare($query2);
  try {
    $stmt2->execute($params);
    flash("Review Submitted");
  } catch (PDOException $e) {
    flash("NOT WORKING");
  }
  // $stmt3 = $db->prepare("UPDATE products SET rating = (SELECT ROUND(AVG(rating),0) FROM ratings WHERE Products.id = ratings.product_id)");
  // try {
  //   $stmt3->execute();
  // } catch (PDOException $e) {
  //   flash("broke");
  // }
}

$item_id = isset($_GET['id'])?$_GET['id']:"0";
$results = [];
$db = getDB();
$stmt = $db->prepare("SELECT * FROM products WHERE stock > 0 && visibility > 0 AND visibility='1'LIMIT 50 "); // admins can't see because visibility checker but this shows how to create shop page for users
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
<div class="container-fluid mt-5">
  <div class="row row-cols-1 row-cols-md-7 g-4">
    <?php foreach ($results as $item) :
       if (se($item, "id", "", false) == $item_id) :
    ?>
        <div class="container">
          <div class="row">
            <div class="col-md-6">
              <h1><?php se($item, "name") ?></h1>
              <?php 

if (has_role("Admin")) {
   ?>
    <a class="edit_btn" href="admin/edit_item.php?id=<?php se($item, "id"); ?>"> EDIT: <?php se($item, "name"); ?></a>
   <?php
}
              ?>

              <p class="mb-2 text-muted text-uppercase small"><?php se($item, "category") ?></p>
              <p><span class="mr-1"><strong>Cost: $<?php
                                                    $item_cost = (int)se($item, "unit_price", "", false);
                                                    $num = deci($item_cost);
                                                    echo $num; ?></strong></span></p>
              <p>
                  Average Rating: <?php 
                    se($item, "rating");
                  ?> Stars
              </p>
              <p class="pt-1"><?php se($item, "description") ?></p>

              <div class="table-responsive">
                <table class="table table-lg table-borderless mb-0">
                  <tbody>
                    <th class="pl-0 w-25" scope="row"><strong>Delivery</strong></th>
                    <td>USA ONLY</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <hr>
              <a href="cart.php?action=add&id=<?php se($item, "id"); ?>" class="addLink"> ADD TO CART </a>
            </div>
          </div>
          <h2>LEAVE A REVIEW: </h2>
          <form method="POST">
            <fieldset>
              <span class="star-cb-group">
                <input type="radio" id="rating-1" name="rating" value="1" checked="checked" /><label for="rating-1">1</label>
                <input type="radio" id="rating-2" name="rating" value="2" /><label for="rating-2">2</label>
                <input type="radio" id="rating-3" name="rating" value="3" /><label for="rating-3">3</label>
                <input type="radio" id="rating-4" name="rating" value="4" /><label for="rating-4">4</label>
                <input type="radio" id="rating-5" name="rating" value="5" /><label for="rating-5">5</label>
              </span>
            </fieldset>
            <label for="comment">Add Review Here: </label><br>
            <textarea id="comment" name="comment" rows="4" cols="50"></textarea>
            <br><br>
            <input type="submit" value="Submit" name="submit">
          </form>
        <?php endif ?>
      <?php endforeach; ?>
        </div>
  </div>
  <div class="ratings">
    <?php
    $ratings = [];
    $db = getDB();
    $prod =isset($_GET['id'])?$_GET['id']:"0";
;
    $base_query = "SELECT * FROM ratings JOIN Users ON ratings.user_id = Users.id";
    $total_query = "SELECT count(1) as total FROM ratings";
    $query = " WHERE `product_id` = $prod";
    $params = [];
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
        $ratings = $r;
      }
    } catch (PDOException $e) {
      flash("<pre>" . var_export($e, true) . "</pre>");
    } ?>

    <table class="t1" style="width:50%" ;>
      <tr>
        <th style="font-size: 18px; color: black; text-transform: uppercase; text-align: center;">User</th></b>
        <th style="font-size: 18px; color: black; text-transform: uppercase; text-align: center;">Rating</th></b>
        <th style="font-size: 18px; color: black; text-transform: uppercase; text-align: center;">Comment</th></b>
        <th style="font-size: 18px; color: black; text-transform: uppercase; text-align: center;">Created</th></b>
      </tr> <?php foreach ($ratings as $item) : ?>
        <tr>
          <th><?php se($item, "username") ?></th>
          <th><?php se($item, "rating") ?></th>
          <th><?php se($item, "comment") ?></th>
          <th><?php se($item, "created") ?></th>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>




</div>
<br>
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
  table {
    margin-right: auto;
    margin-left: auto;
  }

  td,
  th {
    border: 1px solid black;
    padding: 5px 15px;
    justify-items: center;
    color: #543855;
  }
</style>
<?php
require(__DIR__ . "/../../partials/footer.php");
?>