<?php
  require_once('connectvars.php');
  require_once('recipe_app_functions.php');
  use \RecipeAppFunctions\Functions;

  Functions::add_header('Recipes-Add Recipe');  

  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
  or die('Error connecting to database');

  if(isset($_POST['submit'])) {
    $name = $_POST['name'];

    $query = $dbc->prepare('INSERT INTO recipes VALUES(0, ?)');
    $query->bind_param('s', $name);
    $result = $query->execute();
    
    if($result) {
      $last_id = mysqli_insert_id($dbc);
      echo '<p>Successfully created a new recipe</p>';
      echo '<a href="recipe.php?recipe_id=' . $last_id . '">Populate your recipe now!</a>';
    }    
  } else {
?>

<form method='post' action='<?php $_SERVER["PHP_SELF"] ?>'>
  <label for='name'>Recipe Name: </label>
  <input type='text' id='name' name='name'/>
  <input type='submit' id='submit' name='submit'/>
</form>

<?php  
  }
  mysqli_close($dbc);
  Functions::add_footer();
?>