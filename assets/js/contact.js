/**
 * contact.js
 *
 * Check and validate contact-form, handle success or error.
 */

(function ($) {

  'use strict';

  // contact form
  $('.contact-form').each(function (e) {

    var $contact_form = $(e);
    var $contact_button = $contact_form.find('.form-submit');
    var contact_action = '/php/contact.php';

    // display the hidden form
    $contact_form.removeClass('hidden');

    // wait for a mouse to move, indicating they are human
    $('body').on('mousemove', function () {
      $contact_form.attr('action', contact_action);
      $contact_button.first().removeAttribute('disabled');
    });

    // wait for a touch move event, indicating that they are human
    $('body').on('touchmove', function () {
      $contact_form.attr('action', contact_action);
      $contact_button.first().removeAttribute('disabled');
    });

    // a tab or enter key pressed can also indicate they are human
    $('body').on('keydown', function (e) {
      if ((e.keyCode === 9) || (e.keyCode === 13)) {
        $contact_form.attr('action', contact_action);
        $contact_button.first().removeAttribute('disabled');
      }
    });

    // display messages
    if (location.search.substring(1) !== '') {
      switch (location.search.substring(1)) {
        case 'submitted':
          $('.contact-submitted').removeClass('hidden');
          break;
        case 'error':
          $('.contact-error').removeClass('hidden');
          break;
      }
    }

  });

})(u);
