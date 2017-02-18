$(function(){
  $( "#sortable" ).sortable({
    axis: 'y',
    update: function(event, ui) {
      var data = $(this).sortable('serialize');
      $.ajax({
        data: data + "&recipe_id=",
        type: 'POST',
        url: document.URL,
        success: function(data) {
          var order = 1;
          $('.order_display').each(function(){
            $(this).html(order++);
          });          
        }
      });
    }
  });
  $( "#sortable" ).disableSelection();
});