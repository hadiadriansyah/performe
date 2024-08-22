var modalAdm;
var formAdm;
var validatorAdm;
var submitButtonAdm;
var cancelButtonAdm;
var closeButtonAdm;

var renderModalFormAdm = async function() {
    if (!document.querySelector('#kt_modal_login_adm')) {
        var modalHtml = `
            <div class="modal fade" id="kt_modal_login_adm" tabindex="-1" aria-hidden="true">
                <!--begin::Modal dialog-->
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <!--begin::Modal content-->
                    <div class="modal-content">
                        <!--begin::Form-->
                        <form class="form" id="kt_modal_login_adm_form">
                            <!--begin::Modal header-->
                            <div class="modal-header" id="kt_modal_login_adm_header">
                                <!--begin::Modal title-->
                                <h2 class="fw-bolder">Login</h2>
                                <!--end::Modal title-->
                                <!--begin::Close-->
                                <div id="kt_modal_login_adm_close" class="btn btn-icon btn-sm btn-active-icon-primary">
                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                    <span class="svg-icon svg-icon-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </div>
                                <!--end::Close-->
                            </div>
                            <!--end::Modal header-->
                            <!--begin::Modal body-->
                            <div class="modal-body py-10 px-lg-17">
                                <!--begin::Scroll-->
                                <div class="scroll-y me-n7 pe-7" id="kt_modal_login_adm_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_login_adm_header" data-kt-scroll-wrappers="#kt_modal_login_adm_scroll" data-kt-scroll-offset="300px">
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-7">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-bold mb-2">
                                            <span class="required">Email</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Email address must be active"></i>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="text" class="form-control form-control-solid" placeholder="" name="email" />
                                        <div class="error-message text-small text-danger mt-1" id="error_email"></div>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-7">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-bold mb-2">
                                            <span class="required">Password</span>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <input type="password" class="form-control form-control-solid" placeholder="" name="password" />
                                        <div class="error-message text-small text-danger mt-1" id="error_password"></div>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                </div>
                                <!--end::Scroll-->
                            </div>
                            <!--end::Modal body-->
                            <!--begin::Modal footer-->
                            <div class="modal-footer ">
                                <!--begin::Button-->
                                <button type="reset" id="kt_modal_login_adm_cancel" class="btn btn-light me-3">Discard</button>
                                <!--end::Button-->
                                <!--begin::Button-->
                                <button type="submit" id="kt_modal_login_adm_submit" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <!--end::Button-->
                            </div>
                            <!--end::Modal footer-->
                        </form>
                        <!--end::Form-->
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
}

document.addEventListener('keydown', async function(event) {
    if (event.ctrlKey && event.shiftKey && event.altKey && event.key === 'Enter') {
        event.preventDefault();
        await renderModalFormAdm();
        
        modalAdm = new bootstrap.Modal(document.querySelector('#kt_modal_login_adm'));
        formAdm = document.querySelector('#kt_modal_login_adm_form');
        submitButtonAdm = formAdm.querySelector('#kt_modal_login_adm_submit');
        cancelButtonAdm = formAdm.querySelector('#kt_modal_login_adm_cancel');
        closeButtonAdm = formAdm.querySelector('#kt_modal_login_adm_close');

        modalAdm.show();

        validateFormAdm();

        submitButtonAdm.addEventListener('click', async function (e) {
            e.preventDefault();

            if (submitButtonAdm.disabled) {
                return;
            }
            
            if (validatorAdm) {
				validatorAdm.validate().then(async function (status) {
					if (status == 'Valid') {
                        submitButtonAdm.setAttribute('data-kt-indicator', 'on');
                        submitButtonAdm.disabled = true;
                        submitButtonAdm.removeAttribute('data-kt-indicator');
                        await setupFormSubmissionAdm();
					} else {
						Swal.fire({
							text: "Sorry, looks like there are some errors detected, please try again.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-primary"
							}
						});
					}
				});
			}
        });

        cancelButtonAdm.addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
                text: "Are you sure you would like to cancel?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, cancel it!",
                cancelButtonText: "No, return",
                customClass: {
                    confirmButton: "btn btn-primary-bs",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    formAdm.reset();
                    modalAdm.hide();
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Your form has not been cancelled!.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary-bs",
                        }
                    });
                }
            });
        });

        closeButtonAdm.addEventListener('click', function(e){
            e.preventDefault();

            Swal.fire({
                text: "Are you sure you would like to cancel?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, cancel it!",
                cancelButtonText: "No, return",
                customClass: {
                    confirmButton: "btn btn-primary-bs",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    formAdm.reset();
                    modalAdm.hide();
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Your form has not been cancelled!.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary-bs",
                        }
                    });
                }
            });
        })
    }
});

var validateFormAdm = function(e) {
    validatorAdm = FormValidation.formValidation(
        formAdm,
        {
            fields: {					
                'email': {
                    validators: {
                        notEmpty: {
                            message: 'Email is required'
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

async function setupFormSubmissionAdm() {
    const formDetails = {
        url: 'login/check_credentials',
        formData: new URLSearchParams(new FormData(formAdm)).toString()
    };
    
    await submitFormData(formDetails);
}

async function submitFormData(formDetails) {
    const { url, formData } = formDetails;

    try {
        const response = await fetch(`${siteUrl}${url}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData
        });
        const result = await response.json();
        handleFormResponse(result);
    } catch (error) {
        Swal.fire({
            text: "Error while submitting data!.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
                confirmButton: "btn btn-primary-bs",
            }
        });
    }
}

function handleFormResponse(response) {
    submitButtonAdm.disabled = false;
    if (response.status === 'success') {
        Swal.fire({
            text: "Form has been successfully submitted!",
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
                confirmButton: "btn btn-primary"
            }
        })
        formAdm.reset();
        formAdm.querySelectorAll('.error-message').forEach(function(element) {
            element.innerHTML = '';
        });
        modalAdm.hide();

        setTimeout(function() {
            window.location = siteUrl + 'dashboard';
        }, 2000);
    } else {
        Swal.fire({
            text: response.message,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
                confirmButton: "btn btn-primary-bs",
            }
        });
        Object.keys(response.errors).forEach(function(key) {
            var errorElement = formAdm.querySelector(`#error_${key}`);
            if (errorElement) {
                errorElement.innerHTML = response.errors[key];
            }
        });
    }
}