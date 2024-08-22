"use strict";

// Class definition
var KTLogin = function() {
    // Elements
    var form;
    var validator;

    // Handle form
    var validateForm = function(e) {
        validator = FormValidation.formValidation(
			form,
			{
				fields: {					
					'nrik': {
                        validators: {
							notEmpty: {
								message: 'NPP is required'
							},
							stringLength: {
								min: 4,
								max: 4,
								message: 'NPP must be exactly 4 characters long'
							}
						}
					},
                    'password': {
                        validators: {
                            notEmpty: {
                                message: 'The password is required'
                            }
                        }
                    } 
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row'
                    })
				}
			}
		);
    }

    var onChangeInput = function() {
        var nrikInput = document.querySelector('input[name="nrik"]');
        nrikInput.addEventListener('keyup', _.debounce(function() {
            if(nrikInput.value.length > 0) {
                var errorElement = form.querySelector('.error-message#error-nrik');
                if (errorElement) {
                    errorElement.remove();
                }
            }
        }, 300));

        var passwordInput = document.querySelector('input[name="password"]');
        passwordInput.addEventListener('keyup', _.debounce(function() {
            if(passwordInput.value.length > 0) {
                var errorElement = form.querySelector('.error-message#error-password');
                if (errorElement) {
                    errorElement.remove();
                }
            }
        }, 300));
    }

    // Public functions
    return {
        // Initialization
        init: function() {
            form = document.querySelector('#kt_sign_in_form');
            
            validateForm();
            onChangeInput();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTLogin.init();
});