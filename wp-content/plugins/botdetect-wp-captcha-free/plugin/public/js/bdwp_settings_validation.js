(function($) {

  var BDWP_Settings = BDWP_Settings || {};

  BDWP_Settings.run = function() {
    var self = this;
    
    $('#bdwp_btn_save_changes').on('click', function() {
      return (self.minCodeLengthValidate() && self.maxCodeLengthValidate());
    });

    self.input.min.focusout(function() {
      self.minCodeLengthValidate();
    });

    self.input.max.focusout(function() {
      self.maxCodeLengthValidate();
    });
  };

  BDWP_Settings.defaultValue = {
    min: 1,
    max: 15,
    defMin: 3, // on input
    defMax: 5 // on input
  };

  BDWP_Settings.input = {
    min: $('#min_code_length'),
    max: $('#max_code_length')
  };

  BDWP_Settings.minCodeLengthValidate = function() {
    var input = this.input;
    var def = this.defaultValue;

    var min = $.trim(input.min.val());
    var max  = $.trim(input.max.val());

    if (!$.isNumeric(min)) {
      max = parseInt(max);
      if (def.defMin > max) {
        this.setInputValue(input.min, max);
      } else {
        this.setInputValue(input.min, def.defMin);
      }
      this.nextInputFocus(input.max);
      return false;
    }

    if (parseInt(min) < def.min) {
      this.setInputValue(input.min, def.min);
      this.nextInputFocus(input.max);
      return false;
    }

    min = parseFloat(min);
    max = parseInt(max);

    if (0 !== (min % 1) && min > max) {
      this.setInputValue(input.min, max);
      this.nextInputFocus(input.max);
      return false;
    }

    if (0 !== (min % 1) && min < max) {
      this.setInputValue(input.min, parseInt(min));
      this.nextInputFocus(input.max);
      return false;
    }

    min = parseInt(min);

    if (min > max) {
      this.setInputValue(input.min, max);
      this.nextInputFocus(input.max);
      return false;
    }

    return true;
  };

  BDWP_Settings.maxCodeLengthValidate = function() {
    var input = this.input;
    var def = this.defaultValue;

    var min = $.trim(input.min.val());
    var max  = $.trim(input.max.val());

    if (!$.isNumeric(max)) {
      min = parseInt(min);
      if (def.defMax < min) {
        this.setInputValue(input.max, min);
      } else {
        this.setInputValue(input.max, def.defMax);
      }
      this.nextInputFocus(input.min);
      return false;
    }

    if (parseInt(max) > def.max) {
      this.setInputValue(input.max, def.max);
      this.nextInputFocus(input.min);
      return false;
    }

    min = parseInt(min);
    max = parseFloat(max);

    if (0 !== (max % 1) && max < min) {
      this.setInputValue(input.max, min);
      this.nextInputFocus(input.min);
      return false;
    }

    if (0 !== (max % 1) && max > min) {
      this.setInputValue(input.max, parseInt(max));
      this.nextInputFocus(input.min);
      return false;
    }

    max = parseInt(max);

    if (max < min) {
      this.setInputValue(input.max, min);
      this.nextInputFocus(input.min);
      return false;
    }

    return true;
  };

  BDWP_Settings.nextInputFocus = function(input) {
    input.focus();
  };

  BDWP_Settings.setInputValue = function(input, value) {
    input.prop('value', value);
  };

  $(function() {
    BDWP_Settings.run();
  });

})(jQuery);
