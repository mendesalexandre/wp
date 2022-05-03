// eslint-disable-next-line no-unused-vars
// import config from '@config';
// import '@styles/admin';
// Uncomment the following line if needed:
// import 'airbnb-browser-shims';

// Your code goes here ...

// enable submit and deactivate button while clicking on survey list items
(function ($){
  $(document).ready(function () {

    var $feedbackContainer = $('.depicter-survey-container');
    // show the popup survey
    $('#deactivate-depicter').on('click', function (e) {
      e.preventDefault();
      $feedbackContainer.addClass('show');
    });

    // close the popup survey
    $feedbackContainer.on('click', function(e) {
      if ((!$(e.target).parents('.depicter-survey-list').length && !$(e.target).is('.depicter-survey-list')) || $(e.target).is('.depicter-close')) {
        $feedbackContainer.removeClass('show');
      }
    });

    // enable submit button if one reason clicked
    $feedbackContainer.find('input[name="dep_deactivation_reason"]').each(function () {
      $(this).on('click', function () {
        $feedbackContainer.find('.depicter-submit').attr('disabled', false);
      });
    });

    var ajaxDeactivationRequest = function (reason, userDescriptionText) {
      $.ajax({
        url: depDeactivationParams.ajaxUrl,
        method: 'POST',
        data: {
          _wpnonce: $feedbackContainer.find('#_wpnonce').val(),
          action: 'depicter/deactivate/feedback',
          issueRelatesTo: reason,
          userDescription: userDescriptionText,
        },
      }).done(function (res) {
        location.href = $('#deactivate-depicter').attr('href');
      });
    };

    // send deactivation feedback
    $feedbackContainer.find('.depicter-submit').on('click', function () {
      var $selectedRadioInput = $feedbackContainer.find('input[name="dep_deactivation_reason"]:checked'),
        reason = $selectedRadioInput.val(),
        userDescriptionText = $selectedRadioInput.parent('div').find('input[type="text"').length ? $selectedRadioInput.parent('div').find('input[type="text"').val() : '';
      ajaxDeactivationRequest(reason, userDescriptionText);
      $(this).parent('.depicter-button-wrapper').addClass('loading');
    });

    // deactivate plugin if click on skip
    $feedbackContainer.find('.depicter-skip').on('click', function() {
      ajaxDeactivationRequest('skip', '');
      $(this).parent('.depicter-button-wrapper').addClass('loading skipped');
      location.href = $('#deactivate-depicter').attr('href');
    });
  });

  if ( window.elementor ) {
    elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
      $('.depicter-edit-slider').on('click', function(){
        var sliderID = $(this).parents('#elementor-controls').find('select[data-setting="slider_id"] option').filter(":selected").val();
        var editorUrl = depicterParams.editorUrl.replace( 'document=1', 'document=' + sliderID );
        window.open( editorUrl );
      });
    });
  }
})(jQuery);
