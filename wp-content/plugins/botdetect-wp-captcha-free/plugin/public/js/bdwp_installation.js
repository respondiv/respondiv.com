(function($) {
  var BDWP_Installation = BDWP_Installation || {};

  BDWP_Installation.run = function() {
    var self = this;
    $('#bdwp_button_user_register').on('click', function(event) {
      // register user and then check captcha image
      var email = $('#bdwp_customer_email').val();
      var callback = self.checkCaptchaImageRender;
      self.registerUser(email, callback);

      // stops the default action
      event.preventDefault ? event.preventDefault() : event.returnValue = false;
    });
  };

  BDWP_Installation.messages = {
    errorEmail: $('#bdwp_error_invalid_email_message').val(),
    errorCaptchaImage: $('#bdwp_error_captcha_image_message').val(),
    errorSessions: $('#bdwp_error_sessions_disabled').val(),
    errorNetwork: $('#bdwp_error_network').val(),
    errorThirdPartyPlugin: {
      iThemesSecurity: $('#bdwp_error_itheme_security_blocked').val()
    },

    loadingText1: $('#bdwp_loading_message_1').val(),
    loadingText2: $('#bdwp_loading_message_2').val()
  };

  BDWP_Installation.notify = {
    showError: function(message) {
      $('.bdwp_error_message').html(message);
    },

    removeError: function() {
      $('.bdwp_error_message').html('');
    }
  };

  BDWP_Installation.buttonState = {
    enable: function() {
      $('#bdwp_button_user_register').removeClass('bdwp_button_user_register_disable');
    },

    disable: function() {
      $('#bdwp_button_user_register').addClass('bdwp_button_user_register_disable');
    }
  };

  BDWP_Installation.loadingState = {
    running: 0,

    enable: function(text) {
      var dotNumber = 5;
      var loadingContainer = $('.bdwp_loading');

      loadingContainer.html('');
      loadingContainer.text(text);
      loadingContainer.append('<span class="bdwp_loading_box"></span>');

      var loading = $('span.bdwp_loading_box');
  
      var interval = setInterval(function() {
        var currentDot = loading.text();
        if (dotNumber === currentDot.length) {
          loading.text('');
        } else {
          var newDot = currentDot + '.';
          loading.text(newDot);
        }
      }, 100);

      this.running = interval;
    },

    disable: function() {
      clearInterval(this.running);
      $('.bdwp_loading').html('');
    }
  };

  BDWP_Installation.registerUser = function(email, callback) {
    var self = this;
    var urlHandle = $('#bdwp_plugin_dir_url').val() + 'handlers/captcha_provider_user_register_handler.php';

    $.ajax({
      type: 'POST',
      url: urlHandle,
      data: { customerEmail: email },
      beforeSend: function() {
        self.notify.removeError();
        self.buttonState.disable();         
        self.loadingState.enable(self.messages.loadingText2);
      },
      success: function(data, textStatus, jqXHR) {
        try {
          var register = $.parseJSON(data);
          var status = register.status;

          switch(status) {
            case 'OK':
              // hide error message and register form
              $('#bdwp_notice_captcha_library, #bdwp_user_register_form').hide();

              // enable all botdetect input
              $('input[name^="botdetect_options"], #bdwp_button_save_changes').prop('disabled', false);

              // load iframe
              var iframe  = '<iframe style="border: 0px; display: none" ';
                  iframe += 'src="' + register.download_url + '" ';
                  iframe += 'scrolling="no" marginwidth="0" marginheight="0" frameborder="0"></iframe>';
              $('body').append(iframe);

              callback(); // check captcha image url
              break;

            case 'ERR_REMOTE':
              self.notify.showError(self.messages.errorNetwork);
              self.loadingState.disable();
              break;

            default:
              $('#bdwp_customer_email').focus();
              self.notify.showError(self.messages.errorEmail);
              self.loadingState.disable();   
              break;
          }
        } catch(e) { self.loadingState.disable(); }
      },
      error: function(xhr, textStatus, errorThrown) {
        self.loadingState.disable();
      },
      complete: function() {
        self.buttonState.enable();
      }
    });
  };

  BDWP_Installation.checkCaptchaImageRender = function() {
    var self = BDWP_Installation;
    var urlHandle = $('#bdwp_plugin_dir_url').val() + 'handlers/captcha_provider_installation_handler.php';

    $.ajax({
      type: 'POST',
      url: urlHandle,
      data: { captchaImageUrl: $('#bdwp_captcha_image_url').val() },
      beforeSend: function() {
        self.notify.removeError();
        self.loadingState.enable(self.messages.loadingText1);       
      },
      success: function(data, textStatus, jqXHR) {
        try {
          var responseHandler = function() {
            self.loadingState.disable();

            var jsonObj = $.parseJSON(data);
            var status = jsonObj.status;
            
            switch(status) {
              case 'OK':
                // everything is OK, hide the register view
                $('#bdwp_user_register_container').hide();
                break;

              case 'ERR_SESSION_IS_DISABLED':
                self.notify.showError(self.messages.errorSessions);
                break;

              case 'ERR_PROBLEMS_WITH_ITHEMES_SECURITY':
                self.notify.showError(self.messages.errorThirdPartyPlugin.iThemesSecurity);
                break;

              default:
                self.notify.showError(self.messages.errorCaptchaImage);
                break;
            }
    
            // disable login field if error
            if ('OK' !== status) {
              $('input[name="botdetect_options[on_login]"]').prop('checked', false);
            }
          };

          setTimeout(responseHandler, 2000);
        } catch(e) { throw new Error(e); }
      },
      error: function(xhr, textStatus, errorThrown) {
        self.loadingState.disable();
      }
    });
  };

  $(function() {
    BDWP_Installation.run();
  });

})(jQuery);
