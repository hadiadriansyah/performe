"use strict";

const KTApprovalPa = (() => {
    let formApprovalPa;
    let approvalEmployeeContainer;
    let kpiContainer;
    let paSubmitContainer;

    let paYearPeriodId;
    let approvalPaId;

    let datatable;
    let table;

    let approvalEmployeeDatatable = [];
    let approvalEmployeeData = {};

    const initOptionSelect2 = () => {
        $('[name="employee_id"]').select2({
            ajax: {
                url: `${siteUrl}approval/approval_performance_appraisal/get_employee_options`,
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
            const employeeId = formApprovalPa.querySelector('[name="employee_id"]').value;
            const npp = formApprovalPa.querySelector('[name="npp"]');
            const positionSelect = formApprovalPa.querySelector('[name="position_id"]');
            const description = formApprovalPa.querySelector('#description');
            const unitSelect = formApprovalPa.querySelector('[name="unit_id"]');
            const placementUnitSelect = formApprovalPa.querySelector('[name="placement_unit_id"]');

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

            setupApprovalEmployee();
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('[name="year_period_id"]').select2({
            ajax: {
                url: `${siteUrl}approval/approval_performance_appraisal/get_year_period_options`,
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
            paYearPeriodId = this.value;
            setupApprovalEmployee();
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('[name="year_period_id"]').select2({
            ajax: {
                url: `${siteUrl}approval/approval_performance_appraisal/get_year_period_options`,
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
            paYearPeriodId = this.value;
            setupApprovalEmployee();
        });
    }

    const handleSearchDataApprovalEmployee = function () {
        const filterSearch = document.querySelector('[data-kt-approval-employee-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    };

    const handleFilterDataApprovalEmployee = function () {
        const filterPuk = document.querySelector('[data-kt-approval-employee-table-filter="puk"]');
        const filterPukStatus = document.querySelector('[data-kt-approval-employee-table-filter="puk_status"]');

        const filterButton = document.querySelector('[data-kt-approval-employee-table-filter="filter"]');
        
        filterButton.addEventListener('click', function (e) {
            const puk = filterPuk.value;
            const pukStatus = filterPukStatus.value;

            const pukValue = puk === 'All' ? '' : (puk === '1' ? 'As PUK 1' : 'As PUK 2');
            const pukStatusValue = pukStatus === 'All' ? '' : (pukStatus === '0' ? 'Not Submitted' : (pukStatus === '2' ? 'Approved' : 'Rejected'));

            datatable.columns(6).search(pukValue).draw();
            datatable.columns(7).search(pukStatusValue).draw();
        });
    };

    const getPositionUnitPlacementUnitByEmployeeId = async (employeeId) => {
        try {
            const response = await fetch(`${siteUrl}approval/approval_performance_appraisal/get_position_unit_placement_unit_by_employee_id/${employeeId}`);
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
            const employeeSelect = formApprovalPa.querySelector('[name="employee_id"]');
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
            const response = await fetch(`${siteUrl}approval/approval_performance_appraisal/get_employee`, { method: 'GET' });
            const result = await response.json();
            return result.data;
        } catch (error) {
            return false;
        }
    }

    const setupApprovalEmployee = async () => {
        approvalEmployeeContainer.classList.add('d-none');
        kpiContainer.classList.add('d-none');
        paSubmitContainer.classList.add('d-none');
        
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const yearPeriodId = formApprovalPa.querySelector('[name="year_period_id"]').value;
            const unitId = formApprovalPa.querySelector('[name="unit_id"]').value;
            const placementUnitId = formApprovalPa.querySelector('[name="placement_unit_id"]').value;
            const positionId = formApprovalPa.querySelector('[name="position_id"]').value;

            const puk = document.querySelector('[data-kt-approval-employee-table-filter="puk"]').value;
            const pukStatus = document.querySelector('[data-kt-approval-employee-table-filter="puk_status"]').value;

            approvalEmployeeData = {};

            if (yearPeriodId && unitId && placementUnitId && positionId) {
                const approvalEmployees = await getApprovalEmployees(yearPeriodId, unitId, placementUnitId, positionId);

                if (approvalEmployees) {
                    approvalEmployeeDatatable = approvalEmployees;
                    approvalEmployeeDatatable.forEach(item => {
                        item.puk = puk;
                        item.puk_status = pukStatus;
                    });

                    approvalEmployeeContainer.classList.remove('d-none');
                }
                renderApprovalEmployeeTable();
            }
        } catch (error) {

        } finally {
            KTPageLoader.hidePageLoading();
            KTPageLoader.removePageLoading();
        }
    }

    const renderApprovalEmployeeTable = () => {
        if ($.fn.DataTable.isDataTable(table)) {
            datatable = $(table).DataTable();
            datatable.clear().rows.add(approvalEmployeeDatatable).draw();
        } else {
            datatable = $(table).DataTable({
                responsive: true,
                info: false,
                order: [],
                data: approvalEmployeeDatatable,
                paging: true,
                columns: [
                    { data: 'employee_name' },
                    { data: 'employee_npp' },
                    { data: 'position_name' },
                    { data: 'unit_name' },
                    { data: 'placement_unit_name' },
                    { data: null },
                    { data: null },
                    { data: null },
                    { data: null }
                ],
                columnDefs: [
                    {
                        targets: 5,
                        orderable: false,
                        render: function (data, type, row) {
                            return `${getMonthName(row.from_month)} to ${getMonthName(row.to_month)}`;
                        }
                    },
                    {
                        targets: 6,
                        orderable: false,
                        render: function (data, type, row) {
                            const placementUnitId = formApprovalPa.querySelector('[name="placement_unit_id"]').value;
                            const positionId = formApprovalPa.querySelector('[name="position_id"]').value;

                            return (row.puk_1_unit === placementUnitId && row.puk_1_position === positionId) ? 'As PUK 1' :
                                   (row.puk_2_unit === placementUnitId && row.puk_2_position === positionId) ? 'As PUK 2' : '';
                        }
                    },
                    {
                        targets: 7,
                        orderable: false,
                        render: function (data, type, row) {
                            const placementUnitId = formApprovalPa.querySelector('[name="placement_unit_id"]').value;
                            const positionId = formApprovalPa.querySelector('[name="position_id"]').value;

                            const statusMap = {
                                0: 'badge-secondary',
                                2: 'badge-success',
                                3: 'badge-danger'
                            };
                            const statusText = {
                                0: 'Not Submitted',
                                2: 'Approved',
                                3: 'Rejected'
                            };

                            const status = (row.puk_1_unit === placementUnitId && row.puk_1_position === positionId) ? row.puk_1_status :
                                           (row.puk_2_unit === placementUnitId && row.puk_2_position === positionId) ? row.puk_2_status : null;

                            return status !== null && status in statusMap ? `<span class="badge ${statusMap[status]} w-100">${statusText[status]}</span>` : '';
                        }
                    },
                    {
                        targets: -1,
                        orderable: false,
                        className: 'd-flex justify-content-between align-items-center',
                        render: function (data, type, row) {
                            return `
                            <div class="btn btn-icon btn-sm btn-color-gray-400 btn-active-icon-success me-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="View" data-id="${row.id}" data-kt-approval-employee-table-filter="view_row">
                                <span class="svg-icon svg-icon-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="black" />
                                        <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="black" />
                                    </svg>
                                </span>
                            </div>
                            `;
                        },
                    },
                ],
                drawCallback: function () {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    handleApprovalEmployeeViewRows();
                }
            });
        }
    }

    const handleReloadApprovalEmployee = () => {
        const reloadButton = document.querySelector('[data-kt-approval-employee-table-filter="reload_approval_employee"]');
        reloadButton.addEventListener('click', async function() {
            setupApprovalEmployee();
            // KTPageLoader.createPageLoading();
            // KTPageLoader.showPageLoading();
            // try {
                // kpiContainer.classList.add('d-none');
                // paSubmitContainer.classList.add('d-none');
                // reloadButton.classList.add('d-none');
                // renderApprovalEmployeeTable();
            // } catch (error) {
            //     KTPageLoader.hidePageLoading();
            //     KTPageLoader.removePageLoading();
            // } finally {
            //     KTPageLoader.hidePageLoading();
            //     KTPageLoader.removePageLoading();
            // }
        });
    }

    let isEventListenerAdded = false;
    const handleApprovalEmployeeViewRows = () => {
        const reloadButton = document.querySelector('[data-kt-approval-employee-table-filter="reload_approval_employee"]');

        if (!isEventListenerAdded) {
            table.addEventListener('click', async function(event) {
                const button = event.target.closest('[data-kt-approval-employee-table-filter="view_row"]');
                if (!button) return;

                KTPageLoader.createPageLoading();
                KTPageLoader.showPageLoading();
                try {
                    kpiContainer.classList.remove('d-none');
                    paSubmitContainer.classList.remove('d-none');
                    reloadButton.classList.remove('d-none');
                    const id = button.getAttribute('data-id');
                    
                    approvalEmployeeData = approvalEmployeeDatatable.find(item => item.id === id);
                    showDataSelected(id);
                    approvalPaId = id;
                    await KTApprovalPaList.setupKpiData();
                } catch (error) {
                    console.log(error)
                    KTAlertDialog.showErrorMessage('Error while loading KPI data');
                    KTPageLoader.hidePageLoading();
                    KTPageLoader.removePageLoading();
                } finally {
                    KTPageLoader.hidePageLoading();
                    KTPageLoader.removePageLoading();
                }
            });

            isEventListenerAdded = true;
        }
    }

    const showDataSelected = (id) => {
        datatable.rows().every(function(rowIdx, tableLoop, rowLoop) {
            const data = this.data();
            const matchesId = id === '' || data.id === id;

            if (matchesId) {
                $(this.node()).show();
            } else {
                $(this.node()).hide();
            }
        });
        datatable.draw();
    }

    const getApprovalEmployees = async (yearPeriodId, unitId, placementUnitId, positionId) => {
        try {
            const response = await fetch(`${siteUrl}approval/approval_performance_appraisal/get_approval_employees`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    year_period_id: yearPeriodId,
                    unit_id: unitId,
                    placement_unit_id: placementUnitId,
                    position_id: positionId
                }).toString()
            });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            return result.data;
        } catch (error) {
            return false;
        }
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
            formApprovalPa = document.querySelector('#kt_approval_pa_form');
            approvalEmployeeContainer = document.querySelector('#kt_approval_employee_container');

            table = document.querySelector('#kt_table_approval_employee');

            kpiContainer = document.querySelector('#kt_kpi_container');
            paSubmitContainer = document.querySelector('#kt_pa_submit_container');

            initOptionSelect2();
            initEmployee();
            setupApprovalEmployee();
            handleSearchDataApprovalEmployee();
            handleFilterDataApprovalEmployee();
            handleReloadApprovalEmployee();

            formApprovalPa.addEventListener('reset', () => {
                const selects = formApprovalPa.querySelectorAll('select');
                selects.forEach((select) => {
                    select.value = null;
                    select.dispatchEvent(new Event('change'));
                });
            });
        },
        getPaYearPeriodId: function () {
            return paYearPeriodId;
        },
        getApprovalPaId: function () {
            return approvalPaId;
        },
        getApprovalEmployeeData: function () {
            return approvalEmployeeData;
        },
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTApprovalPa.init();
});