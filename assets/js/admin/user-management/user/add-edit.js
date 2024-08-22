"use strict";

const KTUsersModalAddEdit = function () {
    let element;
    let form;
    let modal;
    let submitButton;
    let cancelButton;
    let closeButton;

    let validator;
    let action;

    const initAddEditUser = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'profile_picture': {
                        validators: {
                            file: {
                                extension: 'png,jpg,jpeg',
                                type: 'image/png,image/jpg,image/jpeg',
                                message: 'Please choose a PNG, JPG, or JPEG file',
                            },
                        }
                    },
                    'name': {
                        validators: {
                            notEmpty: {
                                message: 'Name is required'
                            }
                        }
                    },
                    'email': {
                        validators: {
                            notEmpty: {
                                message: 'Email is required'
                            }
                        }
                    },
                    'password': {
                        validators: {
                            callback: {
                                message: 'Password is required',
                                callback: function (input) {
                                    if (action === 'add_user') {
                                        return input.value.length > 0;
                                    } else {
                                        return true;
                                    }
                                }
                            },
                            callback: {
                                message: 'Please enter valid password',
                                callback: function (input) {
                                    if (input.value.length > 0) {
                                        return validatePassword();
                                    }
                                }
                            }
                        }
                    },
                    'confirm_password': {
                        validators: {
                            callback: {
                                message: 'Password is required',
                                callback: function (input) {
                                    if (action === 'add_user') {
                                        return input.value.length > 0;
                                    } else {
                                        return true
                                    }
                                }
                            },
                            identical: {
                                compare: function () {
                                    return form.querySelector('[name="password"]').value;
                                },
                                message: 'The password and its confirm are not the same'
                            }
                        }
                    },
                    'is_active': {
                        validators: {
                            notEmpty: {
                                message: 'Status is required'
                            }
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        submitButton.addEventListener('click', async (e) => {
            e.preventDefault();

            if (submitButton.disabled) return;
            
            if (validator) {
                const status = await validator.validate();
                if (status === 'Valid') {
                    handleFormSubmission();
                } else {
                    KTAlertDialog.showErrorMessage("Sorry, looks like there are some errors detected, please try again.");
                }
            }
        });

        cancelButton.addEventListener('click', (e) => {
            e.preventDefault();
            KTAlertDialog.showConfirmationDialog("Are you sure you would like to cancel?", () => {
                form.reset();
                modal.hide();
            });
        });

        closeButton.addEventListener('click', (e) => {
            e.preventDefault();
            KTAlertDialog.showConfirmationDialog("Are you sure you would like to cancel?", () => {
                form.reset();
                modal.hide();
            });
        });
    };
    
    const fetchUserById = async (id) => {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}user_management/user/get_by_id/${id}`, { method: 'GET' });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            const data = result.data;
            form.querySelector('[name="id"]').value = data.id;

            const imageInputWrapper = document.querySelector('.image-input-wrapper');
            if (imageInputWrapper && data.is_file_exists) {
                imageInputWrapper.style.backgroundImage = `url(uploads/profile_pictures/${data.profile_picture})`;
            }
            form.querySelector('[name="name"]').value = data.name;
            form.querySelector('[name="email"]').value = data.email;
            form.querySelector('[name="is_active"]').value = data.is_active;
            form.querySelector('[name="is_active"]').dispatchEvent(new Event('change'));
        } catch (error) {
            return;
        } finally {
            KTPageLoader.hidePageLoading();
            KTPageLoader.removePageLoading();
        }
    };

    const handleFormSubmission = async () => {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
        try {
            const formDetails = {
                url: action === 'add_user' ? 'user_management/user/store' : 'user_management/user/update',
                formData: new FormData(form)
            };
            await submitFormData(formDetails);
        } catch (error) {
            KTAlertDialog.showErrorMessage("Error while submitting data!.");
        } finally {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
        }
    };

    const submitFormData = async (formDetails) => {
        const { url, formData } = formDetails;
        try {
            const response = await fetch(`${siteUrl}${url}`, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            handleFormResponse(result);
        } catch (error) {
            KTAlertDialog.showErrorMessage("Error while submitting data!.");
        }
    };

    const handleFormResponse = (response) => {
        if (response.status === 'success') {
            KTAlertDialog.showSuccessMessage("Form has been successfully submitted!", () => {
                KTUsersList.datatable().ajax.reload();
                form.reset();
                modal.hide();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    return {
        init: function () {
            element = document.getElementById('kt_modal_add_edit_user');
            modal = new bootstrap.Modal(element);
            
            form = element.querySelector('#kt_modal_add_edit_user_form');
            submitButton = form.querySelector('[data-kt-users-modal-action="submit"]');
            cancelButton = form.querySelector('[data-kt-users-modal-action="cancel"]');
            closeButton = element.querySelector('[data-kt-users-modal-action="close"]');
            
            initAddEditUser();

            element.addEventListener('shown.bs.modal', () => {});
            element.addEventListener('hidden.bs.modal', () => {
                form.reset();
            });

            form.addEventListener('reset', () => {
                const selects = form.querySelectorAll('select');
                selects.forEach((select) => {
                    select.value = null;
                    select.dispatchEvent(new Event('change'));
                });
                const imageInputWrapper = document.querySelector('.image-input-wrapper');
                if (imageInputWrapper) {
                    imageInputWrapper.style.backgroundImage = 'url(assets/media/avatars/blank.png)';
                }
            });

            document.addEventListener('click', async (event) => {
                const addUserButton = event.target.closest('[data-kt-user-table-filter="add_row"]');
                if (addUserButton) {
                    event.preventDefault();
                    action = 'add_user';
                    document.getElementById('kt_modal_add_edit_user_header_title').innerHTML = "Add User";
                }
                const editUserButton = event.target.closest('[data-kt-user-table-filter="edit_row"]');
                if (editUserButton) {
                    event.preventDefault();
                    action = 'edit_user';
                    const id = editUserButton.getAttribute('data-id');
                    document.getElementById('kt_modal_add_edit_user_header_title').innerHTML = "Edit User";
                    await fetchUserById(id);
                    modal.show();
                }
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTUsersModalAddEdit.init();
});