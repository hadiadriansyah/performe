"use strict";

const KTCreateGoalsSettings = function () {
    let form;
    let submitButton;

    let validator;

    const initCreateGs = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'employee_id': {
                        validators: {
                            notEmpty: {
                                message: 'Employee is required'
                            }
                        }
                    },
                    'unit_id': {
                        validators: {
                            notEmpty: {
                                message: 'Unit is required'
                            }
                        }
                    },
                    'position_id': {
                        validators: {
                            notEmpty: {
                                message: 'Position is required'
                            }
                        }
                    },
                    'placement_unit_id': {
                        validators: {
                            notEmpty: {
                                message: 'Placement unit is required'
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
    };
    
    const initOptionSelect2 = () => {
        $('[name="employee_id"]').select2({
            ajax: {
                url: `${siteUrl}goals_settings/create_gs/get_employee_options`,
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
        }).on('change', async function () {
            const employeeId = form.querySelector('[name="employee_id"]').value;
            validator.revalidateField('employee_id');
            const npp = form.querySelector('[name="npp"]');
            const positionSelect = form.querySelector('[name="position_id"]');
            const description = form.querySelector('#description');
            const unitSelect = form.querySelector('[name="unit_id"]');
            const placementUnitSelect = form.querySelector('[name="placement_unit_id"]');
            
            npp.value = '';
            positionSelect.value = null;
            positionSelect.dispatchEvent(new Event('change'));
            unitSelect.value = null;
            unitSelect.dispatchEvent(new Event('change'));
            placementUnitSelect.value = null;
            placementUnitSelect.dispatchEvent(new Event('change'));
            description.textContent = '';

            const positionUnitPlacementUnit = await getPositionUnitPlacementUnitByEmployeeId(employeeId);

            if (positionUnitPlacementUnit) {
                npp.value = positionUnitPlacementUnit.npp;
                updateSelectOptions(positionSelect, positionUnitPlacementUnit.position);
                updateSelectOptions(unitSelect, positionUnitPlacementUnit.unit);
                updateSelectOptions(placementUnitSelect, positionUnitPlacementUnit.placement_unit);
                description.textContent = positionUnitPlacementUnit.description;
            } else {
                npp.value = '';
                clearSelectOptions(positionSelect);
                clearSelectOptions(unitSelect);
                clearSelectOptions(placementUnitSelect);
                description.textContent = '';
            }
            submitButton.disabled = false;
            submitButton.classList.remove('btn-danger');
            submitButton.classList.add('btn-success');
        });

        $('[name="year_period_id"]').select2({
            ajax: {
                url: `${siteUrl}goals_settings/create_gs/get_year_period_options`,
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
        });
    }

    const initEmployee = async () => {
        const employee = await getEmployee();

        if (employee) {
            const employeeSelect = form.querySelector('[name="employee_id"]');
            while (employeeSelect.firstChild) {
                employeeSelect.removeChild(employeeSelect.firstChild);
            }
            const employeeNewOption = document.createElement('option');
            employeeNewOption.value = employee.id_peg;
            employeeNewOption.text = employee.nama;
            employeeNewOption.selected = true;
            employeeSelect.appendChild(employeeNewOption);
            employeeSelect.dispatchEvent(new Event('change'));
        }
    }

    const getEmployee = async () => {
        try {
            const response = await fetch(`${siteUrl}goals_settings/create_gs/get_employee`, { method: 'GET' });
            const result = await response.json();
            const data = result.data;
            return data;
        } catch (error) {
            return false;
        }
    }

    const getPositionUnitPlacementUnitByEmployeeId = async (employeeId) => {
        try {
            const response = await fetch(`${siteUrl}goals_settings/create_gs/get_position_unit_placement_unit_by_employee_id/${employeeId}`);
            const result = await response.json();
            const data = result.data;
            return data;
        } catch (error) {
            return false;
        }
    }

    const updateSelectOptions = (selectElement, data) => {
        while (selectElement.firstChild) {
            selectElement.removeChild(selectElement.firstChild);
        }
        const newOption = document.createElement('option');
        newOption.value = data.id;
        newOption.text = data.nm_jabatan || data.nm_unit_kerja;
        newOption.selected = true;
        selectElement.appendChild(newOption);
        selectElement.dispatchEvent(new Event('change'));
    }
    
    const clearSelectOptions = (selectElement) => {
        selectElement.value = null;
        selectElement.dispatchEvent(new Event('change'));
        while (selectElement.firstChild) {
            selectElement.removeChild(selectElement.firstChild);
        }
    }

    const handleFormSubmission = async (submitButton) => {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
        
        const formData = new URLSearchParams(new FormData(form));
        const unitIdElement = form.querySelector('[name="unit_id"]');
        const positionIdElement = form.querySelector('[name="position_id"]');
        const placementUnitIdElement = form.querySelector('[name="placement_unit_id"]');
        
        formData.append('unit_id', unitIdElement ? unitIdElement.value : '');
        formData.append('position_id', positionIdElement ? positionIdElement.value : '');
        formData.append('placement_unit_id', placementUnitIdElement ? placementUnitIdElement.value : '');

        try {
            const formDetails = {
                url: 'goals_settings/create_gs/create_gs',
                formData: formData
            };
            await submitFormData(formDetails);
        } catch (error) {
            console.log(error);
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
            console.log(error);
            KTAlertDialog.showErrorMessage("Error while submitting data!.");
        }
    };

    const handleFormResponse = (response) => {
        if (response.status === 'success') {
            KTAlertDialog.showSuccessMessage(response.message);
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    return {
        init: function () {
            form = document.querySelector('#kt_create_gs_form');
            submitButton = form.querySelector('[data-kt-create-gs-action="submit"]');

            initCreateGs();
            initOptionSelect2();
            initEmployee();

            form.addEventListener('reset', () => {
                const selects = form.querySelectorAll('select');
                selects.forEach((select) => {
                    select.value = null;
                    select.dispatchEvent(new Event('change'));
                });
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTCreateGoalsSettings.init();
});