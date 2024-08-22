"use strict";

const KTYearPeriodsModalAddEdit = function () {
    let element;
    let form;
    let modal;
    let submitButton;
    let cancelButton;
    let closeButton;

    let validator;
    let action;

    const initAddEditYearPeriod = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'year_period': {
                        validators: {
                            notEmpty: {
                                message: 'Year period is required'
                            },
                            stringLength: {
                                min: 4,
                                max: 4,
                                message: 'Year period must be 4 characters long'
                            }
                        }
                    },
                    'status_appraisal': {
                        validators: {
                            notEmpty: {
                                message: 'Status appraisal is required'
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
    
    const initDatepicker = () => {
        const yearPeriodInput = document.querySelector('[name="year_period"]');
        $(yearPeriodInput).datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            enableOnReadonly: true,
            todayHighlight: true,
        });
    };

    const fetchYearPeriodById = async (id) => {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}master/year_period/get_by_id/${id}`, { method: 'GET' });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            const data = result.data;
            form.querySelector('[name="id"]').value = data.id;
            $(form.querySelector('[name="year_period"]')).datepicker('setDate', data.year_period);
            form.querySelector('[name="status_appraisal"]').value = data.status_appraisal;
            form.querySelector('[name="status_appraisal"]').dispatchEvent(new Event('change'));
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
                url: action === 'add_year_period' ? 'master/year_period/store' : 'master/year_period/update',
                formData: new URLSearchParams(new FormData(form)).toString()
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
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
                KTYearPeriodsList.datatable().ajax.reload();
                form.reset();
                modal.hide();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    return {
        init: function () {
            element = document.getElementById('kt_modal_add_edit_year_period');
            modal = new bootstrap.Modal(element);
            
            form = element.querySelector('#kt_modal_add_edit_year_period_form');
            submitButton = form.querySelector('[data-kt-year-periods-modal-action="submit"]');
            cancelButton = form.querySelector('[data-kt-year-periods-modal-action="cancel"]');
            closeButton = element.querySelector('[data-kt-year-periods-modal-action="close"]');
            
            initAddEditYearPeriod();
            initDatepicker();

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
            });

            document.addEventListener('click', async (event) => {
                const addYearPeriodButton = event.target.closest('[data-kt-year-period-table-filter="add_row"]');
                if (addYearPeriodButton) {
                    event.preventDefault();
                    action = 'add_year_period';
                    document.getElementById('kt_modal_add_edit_year_period_header_title').innerHTML = "Add Year Period";
                }
                const editYearPeriodButton = event.target.closest('[data-kt-year-period-table-filter="edit_row"]');
                if (editYearPeriodButton) {
                    event.preventDefault();
                    action = 'edit_year_period';
                    const id = editYearPeriodButton.getAttribute('data-id');
                    document.getElementById('kt_modal_add_edit_year_period_header_title').innerHTML = "Edit Year Period";
                    await fetchYearPeriodById(id);
                    modal.show();
                }
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTYearPeriodsModalAddEdit.init();
});