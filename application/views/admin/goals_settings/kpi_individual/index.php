<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">KPI Individual</h1>
                <!--end::Title-->
                <!--begin::Separator-->
                <span class="h-20px border-gray-200 border-start mx-4"></span>
                <!--end::Separator-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="<?= site_url('admin/dashboard') ?>" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-200 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">Goals Settings</li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-200 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-dark">KPI Individual</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card body-->
                <div class="card-body">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-12">
                            <!--begin::KPI Individual-->
                            <div class="card card-xxl-stretch">
                                <!--begin::Card header-->
                                <div class="card-header card-header bg-gradient-info">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder text-white">KPI Individual</span>
                                    </h3>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body">
                                    <!--begin::Form-->
                                    <form id="kt_kpi_individual_form" class="form" action="#">
                                        <!--begin::Row-->
                                        <div class="row gx-10 mb-5">
                                            <!--begin::Col-->
                                            <div class="col-12">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7 row">
                                                    <!--begin::Label-->
                                                    <label class="col-sm-6 col-form-label fw-bold fs-6 mb-2">Year Period</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <div class="col-sm-6">
                                                        <select name="year_period_id" aria-label="Select year period" data-control="select2" data-placeholder="Select year period..." class="form-select form-select-solid fw-bolder" data-allow-clear="true">
                                                            <option></option>
                                                        </select>
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7 row">
                                                    <!--begin::Label-->
                                                    <label class="col-sm-6 col-form-label fw-bold fs-6 mb-2">Employee</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <div class="col-sm-6">
                                                        <select name="employee_id" aria-label="Select employee" data-control="select2" data-placeholder="Select employee..." class="form-select form-select-solid fw-bolder" data-allow-clear="true">
                                                            <option></option>
                                                        </select>
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7 row">
                                                    <!--begin::Label-->
                                                    <label class="col-sm-6 col-form-label fw-bold fs-6 mb-2">NPP</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <div class="col-sm-6">
                                                        <input type="text" name="npp" class="form-control form-control-solid bold-input" placeholder="NPP" value="" readonly/>
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7 row">
                                                    <!--begin::Label-->
                                                    <label class="col-sm-6 col-form-label fw-bold fs-6 mb-2">Position<sup>(* currently)</sup></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <div class="col-sm-6">
                                                        <select name="position_id" aria-label="Select position" data-control="select2" data-placeholder="Select position..." class="form-select form-select-solid fw-bolder select2-readonly" data-allow-clear="true">
                                                            <option></option>
                                                        </select>
                                                        <span id="description" class="text-muted"></span>
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7 row">
                                                    <!--begin::Label-->
                                                    <label class="col-sm-6 col-form-label fw-bold fs-6 mb-2">Unit<sup>(* currently)</sup></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <div class="col-sm-6">
                                                        <select name="unit_id" aria-label="Select unit" data-control="select2" data-placeholder="Select unit..." class="form-select form-select-solid fw-bolder select2-readonly" data-allow-clear="true">
                                                            <option></option>
                                                        </select>
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7 row">
                                                    <!--begin::Label-->
                                                    <label class="col-sm-6 col-form-label fw-bold fs-6 mb-2">Placement Unit<sup>(* currently)</sup></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <div class="col-sm-6">
                                                        <select name="placement_unit_id" aria-label="Select placement unit" data-control="select2" data-placeholder="Select placement unit..." class="form-select form-select-solid fw-bolder select2-readonly" data-allow-clear="true">
                                                            <option></option>
                                                        </select>
                                                    </div>
                                                    <!--end::Input-->
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </form>
                                    <!--end::Form-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::KPI Individual-->
                        </div>
                    </div>
                    <!--end::Row-->
                    <!--begin::Row-->
                    <div class="row d-none" id="kt_goals_settings_container">
                        <div class="col-12">
                            <!--begin::Goals Settings-->
                            <div class="card card-xxl-stretch">
                                <!--begin::Card header-->
                                <div class="card-header card-header bg-gradient-primary">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder text-white">Goals Settings</span>
                                    </h3>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body">
                                    <!--begin::Table container-->
                                    <div class="table-responsive" id="kt_table_goals_settings">
                                        <!--begin::Table-->
                                        <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                            <!--begin::Table head-->
                                            <thead>
                                                <tr class="fw-bolder text-muted">
                                                    <th class="min-w-50px">No</th>
                                                    <th class="min-w-125px">Position <sup>(* KPI)</sup></th>
                                                    <th class="min-w-125px">Unit <sup>(* KPI)</sup></th>
                                                    <th class="min-w-125px">Placement Unit <sup>(* KPI)</sup></th>
                                                    <th class="min-w-125px">Month Period</th>
                                                    <th class="min-w-50px text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <!--end::Table head-->
                                            <!--begin::Table body-->
                                            <tbody>
                                            </tbody>
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Table container-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Goals Settings-->
                        </div>
                    </div>
                    <!--end::Row-->
                    <!--begin::Row-->
                    <div class="row d-none" id="kt_kpi_container">
                        <div class="col-12">
                            <!--begin::KPI-->
                            <div class="card card-xxl-stretch">
                                <!--begin::Card header-->
                                <div class="card-header card-header bg-gradient-primary">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder text-white">KPI</span>
                                    </h3>
                                    <div class="card-toolbar">
                                        <!-- <button type="button" class="btn btn-danger min-w-125px me-2 d-flex justify-content-center align-items-center" onclick="window.print()">
                                            <span class="svg-icon svg-icon-2">
                                                    <span class="svg-icon svg-icon-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"/>
                                                <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"/>
                                                <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"/>
                                                </svg></span>
                                            </span>
                                            Print
                                        </button> -->
                                        <!-- <button type="button" class="btn btn-light min-w-125px d-none" data-kt-kpis-button-action="submit" disabled>Submit</button> -->
                                        <?php if ($is_admin == SYSTEM_ADMIN): ?>
                                            <!-- <button type="button" class="btn btn-light min-w-125px d-none" data-kt-kpis-button-action="cancel_submit" disabled>Cancel</button> -->
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body">
                                    <!--begin::KPI Percentage-->
                                    <div class="d-flex flex-wrap flex-stack">
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="required fw-bold fs-6 mb-2">Position</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input type="text" name="position_name" class="form-control form-control-solid mb-3 mb-lg-0 auto-width-input" placeholder="Position" readonly/>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <div class="d-flex flex-wrap">
                                            <!--begin::Percentage-->
                                            <div class="border border-success border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3" id="kt_kpi_percentage_container">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-2hx fw-bolder" id="kt_kpi_percentage" data-kt-countup="true" data-kt-countup-value="0.00" data-kt-countup-suffix=" %" data-kt-countup-decimal-places="2">0.00</div>
                                                </div>
                                                <input type="hidden" name="percentage" id="kt_kpi_percentage_input" value="0">
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-bold fs-6 text-gray-400">Percentage</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Percentage-->
                                        </div>
                                    </div>
                                    <!--end::KPI Percentage-->
                                    <!--begin::Separator-->
                                    <div class="separator separator-dashed my-5"></div>
                                    <!--end::Separator-->
                                    <!--begin::KPI Form-->
                                    <div class="d-flex justify-content-end">
                                        <!--begin::Group actions-->
                                        <div class="d-flex justify-content-end align-items-center me-2 d-none" data-kt-kpi-table-toolbar="selected">
                                            <div class="fw-bolder me-5">
                                            <span class="me-2" data-kt-kpi-table-select="selected_count"></span>Selected</div>
                                            <button type="button" class="btn btn-danger" data-kt-kpi-table-select="delete_selected">Delete Selected</button>
                                        </div>
                                        <!--end::Group actions-->
                                        <!--begin::Add KPI-->
                                        <button type="button" class="btn btn-primary-bs" data-bs-toggle="modal" data-bs-target="#kt_modal_add_kpis"
                                        data-kt-kpi-table-toolbar="base"
                                        data-kt-kpis-button-action="add_kpi">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                            <span class="svg-icon svg-icon-2">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="black" />
                                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            Add KPI
                                        </button>
                                        <!--end::Add KPI-->
                                        <!--begin::Modal - Add KPI-->
                                        <div class="modal fade" id="kt_modal_add_kpis" tabindex="-1" aria-hidden="true">
                                            <!--begin::Modal dialog-->
                                            <div class="modal-dialog modal-dialog-centered mw-650px">
                                                <!--begin::Modal content-->
                                                <div class="modal-content">
                                                    <!--begin::Modal header-->
                                                    <div class="modal-header" id="kt_modal_add_kpis_header">
                                                        <!--begin::Modal title-->
                                                        <h2 class="fw-bolder" id="kt_modal_add_kpis_header_title">Add KPI</h2>
                                                        <!--end::Modal title-->
                                                        <!--begin::Close-->
                                                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-kpis-modal-action="close">
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                                            <span class="svg-icon svg-icon-1">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                        </div>
                                                        <!--end::Close-->
                                                    </div>
                                                    <!--end::Modal header-->
                                                    <!--begin::Modal body-->
                                                    <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                                        <!--begin::Form-->
                                                        <form id="kt_modal_add_kpis_form" class="form" action="#">
                                                            <!--begin::Scroll-->
                                                            <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_kpis_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_kpis_header" data-kt-scroll-wrappers="#kt_modal_add_kpis_scroll" data-kt-scroll-offset="300px">
                                                                <input type="hidden" name="id" />
                                                                <!--begin::Input group-->
                                                                <div class="fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="required fw-bold fs-6 mb-2">Number Of Rows</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="1" name="number_of_rows" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Number Of Rows" value="1"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <!--end::Input group-->
                                                            </div>
                                                            <!--end::Scroll-->
                                                            <!--begin::Actions-->
                                                            <div class="text-center pt-15">
                                                                <button type="reset" class="btn btn-light me-3" data-kt-kpis-modal-action="cancel">Discard</button>
                                                                <button type="submit" class="btn btn-primary" data-kt-kpis-modal-action="submit">
                                                                    <span class="indicator-label">Submit</span>
                                                                    <span class="indicator-progress">Please wait...
                                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                                </button>
                                                            </div>
                                                            <!--end::Actions-->
                                                        </form>
                                                        <!--end::Form-->
                                                    </div>
                                                    <!--end::Modal body-->
                                                </div>
                                                <!--end::Modal content-->
                                            </div>
                                            <!--end::Modal dialog-->
                                        </div>
                                        <!--end::Modal - Add KPI-->
                                    </div>
                                    <!--end::KPI Form-->
                                    <!--begin::Table KPI-->
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_kpis">
                                            <!--begin::Table KPI head-->
                                            <thead>
                                                <!--begin::Table KPI row-->
                                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                                    <th class="w-10px pe-2">
                                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_kpis .form-check-input" value="1" />
                                                        </div>
                                                    </th>
                                                    <th class="min-w-150px">KPI</th>
                                                    <th class="min-w-80px">Measurement</th>
                                                    <th class="min-w-80px">Target</th>
                                                    <th class="min-w-80px">Actual</th>
                                                    <th class="min-w-80px">Counter</th>
                                                    <th class="min-w-80px">Polarization</th>
                                                    <th class="min-w-80px">Weight</th>
                                                    <th class="text-end min-w-100px">Actions</th>
                                                </tr>
                                                <!--end::Table KPI row-->
                                            </thead>
                                            <!--end::Table KPI head-->
                                            <!--begin::Table KPI body-->
                                            <tbody class="fw-bold text-gray-600">
                                                <!-- <tr>
                                                    <td>
                                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox" value="1" />
                                                        </div>
                                                    </td>
                                                </tr> -->
                                            </tbody>
                                            <!--end::Table KPI body-->
                                        </table>
                                    </div>
                                    <!--end::Table KPI-->
                                    <!--begin::Modal - Target Actual-->
                                    <div class="modal fade" id="kt_modal_target_actual" tabindex="-1" aria-hidden="true">
                                        <!--begin::Modal dialog-->
                                        <div class="modal-dialog modal-dialog-centered mw-650px">
                                            <!--begin::Modal content-->
                                            <div class="modal-content">
                                                <!--begin::Modal header-->
                                                <div class="modal-header" id="kt_modal_target_actual_header">
                                                    <!--begin::Modal title-->
                                                    <h2 class="fw-bolder" id="kt_modal_target_actual_header_title">Target/Actual</h2>
                                                    <!--end::Modal title-->
                                                    <!--begin::Close-->
                                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-target-actual-modal-action="close">
                                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                                                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </div>
                                                    <!--end::Close-->
                                                </div>
                                                <!--end::Modal header-->
                                                <!--begin::Modal body-->
                                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                                    <!--begin::Form-->
                                                    <form id="kt_modal_target_actual_form" class="form">
                                                        <!--begin::Scroll-->
                                                        <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_target_actual_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_target_actual_header" data-kt-scroll-wrappers="#kt_modal_target_actual_scroll" data-kt-scroll-offset="300px">
                                                            <input type="hidden" name="kpi_individual_id">
                                                            <input type="hidden" name="target_id">
                                                            <input type="hidden" name="actual_id">
                                                            <!--begin::Input group-->
                                                            <div class="fv-row mb-5 row">
                                                                <!--begin::Label-->
                                                                <label class="col-sm-4 col-form-label fw-bold fs-6 mb-2">KPI</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="kpi_text" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="KPI"/>
                                                                </div>
                                                                <!--end::Input-->
                                                            </div>
                                                            <!--begin::Input group-->
                                                            <div class="fv-row mb-5 row">
                                                                <!--begin::Label-->
                                                                <label class="col-sm-4 col-form-label fw-bold fs-6 mb-2">Measurement</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="measurement" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Measurement"/>
                                                                </div>
                                                                <!--end::Input-->
                                                            </div>
                                                            <!--begin::Input group-->
                                                            <div class="fv-row mb-5 row">
                                                                <!--begin::Label-->
                                                                <label class="col-sm-4 col-form-label fw-bold fs-6 mb-2">Counter</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="counter" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Counter"/>
                                                                </div>
                                                                <!--end::Input-->
                                                            </div>
                                                            <!--begin::Input group-->
                                                            <div class="fv-row mb-5 row">
                                                                <!--begin::Label-->
                                                                <label class="col-sm-4 col-form-label fw-bold fs-6 mb-2">Polarization</label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="polarization" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Polarization"/>
                                                                </div>
                                                                <!--end::Input-->
                                                            </div>
                                                            <!--end::Input group-->
                                                            <!--begin::Menu separator-->
                                                            <div class="separator mb-5"></div>
                                                            <!--end::Menu separator-->
                                                            <!--begin::Input group-->
                                                            <div class="row">
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">January</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_1" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="January"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">February</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_2" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="February"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">March</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_3" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="March"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">April</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_4" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="April"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">May</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_5" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="May"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">June</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_6" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="June"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">July</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_7" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="July"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">August</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_8" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="August"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">September</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_9" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="September"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">October</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_10" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="October"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">November</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_11" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="November"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <div class="col-md-6 fv-row mb-7">
                                                                    <!--begin::Label-->
                                                                    <label class="fw-bold fs-6 mb-2">December</label>
                                                                    <!--end::Label-->
                                                                    <!--begin::Input-->
                                                                    <input type="number" min="0" step="0.01" value="0" name="month_12" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="December"/>
                                                                    <!--end::Input-->
                                                                </div>
                                                            </div>
                                                            <!--end::Input group-->
                                                        </div>
                                                        <!--end::Scroll-->
                                                        <!--begin::Actions-->
                                                        <div class="text-center pt-15">
                                                            <button type="reset" class="btn btn-light me-3" data-kt-target-actual-modal-action="cancel">Discard</button>
                                                            <button type="submit" class="btn btn-primary" data-kt-target-actual-modal-action="submit">
                                                                <span class="indicator-label">Submit</span>
                                                                <span class="indicator-progress">Please wait...
                                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                            </button>
                                                        </div>
                                                        <!--end::Actions-->
                                                    </form>
                                                    <!--end::Form-->
                                                </div>
                                                <!--end::Modal body-->
                                            </div>
                                            <!--end::Modal content-->
                                        </div>
                                        <!--end::Modal dialog-->
                                    </div>
                                    <!--end::Modal - Target Actual-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::KPI-->
                        </div>
                    </div>
                    <!--end::Row-->
                    <!--begin::Row-->
                    <div class="row d-none" id="kt_target_submit_container">
                        <div class="col-12">
                            <!--begin::KPI-->
                            <div class="card card-xxl-stretch">
                                <!--begin::Card header-->
                                <div class="card-header card-header bg-gradient-primary">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder text-white">Target Submit Information</span>
                                    </h3>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body">
                                    
                                    <!--begin::Table Target Submit-->
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_target_submit">
                                            <!--begin::Table Target Submit head-->
                                            <thead>
                                                <!--begin::Table Target Submit row-->
                                                <!--end::Table Target Submit row-->
                                            </thead>
                                            <!--end::Table Target Submit head-->
                                            <!--begin::Table Target Submit body-->
                                            <tbody class="fw-bold text-gray-600">
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td class="text-center"><h5>Status</h5></td>
                                                    <td class="text-center"><h5>Time</h5></td>
                                                </tr>
                                                <tr>
                                                    <td><h5>Self Submit</h5></td>
                                                    <td colspan="4">            
                                                        <form id="kt_target_submit_form" class="form" action="#">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="fv-row">
                                                                    <label class="form-check form-check-custom form-check-solid form-check-inline">
                                                                        <input class="form-check-input" type="checkbox" name="term_and_conditions" />
                                                                        <span class="form-check-label fw-bold text-gray-700 fs-6">I Agree
                                                                        <a href="javascript:void(0)">Terms and conditions</a>.</span>
                                                                    </label>
                                                                </div>
                                                                <button type="submit" class="btn btn-primary min-w-125px" data-kt-target-submit-button-action="submit" disabled>
                                                                    <span class="indicator-label">Submit</span>
                                                                    <span class="indicator-progress">Please wait...
                                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td class="text-center"><span id="self_submit_status"><span class="badge badge-secondary w-100">Not Submitted</span></span></td>
                                                    <td class="text-center">
                                                        <span id="self_submit_time">-</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><h5>Approver</h5></td>
                                                    <td><h5>Unit</h5></td>
                                                    <td><h5>Position</h5></td>
                                                    <td><h5>Comments</h5></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tbody id="kt_table_target_submit_approval"></tbody>
                                            </tbody>
                                            <!--end::Table Target Submit body-->
                                        </table>
                                    </div>
                                    <!--end::Table Target Submit-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::KPI-->
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
<!--end::Content-->