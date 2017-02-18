<?php

namespace RecipeAppFunctions;

class Functions {
  public static function add_header($title = 'Recipes') {
    echo '<html>
          <head>
            <title>'.$title.'</title>
            <meta name="viewport" content="initial-scale=1">  
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
            <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
            <script type="text/javascript" src="recipe.js"></script>
          </head>
          <body>';
  }

  public static function add_footer() {
    echo '</body>
          </html>';
  }

  public static function select_rows_with_id($dbc, $id) {
    $query = $dbc->prepare('SELECT * FROM recipesteps WHERE id=?');
    $query->bind_param('d', $id);
    $query->execute();
    $results=$query->get_result();
    $row = $results->fetch_assoc();

    return $row;
  }
}

?>