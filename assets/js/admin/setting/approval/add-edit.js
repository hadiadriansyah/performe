"use strict";

var KTApprovalAddEditApproval = function () {
    let element;
    let form;
    let modal;
    let submitButton;
    let cancelButton;
    let closeButton;

    let positionList = [];
    let positionTagify1;
    let positionTagify2;
    // let unitList = [];

    let typeFilter;
    let approvalData;

    let validator;
    let action;

    const initAddEditApproval = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    // 'type': {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Type is required'
                    //         },
                    //     }
                    // },
                    // 'temp_id': {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Temp is required'
                    //         },
                    //     }
                    // },
                    // 'position_id': {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Position is required'
                    //         },
                    //     }
                    // },
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
                    handleFormSubmission();
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
    
    var initOptionSelect2 = function () {
        $('[name="approval_unit_type_1"]').select2().on('change', function () {
            const customApprovalUnit = $('[name="custom_approval_unit_1"]').parent();
            const positionApproval = $('[name="position_approval_1"]').parent();

            customApprovalUnit.addClass('d-none');
            positionApproval.addClass('d-none');

            if (this.value !== '0') {
                positionApproval.removeClass('d-none');
                if (this.value === '3') {
                    customApprovalUnit.removeClass('d-none');
                }
            }
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('[name="approval_unit_type_2"]').select2().on('change', function () {
            const customApprovalUnit = $('[name="custom_approval_unit_2"]').parent();
            const positionApproval = $('[name="position_approval_2"]').parent();

            customApprovalUnit.addClass('d-none');
            positionApproval.addClass('d-none');

            if (this.value !== '0') {
                positionApproval.removeClass('d-none');
                if (this.value === '3') {
                    customApprovalUnit.removeClass('d-none');
                }
            }
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('.custom_approval_unit').select2({
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
            // validator.revalidateField('custom_approval_unit_1');
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });
    }

    const initTagify = async () => {
        const positionApproval1 = document.querySelector('[name="position_approval_1"]');
        const positionApproval2 = document.querySelector('[name="position_approval_2"]');

        positionList = await getPositionList();
        // unitList = await getUnitList();

        function tagTemplate(tagData) {
            return `
                <tag title="${(tagData.name)}"
                        contenteditable='false'
                        spellcheck='false'
                        tabIndex="-1"
                        class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ""}"
                        ${this.getAttributes(tagData)}>
                    <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
                    <div class="d-flex align-items-center">
                        <span class='tagify__tag-text'>${tagData.name}</span>
                    </div>
                </tag>
            `
        }

        function suggestionItemTemplate(tagData) {
            return `
                <div ${this.getAttributes(tagData)}
                    class='tagify__dropdown__item d-flex align-items-center ${tagData.class ? tagData.class : ""}'
                    tabindex="0"
                    role="option">

                    <div class="d-flex flex-column">
                        <strong>${tagData.name}</strong>
                    </div>
                </div>
            `
        }

        positionTagify1 = new Tagify(positionApproval1, {
            tagTextProp: 'name',
            enforceWhitelist: true,
            skipInvalid: true,
            dropdown: {
                closeOnSelect: false,
                enabled: 0,
                maxItems: Infinity,
                searchKeys: ['name']
            },
            templates: {
                tag: tagTemplate,
                dropdownItem: suggestionItemTemplate
            },
            whitelist: positionList
        })

        positionTagify2 = new Tagify(positionApproval2, {
            tagTextProp: 'name',
            enforceWhitelist: true,
            skipInvalid: true,
            dropdown: {
                closeOnSelect: false,
                enabled: 0,
                maxItems: Infinity,
                searchKeys: ['name']
            },
            templates: {
                tag: tagTemplate,
                dropdownItem: suggestionItemTemplate
            },
            whitelist: positionList
        })

        // customUnitTagify1 = new Tagify(customApprovalUnit1, {
        //     tagTextProp: 'name',
        //     enforceWhitelist: true,
        //     skipInvalid: true,
        //     dropdown: {
        //         closeOnSelect: false,
        //         enabled: 0,
        //         searchKeys: ['name']
        //     },
        //     templates: {
        //         tag: tagTemplate,
        //         dropdownItem: suggestionItemTemplate
        //     },
        //     whitelist: unitList
        // })

        // customUnitTagify2 = new Tagify(customApprovalUnit2, {
        //     tagTextProp: 'name',
        //     enforceWhitelist: true,
        //     skipInvalid: true,
        //     dropdown: {
        //         closeOnSelect: false,
        //         enabled: 0,
        //         searchKeys: ['name']
        //     },
        //     templates: {
        //         tag: tagTemplate,
        //         dropdownItem: suggestionItemTemplate
        //     },
        //     whitelist: unitList
        // })
    };

    const getPositionList = async () => {
        try {
            const response = await fetch(`${siteUrl}setting/approval/get_position_list`);
            const result = await response.json();
            return result.data.items;
        } catch (error) {
            return [];
        }
    }

    const setupApprovalForm = async () => {
        const approval = JSON.parse(approvalData);

        positionTagify1.removeAllTags();
        positionTagify2.removeAllTags();

        element.querySelector('#kt_modal_add_edit_approval_header_title').innerText = approval.position_name + ' - ' + approval.temp_name;
        element.querySelector('.indicator-label').innerText = typeFilter == 'unit' ? 'Submit' : 'Generate';

        const customApprovalUnit1 = form.querySelector('[name="custom_approval_unit_1"]');
        const newOptionCustomApprovalUnit1 = document.createElement('option');
        newOptionCustomApprovalUnit1.value = approval.unit_approval_1_id;
        newOptionCustomApprovalUnit1.text = approval.unit_approval_1_name;
        newOptionCustomApprovalUnit1.selected = true;
        customApprovalUnit1.appendChild(newOptionCustomApprovalUnit1);
        customApprovalUnit1.dispatchEvent(new Event('change'));

        const customApprovalUnit2 = form.querySelector('[name="custom_approval_unit_2"]');
        const newOptionCustomApprovalUnit2 = document.createElement('option');
        newOptionCustomApprovalUnit2.value = approval.unit_approval_2_id;
        newOptionCustomApprovalUnit2.text = approval.unit_approval_2_name;
        newOptionCustomApprovalUnit2.selected = true;
        customApprovalUnit2.appendChild(newOptionCustomApprovalUnit2);
        customApprovalUnit2.dispatchEvent(new Event('change'));

        form.querySelector('[name="approval_unit_type_1"]').value = approval.unit_type_1;
        form.querySelector('[name="approval_unit_type_1"]').dispatchEvent(new Event('change'));
        form.querySelector('[name="approval_unit_type_2"]').value = approval.unit_type_2;
        form.querySelector('[name="approval_unit_type_2"]').dispatchEvent(new Event('change'));

        const positionApproval1Ids = (approval.position_approval_1_ids ?? '').split(',').map(value => value.trim());
        const positionApproval2Ids = (approval.position_approval_2_ids ?? '').split(',').map(value => value.trim());

        const positionApproval1Tags = positionList.filter(item => positionApproval1Ids.includes(item.value));
        const positionApproval2Tags = positionList.filter(item => positionApproval2Ids.includes(item.value));

        if (positionApproval1Tags.length > 0) {
            positionTagify1.addTags(positionApproval1Tags);
        }

        if (positionApproval2Tags.length > 0) {
            positionTagify2.addTags(positionApproval2Tags);
        }
    };

    const handleFormSubmission = async () => {
        if (typeFilter == 'unit') {
            handleSubmitUnit();
        } else {
            handleSubmitUnitType();
        }
    };

    const handleSubmitUnit = async () => {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;

        try {
            const formData = new FormData(form);
            const approval = JSON.parse(approvalData);
            formData.append('type', typeFilter);
            formData.append('unit_id', approval.temp_id);
            formData.append('position_id', approval.position_id);
            const formDetails = {
                url: 'setting/approval/add_edit_approval',
                formData: new URLSearchParams(formData).toString()
            };
            await submitFormData(formDetails);
        } catch (error) {
            KTAlertDialog.showErrorMessage(`Error while submitting data!`);
        } finally {
            submitButton.removeAttribute('data-kt-indicator');
            submitButton.disabled = false;
        }
    }

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
            KTAlertDialog.showErrorMessage("Error while submitting data!.");
        }
    };

    const handleFormResponse = (response) => {
        if (response.status === 'success') {
            KTAlertDialog.showSuccessMessage("Form has been successfully submitted!", () => {
                KTApprovalList.datatable().ajax.reload();
                form.reset();
                modal.hide();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    const handleSubmitUnitType = async () => {
        Swal.fire({
            text: "Are you sure you want to generate this approval?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            showLoaderOnConfirm: true,
            confirmButtonText: "Yes, generate!",
            cancelButtonText: "No, cancel",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            },
        }).then(async function (result) {
            if (result.value) {
                Swal.fire({
                    title: "Generating approval",
                    icon: "info",
                    html: `
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%;" id="progress-bar"></div>
                        </div>
                        <div id="generate-results" class="mt-2 text-start"></div>
                    `,
                    buttonsStyling: false,
                    showConfirmButton: false,
                    didOpen: async () => {
                        Swal.showLoading();
                        handleGenerateApproval();
                    },
                    timerProgressBar: true,
                });
            } else if (result.dismiss === 'cancel') {
                KTAlertDialog.showErrorMessage("Approval was not generated.");
            }
        });
    }

    const handleGenerateApproval = async () => {
        const approval = JSON.parse(approvalData);
        const progressBar = document.getElementById('progress-bar');
        const generateResults = document.getElementById('generate-results');
        const unit_type_id = approval.temp_id;
        const position_id = approval.position_id;
        let total = 0;
        let progress = 0;
        let successCount = 0;
        let failCount = 0;
        let success = [];
        let fail = [];

        try {
            const units = await getUnitsByUnitTypeId(unit_type_id);
            total = units.length;

            for (let i = 0; i < total; i++) {
                const unit = units[i];
                try {
                    const formData = new FormData(form);
                    formData.append('type', typeFilter);
                    formData.append('unit_id', unit.id);
                    formData.append('position_id', position_id);
                    const response = await fetch(`${siteUrl}setting/approval/add_edit_approval`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams(formData).toString()
                    });
                    await new Promise(resolve => setTimeout(resolve, 300));
                    const result = await response.json();
                    if (result.status === 'success') {
                        successCount++;
                        generateResults.innerHTML += `<div>Generated: ${unit.nm_unit_kerja}</div>`;
                        success.push(unit.nm_unit_kerja);
                    } else {
                        failCount++;
                        generateResults.innerHTML += `<div>Failed to generate: ${unit.nm_unit_kerja}</div>`;
                        fail.push(unit.nm_unit_kerja);
                    }
                } catch (error) {
                    failCount++;
                    generateResults.innerHTML += `<div>Failed to generate: ${unit.nm_unit_kerja}</div>`;
                    fail.push(unit.nm_unit_kerja);
                }
                progress = ((i + 1) / total) * 100;
                progressBar.style.width = `${progress}%`;
            }
        } catch (error) {
            KTAlertDialog.showErrorMessage("Error while generating data.");
        }

        KTAlertDialog.showInfoHtml(
            `Generated ${successCount} approvals, failed to generate ${failCount} approvals.`,
            `<div class="text-start">
                <div class="mb-2">Successfully generated:<br> ${success.join('<br>')}</div>
                <div class="mb-2">Failed to generate:<br> ${fail.join('<br>')}</div>
            </div>`, () => {
            KTApprovalList.datatable().ajax.reload();
            form.reset();
            modal.hide();
        });
    }; 

    const getUnitsByUnitTypeId = async (unitTypeId) => {
        try {
            const response = await fetch(`${siteUrl}setting/approval/get_units_by_unit_type_id`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ unit_type_id: unitTypeId }).toString()
            });
            const result = await response.json();
            return result.data;
        } catch (error) {
            return [];
        }
    };

    return {
        init: function () {
            element = document.getElementById('kt_modal_add_edit_approval');
            modal = new bootstrap.Modal(element);
            
            form = element.querySelector('#kt_modal_add_edit_approval_form');
            submitButton = form.querySelector('[data-kt-approvals-modal-action="submit"]');
            cancelButton = form.querySelector('[data-kt-approvals-modal-action="cancel"]');
            closeButton = element.querySelector('[data-kt-approvals-modal-action="close"]');

            initAddEditApproval();
            initOptionSelect2();
            initTagify();

            element.addEventListener('shown.bs.modal', () => {});

            element.addEventListener('hidden.bs.modal', () => {
                form.reset();
            });

            form.addEventListener('reset', () => {
                const selects = form.querySelectorAll('select');
                selects.forEach((select) => {
                    select.value = null;
                    select.dispatchEvent(new Event('change'));
                });
            });

            document.addEventListener('click', async (event) => {
                const editApprovalButton = event.target.closest('[data-kt-approval-table-filter="edit_row"]');
                if (editApprovalButton) {
                    event.preventDefault();
                    form.reset();
                    typeFilter = editApprovalButton.getAttribute('data-type-filter');
                    approvalData = editApprovalButton.getAttribute('data-approval');
                    await setupApprovalForm();
                    modal.show();
                }
            });
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTApprovalAddEditApproval.init();
});