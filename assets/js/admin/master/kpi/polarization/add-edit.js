"use strict";

const KTKpiPolarizationsAddEditKpiPolarization = function () {
    let element;
    let form;
    let modal;
    let submitButton;
    let cancelButton;
    let closeButton;

    let validator;
    let action;

    const initAddEditKpiPolarization = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'polarization_type': {
                        validators: {
                            notEmpty: {
                                message: 'Polarization is required'
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
        $('[name="polarization_type"]').select2().on('change', function () {
            validator.revalidateField('polarization_type');
            const selectedValue = this.value;
            form.querySelectorAll('.kt_kpi_polarization_formula_minimize, .kt_kpi_polarization_formula_maximize, .kt_kpi_polarization_formula_absolute, .kt_kpi_polarization_formula_stabilize').forEach(element => {
                element.classList.add('d-none');
            });
            const forms = {
                'Minimize': '.kt_kpi_polarization_formula_minimize',
                'Maximize': '.kt_kpi_polarization_formula_maximize',
                'Absolute': '.kt_kpi_polarization_formula_absolute',
                'Stabilize': '.kt_kpi_polarization_formula_stabilize'
            };
            
            if (selectedValue) {
                form.querySelector(forms[selectedValue]).classList.remove('d-none');
            }
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('[name="year_period_id"]').select2({
            ajax: {
                url: `${siteUrl}master/kpi_polarization/get_year_period_options`,
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

    const fetchKpiPolarizationById = async (id) => {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}master/kpi_polarization/get_by_id/${id}`, { method: 'GET' });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            const data = result.data;
            form.querySelector('[name="id"]').value = data.id;
            const polarizationParts = data.polarization.split('_');
            const selectedValue = polarizationParts[0];
            const remainingText = polarizationParts.slice(1).join('_');
            form.querySelector('[name="polarization_type"]').value = selectedValue;
            form.querySelector('[name="polarization_type"]').dispatchEvent(new Event('change'));
            form.querySelector('[name="polarization_text"]').value = remainingText;
            form.querySelector('[name="description"]').value = data.description;

            const yearPeriodSelect = form.querySelector('[name="year_period_id"]');
            const newOption = document.createElement('option');
            newOption.value = data.year_period_id;
            newOption.text = data.year_period;
            newOption.selected = true;
            yearPeriodSelect.appendChild(newOption);
            yearPeriodSelect.dispatchEvent(new Event('change'));

            for (const [key, value] of Object.entries(data.formula)) {
                form.querySelector(`[name="${key}"]`).value = value;
                form.querySelector(`[name="${key}"]`).dispatchEvent(new Event('change'));
            }

            const forms = {
                'Minimize': '.kt_kpi_polarization_formula_minimize',
                'Maximize': '.kt_kpi_polarization_formula_maximize',
                'Absolute': '.kt_kpi_polarization_formula_absolute',
                'Stabilize': '.kt_kpi_polarization_formula_stabilize'
            }

            if (selectedValue) {
                form.querySelector(forms[selectedValue]).classList.remove('d-none');
            }
            
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
                url: action === 'add_kpi_polarization' ? 'master/kpi_polarization/store' : 'master/kpi_polarization/update',
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
                KTKpiPolarizationsList.datatable().ajax.reload();
                form.reset();
                modal.hide();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    return {
        init: function () {
            element = document.getElementById('kt_modal_add_edit_kpi_polarization');
            modal = new bootstrap.Modal(element);
            
            form = element.querySelector('#kt_modal_add_edit_kpi_polarization_form');
            submitButton = form.querySelector('[data-kt-kpi-polarizations-modal-action="submit"]');
            cancelButton = form.querySelector('[data-kt-kpi-polarizations-modal-action="cancel"]');
            closeButton = element.querySelector('[data-kt-kpi-polarizations-modal-action="close"]');

            initAddEditKpiPolarization();
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
                const addKpiPolarizationButton = event.target.closest('[data-kt-kpi-polarization-table-filter="add_row"]');
                if (addKpiPolarizationButton) {
                    event.preventDefault();
                    action = 'add_kpi_polarization';
                    document.getElementById('kt_modal_add_edit_kpi_polarization_header_title').innerHTML = "Add KPI Polarization";
                }
                const editKpiPolarizationButton = event.target.closest('[data-kt-kpi-polarization-table-filter="edit_row"]');
                if (editKpiPolarizationButton) {
                    event.preventDefault();
                    action = 'edit_kpi_polarization';
                    const id = editKpiPolarizationButton.getAttribute('data-id');
                    document.getElementById('kt_modal_add_edit_kpi_polarization_header_title').innerHTML = "Edit KPI Polarization";
                    await fetchKpiPolarizationById(id);
                    modal.show();
                }
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTKpiPolarizationsAddEditKpiPolarization.init();
});