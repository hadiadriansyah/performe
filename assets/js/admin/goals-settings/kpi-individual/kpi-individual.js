"use strict";

// Class definition
const KTKpiIndividual = (() => {
    let formKpiIndividual;
    let goalsSettingsContainer;
    let tableGoalsSettings;
    let kpiContainer;
    let targetSubmitContainer;
    let goalsSettingsDatatable = [];
    let goalsSettingsData = {};
    let kpiPaIndividualId;
    let kpiYearPeriodId;
    
    const initOptionSelect2 = () => {
        $('[name="employee_id"]').select2({
            ajax: {
                url: `${siteUrl}goals_settings/kpi_individual/get_employee_options`,
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
            setupGoalsSettings();
            const employeeId = formKpiIndividual.querySelector('[name="employee_id"]').value;
            const npp = formKpiIndividual.querySelector('[name="npp"]');
            const positionSelect = formKpiIndividual.querySelector('[name="position_id"]');
            const description = formKpiIndividual.querySelector('#description');
            const unitSelect = formKpiIndividual.querySelector('[name="unit_id"]');
            const placementUnitSelect = formKpiIndividual.querySelector('[name="placement_unit_id"]');

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
        });

        $('[name="year_period_id"]').select2({
            ajax: {
                url: `${siteUrl}goals_settings/kpi_individual/get_year_period_options`,
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
            kpiYearPeriodId = this.value;
            setupGoalsSettings();
        });
    }

    const getPositionUnitPlacementUnitByEmployeeId = async (employeeId) => {
        try {
            const response = await fetch(`${siteUrl}goals_settings/kpi_individual/get_position_unit_placement_unit_by_employee_id/${employeeId}`);
            const result = await response.json();
            return result.data;
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
    
    const initEmployee = async () => {
        const employee = await getEmployee();

        if (employee) {
            const employeeSelect = formKpiIndividual.querySelector('[name="employee_id"]');
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
            const response = await fetch(`${siteUrl}goals_settings/kpi_individual/get_employee`, { method: 'GET' });
            const result = await response.json();
            return result.data;
        } catch (error) {
            return false;
        }
    }

    const setupGoalsSettings = async () => {
        goalsSettingsContainer.classList.add('d-none');
        kpiContainer.classList.add('d-none');
        targetSubmitContainer.classList.add('d-none');

        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        
        const yearPeriodId = formKpiIndividual.querySelector('[name="year_period_id"]').value;
        const employeeId = formKpiIndividual.querySelector('[name="employee_id"]').value;

        if (yearPeriodId && employeeId) {
            const goalsSettings = await getGoalsSettings(yearPeriodId, employeeId);
            const tbody = tableGoalsSettings.querySelector('tbody');
            tbody.innerHTML = '';
            if (goalsSettings) {
                goalsSettingsDatatable = goalsSettings;
                goalsSettingsDatatable.forEach((gs, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${gs.position_name}</td>
                        <td>${gs.unit_name}</td>
                        <td>${gs.placement_unit_name}</td>
                        <td>${getMonthName(gs.from_month)} to ${getMonthName(gs.to_month)}</td>
                        <td class="text-end">
                            <div class="btn btn-icon btn-sm btn-color-gray-400 btn-active-icon-success me-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="View" data-id="${gs.id}" data-kt-create-gs-table-filter="view_row">
                                <span class="svg-icon svg-icon-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="black" />
                                        <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="black" />
                                    </svg>
                                </span>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                goalsSettingsContainer.classList.remove('d-none');
                $('[data-bs-toggle="tooltip"]').tooltip();
                handleGoalsSettingsViewRows();
            } else {
                KTAlertDialog.showErrorMessage("Employee has not created goals settings yet");
            }
        }
        KTPageLoader.hidePageLoading();
        KTPageLoader.removePageLoading();
    }

    const getGoalsSettings = async (yearPeriodId, employeeId) => {
        try {
            const response = await fetch(`${siteUrl}goals_settings/kpi_individual/get_goals_settings`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    year_period_id: yearPeriodId,
                    employee_id: employeeId
                }).toString()
            });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            return result.data;
        } catch (error) {
            return false;
        }
    }

    const handleGoalsSettingsViewRows = () => {
        const viewButtons = tableGoalsSettings.querySelectorAll('[data-kt-create-gs-table-filter="view_row"]');
        viewButtons.forEach(button => {
            button.addEventListener('click', async function() {
                KTPageLoader.createPageLoading();
                KTPageLoader.showPageLoading();
                try {
                    kpiContainer.classList.remove('d-none');
                    targetSubmitContainer.classList.remove('d-none');
                    const id = this.getAttribute('data-id');
                    goalsSettingsData = goalsSettingsDatatable.find(gs => gs.id === id);
                    kpiPaIndividualId = id;
                    await KTKpiIndividualList.setupKpiData();
                } catch (error) {
                    KTAlertDialog.showErrorMessage('Error while loading KPI data');
                    KTPageLoader.hidePageLoading();
                    KTPageLoader.removePageLoading();
                } finally {
                    KTPageLoader.hidePageLoading();
                    KTPageLoader.removePageLoading();
                }
            });
        });
    }

    const getMonthName = (month) => {
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        return monthNames[month - 1] || 'Invalid Month';
    }

    return {
        init: function () {
            formKpiIndividual = document.querySelector('#kt_kpi_individual_form');

            goalsSettingsContainer = document.querySelector('#kt_goals_settings_container');
            tableGoalsSettings = goalsSettingsContainer.querySelector('#kt_table_goals_settings');

            kpiContainer = document.querySelector('#kt_kpi_container');
            targetSubmitContainer = document.querySelector('#kt_target_submit_container');

            initOptionSelect2();
            initEmployee();
            setupGoalsSettings();

            formKpiIndividual.addEventListener('reset', () => {
                const selects = formKpiIndividual.querySelectorAll('select');
                selects.forEach((select) => {
                    select.value = null;
                    select.dispatchEvent(new Event('change'));
                });
            });
        },
        getGoalsSettingsData: function () {
            return goalsSettingsData;
        },
        getKpiPaIndividualId: function () {
            return kpiPaIndividualId;
        },
        getKpiYearPeriodId: function () {
            return kpiYearPeriodId;
        }
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTKpiIndividual.init();
});