"use strict";

const KTKpisAddEditKpi = function () {
    let element;
    let form;
    let modal;
    let submitButton;
    let cancelButton;
    let closeButton;

    let validator;
    let action;

    const initAddEditKpi = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'kpi': {
                        validators: {
                            notEmpty: {
                                message: 'KPI is required'
                            },
                        }
                    },
                    'kpi_counter_id': {
                        validators: {
                            notEmpty: {
                                message: 'KPI Counter is required'
                            }
                        }
                    },
                    'measurement': {
                        validators: {
                            notEmpty: {
                                message: 'Measurement is required'
                            }
                        }
                    },
                    'kpi_polarization_id': {
                        validators: {
                            notEmpty: {
                                message: 'KPI Polarization is required'
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
        $('[name="measurement"]').select2().on('change', function () {
            validator.revalidateField('measurement');
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('[name="year_period_id"]').select2({
            ajax: {
                url: `${siteUrl}master/kpi/get_year_period_options`,
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
            form.querySelector('[name="kpi_counter_id"]').value = null;
            form.querySelector('[name="kpi_counter_id"]').dispatchEvent(new Event('change'));
            form.querySelector('[name="kpi_polarization_id"]').value = null;
            form.querySelector('[name="kpi_polarization_id"]').dispatchEvent(new Event('change'));
            validator.revalidateField('year_period_id');
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('[name="kpi_counter_id"]').select2({
            ajax: {
                url: `${siteUrl}master/kpi/get_kpi_counter_options_by_year_period_id`,
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term || '',
                    page: params.page || 1,
                    year_period_id: form.querySelector('[name="year_period_id"]').value
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
            validator.revalidateField('kpi_counter_id');
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });
        
        $('[name="kpi_polarization_id"]').select2({
            ajax: {
                url: `${siteUrl}master/kpi/get_kpi_polarization_options_by_year_period_id`,
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term || '',
                    page: params.page || 1,
                    year_period_id: form.querySelector('[name="year_period_id"]').value
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
            validator.revalidateField('kpi_polarization_id');
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });
    }

    const fetchKpiById = async (id) => {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}master/kpi/get_by_id/${id}`, { method: 'GET' });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            const data = result.data;
            form.querySelector('[name="id"]').value = data.id;
            form.querySelector('[name="kpi"]').value = data.kpi;
            form.querySelector('[name="measurement"]').value = data.measurement;
            form.querySelector('[name="measurement"]').dispatchEvent(new Event('change'));

            const yearPeriodSelect = form.querySelector('[name="year_period_id"]');
            const yearPeriodNewOption = document.createElement('option');
            yearPeriodNewOption.value = data.year_period_id;
            yearPeriodNewOption.text = data.year_period;
            yearPeriodNewOption.selected = true;
            yearPeriodSelect.appendChild(yearPeriodNewOption);
            yearPeriodSelect.dispatchEvent(new Event('change'));

            const kpiCounterSelect = form.querySelector('[name="kpi_counter_id"]');
            const kpiCounterNewOption = document.createElement('option');
            kpiCounterNewOption.value = data.kpi_counter_id;
            kpiCounterNewOption.text = data.counter;
            kpiCounterNewOption.selected = true;
            kpiCounterSelect.appendChild(kpiCounterNewOption);
            kpiCounterSelect.dispatchEvent(new Event('change'));

            const kpiPolarizationSelect = form.querySelector('[name="kpi_polarization_id"]');
            const kpiPolarizationNewOption = document.createElement('option');
            kpiPolarizationNewOption.value = data.kpi_polarization_id;
            kpiPolarizationNewOption.text = data.polarization;
            kpiPolarizationNewOption.selected = true;
            kpiPolarizationSelect.appendChild(kpiPolarizationNewOption);
            kpiPolarizationSelect.dispatchEvent(new Event('change'));
            
            form.querySelector('[name="description"]').value = data.description;
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
                url: action === 'add_kpi' ? 'master/kpi/store' : 'master/kpi/update',
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
                KTKpisList.datatable().ajax.reload();
                form.reset();
                modal.hide();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    return {
        init: function () {
            element = document.getElementById('kt_modal_add_edit_kpi');
            modal = new bootstrap.Modal(element);
            
            form = element.querySelector('#kt_modal_add_edit_kpi_form');
            submitButton = form.querySelector('[data-kt-kpis-modal-action="submit"]');
            cancelButton = form.querySelector('[data-kt-kpis-modal-action="cancel"]');
            closeButton = element.querySelector('[data-kt-kpis-modal-action="close"]');

            initAddEditKpi();
            initOptionSelect2();

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
                const addKpiButton = event.target.closest('[data-kt-kpi-table-filter="add_row"]');
                if (addKpiButton) {
                    event.preventDefault();
                    action = 'add_kpi';
                    document.getElementById('kt_modal_add_edit_kpi_header_title').innerHTML = "Add KPI";
                }
                const editKpiButton = event.target.closest('[data-kt-kpi-table-filter="edit_row"]');
                if (editKpiButton) {
                    event.preventDefault();
                    action = 'edit_kpi';
                    const id = editKpiButton.getAttribute('data-id');
                    document.getElementById('kt_modal_add_edit_kpi_header_title').innerHTML = "Edit KPI";
                    await fetchKpiById(id);
                    modal.show();
                }
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTKpisAddEditKpi.init();
});