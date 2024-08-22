"use strict";

const KTKpiIndividualList = (() => {
    let kpiContainer;
    let submitButton;
    let cancelSubmitButton;

    let datatable;
    let table;
    let dataCheckboxes = [];

    let isSubmit = false;
    let kpiData = [];
    let kpiDataBeforeEdit = [];

    const initOptionSelect2 = (row) => {
        const yearPeriodId = document.querySelector('[name="year_period_id"]').value;
        $(`#kpi_id_${row.id}`).select2({
            ajax: {
                url: `${siteUrl}goals_settings/kpi_individual/get_kpi_options_by_year_period_id`,
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term || '',
                    page: params.page || 1,
                    year_period_id: yearPeriodId
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
        }).on('select2:select', async function (e) {
            const kpi = await getKpiById(e.target.value);
            if (kpi) {
                const kpiIdElement = document.querySelector(`#kpi_id_${row.id}`);
                const selectNewOption = document.createElement('option');
                selectNewOption.value = kpi.id;
                selectNewOption.text = kpi.kpi;
                selectNewOption.selected = true;
                kpiIdElement.appendChild(selectNewOption);
                kpiIdElement.dispatchEvent(new Event('change'));

                const measurementElement = document.querySelector(`#measurement_${row.id}`);
                measurementElement.innerText = kpi.measurement;

                const counterElement = document.querySelector(`#counter_${row.id}`);
                counterElement.innerText = kpi.counter;

                const polarizationElement = document.querySelector(`#polarization_${row.id}`);
                polarizationElement.innerText = kpi.polarization;

                const paIndividualId = KTKpiIndividual.getKpiPaIndividualId();
                const yearPeriodId = KTKpiIndividual.getKpiYearPeriodId();
                
                const data = {
                    id: row.id,
                    pa_individual_id: paIndividualId,
                    year_period_id: yearPeriodId,
                    mode: row.mode,
                    kpi_id: kpi.id,
                    weight: row.weight,
                }

                if (checkPercentage(data) > 100) {
                    KTAlertDialog.showErrorMessage('Total weight is more than 100%, please check your data.');
                } else {
                    await storeUpdateKpi(data);
                }
            }
        });
    }

    async function storeUpdateKpi(data, isSave = false) {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}goals_settings/kpi_individual/store_update_kpi`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data).toString()
            });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            await handleStoreUpdateFormResponse(result, data, isSave);
        } catch (error) {
            KTAlertDialog.showErrorMessage('Error while submitting data');
            KTPageLoader.hidePageLoading();
            KTPageLoader.removePageLoading();
        } finally {
            calculateAndDisplayPercentage();
            KTPageLoader.hidePageLoading();
            KTPageLoader.removePageLoading();
        }
    }
    
    async function handleStoreUpdateFormResponse(response, data, isSave) {
        if (response.status === 'success') {
            document.querySelector(`#error-kpi_id_${data.id}`).innerHTML = '';

            let id = data.id;
            const updateKpi = kpiData.find(kpi => kpi.id === id);
            if (updateKpi) {
                updateKpi.kpi_id = data.kpi_id;
                updateKpi.weight = parseFloat(data.weight);
                updateKpi.mode = '';
            }
            
            if (isSave) {
                KTAlertDialog.showSuccessMessage(response.message);
                const dataKpiBeforeEditIndex = kpiDataBeforeEdit.findIndex(item => item.id === id);
                if (dataKpiBeforeEditIndex !== -1) {
                    kpiDataBeforeEdit.splice(dataKpiBeforeEditIndex, 1);
                }
                toggleEditButtons(id, true);
            }
        } else {
            KTAlertDialog.showErrorMessage(response.message);
            document.querySelector(`#error-kpi_id_${data.id}`).innerHTML = response.message;
        }
    }

    const setupKpiData = async () => {
        const goalsSettingsData = KTKpiIndividual.getGoalsSettingsData() ?? {};
        kpiContainer.querySelector('[name="position_name"]').value = goalsSettingsData.position_name;
        const input = kpiContainer.querySelector('.auto-width-input');
        input.style.width = ((input.value.length + 1) * 8) + 'px';
        kpiData = [];
        const kpiIndividual = await getKpiIndividualByPaId(goalsSettingsData.id);
        if (kpiIndividual) {
            kpiData = kpiIndividual;
            kpiData.forEach(item => {
                item.mode = null;
            });
        }
        
        renderKpi();
        await KTSubmitTarget.setupTargetSubmitApproval(goalsSettingsData);
    }

    const renderKpi = () => {
        if ($.fn.DataTable.isDataTable(table)) {
            datatable = $(table).DataTable();
            datatable.clear().rows.add(kpiData).draw();
        } else {
            datatable = $(table).DataTable({
                // responsive: true,
                info: false,
                order: [],
                data: kpiData,
                paging: false,
                columns: [
                    { data: 'id' },
                    { data: null },
                    { data: null },
                    { data: null },
                    { data: null },
                    { data: null },
                    { data: null },
                    { data: null },
                    { data: null }
                ],
                columnDefs: [
                    {
                        targets: 0,
                        orderable: false,
                        render: function (data) {
                            return `
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="${data}" />
                                </div>`;
                        }
                    },
                    {
                        targets: 1,
                        orderable: false,
                        render: function (data, type, row) {
                            return `<select id="kpi_id_${row.id}" name="kpi_id" aria-label="Select KPI" data-control="select2" data-placeholder="Select KPI..." class="form-select form-select-solid fw-bolder select2-readonly">
                                <option></option>
                            </select>
                            <div class="error-message small text-danger mt-1" id="error-kpi_id_${row.id}"></div>
                            `;
                        }
                    },
                    {
                        targets: 2,
                        render: function (data, type, row) {
                            return `<span id="measurement_${row.id}">-</span>`;
                        }
                    },
                    {
                        targets: 3,
                        render: function (data, type, row) {
                            return `<button type="button" class="btn ${row.target_id ? 'btn-light-success' : 'btn-light-danger'} btn-sm" id="target_${row.id}" data-bs-toggle="modal" data-bs-target="#kt_modal_target_actual"
                            data-kt-target-actual-button-action="target"
                            data-id="${row.id}"
                            disabled>Target</button>`;
                        }
                    },
                    {
                        targets: 4,
                        render: function (data, type, row) {
                            return `<button type="button" class="btn btn-light-danger btn-sm" id="actual_${row.id}" disabled>Actual</button>`;
                        }
                    },
                    {
                        targets: 5,
                        render: function (data, type, row) {
                            return `<span id="counter_${row.id}">-</span>`;
                        }
                    },
                    {
                        targets: 6,
                        render: function (data, type, row) {
                            return `<span id="polarization_${row.id}">-</span>`;
                        }
                    },
                    {
                        targets: 7,
                        render: function (data, type, row) {
                            return `<input id="weight_${row.id}" type="text" class="form-control" value="${row.weight}" disabled/>`;
                        }
                    },
                    {
                        targets: -1,
                        orderable: false,
                        className: 'text-end',
                        render: function (data, type, row) {
                            return `
                                <div class="btn btn-success btn-icon d-none" data-bs-dismiss="click" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Save KPI" data-kt-kpi-table-filter="save_row" data-id="${row.id}">
                                    <span class="svg-icon svg-icon-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="black"/>
                                    </svg></span>
                                </div>
                                <div class="btn btn-danger btn-icon d-none" data-bs-dismiss="click" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Edit KPI" data-kt-kpi-table-filter="cancel_row" data-id="${row.id}">
                                    <span class="svg-icon svg-icon-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black"/>
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"/>
                                    </svg></span>
                                </div>
                                <div class="btn btn-warning btn-icon" data-bs-dismiss="click" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Edit KPI" data-kt-kpi-table-filter="edit_row" data-id="${row.id}">
                                    <span class="svg-icon svg-icon-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="btn btn-danger btn-icon" data-bs-dismiss="click" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Delete KPI" data-id="${row.id}" data-kt-kpi-table-filter="delete_row" id="kt_modal_view_event_delete">
                                    <span class="svg-icon svg-icon-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black" />
                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black" />
                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black" />
                                        </svg>
                                    </span>
                                </div>
                            `;
                        },
                    },
                ],
                drawCallback: function () {
                    const api = this.api();
                    api.rows().every(function() {
                        const row = this.data();
                        initOptionSelect2(row);
                        initKpiSelect(row);
                    });
                    
                    initToggleToolbar();
                    handleDeleteRows();
                    toggleToolbars();
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    
                    calculateAndDisplayPercentage();
                }
            });
        }
    }

    async function initKpiSelect(row) {
        if (row.kpi_id) {
            const data = await getKpiById(row.kpi_id);
            if (data) {
                const kpiIdElement = document.querySelector(`#kpi_id_${row.id}`);
                const selectNewOption = document.createElement('option');
                selectNewOption.value = data.id;
                selectNewOption.text = data.kpi;
                selectNewOption.selected = true;
                kpiIdElement.appendChild(selectNewOption);
                kpiIdElement.dispatchEvent(new Event('change'));

                const measurementElement = document.querySelector(`#measurement_${row.id}`);
                measurementElement.innerText = data.measurement;

                const counterElement = document.querySelector(`#counter_${row.id}`);
                counterElement.innerText = data.counter;

                const polarizationElement = document.querySelector(`#polarization_${row.id}`);
                polarizationElement.innerText = data.polarization;
            }
        }
    }

    async function getKpiById(id) {
        try {
            const response = await fetch(`${siteUrl}goals_settings/kpi_individual/get_kpi_by_id/${id}`, { method: 'GET' });
            const result = await response.json();
            const data = result.data;
            return data;
        } catch (error) {
            return false;
        }
    }
    
    const initToggleToolbar = function () {
        const container = document.querySelector('#kt_table_kpis');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        checkboxes.forEach(c => {
            c.addEventListener('click', function () {
                setTimeout(toggleToolbars, 50);
            });
        });
    };

    const toggleToolbars = function () {
        const container = document.querySelector('#kt_table_kpis');
        const toolbarBase = document.querySelector('[data-kt-kpi-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-kpi-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-kpi-table-select="selected_count"]');
        const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');

        let checkedState = false;
        let count = 0;
        dataCheckboxes = [];
        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
                const row = c.closest('tr');
                const text = row.querySelector('td:nth-child(2)').innerText;
                dataCheckboxes.push({ value: c.value, text: text });
            }
        });

        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    };

    const calculateAndDisplayPercentage = () => {
        let totalPercentage = 0;
        totalPercentage = kpiData.reduce((sum, item) => sum + parseFloat(item.weight ?? 0), 0);

        updatePercentage(totalPercentage);
    }

    const updatePercentage = (percentage) => {
        const percentageContainer = document.getElementById('kt_kpi_percentage_container');
        const percentageElement = document.getElementById('kt_kpi_percentage');
        const percentageInputElement = document.getElementById('kt_kpi_percentage_input');
        if (percentage < 100) {
            percentageContainer.classList.add('border-danger');
            percentageContainer.classList.add('text-danger');
            percentageContainer.classList.remove('border-success');
            percentageContainer.classList.remove('text-success');
        } else {
            percentageContainer.classList.remove('border-danger');
            percentageContainer.classList.remove('text-danger');
            percentageContainer.classList.add('border-success');
            percentageContainer.classList.add('text-success');
        }
        percentageElement.classList.remove('counted');
        percentageElement.setAttribute('data-kt-countup-value', percentage);
        percentageInputElement.value = percentage;
        percentageInputElement.dispatchEvent(new Event('input'));
    }

    const initSubmitButton = () => {
        const percentageInput = document.querySelector('#kt_kpi_percentage_input');
        percentageInput.addEventListener('input', async function() {
            const percentage = parseFloat(this.value) || 0;
            if (percentage < 100) {
                isSubmit = false;
            } else {
                const kpiIndividualTarget = await getKpiIndividualTargetByPaIndividualIdYearPeriodId();
                
                if (kpiIndividualTarget.length === kpiData.length) {
                    isSubmit = true;
                } else {
                    isSubmit = false;
                }
            }
            
            toggleSubmitButton();
        });
    }
    
    const toggleSubmitButton = () => {
        const elementTargetSubmit = document.getElementById('kt_table_target_submit');
        const formTargetSubmit = elementTargetSubmit.querySelector('#kt_target_submit_form');
        const targetSubmitButton = formTargetSubmit.querySelector('[data-kt-target-submit-button-action="submit"]');
        if (isSubmit) {
            targetSubmitButton.disabled = false;
            if (targetSubmitButton.classList.contains('btn-secondary')) {
                targetSubmitButton.classList.remove('btn-secondary');
            }
            if (!targetSubmitButton.classList.contains('btn-primary-bs')) {
                targetSubmitButton.classList.add('btn-primary-bs');
            }
        } else {
            targetSubmitButton.disabled = true;
            if (targetSubmitButton.classList.contains('btn-primary-bs')) {
                targetSubmitButton.classList.remove('btn-primary-bs');
            }
            if (!targetSubmitButton.classList.contains('btn-secondary')) {
                targetSubmitButton.classList.add('btn-secondary');
            }
        }
    }

    const getKpiIndividualTargetByPaIndividualIdYearPeriodId = async () => {
        try {
            const response = await fetch(`${siteUrl}goals_settings/kpi_individual/get_kpi_individual_target_by_pa_individual_id_year_period_id`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    pa_individual_id: KTKpiIndividual.getKpiPaIndividualId(),
                    year_period_id: KTKpiIndividual.getKpiYearPeriodId(),
                }).toString()
            });
            const result = await response.json();
            return result.data;
        } catch (error) {
            return false
        }
    }

    const getKpiIndividualByPaId = async (paId) => {
        try {
            const response = await fetch(`${siteUrl}goals_settings/kpi_individual/get_kpi_individual_by_pa_id/${paId}`);
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            return result.data;
        } catch (error) {
            return false;
        }
    }

    const handleSaveRows = function () {
        table.addEventListener('click', async function (e) {
            if (e.target.closest('[data-kt-kpi-table-filter="save_row"]')) {
                e.preventDefault();
                const id = e.target.closest('[data-kt-kpi-table-filter="save_row"]').getAttribute('data-id');

                const viewSelectKpi = table.querySelector(`#kpi_id_${id}`);
                const selectedValue = viewSelectKpi.options[viewSelectKpi.selectedIndex].value;
                const selectedText = viewSelectKpi.options[viewSelectKpi.selectedIndex].text;

                const viewWeightKpi = table.querySelector(`#weight_${id}`);

                const paIndividualId = KTKpiIndividual.getKpiPaIndividualId();
                const yearPeriodId = KTKpiIndividual.getKpiYearPeriodId();

                const row = kpiData.find(kpi => kpi.id === id);

                const data = {
                    id: id,
                    pa_individual_id: paIndividualId,
                    year_period_id: yearPeriodId,
                    kpi_id: selectedValue,
                    mode: row.mode,
                    weight: viewWeightKpi.value,
                }

                if (checkPercentage(data) > 100) {
                    KTAlertDialog.showErrorMessage('Total weight is more than 100%, please check your data.');
                } else {
                    await storeUpdateKpi(data, true);
                }
            }
        });
    };

    const handleCancelRows = function () {
        table.addEventListener('click', async function (e) {
            if (e.target.closest('[data-kt-kpi-table-filter="cancel_row"]')) {
                e.preventDefault();
                const id = e.target.closest('[data-kt-kpi-table-filter="cancel_row"]').getAttribute('data-id');

                const dataBeforeEditIndex = kpiDataBeforeEdit.findIndex(item => item.id === id);
                
                if (dataBeforeEditIndex !== -1) {
                    const dataBeforeEdit = kpiDataBeforeEdit[dataBeforeEditIndex];
                    
                    if (!dataBeforeEdit.kpi_id) {
                        handleDeleteRow(id, 'KPI');
                        return;
                    }

                    const viewSelectKpi = table.querySelector(`#kpi_id_${id}`);
                    const viewMeasurementKpi = table.querySelector(`#measurement_${id}`);
                    const viewCounterKpi = table.querySelector(`#counter_${id}`);
                    const viewPolarizationKpi = table.querySelector(`#polarization_${id}`);
                    const viewWeightKpi = table.querySelector(`#weight_${id}`);

                    const newOption = document.createElement('option');
                    newOption.value = dataBeforeEdit.kpi_id;
                    newOption.text = dataBeforeEdit.kpi_name;
                    newOption.selected = true;
                    newOption.defaultSelected = true;
                    viewSelectKpi.appendChild(newOption);
                    viewSelectKpi.dispatchEvent(new Event('change'));

                    viewMeasurementKpi.textContent = dataBeforeEdit.measurement;
                    viewCounterKpi.textContent = dataBeforeEdit.counter;
                    viewPolarizationKpi.textContent = dataBeforeEdit.polarization;

                    viewWeightKpi.value = dataBeforeEdit.weight;

                    const paIndividualId = KTKpiIndividual.getKpiPaIndividualId();
                    const yearPeriodId = KTKpiIndividual.getKpiYearPeriodId();

                    const data = {
                        id: id,
                        pa_individual_id: dataBeforeEdit.pa_individual_id ?? paIndividualId,
                        year_period_id: dataBeforeEdit.year_period_id ?? yearPeriodId,
                        kpi_id: dataBeforeEdit.kpi_id,
                        mode: dataBeforeEdit.mode,
                        weight: dataBeforeEdit.weight,
                    }
    
                    if (checkPercentage(data) > 100) {
                        KTAlertDialog.showErrorMessage('Total weight is more than 100%, please check your data.');
                    } else {
                        await storeUpdateKpi(data);
                    }
                    kpiDataBeforeEdit.splice(dataBeforeEditIndex, 1);
                }
                
                toggleEditButtons(id, true);
            }
        });
    };

    const handleEditRows = function () {
        table.addEventListener('click', function (e) {
            if (e.target.closest('[data-kt-kpi-table-filter="edit_row"]')) {
                e.preventDefault();
                const id = e.target.closest('[data-kt-kpi-table-filter="edit_row"]').getAttribute('data-id');

                const viewSelectKpi = table.querySelector(`#kpi_id_${id}`);
                const viewMeasurementKpi = table.querySelector(`#measurement_${id}`);
                const viewCounterKpi = table.querySelector(`#counter_${id}`);
                const viewPolarizationKpi = table.querySelector(`#polarization_${id}`);
                const viewWeightKpi = table.querySelector(`#weight_${id}`);

                const selectedValue = viewSelectKpi.options[viewSelectKpi.selectedIndex].value;
                const selectedText = viewSelectKpi.options[viewSelectKpi.selectedIndex].text;
  
                const row = kpiData.find(kpi => kpi.id === id);

                const dataBeforeEdit = {
                    id: id,
                    kpi_id: selectedValue,
                    kpi_name: selectedText,
                    mode: row.mode,
                    pa_individual_id: row.pa_individual_id,
                    year_period_id: row.year_period_id,
                    measurement: viewMeasurementKpi.textContent,
                    counter: viewCounterKpi.textContent,
                    polarization: viewPolarizationKpi.textContent,
                    weight: viewWeightKpi.value,
                }
                kpiDataBeforeEdit.push(dataBeforeEdit);
                toggleEditButtons(id, false);
            }
        });
    };

    const handleDeleteRows = function () {
        table.addEventListener('click', function (e) {
            if (e.target.closest('[data-kt-kpi-table-filter="delete_row"]')) {
                e.preventDefault();
                const id = e.target.closest('[data-kt-kpi-table-filter="delete_row"]').getAttribute('data-id');
                
                const viewSelectKpi = table.querySelector(`#kpi_id_${id}`);
                const selectedText = viewSelectKpi.options[viewSelectKpi.selectedIndex].text;

                Swal.fire({
                    text: "Are you sure you want to delete " + selectedText + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        Swal.fire({
                            text: "Deleting " + selectedText,
                            icon: "info",
                            buttonsStyling: false,
                            showConfirmButton: false,
                            didOpen: async () => {
                                Swal.showLoading();
                                await new Promise(resolve => setTimeout(resolve, 300));
                                await handleDeleteRow(id, selectedText);
                            },
                            timerProgressBar: true,
                        });
                    } else if (result.dismiss === 'cancel') {
                        KTAlertDialog.showErrorMessage(selectedText + " was not deleted.");
                    }
                });
             }
         });
     };

     const handleDeleteRow = async (id, selectedText) => {
        try {
            let kpiDataItem = kpiData.find(item => item.id === id);
        
            if (kpiDataItem && kpiDataItem.mode == 'add') {
                KTAlertDialog.showSuccessMessage('Your data has been deleted')

                const kpiDataIndex = kpiData.findIndex(item => item.id === id);
                if (kpiDataIndex !== -1) {
                    kpiData.splice(kpiDataIndex, 1);
                }

                const dataBeforeEditIndex = kpiDataBeforeEdit.findIndex(item => item.id === id);
                if (dataBeforeEditIndex !== -1) {
                    kpiDataBeforeEdit.splice(dataBeforeEditIndex, 1);
                }

                datatable.clear().rows.add(kpiData).draw();

                KTAlertDialog.showSuccessMessage(selectedText + ' was deleted.');
            } else {
                const response = await fetch(`${siteUrl}goals_settings/kpi_individual/delete_kpi`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ id }).toString()
                });
    
                const result = await response.json();
                if (result.status === 'success') {
                    KTAlertDialog.showSuccessMessage(selectedText + " was deleted.");
                    
                    const kpiDataIndex = kpiData.findIndex(item => item.id === id);
                    if (kpiDataIndex !== -1) {
                        kpiData.splice(kpiDataIndex, 1);
                    }
        
                    const dataBeforeEditIndex = kpiDataBeforeEdit.findIndex(item => item.id === id);
                    if (dataBeforeEditIndex !== -1) {
                        kpiDataBeforeEdit.splice(dataBeforeEditIndex, 1);
                    }
                   
                    datatable.clear().rows.add(kpiData).draw();
                } else {
                    KTAlertDialog.showErrorMessage(response.message);
                }
            }
        } catch (error) {
            KTAlertDialog.showErrorMessage('There was an error deleting the data target.');
        }
    };

    const deleteSelected = document.querySelector('[data-kt-kpi-table-select="delete_selected"]');
    deleteSelected.addEventListener('click', function () {
        Swal.fire({
            text: "Are you sure you want to delete selected KPIs?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            showLoaderOnConfirm: true,
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "No, cancel",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            },
        }).then(async function (result) {
            if (result.value) {
                Swal.fire({
                    text: "Deleting " + dataCheckboxes.length + " KPIs",
                    icon: "info",
                    html: `
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%;" id="progress-bar"></div>
                        </div>
                        <div id="delete-results" class="mt-2 text-start"></div>
                    `,
                    buttonsStyling: false,
                    showConfirmButton: false,
                    didOpen: async () => {
                        Swal.showLoading();
                        handleDeleteSelected(dataCheckboxes);
                    },
                    timerProgressBar: true,
                });
            } else if (result.dismiss === 'cancel') {
                KTAlertDialog.showErrorMessage("Selected KPIs was not deleted.");
            }
        });
    });

    const handleDeleteSelected = async (dataCheckboxes) => {
        const progressBar = document.getElementById('progress-bar');
        const deleteResults = document.getElementById('delete-results');
        let total = dataCheckboxes.length;
        let progress = 0;
        let successCount = 0;
        let failCount = 0;

        for (let i = 0; i < total; i++) {
            try {
                const response = await fetch(`${siteUrl}goals_settings/kpi_individual/delete_kpi`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ id: dataCheckboxes[i].value }).toString()
                });
                await new Promise(resolve => setTimeout(resolve, 300));
                const result = await response.json();
                if (result.status === 'success') {
                    const kpiDataIndex = kpiData.findIndex(item => item.id === dataCheckboxes[i].value);
                    if (kpiDataIndex !== -1) {
                        kpiData.splice(kpiDataIndex, 1);
                    }
        
                    const dataBeforeEditIndex = kpiDataBeforeEdit.findIndex(item => item.id === dataCheckboxes[i].value);
                    if (dataBeforeEditIndex !== -1) {
                        kpiDataBeforeEdit.splice(dataBeforeEditIndex, 1);
                    }
                    successCount++;
                    deleteResults.innerHTML += `<div>Deleted: ${dataCheckboxes[i].text}</div>`;
                } else {
                    failCount++;
                    deleteResults.innerHTML += `<div>Failed to delete: ${dataCheckboxes[i].text}</div>`;
                }
            } catch (error) {
                failCount++;
                deleteResults.innerHTML += `<div>Failed to delete: ${dataCheckboxes[i].text}</div>`;
            }
            progress = ((i + 1) / total) * 100;
            progressBar.style.width = `${progress}%`;
        }
        KTAlertDialog.showSuccessMessage(`Deleted ${successCount} KPIs, failed to delete ${failCount} KPIs.`, () => {
            datatable.clear().rows.add(kpiData).draw();
            const container = document.querySelector('#kt_table_kpis');
            const headerCheckbox = container.querySelectorAll('[type="checkbox"]')[0];
            headerCheckbox.checked = false;
        });
    };

    const checkPercentage = (data) => {
        let totalWeight = 0;
        kpiData.forEach(kpi => {
            const weight = (kpi.id === data.id && kpi.weight !== data.weight) ? data.weight : kpi.weight;
            totalWeight += parseFloat(weight);
        });
        return totalWeight;
    }
    
    const toggleEditButtons = (id, isCancel) => {
        const viewEditButton = table.querySelector(`[data-kt-kpi-table-filter="edit_row"][data-id="${id}"]`);
        const viewSaveButton = table.querySelector(`[data-kt-kpi-table-filter="save_row"][data-id="${id}"]`);
        const viewCancelButton = table.querySelector(`[data-kt-kpi-table-filter="cancel_row"][data-id="${id}"]`);
        const viewDeleteButton = table.querySelector(`[data-kt-kpi-table-filter="delete_row"][data-id="${id}"]`);

        const viewSelectKpi = table.querySelector(`#kpi_id_${id}`);
        const viewWeightKpi = table.querySelector(`#weight_${id}`);

        const viewTarget = table.querySelector(`#target_${id}`);

        if (viewSaveButton) {
            viewSaveButton.classList.toggle('d-none', isCancel);
        }
        if (viewCancelButton) {
            viewCancelButton.classList.toggle('d-none', isCancel);
        }
        if (viewEditButton) {
            viewEditButton.classList.toggle('d-none', !isCancel);
        }
        if (viewDeleteButton) {
            viewDeleteButton.classList.toggle('d-none', !isCancel);
        }

        if (isCancel) {
            viewSelectKpi.classList.add('select2-readonly');
        } else {
            viewSelectKpi.classList.remove('select2-readonly');
        }

        viewWeightKpi.disabled = isCancel;
        viewTarget.disabled = isCancel;
    }

    const updateKpiData = (newKpiData) => {
        kpiData = newKpiData;
    }

    return {
        init: function () {
            kpiContainer = document.querySelector('#kt_kpi_container');
            submitButton = kpiContainer.querySelector('[data-kt-kpis-button-action="submit"]');
            cancelSubmitButton = kpiContainer.querySelector('[data-kt-kpis-button-action="cancel_submit"]');

            table = document.querySelector('#kt_table_kpis');

            if (!table) {
                return;
            }
            initSubmitButton();
            handleSaveRows();
            handleCancelRows();
            handleEditRows();
            handleDeleteRows();
        },
        setupKpiData: setupKpiData,
        datatable: () => {
            return datatable;
        },
        kpiData: () => {
            return kpiData;
        },
        updateKpiData: updateKpiData,
        isSubmit: () => {
            return isSubmit;
        }
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTKpiIndividualList.init();
});