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

    const fetchActual = async (id) => {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}performance_appraisal/individual_performance_appraisal/get_actual_by_id/${id}`, { method: 'GET' });
            
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            const data = result.data;
            
            if (data && data.actual) {
                const actual = JSON.parse(data.actual);
                for (const key in actual) {
                    if (actual.hasOwnProperty(key)) {
                        form.querySelector(`[name="month_${key}"]`).value = actual[key];
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

    const fetchTarget = async (id) => {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}performance_appraisal/individual_performance_appraisal/get_target_by_id/${id}`, { method: 'GET' });
            
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
                url: 'performance_appraisal/individual_performance_appraisal/add_edit_actual',
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
            const actual_id = data.id;
            const kpi_individual_id = data.kpi_individual_id;
            const actualButton = document.querySelector(`[data-kt-target-actual-button-action="actual"][data-id="${kpi_individual_id}"]`);
            
            await new Promise(resolve => {
                form.querySelector('[name="actual_id"]').value = actual_id;
                resolve();
            });

            const updateKpi = kpiData.find(kpi => kpi.id === kpi_individual_id);
            if (updateKpi) {
                updateKpi.actual_id = actual_id;
            }
            
            KTIndividualPerformanceAppraisalList.updateKpiData(kpiData);
            KTIndividualPerformanceAppraisalList.calculateDataPerformanceOnChange();
            KTAlertDialog.showSuccessMessage("Form has been successfully submitted!");

            if (actualButton) {
                if (actualButton.classList.contains('btn-color-danger')) {
                    actualButton.classList.remove('btn-color-danger');
                }
                if (!actualButton.classList.contains('btn-color-success')) {
                    actualButton.classList.add('btn-color-success');
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
                const actualButton = event.target.closest('[data-kt-target-actual-button-action="actual"]');
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

                    kpiData = KTIndividualPerformanceAppraisalList.kpiData();

                    const row = kpiData.find(kpi => kpi.id === id);

                    if (row) {
                        form.querySelector('[name="kpi_individual_id"]').value = row.id;

                        if (row.target_id) {
                            form.querySelector('[name="target_id"]').value = row.target_id;
                            await fetchTarget(row.target_id);
                        } else {
                            form.querySelector('[name="target_id"]').value = null;
                        }   
                    }
                }
                if (actualButton) {
                    event.preventDefault();
                    submitButton.disabled = true;
                    action = 'actual';
                    const id = actualButton.getAttribute('data-id');
                    const table = document.querySelector('#kt_table_kpis');

                    const viewSelectKpi = table.querySelector(`#kpi_id_${id}`);
                    const selectedKpiText = viewSelectKpi.options[viewSelectKpi.selectedIndex].text;

                    const viewMeasurementKpi = table.querySelector(`#measurement_${id}`).textContent;
                    const viewCounterKpi = table.querySelector(`#counter_${id}`).textContent;
                    const viewPolarizationKpi = table.querySelector(`#polarization_${id}`).textContent;

                    element.querySelector('#kt_modal_target_actual_header_title').innerHTML = "Actual";

                    form.querySelector('[name="kpi_text"]').value = selectedKpiText;
                    form.querySelector('[name="measurement"]').value = viewMeasurementKpi;
                    form.querySelector('[name="counter"]').value = viewCounterKpi;
                    form.querySelector('[name="polarization"]').value = viewPolarizationKpi;

                    kpiData = KTIndividualPerformanceAppraisalList.kpiData();

                    const row = kpiData.find(kpi => kpi.id === id);

                    if (row) {
                        submitButton.disabled = false;
                        
                        form.querySelector('[name="kpi_individual_id"]').value = row.id;

                        if (row.actual_id) {
                            form.querySelector('[name="actual_id"]').value = row.actual_id;
                            await fetchActual(row.actual_id);
                        } else {
                            form.querySelector('[name="actual_id"]').value = null;
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