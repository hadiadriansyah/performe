"use strict";

// Class definition
const KTModalKpiAdd = (() => {
    let element;
    let form;
    let modal;
    let submitButton;
    let cancelButton;
    let closeButton;

    let validator;
    let action;
    
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

    const initAddKpi = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'number_of_rows': {
                        validators: {
                            notEmpty: {
                                message: 'Number of rows is required'
                            },
                            numeric: {
                                message: 'Number of rows must be a number'
                            },
                            between: {
                                message: 'Number of rows must be greater than or equal to 1',
                                min: 1,
                                max: Infinity
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
                    handleAddKpi();
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

    const handleAddKpi = async () => {
        const numberOfRows = form.querySelector('[name="number_of_rows"]').value;

        for (let i = 0; i < numberOfRows; i++) {
            let newItem = {
                ...newItemKpi
            }

            const newKPIDetail = {
                id: generateUUIDv7(),
                kpi_id: null,
                target_id: null,
                actual_id: null,
                weight: 0,
                score: 0,
                mode: 'add'
            };
            newItem = {
                ...newItem,
                ...newKPIDetail
            };

            KTKpiIndividualList.kpiData().push(newItem);

            KTKpiIndividualList.datatable().clear().rows.add(KTKpiIndividualList.kpiData()).draw();
        }

        form.reset();
        modal.hide();
    };

    const generateUUIDv7 = () => {
        const now = Date.now();
        const ts = BigInt(now);
    
        // Generate 10 random bytes
        const randomBytes = new Uint8Array(10);
        window.crypto.getRandomValues(randomBytes);
    
        // Construct the UUID v7
        const timeHigh = (ts >> 28n) & 0xFFFFFFFFn;
        const timeMid = (ts >> 12n) & 0xFFFFn;
        const timeLow = ts & 0xFFFn;
        const version = 0x7n;
    
        const uuid = [
            timeHigh.toString(16).padStart(8, '0'), // time_high
            timeMid.toString(16).padStart(4, '0'), // time_mid
            version.toString(16) + timeLow.toString(16).padStart(3, '0'), // version and time_low
            (randomBytes[0] & 0x3F | 0x80).toString(16).padStart(2, '0') + // variant
            randomBytes[1].toString(16).padStart(2, '0'), // random part
            Array.from(randomBytes.slice(2, 8)).map(b => b.toString(16).padStart(2, '0')).join('') // remaining random part
        ].join('-');
    
        return uuid;
    }

    return {
        init: function () {
            element = document.getElementById('kt_modal_add_kpis');
            modal = new bootstrap.Modal(element);

            form = element.querySelector('#kt_modal_add_kpis_form');
            submitButton = form.querySelector('[data-kt-kpis-modal-action="submit"]');
            cancelButton = form.querySelector('[data-kt-kpis-modal-action="cancel"]');
            closeButton = element.querySelector('[data-kt-kpis-modal-action="close"]');

            initAddKpi();

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
    KTModalKpiAdd.init();
});