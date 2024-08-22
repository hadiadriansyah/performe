"use strict";

const KTKpiCountersAddEditKpiCounter = function () {
    let element;
    let form;
    let modal;
    let submitButton;
    let cancelButton;
    let closeButton;

    let validator;
    let action;

    const initAddEditKpiCounter = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'counter_type': {
                        validators: {
                            notEmpty: {
                                message: 'Counter is required'
                            },
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
        $('[name="counter_type"]').select2().on('change', function () {
            validator.revalidateField('counter_type');
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('[name="year_period_id"]').select2({
            ajax: {
                url: `${siteUrl}master/kpi_counter/get_year_period_options`,
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

    const fetchKpiCounterById = async (id) => {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}master/kpi_counter/get_by_id/${id}`, { method: 'GET' });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            const data = result.data;
            form.querySelector('[name="id"]').value = data.id;
            const counterParts = data.counter.split('_');
            const selectedValue = counterParts[0];
            const remainingText = counterParts.slice(1).join('_');
            form.querySelector('[name="counter_type"]').value = selectedValue;
            form.querySelector('[name="counter_type"]').dispatchEvent(new Event('change'));
            form.querySelector('[name="counter_text"]').value = remainingText;
            form.querySelector('[name="description"]').value = data.description;

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
                url: action === 'add_kpi_counter' ? 'master/kpi_counter/store' : 'master/kpi_counter/update',
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
                KTKpiCountersList.datatable().ajax.reload();
                form.reset();
                modal.hide();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    return {
        init: function () {
            element = document.getElementById('kt_modal_add_edit_kpi_counter');
            modal = new bootstrap.Modal(element);
            
            form = element.querySelector('#kt_modal_add_edit_kpi_counter_form');
            submitButton = form.querySelector('[data-kt-kpi-counters-modal-action="submit"]');
            cancelButton = form.querySelector('[data-kt-kpi-counters-modal-action="cancel"]');
            closeButton = element.querySelector('[data-kt-kpi-counters-modal-action="close"]');

            initAddEditKpiCounter();
            initOptionSelect2();
            
            element.addEventListener('shown.bs.modal', () => {});

            element.addEventListener('hidden.bs.modal', () => {
                form.reset();
            });

            form.addEventListener('reset', () => {
                const selects = form.querySelectorAll('select');
                selects.forEach((select) => {
                    select.value = '0';
                    select.dispatchEvent(new Event('change'));
                });
            });

            document.addEventListener('click', async (event) => {
                const addKpiCounterButton = event.target.closest('[data-kt-kpi-counter-table-filter="add_row"]');
                if (addKpiCounterButton) {
                    event.preventDefault();
                    action = 'add_kpi_counter';
                    document.getElementById('kt_modal_add_edit_kpi_counter_header_title').innerHTML = "Add KPI Counter";
                }
                const editKpiCounterButton = event.target.closest('[data-kt-kpi-counter-table-filter="edit_row"]');
                if (editKpiCounterButton) {
                    event.preventDefault();
                    action = 'edit_kpi_counter';
                    const id = editKpiCounterButton.getAttribute('data-id');
                    document.getElementById('kt_modal_add_edit_kpi_counter_header_title').innerHTML = "Edit KPI Counter";
                    await fetchKpiCounterById(id);
                    modal.show();
                }
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTKpiCountersAddEditKpiCounter.init();
});