"use strict";

const KTIndexScoresAddEditIndexScore = function () {
    let element;
    let form;
    let modal;
    let submitButton;
    let cancelButton;
    let closeButton;

    let validator;
    let action;

    const initAddEditIndexScore = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'index_value': {
                        validators: {
                            notEmpty: {
                                message: 'Index score is required'
                            },
                            callback: {
                                message: 'The value must be between 1 and 5',
                                callback: function (input) {
                                    const validValues = [1, 2, 3, 4, 5];
                                    return validValues.includes(parseInt(input.value, 10));
                                },
                            },
                        }
                    },
                    'value_1': {
                        validators: {
                            numeric: {
                                message: 'The value must be a number'
                            }
                        }
                    },
                    'value_2': {
                        validators: {
                            numeric: {
                                message: 'The value must be a number'
                            }
                        }
                    },
                    'order': {
                        validators: {
                            notEmpty: {
                                message: 'Order is required'
                            },
                            numeric: {
                                message: 'The value must be a number'
                            },
                            between: {
                                message: 'The value must be greater than or equal to 1',
                                min: 1,
                                max: Infinity
                            }
                        }
                    },
                    'year_period_id': {
                        validators: {
                            notEmpty: {
                                message: 'Year period is required'
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
                    handleFormSubmission(submitButton);
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
    
    const initOptionSelect2 = function () {
        $('[name="year_period_id"]').select2({
            ajax: {
                url: `${siteUrl}master/index_score/get_year_period_options`,
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term || '',
                    page: params.page || 1
                }),
                processResults: (data, params) => {
                    params.page = params.page || 1;
                    return {
                        results: data.data.items,
                        pagination: {
                            more: (params.page * 10) < data.data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0
        }).on('change', function () {
            validator.revalidateField('year_period_id');
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });
    }

    const initializeColorPicker = () => {
        if ($(".spectrum").length) {
            $('.spectrum').spectrum({
                type: "component"
            });
        }
    }

    const fetchIndexScoreById = async (id) => {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}master/index_score/get_by_id/${id}`, { method: 'GET' });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            const data = result.data;
            form.querySelector('[name="id"]').value = data.id;
            form.querySelector('[name="index_value"]').value = data.index_value;
            form.querySelector('[name="operator_1"]').value = data.operator_1;
            form.querySelector('[name="operator_1"]').dispatchEvent(new Event('change'));
            form.querySelector('[name="value_1"]').value = data.value_1;
            form.querySelector('[name="operator_2"]').value = data.operator_2;
            form.querySelector('[name="operator_2"]').dispatchEvent(new Event('change'));
            form.querySelector('[name="value_2"]').value = data.value_2;
            form.querySelector('[name="description"]').value = data.description;
            $('#color').spectrum('set', data.color);
            form.querySelector('[name="order"]').value = data.order;

            const yearPeriodSelect = form.querySelector('[name="year_period_id"]');
            const newOption = document.createElement('option');
            newOption.value = data.year_period_id;
            newOption.text = data.year_period;
            newOption.selected = true;
            yearPeriodSelect.appendChild(newOption);
            yearPeriodSelect.dispatchEvent(new Event('change'));
        } catch (error) {
            return;
        } finally {
            KTPageLoader.hidePageLoading();
            KTPageLoader.removePageLoading();
        }
    };

    const handleFormSubmission = async (submitButton) => {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        try {
            const formDetails = {
                url: action === 'add_index_score' ? 'master/index_score/store' : 'master/index_score/update',
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
                KTIndexScoresList.datatable().ajax.reload();
                form.reset();
                modal.hide();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    return {
        init: function () {
            element = document.getElementById('kt_modal_add_edit_index_score');
            modal = new bootstrap.Modal(element);
            
            form = element.querySelector('#kt_modal_add_edit_index_score_form');
            submitButton = form.querySelector('[data-kt-index-scores-modal-action="submit"]');
            cancelButton = form.querySelector('[data-kt-index-scores-modal-action="cancel"]');
            closeButton = element.querySelector('[data-kt-index-scores-modal-action="close"]');

            initAddEditIndexScore();
            initOptionSelect2();
            initializeColorPicker();

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
                const addIndexScoreButton = event.target.closest('[data-kt-index-score-table-filter="add_row"]');
                if (addIndexScoreButton) {
                    event.preventDefault();
                    action = 'add_index_score';
                    document.getElementById('kt_modal_add_edit_index_score_header_title').innerHTML = "Add Index Score";
                }
                const editIndexScoreButton = event.target.closest('[data-kt-index-score-table-filter="edit_row"]');
                if (editIndexScoreButton) {
                    event.preventDefault();
                    action = 'edit_index_score';
                    const id = editIndexScoreButton.getAttribute('data-id');
                    document.getElementById('kt_modal_add_edit_index_score_header_title').innerHTML = "Edit Index Score";
                    await fetchIndexScoreById(id);
                    modal.show();
                }
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTIndexScoresAddEditIndexScore.init();
});