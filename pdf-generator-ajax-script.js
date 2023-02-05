jQuery(document).ready(function($) {
    // Button click event to flush out generated data
    $('#delete_data_btn').click(function() {
        console.log('Clicked');
      $.ajax({
        type: 'GET',
        url: pdf_generator_ajax_params.ajax_url,
        data: {
          'action': 'cleardata'
        },
        success: function(response) {
          console.log(response);
        }
      });
    });
  });
  