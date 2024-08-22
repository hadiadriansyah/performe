"use strict";

const KTSubmitTarget = (() => {
    let element;
    let form;
    let approvalContainer;
    let submitButton;
    let validator;

    let goalsSettingsData;
    let approvalData = null;
    let idSubmitTarget = null;

    let unitApproval1Id = null;
    let positionApproval1Id = null;
    let unitApproval2Id = null;
    let positionApproval2Id = null;

    const initSubmitTarget = () => {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'term_and_conditions': {
                        validators: {
                            notEmpty: {
                                message: 'Term and conditions is required'
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
                    KTAlertDialog.showConfirmationDialog("Are you sure you would like to submit?", () => {
                        handleSubmitPa();
                    });
                } else {
                    KTAlertDialog.showErrorMessage("Please check the term and conditions.");
                }
            }
        });
    };

    const handleSubmitPa = async () => {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
        try {
            const paIndividualId = KTIndividualPerformanceAppraisal.getKpiPaIndividualId();
            const term_and_conditions = form.querySelector('[name="term_and_conditions"]').checked;
            const formDetails = {
                url: 'performance_appraisal/individual_performance_appraisal/submit_pa',
                formData: new URLSearchParams({
                    id: idSubmitTarget,
                    pa_individual_id: paIndividualId,
                    term_and_conditions: term_and_conditions ? 1 : 0,
                    unit_approval_1_id: unitApproval1Id ?? '',
                    position_approval_1_id: positionApproval1Id ?? '',
                    unit_approval_2_id: unitApproval2Id ?? '',
                    position_approval_2_id: positionApproval2Id ?? ''
                }).toString()
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
            handleFormResponse(result);
        } catch (error) {
            KTAlertDialog.showErrorMessage("Error while submitting data!.");
        }
    };

    const handleFormResponse = (response) => {
        if (response.status === 'success') {
            KTAlertDialog.showSuccessMessage("Form has been successfully submitted!", async () => {
                await getApprovalSubmitPa();
            });
        } else {
            KTAlertDialog.showErrorMessage(response.message);
        }
    };

    const setupPaSubmitApproval = async (gsData) => {
        goalsSettingsData = gsData;
        idSubmitTarget = '';

        approvalContainer.innerHTML = '';
        approvalData = await getApproval();
        if (approvalData) {
            await getApprovalSubmitPa();
        }
    };

    const getApproval = async () => {
        try {
            const response = await fetch(`${siteUrl}performance_appraisal/individual_performance_appraisal/get_approval_by_goals_settings`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(goalsSettingsData).toString()
            });
            const result = await response.json();
            return result.data;
        } catch (error) {
            return null;
        }
    };

    const getApprovalSubmitPa = async () => {
        try {
            const response = await fetch(`${siteUrl}performance_appraisal/individual_performance_appraisal/get_approval_pa`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    pa_individual_id: KTIndividualPerformanceAppraisal.getKpiPaIndividualId()
                }).toString()
            });
            await new Promise(resolve => setTimeout(resolve, 300));
            const result = await response.json();
            const data = result.data;
            await displayApprovalData();

            if (data) {
                console.log(data)
                idSubmitTarget = data.id;
                updateSubmitStatus(data.self_submit, '#self_submit_status');
                updateDateTime(data.created_at, '#self_submit_time');

                updateApprovalStatus(data.status_puk_1, '#approval_1_status');
                updateComment(data.status_puk_1, data.comment_puk_1, data.employee_name_puk_1, '#approval_1_comment');
                updateDateTime(data.puk_1_submit_time, '#approval_1_submit_time');

                updateApprovalStatus(data.status_puk_2, '#approval_2_status');
                // updateComment(data.status_puk_2, data.comment_puk_2, data.employee_name_puk_2, '#approval_2_comment');
                updateDateTime(data.puk_2_submit_time, '#approval_2_submit_time');
            }
            
        } catch (error) {
            console.log(error)
            submitButton.disabled = false;
            form.querySelector('[name="term_and_conditions"]').checked = false;
            form.querySelector('[name="term_and_conditions"]').disabled = false;
            // document.querySelector('#self_submit_status').innerHTML = `<span class="badge badge-secondary w-100">Not Submitted</span>`;
            // document.querySelector('#approval_1_status').innerHTML = `<span class="badge badge-secondary w-100">Not Submitted</span>`;
            // document.querySelector('#approval_2_status').innerHTML = `<span class="badge badge-secondary w-100">Not Submitted</span>`;
            // document.querySelector('#approval_1_comment').innerHTML = '-';
            // document.querySelector('#approval_2_comment').innerHTML = '-';
            // document.querySelector('#self_submit_time').innerHTML = '-';
            // document.querySelector('#approval_1_submit_time').innerHTML = '-';
            // document.querySelector('#approval_2_submit_time').innerHTML = '-';
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
        if (status != 0) {
            submitButton.disabled = true;
            document.querySelectorAll('[data-kt-kpi-table-filter="edit_row"]').forEach(element => {
                element.classList.add('d-none');
            });
            form.querySelector('[name="term_and_conditions"]').checked = true;
            form.querySelector('[name="term_and_conditions"]').disabled = true;
        } else {
            submitButton.disabled = false;
            document.querySelectorAll('[data-kt-kpi-table-filter="edit_row"]').forEach(element => {
                element.classList.remove('d-none');
            });
            form.querySelector('[name="term_and_conditions"]').checked = false;
            form.querySelector('[name="term_and_conditions"]').disabled = false;
        }
    };

    const updateApprovalStatus = (status, selector) => {
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
        let html = '';
        if (approvalData.approval_1 && Array.isArray(approvalData.approval_1)) {
            approvalData.approval_1.forEach((approver, index) => {
                if (index === 0) {
                    unitApproval1Id = approver.unit_approval_1_id;
                    positionApproval1Id = approver.position_approval_1_id;
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
                    unitApproval2Id = approver.unit_approval_2_id;
                    positionApproval2Id = approver.position_approval_2_id;
                    html += `<tr>
                                <td rowspan="${approvalData.approval_2.length}"><h5>PUK 2</h5></td>
                                <td class="ps-0">${approver.approval_2_npp} <br> ${approver.approval_2_name}</td>
                                <td class="ps-0">${approver.unit_approval_2_name}</td>
                                <td class="ps-0">${approver.position_approval_2_name}</td>
                                <td rowspan="${approvalData.approval_2.length}"><span id="approval_2_comment">-</span></td>
                                <td rowspan="${approvalData.approval_2.length}" class="text-center"><span id="approval_2_status"><span class="badge badge-secondary w-100">Not Submitted</span></span></td>
                                <td rowspan="${approvalData.approval_1.length}" class="text-center">
                                    <span id="approval_2_submit_time">-</span>
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
    };

    return {
        init: function () {
            element = document.getElementById('kt_table_pa_submit');
            form = element.querySelector('#kt_pa_submit_form');
            approvalContainer = document.querySelector('#kt_table_pa_submit_approval');

            submitButton = form.querySelector('[data-kt-pa-submit-button-action="submit"]');

            initSubmitTarget();
        },
        setupPaSubmitApproval: setupPaSubmitApproval
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTSubmitTarget.init();
});