<?php
  require_once('connectvars.php');
  require_once('recipe_app_functions.php');
  use \RecipeAppFunctions\Functions;

  Functions::add_header('Recipes-Add Step');

  $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
  or die('Error connecting to database');

  // $recipe_id = $_GET['recipe_id'];
  // $step_order = $_GET['next_order'];
  // $id = isset($_GET['id']) ? $id = $_GET['id'] : NULL;
  $result = false;

  if(isset($_POST['remove'])) { // User clicks on remove button
    $row = isset($_POST['id']) ? Functions::select_rows_with_id($dbc, $_POST['id']) : NULL;  // Check if entry exists

    if($row) {
      $query = $dbc->prepare('DELETE FROM recipesteps WHERE id=? LIMIT 1');
      $query->bind_param('d', $_POST['id']);
      $result = $query->execute();
      echo 'Step deleted</br>';  
      if(isset($row['photo'])) { 
        @unlink($row['photo']); // Remove image if photo exists
      } 
    } else {
      echo 'Entry does not exist!</br>';
    }    
    echo '<a href="recipe.php?recipe_id=' . $row['recipe_id'] . '">Back to recipe</a>';       
  }

  if(isset($_POST['submit'])) { // If form has been submitted
    $row = isset($_POST['id']) ? Functions::select_rows_with_id($dbc, $_POST['id']) : NULL;  

    if(!empty($_POST['step'])) { // If the form submitted is an insertion/update in text
      if(!$row) { // If form is for addition of new step
        $query = $dbc->prepare('INSERT INTO recipesteps VALUES(0, ?, "", ?, ?)');
        $query->bind_param('sdd', $_POST['step'], $_POST['recipe_id'], $_POST['step_order']);
        $result = $query->execute();        
      } else { // Form submitted was for an update
        $query = $dbc->prepare('UPDATE recipesteps SET step=? WHERE id=? LIMIT 1');
        $query->bind_param('sd', $_POST['step'], $_POST['id']);
        $result = $query->execute();
      }
    } 
    // If form submitted is an image upload
    if(isset($_FILES['photo']) && file_exists($_FILES['photo']['tmp_name']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
      $photo = time().$_FILES['photo']['name'];
      $target = PHOTO_UPLOADPATH . $photo;
      if(move_uploaded_file($_FILES['photo']['tmp_name'] , $target)) { // move file from temporary folder to images folder
        if(!$row) { // If it is a new image upload
          $query = $dbc->prepare('INSERT INTO recipesteps VALUES(0, "", ?, ?, ?)');
          $query->bind_param('sdd', $target, $_POST['recipe_id'], $_POST['step_order']);
          $result = $query->execute();
        } else { // For updating image
          @unlink($row['photo']); // delete old image
          $query = $dbc->prepare('UPDATE recipesteps SET photo=? WHERE id=?');
          $query->bind_param('sd', $target, $_POST['id']);
          $result = $query->execute();
        }
      }
    }        
    // print if success
    if($result) {
      echo 'successfully inserted or updated recipe step</br>';        
    } else {
      echo 'Image must be uploaded or step must not be blank!<br/>';
    }
    echo '<a href="recipe.php?recipe_id=' . $_POST['recipe_id'] . '">Back to recipe</a>';        
  } 
  // Display forms if no form submission
  if(!isset($_POST['remove']) && !isset($_POST['submit'])) {
    $recipe_id = $_GET['recipe_id'];
    $step_order = $_GET['next_order'];
    $id = isset($_GET['id']) ? $id = $_GET['id'] : NULL;
?>
    <form enctype="multipart/form-data" method='post' action="<?php $_SERVER['PHP_SELF'] ?>">      
      
<?php
      // Check for existing entry in database i.e determine if an update
      $row = isset($id) ? Functions::select_rows_with_id($dbc, $id) : NULL;  

      $button_display = !$row ? 'Add Instruction' : 'Edit Instruction';
      if($_GET['op'] == 'addstep') { // To display a form to add or change instructions
        echo "<label for='step'>Instruction:</label>";
        if($row) { // If existing entry to show exiting value in input
          echo "<input type='text' name='step' id='step' value='" . $row['step'] ."'/>";  
          echo '<input type="hidden" name="id" id="id" value="'. $id .'"/>';
        } else { // Show empty field for addition if no existing entry
          echo '<input type="hidden" name="step_order" id="step_order" value="'. $step_order .'"/>';
          echo "<input type='text' name='step' id='step'/>";  
        }    
      } else { // To display form to add or change image  
        echo "<label for='photo'>Photo:</label>";
        if($row) { // If existing entry to show existing value in input
          echo '<img src="' . $row['photo'] . '" alt="photo" height=100 width=200/><br/>';
          echo '<input type="hidden" name="id" id="id" value="'. $id .'"/>';
          echo "<input type='file' name='photo' id='photo'/>";
        } else { // Show empty field for addition if no existing entry
          echo '<input type="hidden" name="step_order" id="step_order" value="'. $step_order .'"/>';
          echo "<input type='file' name='photo' id='photo'/>";
        }    
      }    
?>
      <input type="hidden" name="recipe_id" id="recipe_id" value="<?php echo $recipe_id ?>"/>
      <input type='submit' name='submit' id='submit' value="<?php echo $button_display ?>"/>
    </form>

<?php
    if($row) { // Allow removal of entry if it exists
?>
      <form method='post' action="<?php $_SERVER['PHP_SELF'] ?>">
        <input type='hidden' name='recipe_id' id='recipe_id' value='<?php echo $recipe_id ?>'>
        <input type='hidden' name='id' id='id' value='<?php echo $id ?>'>
        <input type='submit' name='remove' id='remove' value='Remove'/>
      </form>
<?php
    }
?>

<?php
  }
  mysqli_close($dbc);
  Functions::add_footer();
?>