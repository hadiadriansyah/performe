<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Authentication - Error 403 -->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div class="d-flex flex-column flex-column-fluid text-center p-10 py-lg-15">
            <!--begin::Logo-->
            <a href="<?= site_url(); ?>" class="mb-10 pt-lg-10">
                <img alt="Logo" src="assets/media/logo-blue.png" class="h-40px mb-5" />
            </a>
            <!--end::Logo-->
            <!--begin::Wrapper-->
            <div class="pt-lg-10 mb-10">
                <!--begin::Logo-->
                <h1 class="fw-bolder fs-2qx text-gray-800 mb-10">Sorry</h1>
                <!--end::Logo-->
                <!--begin::Message-->
                <div class="fw-bold fs-3 text-muted mb-15">You don't have permission to access this page.</div>
                <!--end::Message-->
                <!--begin::Action-->
                <div class="text-center">
                    <a href="javascript:history.back()" class="btn btn-lg btn-primary-bs fw-bolder">Back to previous page</a>
                </div>
                <!--end::Action-->
            </div>
            <!--end::Wrapper-->
            <!--begin::Illustration-->
            <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-100px min-h-lg-350px" style="background-image: url(assets/vendors/metronic-admin/dist/assets/media/illustrations/sketchy-1/17.png"></div>
            <!--end::Illustration-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Authentication - Error 403-->
</div>
<!--end::Main-->