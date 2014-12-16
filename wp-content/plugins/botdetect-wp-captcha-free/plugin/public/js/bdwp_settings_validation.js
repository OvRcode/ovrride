jQuery(document).ready(function(){

    if ( typeof(BDWP_SettingsValidation) == "undefined" ) {

        var BDWP_SettingsValidation = function () {
            this.minCodeLength = jQuery('#min_code_length');
            this.maxCodeLength = jQuery('#max_code_length');
            this._minLengthDefault = 3;
            this._maxLengthDefault = 5;
            this._minLength = 1;
            this._maxLength = 15;
        }

        BDWP_SettingsValidation.prototype.IsSubmit = function() {

            jQuery('#btnBDSettingsSaveChanges').click(function() {
                var self = BDWP_SettingsValidation.prototype.Init.call(this);
                var minTextBox = BDWP_SettingsValidation.prototype.IsCorrectMinCodeLength.call(this, self);
                var maxTextBox = BDWP_SettingsValidation.prototype.IsCorrectMaxCodeLength.call(this, self);

                if (minTextBox && maxTextBox) {
                    return true;
                } else {
                    return false;
                }
            });
        }

        BDWP_SettingsValidation.prototype.SettingsCheck =  function() {
            var self;
            this.minCodeLength.focusout(function() {
                self = BDWP_SettingsValidation.prototype.Init.call(this);
                BDWP_SettingsValidation.prototype.IsCorrectMinCodeLength.call(this, self);
            });

            this.maxCodeLength.focusout(function() {
                self = BDWP_SettingsValidation.prototype.Init.call(this);
                BDWP_SettingsValidation.prototype.IsCorrectMaxCodeLength.call(this, self);
            });
        }

        BDWP_SettingsValidation.prototype.Init =  function() {
            return new BDWP_SettingsValidation();
        }

        BDWP_SettingsValidation.prototype.IsCorrectMinCodeLength = function(self) {
            /**
             * Number of characters: __A__ -  __B__
             */
            var minValue = jQuery.trim(self.minCodeLength.val());
            var maxValue  = jQuery.trim(self.maxCodeLength.val());
            var flag = true;

            if (!jQuery.isNumeric(minValue)) {
                if (self._minLengthDefault > parseInt(maxValue)) {
                    self.minCodeLength.prop('value', parseInt(maxValue)); // Ex: default A = 1, B = 2 . After set A = abc
                } else {
                    self.minCodeLength.prop('value', self._minLengthDefault); // Ex: default A = 2, B = 5 . After set A = abc
                }
                flag = false;
            } else if (parseInt(minValue) < self._minLength) { // A < 1
                self.minCodeLength.prop('value', self._minLength);
                flag = false;
            } else {
                minValue = parseFloat(minValue);
                maxValue = parseInt(maxValue);
                if ((minValue % 1) !== 0 && minValue > maxValue) { // Ex: A = 7.5123 , B = 4
                    self.minCodeLength.prop('value', maxValue);
                    flag = false;
                } else if ((minValue % 1) !== 0 && minValue < maxValue) { // Ex: A = 10.5 , B = 14
                    self.minCodeLength.prop('value', parseInt(minValue));
                    flag = false;
                } else {
                    minValue = parseInt(minValue);
                    if (minValue > maxValue) { // Ex: A = 8, B = 4
                        self.minCodeLength.prop('value', maxValue);
                        flag = false;
                    }
                }
            }

            if (!flag) {
                self.maxCodeLength.focus();
            }

            return flag;
        }

        BDWP_SettingsValidation.prototype.IsCorrectMaxCodeLength = function(self) {
            /**
             * Number of characters: __A__ -  __B__
             */
            var minValue = jQuery.trim(self.minCodeLength.val());
            var maxValue  = jQuery.trim(self.maxCodeLength.val());
            var flag = true;

            if (!jQuery.isNumeric(maxValue)) {
                if (self._maxLengthDefault < parseInt(minValue)) {
                    self.maxCodeLength.prop('value', parseInt(minValue)); // Ex: default A = 12, B = 15 . After set B = abc
                } else {
                    self.maxCodeLength.prop('value', self._maxLengthDefault); // Ex: default A = 3, B = 5 . After set B = abc
                }
                flag = false;
            } else if (parseInt(maxValue) > self._maxLength) { // A > 15
                self.maxCodeLength.prop('value', self._maxLength);
                flag = false;
            } else {
                minValue = parseInt(minValue);
                maxValue = parseFloat(maxValue);
                if ((maxValue % 1) !== 0 && maxValue < minValue) { // Ex: // A = 7 , B = 3.5
                    self.maxCodeLength.prop('value', minValue);
                    flag = false;
                } else if ((maxValue % 1) !== 0 && maxValue > minValue) { // Ex: // A = 3 , B = 3.5
                    self.maxCodeLength.prop('value', parseInt(maxValue));
                    flag = false;
                } else {
                    maxValue = parseInt(maxValue);
                    if (maxValue < minValue) {
                        self.maxCodeLength.prop('value', minValue);
                        flag = false;
                    }
                }
            }

            if (!flag) {
                self.minCodeLength.focus();
            }

            return flag;
        }

        var validSettingsObj = new BDWP_SettingsValidation();
        validSettingsObj.SettingsCheck();
        validSettingsObj.IsSubmit();
    }
});