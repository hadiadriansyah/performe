<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <!--begin::Container-->
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <!--begin::Page title-->
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <!--begin::Title-->
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Approval Target</h1>
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
                    <li class="breadcrumb-item text-muted">Approval</li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-200 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-dark">Approval Target</li>
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
                                        <span class="card-label fw-bolder text-white">Approval Target</span>
                                    </h3>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body">
                                    <!--begin::Form-->
                                    <form id="kt_approval_target_form" class="form" action="#">
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
                    <div class="row d-none" id="kt_approval_employee_container">
                        <div class="col-12">
                            <!--begin::Approval Employee-->
                            <div class="card card-xxl-stretch">
                                <!--begin::Card header-->
                                <div class="card-header border-0 pt-6">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <!--begin::Search-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <input type="text" data-kt-approval-employee-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search ..." />
                                        </div>
                                        <!--end::Search-->
                                    </div>
                                    <!--begin::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <!--begin::Toolbar-->
                                        <div class="d-flex justify-content-end" data-kt-approval-employee-table-toolbar="base">
                                            <!--begin::Filter-->
                                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                            <span class="svg-icon svg-icon-2">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->Filter</button>
                                            <!--begin::Menu 1-->
                                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                                <!--begin::Header-->
                                                <div class="px-7 py-5">
                                                    <div class="fs-5 text-dark fw-bolder">Filter Options</div>
                                                </div>
                                                <!--end::Header-->
                                                <!--begin::Separator-->
                                                <div class="separator border-gray-200"></div>
                                                <!--end::Separator-->
                                                <!--begin::Content-->
                                                <div class="px-7 py-5" data-kt-approval-employee-table-filter="form">
                                                    <!--begin::Input group-->
                                                    <div class="mb-10">
                                                        <label class="form-label fs-6 fw-bold">PUK:</label>
                                                        <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-kt-approval-employee-table-filter="puk" name="puk" data-hide-search="true">
                                                            <option></option>
                                                            <option value="All" selected>All</option>
                                                            <option value="1">As PUK 1</option>
                                                            <option value="2">As PUK 2</option>
                                                        </select>
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="mb-10">
                                                        <label class="form-label fs-6 fw-bold">PUK Status:</label>
                                                        <select class="form-select form-select-solid fw-bolder" data-kt-select2="true" data-placeholder="Select option" data-kt-approval-employee-table-filter="puk_status" name="puk_status" data-hide-search="true">
                                                            <option></option>
                                                            <option value="All" selected>All</option>
                                                            <option value="0">Not Submitted</option>
                                                            <option value="2">Approved</option>
                                                            <option value="3">Rejected</option>
                                                        </select>
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Actions-->
                                                    <div class="d-flex justify-content-end">
                                                        <button type="reset" class="btn btn-light btn-active-light-primary fw-bold me-2 px-6" data-kt-menu-dismiss="true" data-kt-approval-employee-table-filter="reset">Reset</button>
                                                        <button type="submit" class="btn btn-primary fw-bold px-6" data-kt-menu-dismiss="true" data-kt-approval-employee-table-filter="filter">Apply</button>
                                                    </div>
                                                    <!--end::Actions-->
                                                </div>
                                                <!--end::Content-->
                                            </div>
                                            <!--end::Menu 1-->
                                            <!--end::Filter-->
                                            <!--begin::Reload Data-->
                                            <button type="button" class="btn btn-light-info d-none" data-kt-approval-employee-table-filter="reload_approval_employee">
                                                <!--begin::Svg Icon | path: assets/media/icons/duotune/arrows/arr029.svg-->
                                                <span class="svg-icon svg-icon-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M14.5 20.7259C14.6 21.2259 14.2 21.826 13.7 21.926C13.2 22.026 12.6 22.0259 12.1 22.0259C9.5 22.0259 6.9 21.0259 5 19.1259C1.4 15.5259 1.09998 9.72592 4.29998 5.82592L5.70001 7.22595C3.30001 10.3259 3.59999 14.8259 6.39999 17.7259C8.19999 19.5259 10.8 20.426 13.4 19.926C13.9 19.826 14.4 20.2259 14.5 20.7259ZM18.4 16.8259L19.8 18.2259C22.9 14.3259 22.7 8.52593 19 4.92593C16.7 2.62593 13.5 1.62594 10.3 2.12594C9.79998 2.22594 9.4 2.72595 9.5 3.22595C9.6 3.72595 10.1 4.12594 10.6 4.02594C13.1 3.62594 15.7 4.42595 17.6 6.22595C20.5 9.22595 20.7 13.7259 18.4 16.8259Z" fill="black"/>
                                                <path opacity="0.3" d="M2 3.62592H7C7.6 3.62592 8 4.02592 8 4.62592V9.62589L2 3.62592ZM16 14.4259V19.4259C16 20.0259 16.4 20.4259 17 20.4259H22L16 14.4259Z" fill="black"/>
                                                </svg></span>
                                                <!--end::Svg Icon-->
                                                Reload
                                            </button>
                                            <!--end::Reload Data-->
                                        </div>
                                        <!--end::Toolbar-->
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body">
                                    <!--begin::Table container-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_approval_employee">
                                            <!--begin::Table head-->
                                            <thead>
                                                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                                    <th class="min-w-125px">Name</th>
                                                    <th class="min-w-125px">NPP</th>
                                                    <th class="min-w-200px">Position <sup>(* KPI)</sup></th>
                                                    <th class="min-w-125px">Unit <sup>(* KPI)</sup></th>
                                                    <th class="min-w-125px">Placement Unit <sup>(* KPI)</sup></th>
                                                    <th class="min-w-125px">Month Period</th>
                                                    <th class="min-w-125px">PUK</th>
                                                    <th class="min-w-125px">PUK Status</th>
                                                    <th class="min-w-50px text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <!--end::Table head-->
                                            <!--begin::Table body-->
                                            <tbody class="fw-bold text-gray-600">
                                            </tbody>
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Table container-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Approval Employee-->
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
                                                    <td class="text-center"><h5>Action</h5></td>
                                                </tr>
                                                <tr>
                                                    <td><h5>Applicant</h5></td>
                                                    <td colspan="4"></td>
                                                    <td class="text-center"><span id="applicant_submit_status"><span class="badge badge-secondary w-100">Not Submitted</span></span></td>
                                                    <td class="text-center">
                                                        <span id="applicant_submit_time"></span>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><h5>Approver</h5></td>
                                                    <td><h5>Unit</h5></td>
                                                    <td><h5>Position</h5></td>
                                                    <td><h5>Comments</h5></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tbody id="kt_table_target_submit_approval"></tbody>
                                            </tbody>
                                            <!--end::Table Target Submit body-->
                                        </table>
                                    </div>
                                    <!--end::Table Target Submit-->
                                    <!--begin::Modal - Submit Target-->
                                    <div class="modal fade" id="kt_modal_submit_target" tabindex="-1" aria-hidden="true">
                                        <!--begin::Modal dialog-->
                                        <div class="modal-dialog modal-dialog-centered mw-650px">
                                            <!--begin::Modal content-->
                                            <div class="modal-content">
                                                <!--begin::Modal header-->
                                                <div class="modal-header" id="kt_modal_submit_target_header">
                                                    <!--begin::Modal title-->
                                                    <h2 class="fw-bolder" id="kt_modal_submit_target_header_title">Submit Target</h2>
                                                    <!--end::Modal title-->
                                                    <!--begin::Close-->
                                                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-submit-target-modal-action="close">
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
                                                    <form id="kt_modal_submit_target_form" class="form">
                                                        <!--begin::Scroll-->
                                                        <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_submit_target_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_submit_target_header" data-kt-scroll-wrappers="#kt_modal_submit_target_scroll" data-kt-scroll-offset="300px">
                                                            <!--begin::Input group-->
                                                            <div class="fv-row mb-7">
                                                                <!--begin::Label-->
                                                                <label class="required fs-6 fw-bold form-label mb-2">Status</label>
                                                                <!--end::Label-->
                                                                <!--begin::Options-->
                                                                <div class="d-flex">
                                                                    <!--begin::Options-->
                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5">
                                                                        <input class="form-check-input" type="radio" name="status" value="2" />
                                                                        <span class="form-check-label">Approved</span>
                                                                    </label>
                                                                    <!--end::Options-->
                                                                    <!--begin::Options-->
                                                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <input class="form-check-input" type="radio" name="status" value="3" />
                                                                        <span class="form-check-label">Rejected</span>
                                                                    </label>
                                                                    <!--end::Options-->
                                                                </div>
                                                                <!--end::Options-->
                                                            </div>
                                                            <!--end::Input group-->
                                                            <!--begin::Input group-->
                                                            <div class="fv-row mb-15">
                                                                <!--begin::Label-->
                                                                <label class="fs-6 fw-bold form-label mb-2">
                                                                    <span class="required">Comment</span>
                                                                </label>
                                                                <!--end::Label-->
                                                                <!--begin::Input-->
                                                                <textarea class="form-control form-control-solid rounded-3" name="comment"></textarea>
                                                                <!--end::Input-->
                                                            </div>
                                                            <!--end::Input group-->
                                                        </div>
                                                        <!--end::Scroll-->
                                                        <!--begin::Actions-->
                                                        <div class="text-center pt-15">
                                                            <button type="reset" class="btn btn-light me-3" data-kt-submit-target-modal-action="cancel">Discard</button>
                                                            <button type="submit" class="btn btn-primary" data-kt-submit-target-modal-action="submit">
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
                                    <!--end::Modal - Submit Target-->
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