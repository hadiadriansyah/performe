"use strict";

const KTModalTargetActual = (() => {
    let element;
    let form;
    let modal;
    let submitButton;
    let cancelButton;
    let closeButton;

    let validator;
    let action;

    let kpiData;
    
    const newItemKpi = {
        id: null,
        pa_individual_id: null,
        year_period_id: null,
        kpi_id: null,
        weight: 0,
        score: 0,
        description: null,
        created_at: null,
        updated_at: null,
        created_by: null,
        updated_by: null,
        target_id: null,
        actual_id: null,
        mode: ''
    };

    const initTarget = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'month_1': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            }
                        }
                    },
                    'month_2': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
                        }
                    },
                    'month_3': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
                        }
                    },
                    'month_4': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
                        }
                    },
                    'month_5': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
                        }
                    },
                    'month_6': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
                        }
                    },
                    'month_7': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
                        }
                    },
                    'month_8': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
                        }
                    },
                    'month_9': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
                        }
                    },
                    'month_10': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
                        }
                    },
                    'month_11': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
                        }
                    },
                    'month_12': {
                        validators: {
                            notEmpty: {
                                message: 'Target is required'
                            },
                            numeric: {
                                message: 'Target must be a number'
                            },
                            between: {
                                message: 'Target must be greater than or equal to 0',
                                min: 0,
                                max: Infinity
                            },
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
                    await handleFormSubmission();
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

    const fetchTarget = async (id) => {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}goals_settings/kpi_individual/get_target_by_id/${id}`, { method: 'GET' });
            
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            const data = result.data;
            
            if (data && data.target) {
                const target = JSON.parse(data.target);
                for (const key in target) {
                    if (target.hasOwnProperty(key)) {
                        form.querySelector(`[name="month_${key}"]`).value = target[key];
                    }
                }
            }
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
                url: 'goals_settings/kpi_individual/add_edit_target',
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
            await handleFormResponse(result);
        } catch (error) {
            KTAlertDialog.showErrorMessage("Error while submitting data!.");
        }
    };

    const handleFormResponse = async (response) => {
        const data = response.data;
        
        if (response.status === 'success') {
            const target_id = data.id;
            const kpi_individual_id = data.kpi_individual_id;
            const targetButton = document.querySelector(`[data-kt-target-actual-button-action="target"][data-id="${kpi_individual_id}"]`);
            
            await new Promise(resolve => {
                form.querySelector('[name="target_id"]').value = target_id;
                resolve();
            });

            const updateKpi = kpiData.find(kpi => kpi.id === kpi_individual_id);
            if (updateKpi) {
                updateKpi.target_id = target_id;
            }
            
            KTKpiIndividualList.updateKpiData(kpiData);
            
            KTAlertDialog.showSuccessMessage("Form has been successfully submitted!");

            if (targetButton) {
                if (targetButton.classList.contains('btn-light-danger')) {
                    targetButton.classList.remove('btn-light-danger');
                }
                if (!targetButton.classList.contains('btn-light-success')) {
                    targetButton.classList.add('btn-light-success');
                }
            }
            
            modal.hide();
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    return {
        init: function () {
            element = document.getElementById('kt_modal_target_actual');
            modal = new bootstrap.Modal(element);

            form = element.querySelector('#kt_modal_target_actual_form');
            submitButton = form.querySelector('[data-kt-target-actual-modal-action="submit"]');
            cancelButton = form.querySelector('[data-kt-target-actual-modal-action="cancel"]');
            closeButton = element.querySelector('[data-kt-target-actual-modal-action="close"]');

            initTarget();

            element.addEventListener('shown.bs.modal', () => {});

            element.addEventListener('hidden.bs.modal', () => {
                form.reset();
            });

            document.addEventListener('click', async (event) => {
                const targetButton = event.target.closest('[data-kt-target-actual-button-action="target"]');
                if (targetButton) {
                    event.preventDefault();
                    submitButton.disabled = true;
                    action = 'target';
                    const id = targetButton.getAttribute('data-id');
                    const table = document.querySelector('#kt_table_kpis');

                    const viewSelectKpi = table.querySelector(`#kpi_id_${id}`);
                    const selectedKpiText = viewSelectKpi.options[viewSelectKpi.selectedIndex].text;

                    const viewMeasurementKpi = table.querySelector(`#measurement_${id}`).textContent;
                    const viewCounterKpi = table.querySelector(`#counter_${id}`).textContent;
                    const viewPolarizationKpi = table.querySelector(`#polarization_${id}`).textContent;

                    element.querySelector('#kt_modal_target_actual_header_title').innerHTML = "Target";

                    form.querySelector('[name="kpi_text"]').value = selectedKpiText;
                    form.querySelector('[name="measurement"]').value = viewMeasurementKpi;
                    form.querySelector('[name="counter"]').value = viewCounterKpi;
                    form.querySelector('[name="polarization"]').value = viewPolarizationKpi;

                    kpiData = KTKpiIndividualList.kpiData();

                    const row = kpiData.find(kpi => kpi.id === id);

                    if (row) {
                        submitButton.disabled = false;
                        
                        if (row.mode === 'add') {
                            KTAlertDialog.showErrorMessage("Make sure you have added and saved the KPI.", () => {
                                modal.hide();
                            });
                            return;
                        }
                        
                        form.querySelector('[name="kpi_individual_id"]').value = row.id;

                        if (row.target_id) {
                            form.querySelector('[name="target_id"]').value = row.target_id;
                            await fetchTarget(row.target_id);
                        } else {
                            form.querySelector('[name="target_id"]').value = null;
                        }   
                    }
                }
            });

            form.addEventListener('reset', () => {
                const selects = form.querySelectorAll('select');
                selects.forEach((select) => {
                    select.value = null;
                    select.dispatchEvent(new Event('change'));
                });
            });
        }
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTModalTargetActual.init();
});