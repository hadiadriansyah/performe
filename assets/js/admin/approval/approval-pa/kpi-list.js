"use strict";

const KTApprovalPaList = (() => {
    let kpiContainer;
    let submitButton;
    let cancelSubmitButton;

    let datatable;
    let table;

    let isSubmit = false;
    let kpiData = [];
    let indexScoreRule = [];

    let firstMonthPeriod;
    let lastMonthPeriod;

    const initIndexScoreRule = async () => {
        try {
            const response = await fetch(`${siteUrl}performance_appraisal/individual_performance_appraisal/get_index_scores`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ year_period_id: KTIndividualPerformanceAppraisal.getKpiYearPeriodId() }).toString()
            });
            const result = await response.json();
            indexScoreRule = result
        } catch (error) {}
    }

    const initOption = () => {
        $(`[name="from_month"]`).select2().on('select2:select', function (e) {
            firstMonthPeriod = e.params.data.id;
            if (firstMonthPeriod && lastMonthPeriod) {
                calculateDataPerformanceOnChange();
            }
        });
        $(`[name="to_month"]`).select2().on('select2:select', function (e) {
            lastMonthPeriod = e.params.data.id;
            if (firstMonthPeriod && lastMonthPeriod) {
                calculateDataPerformanceOnChange();
            }
        });
    }

    const initOptionRow = (row) => {
        const yearPeriodId = document.querySelector('[name="year_period_id"]').value;

        $(`#kpi_id_${row.id}`).select2({
            ajax: {
                url: `${siteUrl}performance_appraisal/individual_performance_appraisal/get_kpi_options_by_year_period_id`,
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
        });
    }
    
    const setupKpiData = async () => {
        const approvalEmployeeData = KTApprovalPa.getApprovalEmployeeData() ?? {};
        kpiContainer.querySelector('[name="position_name"]').value = approvalEmployeeData.position_name;

        firstMonthPeriod = approvalEmployeeData.from_month;
        lastMonthPeriod = approvalEmployeeData.to_month;

        kpiContainer.querySelector('[name="from_month"]').value = firstMonthPeriod;
        kpiContainer.querySelector('[name="from_month"]').dispatchEvent(new Event('change'));

        kpiContainer.querySelector('[name="to_month"]').value = lastMonthPeriod;
        kpiContainer.querySelector('[name="to_month"]').dispatchEvent(new Event('change'));

        const input = kpiContainer.querySelector('.auto-width-input');
        input.style.width = ((input.value.length + 1) * 8) + 'px';
        kpiData = [];
        const kpiIndividual = await getIndividualPerformanceAppraisalByPaId(approvalEmployeeData.pa_individual_id);
        if (kpiIndividual) {
            kpiData = kpiIndividual;
            kpiData.forEach(item => {
                item.mode = null;
            });
        }
        
        renderKpi();
        await KTSubmitPa.setupPaSubmitApproval(approvalEmployeeData);
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
                    { data: null },
                    { data: null },
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
                        render: function (data, type, row) {
                            return `<select id="kpi_id_${row.id}" name="kpi_id" aria-label="Select KPI" data-control="select2" data-placeholder="Select KPI..." class="form-select form-select-solid fw-bolder select2-readonly">
                                <option></option>
                            </select>
                            <div class="error-message small text-danger mt-1" id="error-kpi_id_${row.id}"></div>
                            `;
                        }
                    },
                    {
                        targets: 1,
                        render: function (data, type, row) {
                            return `<span id="measurement_${row.id}">-</span>`;
                        }
                    },
                    {
                        targets: 2,
                        render: function (data, type, row) {
                            return `<button type="button" class="btn btn-link ${row.target_id ? 'btn-color-success' : 'btn-color-danger'} btn-active-color-primary kpi_loading" id="target_${row.id}" data-bs-toggle="modal" data-bs-target="#kt_modal_target_actual"
                            data-kt-target-actual-button-action="target"
                            data-id="${row.id}"
                            disabled>0</button>`;
                        }
                    },
                    {
                        targets: 3,
                        render: function (data, type, row) {
                            return `<button type="button" class="btn btn-link ${row.actual_id ? 'btn-color-success' : 'btn-color-danger'} btn-active-color-primary kpi_loading" id="actual_${row.id}" data-bs-toggle="modal" data-bs-target="#kt_modal_target_actual"
                            data-kt-target-actual-button-action="actual"
                            data-id="${row.id}"
                            disabled>0</button>`;
                        }
                    },
                    {
                        targets: 4,
                        render: function (data, type, row) {
                            return `<span id="counter_${row.id}">-</span>`;
                        }
                    },
                    {
                        targets: 5,
                        render: function (data, type, row) {
                            return `<span id="polarization_${row.id}">-</span>`;
                        }
                    },
                    {
                        targets: 6,
                        render: function (data, type, row) {
                            return `<span class="kpi_loading" id="index_${row.id}">-</span>`;
                        }
                    },
                    {
                        targets: 7,
                        render: function (data, type, row) {
                            return `<input id="weight_${row.id}" type="text" class="form-control" value="${row.weight}" disabled/>`;
                        }
                    },
                    {
                        targets: 8,
                        render: function (data, type, row) {
                            return `<span class="kpi_loading" id="score_${row.id}">-</span>`;
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
                                <div class="btn btn-warning btn-icon" data-bs-dismiss="click" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Edit KPI" data-kt-kpi-table-filter="edit_row" data-id="${row.id}">
                                    <span class="svg-icon svg-icon-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                        </svg>
                                    </span>
                                </div>
                            `;
                        },
                    },
                ],
                drawCallback: async function () {
                    const api = this.api();
                    api.rows().every(async function() {
                        const row = this.data();
                        initOptionRow(row);
                        initKpiSelect(row);
                    });
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    
                    calculateAndDisplayPercentage();
                    calculateDataPerformanceOnChange();
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
            const response = await fetch(`${siteUrl}approval/approval_performance_appraisal/get_kpi_by_id/${id}`, { method: 'GET' });
            const result = await response.json();
            const data = result.data;
            return data;
        } catch (error) {
            return false;
        }
    }
    async function getTargetById(id) {
        try {
            const response = await fetch(`${siteUrl}performance_appraisal/individual_performance_appraisal/get_target_by_id/${id}`, { method: 'GET' });
            const result = await response.json();
            const data = result.data;
            return data;
        } catch (error) {
            return false;
        }
    }

    async function getActualById(id) {
        try {
            const response = await fetch(`${siteUrl}performance_appraisal/individual_performance_appraisal/get_actual_by_id/${id}`, { method: 'GET' });
            const result = await response.json();
            const data = result.data;
            return data;
        } catch (error) {
            return false;
        }
    }

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

    function updateScore() {
        const totalScore = kpiData.reduce((sum, kpi) => {
            const score = parseFloat(document.querySelector(`#score_${kpi.id}`).innerText);
            return sum + (isNaN(score) ? 0 : score);
        }, 0);
        const scoreElement = document.getElementById('kt_kpi_score');
        scoreElement.classList.remove('counted');
        scoreElement.setAttribute('data-kt-countup-value', totalScore);
    }

    const initSubmitButton = () => {
        const percentageInput = document.querySelector('#kt_kpi_percentage_input');
        percentageInput.addEventListener('input', async function() {
            const percentage = parseFloat(this.value) || 0;
            if (percentage < 100) {
                isSubmit = false;
            } else {
                const kpiIndividualPa = await getIndividualPerformanceAppraisalActualByPaIndividualIdYearPeriodId();
                console.log(kpiIndividualPa, kpiData)
                if (kpiIndividualPa.length === kpiData.length) {
                    isSubmit = true;
                } else {
                    isSubmit = false;
                }
            }
        });
    }
    
    const getIndividualPerformanceAppraisalActualByPaIndividualIdYearPeriodId = async () => {
        try {
            const response = await fetch(`${siteUrl}performance_appraisal/individual_performance_appraisal/get_kpi_individual_pa_by_pa_individual_id_year_period_id`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    pa_individual_id: KTIndividualPerformanceAppraisal.getKpiPaIndividualId(),
                    year_period_id: KTIndividualPerformanceAppraisal.getKpiYearPeriodId(),
                }).toString()
            });
            const result = await response.json();
            return result.data;
        } catch (error) {
            return false
        }
    }

    const getIndividualPerformanceAppraisalByPaId = async (paId) => {
        KTPageLoader.createPageLoading();
        KTPageLoader.showPageLoading();
        try {
            const response = await fetch(`${siteUrl}performance_appraisal/individual_performance_appraisal/get_kpi_individual_by_pa_id/${paId}`);
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            return result.data;
        } catch (error) {
            return false;
        } finally {
            KTPageLoader.hidePageLoading();
            KTPageLoader.removePageLoading();
        }
    }

    const handleSaveRows = function () {
        table.addEventListener('click', async function (e) {
            if (e.target.closest('[data-kt-kpi-table-filter="save_row"]')) {
                e.preventDefault();
                const id = e.target.closest('[data-kt-kpi-table-filter="save_row"]').getAttribute('data-id');
                toggleEditButtons(id, true);
            }
        });
    };

    const handleEditRows = function () {
        table.addEventListener('click', function (e) {
            if (e.target.closest('[data-kt-kpi-table-filter="edit_row"]')) {
                e.preventDefault();
                const id = e.target.closest('[data-kt-kpi-table-filter="edit_row"]').getAttribute('data-id');
                toggleEditButtons(id, false);
            }
        });
    };
    
    

    const toggleEditButtons = (id, isDone) => {
        const viewEditButton = table.querySelector(`[data-kt-kpi-table-filter="edit_row"][data-id="${id}"]`);
        const viewSaveButton = table.querySelector(`[data-kt-kpi-table-filter="save_row"][data-id="${id}"]`);
        
        const viewTarget = table.querySelector(`#target_${id}`);
        const viewActual = table.querySelector(`#actual_${id}`);

        if (viewSaveButton) {
            viewSaveButton.classList.toggle('d-none', isDone);
        }
        if (viewEditButton) {
            viewEditButton.classList.toggle('d-none', !isDone);
        }

        viewTarget.disabled = isDone;
        viewActual.disabled = isDone;
    }

    const updateKpiData = (newKpiData) => {
        kpiData = newKpiData;
    }

    const calculateDataPerformanceOnChange = async () => {
        document.querySelectorAll('.kpi_loading').forEach(element => {
            element.innerText = "Loading...";
        });
        try {
            kpiData.forEach(async kpi => {
                let targetCounter = 0;
                let actualCounter = 0;
                let formula = {};
                let target;
                let actual;
                
                const data = await getKpiById(kpi.kpi_id);
                if (kpi.target_id) {
                    target = await getTargetById(kpi.target_id);
                }
                if (kpi.actual_id) {
                    actual = await getActualById(kpi.actual_id);
                }
    
                const counter = data.counter ?? '';
                const polarization = data.polarization ?? '';

                if (data && data.formula) {
                    try {
                        formula =  JSON.parse(data.formula)
                    } catch (error) {}
                }

                if (target && target.target) {
                    try {
                        const targetArr = JSON.parse(target.target)
                        const resultTarget = [];
                        for (let i = firstMonthPeriod; i <= lastMonthPeriod; i++) {
                            if (targetArr.hasOwnProperty(i.toString())) {
                                resultTarget.push(parseInt(targetArr[i.toString()], 10));
                            }
                        }
                        targetCounter = calculateCounter(counter, resultTarget);
                    } catch (error) {}
                }

                if (actual && actual.actual) {
                    try {
                        const actualArr = JSON.parse(actual.actual)
                        const resultTarget = [];
                        for (let i = firstMonthPeriod; i <= lastMonthPeriod; i++) {
                            if (actualArr.hasOwnProperty(i.toString())) {
                                resultTarget.push(parseInt(actualArr[i.toString()], 10));
                            }
                        }
                        actualCounter = calculateCounter(counter, resultTarget);
                    } catch (error) {}
                }
                const targetButton = document.querySelector(`[data-kt-target-actual-button-action="target"][data-id="${kpi.id}"]`);
                const actualButton = document.querySelector(`[data-kt-target-actual-button-action="actual"][data-id="${kpi.id}"]`);
                targetButton.innerText = targetCounter;
                actualButton.innerText = actualCounter;

                let indexCounter = calculateIndex(actualCounter, targetCounter, polarization, formula);
                let num = isNaN(parseFloat(indexCounter)) ? 0 : parseFloat(indexCounter);
                const indexValue = getIndexValue(num);
                const weightValue = parseFloat(kpi.weight);
                const scoreValue = (parseFloat(indexValue) * parseFloat(kpi.weight)) / 100;
                document.querySelector(`#index_${kpi.id}`).innerText = indexValue;
                document.querySelector(`#weight_${kpi.id}`).value = weightValue;
                document.querySelector(`#score_${kpi.id}`).innerText = scoreValue;
                updateScore();
            });

            
        } catch (error) {
            return false;
        }
    }

    const calculateCounter = (counter, counterData) => {
        if (counter.includes('SUM')) {
            return counterData.reduce((a, b) => a + b, 0);
        } else if (counter.includes('AVG')) {
            const sum = counterData.reduce((a, b) => a + b, 0);
            return sum / counterData.length;
        } else if (counter.includes('LAST')) {
            return counterData[counterData.length - 1];
        } else {
            return "Error 404";
        }
    }

    const compare = (a, operator, b) => {
        switch (operator) {
            case '<=':
                return a <= b;
            case '>':
                return a > b;
            case '==':
                return a == b;
            case '<':
                return a < b;
            case '>=':
                return a >= b;
            default:
                throw new Error("Operator tidak valid");
        }
    }

    const calculateIndex = (actual, target, polarization, polarizationData) => {
        let valueapp = (actual / target) * 100;
        if (polarization.includes('Minimize')) {
            if (compare(valueapp, polarizationData['min_opr_1'], polarizationData['value_min_1'])) {
                return polarizationData['pol_min_index_1'];
            } else if (compare(valueapp, polarizationData['min_opr_2'], polarizationData['value_min_1']) && compare(valueapp, polarizationData['min_opr_1'], polarizationData['value_min_2'])) {
                return polarizationData['pol_min_index_2'];
            } else if (compare(valueapp, polarizationData['min_opr_2'], polarizationData['value_min_2']) && compare(valueapp, polarizationData['min_opr_1'], polarizationData['value_min_3'])) {
                return polarizationData['pol_min_index_3'];
            } else if (compare(valueapp, polarizationData['min_opr_2'], polarizationData['value_min_3']) && compare(valueapp, polarizationData['min_opr_1'], polarizationData['value_min_4'])) {
                return polarizationData['pol_min_index_4'];
            } else if (compare(valueapp, polarizationData['min_opr_2'], polarizationData['value_min_4'])) {
                return polarizationData['pol_min_index_5'];
            } else {
                return "Error";
            }
        } else if (polarization.includes('Absolute')) {
            if (compare(valueapp, polarizationData['abs_opr_1'], target)) {
                return polarizationData['pol_abs_index_1'];
            } else if (compare(valueapp, polarizationData['abs_opr_2'], target)) {
                return polarizationData['pol_abs_index_2'];
            } else {
                return "Error";
            }
        } else if (polarization.includes('Stabilize')) {
            valueapp = actual - target;
            if (compare(valueapp, polarizationData.stab_opr_1_target, target)) {
                return polarizationData.pol_stab_index_1;
            } else if (compare(valueapp, polarizationData.stab_opr_2_target, target)) {
                return polarizationData.pol_stab_index_2;
            } else if (compare(valueapp, polarizationData.stab_opr_2_target, target)) {
                return polarizationData.pol_stab_index_3;
            } else if (compare(valueapp, polarizationData.stab_opr_2_target, target)) {
                return polarizationData.pol_stab_index_4;
            } else if (compare(valueapp, polarizationData.stab_opr_2_target, target)) {
                return polarizationData.pol_stab_index_5;
            } else {
                return "Error";
            }
        } else if (polarization.includes('Maximize')) {
            if (compare(valueapp, polarizationData['max_opr_1'], polarizationData['value_max_1'])) {
                return polarizationData['pol_max_index_1'];
            } else if (compare(valueapp, polarizationData['max_opr_2'], polarizationData['value_max_1']) && compare(valueapp, polarizationData['max_opr_1'], polarizationData['value_max_2'])) {
                return polarizationData['pol_max_index_2'];
            } else if (compare(valueapp, polarizationData['max_opr_2'], polarizationData['value_max_2']) && compare(valueapp, polarizationData['max_opr_1'], polarizationData['value_max_3'])) {
                return polarizationData['pol_max_index_3'];
            } else if (compare(valueapp, polarizationData['max_opr_2'], polarizationData['value_max_3']) && compare(valueapp, polarizationData['max_opr_1'], polarizationData['value_max_4'])) {
                return polarizationData['pol_max_index_4'];
            } else if (compare(valueapp, polarizationData['max_opr_2'], polarizationData['value_max_4'])) {
                return polarizationData['pol_max_index_5'];
            } else {
                return "Error";
            }
        } else {
            return "Error";
        }
    }
    
    const compareIndexScore = (value, operator, threshold) => {
        if (operator === null || operator === '') {
            return true;
        }
        switch (operator) {
            case '>=':
                return value >= threshold;
            case '<':
                return value < threshold;
            default:
                throw new Error(`Invalid operator: ${operator}`);
        }
    }

    const getIndexValue = (value) => {
        for (const rule of indexScoreRule) {
            if (compareIndexScore(value, rule.operator_1, rule.value_1) && compareIndexScore(value, rule.operator_2, rule.value_2)) {
                return rule.index_value;
            }
        }
        return 1;
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
            initOption();
            handleSaveRows();
            handleEditRows();
        },
        setupKpiData: setupKpiData,
        datatable: () => {
            return datatable;
        },
        kpiData: () => {
            return kpiData;
        },
        calculateDataPerformanceOnChange: calculateDataPerformanceOnChange,
        isSubmit: () => {
            return isSubmit;
        }
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTApprovalPaList.init();
});