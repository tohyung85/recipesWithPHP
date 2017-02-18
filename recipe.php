<?php  
  require_once('connectvars.php');
  require_once('recipe_app_functions.php');
  use \RecipeAppFunctions\Functions;

  Functions::add_header(); // create HTML header

  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
  or die('Error connecting to database');

  $recipe_id = $_GET['recipe_id'];

  $query = $dbc->prepare('SELECT * FROM recipesteps WHERE recipe_id=? ORDER BY step_order ASC');
  $query->bind_param('d', $recipe_id);
  $query->execute();
  $result=$query->get_result();

  $order_display = 1;
  $last_order = 0;
  echo '<ul style="list-style:none;" id="sortable" data-recipe="'.$recipe_id.'">';
  while($row = $result->fetch_assoc()) { // Display existing steps
    if($row['step']) {
      echo "<li id=step_id-" . $row['id'] . "><span class='order_display'>" . $order_display++ . "</span>. " .$row['step'] . "   ";
      echo "<a href='step.php?recipe_id=". $row['recipe_id'] . "&amp;next_order=" . $row['step_order'] . "&amp;id=" . $row['id'] ."&amp;op=addstep'>Edit</a></li>";
    } else {
      echo "<li id=step_id-" . $row['id'] . "><img src='" . $row['photo'] . "' alt='photo' height=100 width=200 />   ";
      echo "<a href='step.php?recipe_id=" . $row['recipe_id'] . "&amp;next_order=" . $row['step_order'] . "&amp;id=" . $row['id'] ."&amp;op=addphoto'>Change</a></li>";
    }    
    $last_order = $row['step_order'];
  }
  echo '</ul>';

  $last_order++;

  echo '<a href="step.php?recipe_id=' . $recipe_id . '&amp;next_order=' . $last_order . '&amp;op=addstep">Add Step  </a>';
  echo '<a href="step.php?recipe_id=' . $recipe_id . '&amp;next_order=' . $last_order . '&amp;op=addphoto">Add Photo</a></br>';

  echo '<a href="index.php">See all Recipes</a>';

  if(isset($_POST['step_id'])) { // For updating of recipe steps order - done via ajax from js file
    $i = 1;
    foreach($_POST['step_id'] as $id) {
      $query = $dbc->prepare('UPDATE recipesteps SET step_order=? WHERE id=?');
      $order = $i++; // required to create a reference for bind params
      $query->bind_param('ss', $order, $id); // arguments have to be passed by reference instead of value
      $result = $query->execute();
    }
  }
  mysqli_close($dbc);

  Functions::add_footer();
?>