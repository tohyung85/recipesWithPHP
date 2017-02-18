<?php
  require_once('connectvars.php');
  require_once('recipe_app_functions.php');
  use \RecipeAppFunctions\Functions;

  Functions::add_header(); // create HTML header

  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
  or die('Error connecting to database');

  if(isset($_GET['recipe_id']) && is_numeric($_GET['recipe_id'])) { // For deletion of entire recipes
    $query = $dbc->prepare('SELECT * FROM recipesteps WHERE recipe_id=?');
    $query->bind_param('d', $_GET['recipe_id']);
    $query->execute();
    $result = $query->get_result();

    if($result->num_rows) { // Remove image files associated with recipe from images folder
      while($row = $result->fetch_assoc()) {
        if($row['photo']) {
          @unlink($row['photo']);
        }
      }       
    }

    $query = $dbc->prepare('DELETE FROM recipes WHERE id=? LIMIT 1'); // Delete recipe
    $query->bind_param('d', $_GET['recipe_id']);
    $result = $query->execute();   
  }

  $query = $dbc->prepare('SELECT * FROM recipes');
  $query->execute();
  $result = $query->get_result();

  if($result) { // Display recipes
    while($row = mysqli_fetch_array($result)) {
      echo "<a href='recipe.php?recipe_id=" . $row['id'] . "'>" . $row['name'] . "    </a>";
      echo "<a href='index.php?recipe_id=" . $row['id'] . "'>Delete Recipe</a><br/>";
    }  
  } else {
    echo 'No recipes! Add one now!';
  }
  
  mysqli_close($dbc);

  echo '<br/><br/><a href="addrecipe.php">Add a Recipe</a>';

  Functions::add_footer();
?>