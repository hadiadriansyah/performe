"use strict";

const KTUsersList = (function () {
    let datatable;
    let table;
    let dataCheckboxes = [];

    const initUserData = function () {
        datatable = $(table).DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [],
            stateSave: true,
            select: {
                style: 'os',
                selector: 'td:first-child',
                className: 'row-selected'
            },
            ajax: {
                url: `${siteUrl}user_management/user/data_server`,
                type: "POST",
                data: function (data) {
                    data.is_active = $('[data-kt-user-table-filter="is_active"]').val();
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'email' },
                { data: 'profile_picture' },
                { data: 'is_active' },
                { data: 'created_at' },
                { data: null },
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
                    targets: 3,
                    data: "profile_picture",
                    render: (data, type, row) => `<div class="d-flex justify-content-center align-items-center">
                                            <img
                                                src="uploads/profile_pictures/${row.profile_picture}"
                                                alt="profile"
                                                class="h-50px"
                                                onerror="this.src='assets/media/logo-blue-mini.png'"
                                            />
                                        </div>`
                },
                {
                    targets: 4,
                    data: "is_active",
                    render: (data) => `<span class="badge ${data == '1' ? 'badge-success' : 'badge-danger'}">${data == '1' ? 'Active' : 'Not Active'}</span>`
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <div class="btn btn-icon btn-sm btn-color-gray-400 btn-active-icon-primary me-2" data-bs-dismiss="click" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Edit User" data-kt-user-table-filter="edit_row" data-id="${row.id}" id="kt_modal_view_event_edit">
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="black" />
                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="black" />
                                    </svg>
                                </span>
                            </div>
                            <div class="btn btn-icon btn-sm btn-color-gray-400 btn-active-icon-danger me-2" data-bs-dismiss="click" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Delete User" data-id="${row.id}" data-kt-user-table-filter="delete_row" id="kt_modal_view_event_delete">
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
        });

        datatable.on('draw', function () {
            initToggleToolbar();
            toggleToolbars();
            handleDeleteRows();
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    };

    const handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    };

    const handleFilterDatatable = function () {
        const filterButton = document.querySelector('[data-kt-user-table-filter="filter"]');
        filterButton.addEventListener('click', function () {
            datatable.ajax.reload();
        });
    };

    const handleResetForm = function () {
        const resetButton = document.querySelector('[data-kt-user-table-filter="reset"]');
        resetButton.addEventListener('click', function () {
            const filterForm = document.querySelector('[data-kt-user-table-filter="form"]');
            const selectOptions = filterForm.querySelectorAll('select');
            selectOptions.forEach(select => {
                select.value = '';
                select.dispatchEvent(new Event('change'));
            });
            datatable.search('').draw();
        });
    };

    const initToggleToolbar = function () {
        const container = document.querySelector('#kt_table_users');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        checkboxes.forEach(c => {
            c.addEventListener('click', function () {
                setTimeout(toggleToolbars, 50);
            });
        });
    };

    const toggleToolbars = function () {
        const container = document.querySelector('#kt_table_users');
        const toolbarBase = document.querySelector('[data-kt-user-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-user-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-user-table-select="selected_count"]');
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

    const deleteSelected = document.querySelector('[data-kt-user-table-select="delete_selected"]');
    deleteSelected.addEventListener('click', function () {
        Swal.fire({
            text: "Are you sure you want to delete selected users?",
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
                    text: "Deleting " + dataCheckboxes.length + " users",
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
                        await handleDeleteSelected(dataCheckboxes);
                    },
                    timerProgressBar: true,
                });
            } else if (result.dismiss === 'cancel') {
                KTAlertDialog.showErrorMessage("Selected users was not deleted.");
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
                const response = await fetch(`${siteUrl}user_management/user/delete`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ id: dataCheckboxes[i].value }).toString()
                });
                await new Promise(resolve => setTimeout(resolve, 300));
                const result = await response.json();
                if (result.status === 'success') {
                    successCount++;
                    deleteResults.innerHTML += `<div>Successfully deleted: ${dataCheckboxes[i].text}</div>`;
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

        KTAlertDialog.showSuccessMessage(`Deleted ${successCount} users, failed to delete ${failCount} users.`,  () => {
            datatable.draw();
            const container = document.querySelector('#kt_table_users');
            const headerCheckbox = container.querySelectorAll('[type="checkbox"]')[0];
            headerCheckbox.checked = false;
        });
    }; 
    
    const handleDeleteRows = function () {
       table.addEventListener('click', function (e) {
            if (e.target.closest('[data-kt-user-table-filter="delete_row"]')) {
                e.preventDefault();
                let parent = e.target.closest('tr');
                if (parent.classList.contains('child')) {
                    parent = parent.previousElementSibling;
                }
                const name = parent.querySelectorAll('td')[1].innerText;
                const id = e.target.closest('[data-kt-user-table-filter="delete_row"]').getAttribute('data-id');

                Swal.fire({
                    text: "Are you sure you want to delete " + name + "?",
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
                            text: "Deleting " + name,
                            icon: "info",
                            buttonsStyling: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                                handleDeleteRow(id, name);
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        KTAlertDialog.showErrorMessage(name + " was not deleted.");
                    }
                });
            }
        });
    };

    const handleDeleteRow = async (id, name) => {
        try {
            const response = await fetch(`${siteUrl}user_management/user/delete`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ id }).toString()
            });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            handleDeleteResponse(result, name);
        } catch (error) {
            KTAlertDialog.showErrorMessage("Data could not be deleted. The record is still related to other records.");
        }
    };    

    const handleDeleteResponse = (response, name) => {
        if (response.status === 'success') {
            KTAlertDialog.showSuccessMessage(name + " was deleted.", () => {
                datatable.draw();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    return {
        init: function () {
            table = document.querySelector("#kt_table_users");

            if (!table) {
                return;
            }
            
            initUserData();
            handleSearchDatatable();
            initToggleToolbar();
            handleFilterDatatable();
            handleDeleteRows();
            handleResetForm();
        },
        datatable: () => {
            return datatable;
        }
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTUsersList.init();
});