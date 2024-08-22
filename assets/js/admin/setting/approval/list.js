"use strict";

const KTApprovalList = (function () {
    let datatable;
    let table;
    let dataCheckboxes = [];
    let typeFilter = 'unit';
    let unitTypeIdFilter = '';
    let unitIdFilter = '';
    let isLoading = false;
    let form;
    let validator;

    const initOptionSelect2 = function () {
        $('#kt_approval_filter_form [name="type"]').select2().on('change', function () {
            setupFilter();
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });
        $('#kt_approval_filter_form [name="unit_type_id"]').select2({
            ajax: {
                url: `${siteUrl}setting/approval/get_unit_type_options`,
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
            validator.revalidateField('unit_type_id');
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('#kt_approval_filter_form [name="unit_id"]').select2({
            ajax: {
                url: `${siteUrl}setting/approval/get_unit_options`,
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
            // validator.revalidateField('unit_id');
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });
    }

    const initApprovalData = function () {
        datatable = $(table).DataTable({
            searchDelay: 500,
            initComplete: function () {
                this.api().columns([1, 2]).every(function () {
                    const column = this;
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-control form-control-solid my-3';
                    input.placeholder = `Search ${$(column.header()).text()}`;
                    const header = $(column.header());
                    const headerContent = header.html();
                    header.html(`
                        <div style="width: ${header.width()}px;">
                            ${headerContent}
                        </div>
                    `);
                    $(header).append(input);

                    // $(header).off('click.DT');
                    $(input).on('click', function (e) {
                        e.stopPropagation();
                    });

                    let searchTimeout;
                    $(input).on('keyup change clear', function () {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        }, 300);
                    });
                });

                this.api().columns().every(function () {
                    const column = this;
                    if (![1, 2].includes(column.index())) {
                        const header = $(column.header());
                        const headerContent = header.html();
                        header.html(`
                            <div style="height: ${header.height()}px; width: ${header.width()}px;">
                                ${headerContent}
                            </div>
                        `);
                    }
                });
            },
            responsive: true,
            processing: true,
            serverSide: true,
            order: [],
            stateSave: false,
            select: {
                style: 'os',
                selector: 'td:first-child',
                className: 'row-selected'
            },
            ajax: {
                url: `${siteUrl}setting/approval/data_server`,
                type: "POST",
                data: function (data) {
                    data.type = typeFilter;
                    data.unit_type_id = unitTypeIdFilter;
                    data.unit_id = unitIdFilter;
                },
                // beforeSend: function() {
                //     KTPageLoader.createPageLoading();
                //     KTPageLoader.showPageLoading();
                //     isLoading = true;
                // },
                // complete: function() {
                //     isLoading = false;
                //     KTPageLoader.hidePageLoading();
                //     KTPageLoader.removePageLoading();
                // }
            },
            columns: [
                { data: 'no' },
                { data: 'position_name' },
                { data: 'temp_name' },
                { data: 'position_approval_1_names', orderable: false },
                { data: 'position_approval_2_names', orderable: false },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data, type, row) {
                        return typeFilter === 'unit' ? `
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value='${JSON.stringify(row)}' />
                            </div>` : '';
                    }
                },
                {
                    targets: 3,
                    orderable: false,
                    render: function (data, type, row) {
                        
                        return `${row.unit_type_1_name != '' ? row.unit_type_1_name  : ''}<input class="form-control" value="${data}" readonly id="kt_tagify_approval_1_${row.no}"/>`;
                    }
                },
                {
                    targets: 4,
                    orderable: false,
                    render: function (data, type, row) {
                        return `${row.unit_type_2_name != '' ? row.unit_type_2_name : ''}<input class="form-control" value="${data}" readonly id="kt_tagify_approval_2_${row.no}"/>`;
                    }
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <div class="btn btn-icon btn-sm btn-color-gray-400 btn-active-icon-primary me-2" data-bs-dismiss="click" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Edit Approval" data-kt-approval-table-filter="edit_row" data-type-filter="${typeFilter}" data-approval='${JSON.stringify(row)}' id="kt_modal_view_event_edit">
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                    </svg>
                                </span>
                            </div>
                            <div class="btn btn-icon btn-sm btn-color-gray-400 btn-active-icon-danger me-2" data-bs-dismiss="click" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Delete Approval" data-temp-id="${row.temp_id}" data-position-id="${row.position_id}" data-kt-approval-table-filter="delete_row" id="kt_modal_view_event_delete">
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
            drawCallback: async function () {
                const api = this.api();
                api.rows().every(async function() {
                    const row = this.data();
                    handleViewApproval(row);
                });
            }
        });

        datatable.on('draw', function () {
            initToggleToolbar();
            toggleToolbars();
            handleDeleteRows();
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(table).on('click', 'td.dtr-control', function () {
            const tr = $(this).closest('tr');
            const row = datatable.row(tr);

            if (row.child.isShown()) {
                handleViewApproval(row.data());
            }
        });

    };

    const setupFilter = function () {
        const type = document.querySelector('[data-kt-approval-table-filter="type"]');
        const unitType = document.querySelector('[data-kt-approval-table-filter="unit_type_id"]');
        const unit = document.querySelector('[data-kt-approval-table-filter="unit_id"]');
        
        if (type.value == 'unit_type') {
            unitType.value = '';
            unitType.dispatchEvent(new Event('change'));
            $('[data-kt-approval-table-filter="unit_type_id"]').parent().removeClass('d-none');
            $('[data-kt-approval-table-filter="unit_id"]').parent().addClass('d-none');
        } else {
            unit.value = '';
            unit.dispatchEvent(new Event('change'));
            $('[data-kt-approval-table-filter="unit_type_id"]').parent().addClass('d-none');
            $('[data-kt-approval-table-filter="unit_id"]').parent().removeClass('d-none');
        }
    };
    
    const handleViewApproval = function (row) {
        const inputs1 = document.querySelectorAll(`#kt_tagify_approval_1_${row.no}`);
        const inputs2 = document.querySelectorAll(`#kt_tagify_approval_2_${row.no}`);

        inputs1.forEach(input1 => {
            const tagsElement = input1.previousElementSibling;
            if (!tagsElement || !tagsElement.classList.contains('tagify')) {
                new Tagify(input1);
            }
        });

        inputs2.forEach(input2 => {
            const tagsElement = input2.previousElementSibling;
            if (!tagsElement || !tagsElement.classList.contains('tagify')) {
                new Tagify(input2);
            }
        });
    };
    
    const handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-approval-table-filter="search"]');
        filterSearch.addEventListener('keyup', _.debounce(function (e) {
            datatable.search(e.target.value).draw();
        }, 300));
    };

    const handleFilterDatatable = function () {
        const filterButton = document.querySelector('[data-kt-approval-table-filter="filter"]');
        const type = document.querySelector('[data-kt-approval-table-filter="type"]');
        const unitTypeId = document.querySelector('[data-kt-approval-table-filter="unit_type_id"]');
        const unitId = document.querySelector('[data-kt-approval-table-filter="unit_id"]');

        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'type': {
                        validators: {
                            notEmpty: {
                                message: 'Type is required'
                            }
                        }
                    },
                    'unit_type_id': {
                        validators: {
                            callback: {
                                message: 'Unit is required',
                                callback: function (input) {
                                    return type.value == 'unit_type' && input.value == '' ? false : true;
                                },
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

        filterButton.addEventListener('click', async function (e) {
            e.preventDefault();
            
            if (validator) {
                const status = await validator.validate();
                if (status === 'Valid') {
                    typeFilter = type.value;
                    unitTypeIdFilter = unitTypeId.value;
                    unitIdFilter = unitId.value;
                    datatable.ajax.reload();
                    
                    const t =KTMenu.getInstance(this);
                    if(null!==t)return t.dismiss(this,e)
                }
            }
        });
    };

    const handleResetForm = function () {
        const resetButton = document.querySelector('[data-kt-approval-table-filter="reset"]');
        resetButton.addEventListener('click', function () {
            const filterForm = document.querySelector('[data-kt-approval-table-filter="form"]');
            const selectOptions = filterForm.querySelectorAll('select');
            selectOptions.forEach(select => {
                select.value = '';
                select.dispatchEvent(new Event('change'));
            });
            datatable.search('').draw();
        });
    };

    const initToggleToolbar = function () {
        const container = document.querySelector('#kt_table_approvals');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        checkboxes.forEach(c => {
            c.addEventListener('click', function () {
                setTimeout(toggleToolbars, 50);
            });
        });
    };

    const toggleToolbars = function () {
        const container = document.querySelector('#kt_table_approvals');
        const toolbarBase = document.querySelector('[data-kt-approval-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-approval-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-approval-table-select="selected_count"]');
        const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');

        let checkedState = false;
        let count = 0;
        dataCheckboxes = [];
        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
                const row = c.closest('tr');
                const text = row.querySelector('td:nth-child(2)').innerText + ' - ' + row.querySelector('td:nth-child(3)').innerText;
                const dataApproval = JSON.parse(c.value);
                dataCheckboxes.push({ value: dataApproval, text: text });
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

    const deleteSelected = document.querySelector('[data-kt-approval-table-select="delete_selected"]');
    deleteSelected.addEventListener('click', function () {
        Swal.fire({
            text: "Are you sure you want to delete selected approvals?",
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
                    title: "Deleting " + dataCheckboxes.length + " approvals",
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
                KTAlertDialog.showErrorMessage("Selected approvals was not deleted.");
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
        let success = [];
        let fail = [];

        for (let i = 0; i < total; i++) {
            const dataApproval = dataCheckboxes[i].value;
            try {
                const response = await fetch(`${siteUrl}setting/approval/delete_unit_approval`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ unit_id: dataApproval.temp_id, position_id: dataApproval.position_id }).toString()
                });
                await new Promise(resolve => setTimeout(resolve, 300));
                const result = await response.json();
                if (result.status === 'success') {
                    successCount++;
                    deleteResults.innerHTML += `<div>Deleted: ${dataCheckboxes[i].text}</div>`;
                    success.push(dataCheckboxes[i].text);
                } else {
                    failCount++;
                    deleteResults.innerHTML += `<div>Failed to delete: ${dataCheckboxes[i].text}</div>`;
                    fail.push(dataCheckboxes[i].text);
                }
            } catch (error) {
                failCount++;
                deleteResults.innerHTML += `<div>Failed to delete: ${dataCheckboxes[i].text}</div>`;
            }
            progress = ((i + 1) / total) * 100;
            progressBar.style.width = `${progress}%`;
        }
        await new Promise(resolve => setTimeout(resolve, 1000));
        KTAlertDialog.showInfoHtml(
            `Deleted ${successCount} approvals, failed to delete ${failCount} approvals.`,
            `<div class="text-start">
                <div class="mb-2">Successfully deleted:<br> ${success.join('<br>')}</div>
                <div class="mb-2">Failed to delete:<br> ${fail.join('<br>')}</div>
            </div>`,
            () => {
                datatable.draw();
                const container = document.querySelector('#kt_table_approvals');
                const headerCheckbox = container.querySelectorAll('[type="checkbox"]')[0];
                headerCheckbox.checked = false;
            }
        );
    }; 
    
    const handleDeleteRows = function () {
       table.addEventListener('click', function (e) {
            if (e.target.closest('[data-kt-approval-table-filter="delete_row"]')) {
                e.preventDefault();
                let parent = e.target.closest('tr');
                if (parent.classList.contains('child')) {
                    parent = parent.previousElementSibling;
                }
                const indexValueText = parent.querySelectorAll('td')[1].innerText + ' - ' + parent.querySelectorAll('td')[2].innerText;
                const tempId = e.target.closest('[data-kt-approval-table-filter="delete_row"]').getAttribute('data-temp-id');
                const positionId = e.target.closest('[data-kt-approval-table-filter="delete_row"]').getAttribute('data-position-id');
                

                Swal.fire({
                    text: "Are you sure you want to delete " + indexValueText + "?",
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
                            text: "Deleting " + indexValueText,
                            icon: "info",
                            buttonsStyling: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                                handleDeleteRow(tempId, positionId, indexValueText);
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        KTAlertDialog.showErrorMessage(indexValueText + " was not deleted.");
                    }
                });
            }
        });
    };

    const handleDeleteRow = async (tempId, positionId, indexValueText) => {
        try {
            let response;
            if (typeFilter === 'unit') {
                response = await fetch(`${siteUrl}setting/approval/delete_unit_approval`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ unit_id: tempId, position_id: positionId }).toString()
                });
            } else {
                response = await fetch(`${siteUrl}setting/approval/delete_unit_type_approval`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ unit_type_id: tempId, position_id: positionId }).toString()
                });
            }
            
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            handleDeleteResponse(result, indexValueText);
        } catch (error) {
            KTAlertDialog.showErrorMessage("Error while deleting data!.");
        }
    };    

    const handleDeleteResponse = (response, indexValueText) => {
        if (response.status === 'success') {
            Swal.fire({
                text: "You have deleted " + indexValueText + "!.",
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            }).then(function () {
                datatable.draw();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    return {
        init: function () {
            table = document.querySelector("#kt_table_approvals");
            
            form = document.querySelector('#kt_approval_filter_form');

            if (!table) {
                return;
            }

            initOptionSelect2();
            initApprovalData();
            initToggleToolbar();
            setupFilter();
            handleSearchDatatable();
            handleFilterDatatable();
            handleDeleteRows();
            handleResetForm();
        },
        datatable: () => {
            return datatable;
        },
        typeFilter: () => {
            return typeFilter;
        },
        unitTypeIdFilter: () => {
            return unitTypeIdFilter;
        }
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTApprovalList.init();
});