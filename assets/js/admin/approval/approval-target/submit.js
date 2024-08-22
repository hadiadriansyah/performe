"use strict";

const KTSubmitTarget = (() => {
    let element;
    let form;
    let modal;
    let approvalContainer;
    let submitButton;
    let cancelButton;
    let closeButton;

    let validator;

    let approvalEmployeeData;

    let approvalData = null;
    let idSubmitTarget = '';
    let numberOfPuks = 0;
    let pukNumber = null;

    const initSubmitTarget = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'status': {
                        validators: {
                            notEmpty: {
                                message: 'Status is required'
                            }
                        }
                    },
                    'comment': {
                        validators: {
                            notEmpty: {
                                message: 'The comment is required'
                            },
                            stringLength: {
                                min: 15,
                                message: 'The comment must be more than 15 characters long',
                            },
                        },
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
                    KTAlertDialog.showConfirmationDialog("Are you sure you would like to submit?", () => {
                        handleFormSubmission();
                    });
                } else {
                    KTAlertDialog.showErrorMessage("Please fill in the form correctly.");
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


    const handleFormSubmission = async () => {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
        try {
            const employeeId = document.querySelector('[name="employee_id"]').value;
            const unitId = document.querySelector('[name="placement_unit_id"]').value;
            const positionId = document.querySelector('[name="position_id"]').value;
            const formData = new FormData(form);
            formData.append('id', idSubmitTarget);
            formData.append('puk_number', pukNumber);
            formData.append('number_of_puks', numberOfPuks);
            formData.append('employee_id', employeeId);
            formData.append('unit_id', unitId);
            formData.append('position_id', positionId);
            const formDetails = {
                url: 'approval/approval_target/submit_target',
                formData: new URLSearchParams(formData).toString()
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
        if (response.status === 'success') {
            KTAlertDialog.showSuccessMessage("Form has been successfully submitted!", async () => {
                modal.hide();
                await getApprovalSubmitTarget();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    const setupTargetSubmitApproval = async (data) => {
        approvalEmployeeData = data;
        idSubmitTarget = '';
        
        approvalContainer.innerHTML = '';

        approvalData = await getApproval();
        if (approvalData) {
            await getApprovalSubmitTarget();
            // await getHistApproval();
        }
    };

    const getHistApproval = async () => {
        try {
            const response = await fetch(`${siteUrl}approval/approval_target/get_hist_approval_target`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    approval_target_id: idSubmitTarget
                }).toString()
            });
            const result = await response.json();
            return result.data;
        } catch (error) {
            return null;
        }
    };

    const getApproval = async () => {
        try {
            const response = await fetch(`${siteUrl}approval/approval_target/get_approval_by_approval_target`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(approvalEmployeeData).toString()
            });
            const result = await response.json();
            return result.data;
        } catch (error) {
            return null;
        }
    };

    const getApprovalSubmitTarget = async () => {
        try {
            const response = await fetch(`${siteUrl}approval/approval_target/get_approval_target`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    pa_individual_id: KTApprovalTarget.getApprovalEmployeeData().pa_individual_id
                }).toString()
            });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            const data = result.data;

            await displayApprovalData();
            
            numberOfPuks = 0;
            if (approvalData.approval_1 && Array.isArray(approvalData.approval_1)) {
                numberOfPuks++;
            }

            if (approvalData.approval_2 && Array.isArray(approvalData.approval_2)) {
                numberOfPuks++;
            }

            idSubmitTarget = data.id;

            updateSubmitStatus(data.self_submit, '#applicant_submit_status');

            updateDateTime(data.self_submit_time, '#applicant_submit_time');

            updateApprovalStatus(data.puk_1_status, '#approval_1_status', data);
            updateComment(data.status_puk_1, data.comment_puk_1, data.employee_name_puk_1, '#approval_1_comment');
            updateDateTime(data.puk_1_submit_time, '#approval_1_submit_time');

            updateApprovalStatus(data.puk_2_status, '#approval_2_status', data);
            updateComment(data.status_puk_2, data.comment_puk_2, data.employee_name_puk_2, '#approval_2_comment');
            updateDateTime(data.puk_2_submit_time, '#approval_2_submit_time');

        } catch (error) {
            submitButton.disabled = false;
        }
    };
    
    const updateSubmitStatus = (status, selector) => {
        const statusMap = {
            1: 'Submitted',
            2: 'Approved',
            3: 'Rejected'
        };
        const statusClass = status == 1 ? 'badge-info' : status == 2 ? 'badge-success' : status == 3 ? 'badge-danger' : 'badge-secondary';
        const statusText = statusMap[status] || 'Not Submitted';
        document.querySelector(selector).innerHTML = `<span class="badge ${statusClass} w-100">${statusText}</span>`;
    };

    const updateComment = (status, comment, employeeName, selector) => {
        const statusMap = {
            1: 'Submitted',
            2: 'Approved',
            3: 'Rejected'
        };
        if (comment && (status != 0 || status != 1)) {
            document.querySelector(selector).innerHTML = employeeName ? statusMap[status] + ' by <br>' + employeeName + '<br><br>' + comment : comment;
        } else {
            document.querySelector(selector).innerHTML = '-';
        }
    };

    const updateApprovalStatus = (status, selector, data) => {
        const submitTargetButton = document.querySelector('[data-kt-submit-target-button-action="submit_puk"]');
        if (submitTargetButton) {
            
            pukNumber = submitTargetButton.getAttribute('data-puk-number');
            if (pukNumber == 1) {
                submitTargetButton.disabled = data.puk_1_status == 2 || data.puk_1_status == 3;

            } else {
                submitTargetButton.disabled = data.puk_2_status == 2 || data.puk_2_status == 3;
            }
        }
        const statusMap = {
            1: 'Submitted',
            2: 'Approved',
            3: 'Rejected'
        };
        const statusClass = status == 1 ? 'badge-info' : status == 2 ? 'badge-success' : status == 3 ? 'badge-danger' : 'badge-secondary';
        const statusText = statusMap[status] || 'Not Submitted';
        document.querySelector(selector).innerHTML = `<span class="badge ${statusClass} w-100">${statusText}</span>`;
    };

    const updateDateTime = (dateTime, selector) => {
        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        if (dateTime) {
            const formattedDateTime = new Date(dateTime).toLocaleDateString('en-US', options);
            document.querySelector(selector).innerHTML = formattedDateTime;
        } else {
            document.querySelector(selector).innerHTML = '-';
        }
    };
    
    const displayApprovalData = async () => {
        const formApprovalTarget = document.querySelector('#kt_approval_target_form');
        const placementUnitId = formApprovalTarget.querySelector('[name="placement_unit_id"]').value;
        const positionId = formApprovalTarget.querySelector('[name="position_id"]').value;

        let html = '';
        if (approvalData.approval_1 && Array.isArray(approvalData.approval_1)) {
            approvalData.approval_1.forEach((approver, index) => {
                if (index === 0) {
                    html += `<tr>
                                <td rowspan="${approvalData.approval_1.length}"><h5>PUK 1</h5></td>
                                <td class="ps-0">${approver.approval_1_npp} <br> ${approver.approval_1_name}</td>
                                <td class="ps-0">${approver.unit_approval_1_name}</td>
                                <td class="ps-0">${approver.position_approval_1_name}</td>
                                <td rowspan="${approvalData.approval_1.length}"><span id="approval_1_comment">-</span></td>
                                <td rowspan="${approvalData.approval_1.length}" class="text-center"><span id="approval_1_status"><span class="badge badge-secondary w-100">Not Submitted</span></span></td>
                                <td rowspan="${approvalData.approval_1.length}" class="text-center">
                                    <span id="approval_1_submit_time">-</span>
                                </td>
                                <td rowspan="${approvalData.approval_1.length}" class="text-center">
                                    ${approver.unit_approval_1_id === placementUnitId && approver.position_approval_1_id === positionId ? '<button class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_submit_target" data-kt-submit-target-button-action="submit_puk" data-puk-number="1" disabled>Submit</button>' : ''}
                                </td>
                            </tr>`;
                } else {
                    html += `<tr>
                                <td class="ps-0">${approver.approval_1_npp} <br> ${approver.approval_1_name}</td>
                                <td class="ps-0">${approver.unit_approval_1_name}</td>
                                <td class="ps-0">${approver.position_approval_1_name}</td>
                            </tr>`;
                }
            });
        }
    
        if (approvalData.approval_2 && Array.isArray(approvalData.approval_2)) {
            approvalData.approval_2.forEach((approver, index) => {
                if (index === 0) {
                    html += `<tr>
                                <td rowspan="${approvalData.approval_2.length}"><h5>PUK 2</h5></td>
                                <td class="ps-0">${approver.approval_2_npp} <br> ${approver.approval_2_name}</td>
                                <td class="ps-0">${approver.unit_approval_2_name}</td>
                                <td class="ps-0">${approver.position_approval_2_name}</td>
                                <td rowspan="${approvalData.approval_2.length}"><span id="approval_2_comment">-</span></td>
                                <td rowspan="${approvalData.approval_2.length}" class="text-center"><span id="approval_2_status"><span class="badge badge-secondary w-100">Not Submitted</span></span></td>
                                <td rowspan="${approvalData.approval_2.length}" class="text-center">
                                    <span id="approval_2_submit_time">-</span>
                                </td>
                                <td rowspan="${approvalData.approval_2.length}" class="text-center">
                                    ${approver.unit_approval_2_id === placementUnitId && approver.position_approval_2_id === positionId ? '<button class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_submit_target" data-kt-submit-target-button-action="submit_puk" data-puk-number="2" disabled>Submit</button>' : ''}
                                </td>
                            </tr>`;
                } else {
                    html += `<tr>
                                <td class="ps-0">${approver.approval_2_npp} <br> ${approver.approval_2_name}</td>
                                <td class="ps-0">${approver.unit_approval_2_name}</td>
                                <td class="ps-0">${approver.position_approval_2_name}</td>
                            </tr>`;
                }
            });
        }
    
        if (!html) {
            html = '<tr><td colspan="7" class="text-center">No approval data available</td></tr>';
        }
    
        approvalContainer.innerHTML = html;
        modal = new bootstrap.Modal(element);
    };

    return {
        init: function () {
            element = document.getElementById('kt_modal_submit_target');

            form = element.querySelector('#kt_modal_submit_target_form');
            approvalContainer = document.querySelector('#kt_table_target_submit_approval');
            submitButton = form.querySelector('[data-kt-submit-target-modal-action="submit"]');
            cancelButton = form.querySelector('[data-kt-submit-target-modal-action="cancel"]');
            closeButton = element.querySelector('[data-kt-submit-target-modal-action="close"]');

            initSubmitTarget();

            element.addEventListener('shown.bs.modal', () => {});

            element.addEventListener('hidden.bs.modal', () => {
                form.reset();
            });

            // document.addEventListener('click', async (event) => {
            //     const submitTargetButton = event.target.closest('[data-kt-submit-target-button-action="submit_puk"]');
            //     if (submitTargetButton) {
            //         pukNumber = submitTargetButton.getAttribute('data-puk-number');
            //     }
            // });
        },
        setupTargetSubmitApproval: setupTargetSubmitApproval
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTSubmitTarget.init();
});