<div class="d-flex flex-column flex-root">
	<!--begin::Authentication - Sign-in -->
	<div class="d-flex flex-column flex-lg-row flex-column-fluid">
		<!--begin::Aside-->
		<div class="d-flex flex-column flex-lg-row-auto w-xl-600px positon-xl-relative" style="background: url(assets/media/bg-login.jpg); background-size: cover;">
			<!--begin::Wrapper-->
			<div class="d-flex flex-column position-xl-fixed top-0 bottom-0 w-xl-600px scroll-y">
				<!--begin::Content-->
				<div class="d-flex flex-row-fluid flex-column text-center p-20 pt-lg-20">
					<!--begin::Logo-->
					<a href="<?= site_url() ?>" class="py-9 mb-5">
						<img alt="Logo" src="assets/media/logo-blue.png" class="h-40px" />
					</a>
					<!--end::Logo-->
					
					<!--begin::PaDI-->
					<!-- <div class="d-flex justify-content-center align-items-center position-relative" style="height: 100%; width: 100%;">
						<a href="javascript:void(0)" class="position-absolute top-50 start-50 translate-middle">
							<img alt="PaDI" src="assets/media/PaDI.png" class="h-300px" />
						</a>
					</div> -->
					
					<div class="my-10">
						<img alt="PaDI" src="assets/media/PaDI.png" class="w-100"/>
					</div>
					<!--end::PaDI-->
					<!--begin::Title-->
					<!-- <h1 class="fw-bolder fs-2qx pb-5 pb-md-10" style="color: #986923;"></h1> -->
					<!--end::Title-->
					<!--begin::Description-->
					<p class="fw-bold fs-4" style="color: #986923; text-align: justify; background-color: rgba(255, 255, 255, 0.9); border-radius: 5px; padding: 20px; font-family: 'Montserrat', sans-serif;">
						Setiap pegawai saling bersinergi menerapkan ilmu Padi yang diimplementasikan didalam lingkungan kerja. Investasi yang diberikan Bank kepada pegawai dalam bentuk Pendidikan dan Pelatihan bertujuan meningkatkan kinerja Bank Sumut
					</p>
					<!--end::Description-->
				</div>
				<!--end::Content-->
				<!--begin::Illustration-->
				<!-- <div class="d-flex flex-row-auto bgi-no-repeat bgi-position-x-center bgi-size-contain bgi-position-y-bottom min-h-100px min-h-lg-350px" style="background-image: url(assets/media/illustrations/sketchy-1/13.png"></div> -->
				<!--end::Illustration-->
			</div>
			<!--end::Wrapper-->
		</div>
		<!--end::Aside-->
		<!--begin::Body-->
		<div class="d-flex flex-column flex-lg-row-fluid py-10">
			<!--begin::Content-->
			<div class="d-flex flex-center flex-column flex-column-fluid">
				<!--begin::Wrapper-->
				<div class="w-lg-500px p-10 p-lg-15 mx-auto">
					<!--begin::Form-->
					<form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" action="<?php echo base_url('login/check_credentials_emp'); ?>" method="post" onsubmit="return validateForm()">
						<!--begin::Heading-->
						<div class="mb-10">
							<!--begin::Title-->
							<h1 class="text-dark mb-3">PerforMe</h1>
							<!--end::Title-->
							<!--begin::Link-->
							<div class="text-gray-400 fw-bold fs-4">
								Login using HRIS username and password
								<?php if ($this->session->flashdata('error')): ?>
									<script>
										Swal.fire({
											text: "<?php echo $this->session->flashdata('error'); ?>",
											icon: "error",
											buttonsStyling: false,
											confirmButtonText: "Ok, got it!",
											customClass: {
												confirmButton: "btn btn-primary-bs"
											}
										});
									</script>
								<?php endif; ?>
								
								<?php if ($this->session->flashdata('success')): ?>
									<script>
										Swal.fire({
											text: 'You have successfully logged in!',
											icon: 'success',
											buttonsStyling: false,
											confirmButtonText: 'Ok, got it!',
											customClass: {
												confirmButton: 'btn btn-primary'
											}
										});
										setTimeout(function() {
											window.location = '<?php echo site_url('dashboard'); ?>';
										}, 2000);
									</script>
								<?php elseif ($this->session->userdata('is_logged_in')): ?>
									<script>
										window.location = '<?php echo site_url('dashboard'); ?>';
									</script>
								<?php endif; ?>
							</div>
							<!--end::Link-->
						</div>
						<!--begin::Heading-->
						<!--begin::Input group-->
						<div class="fv-row mb-10">
							<!--begin::Label-->
							<label class="form-label fs-6 fw-bolder text-dark">NPP</label>
							<!--end::Label-->
							<!--begin::Input-->
							<input class="form-control form-control-lg form-control-solid" type="text" name="nrik" placeholder="NPP (4 digit pertama)" value="<?php echo $this->session->flashdata('nrik'); ?>" autocomplete="off" />
							<?php if (validation_errors()): ?>
								<?php echo form_error('nrik', '<div class="error-message text-small text-danger" id="error-nrik">', '</div>'); ?>
							<?php endif; ?>
							<!--end::Input-->
						</div>
						<!--end::Input group-->
						<!--begin::Input group-->
						<div class="fv-row mb-10">
							<!--begin::Wrapper-->
							<div class="d-flex flex-stack mb-2">
								<!--begin::Label-->
								<label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
								<!--end::Label-->
								<!--begin::Link-->
								<!-- <a href="" class="link-primary fs-6 fw-bolder">Forgot Password ?</a> -->
								<!--end::Link-->
							</div>
							<!--end::Wrapper-->
							<!--begin::Input-->
							<input class="form-control form-control-lg form-control-solid" type="password" name="password" placeholder="Password" value="<?php echo $this->session->flashdata('password'); ?>" autocomplete="off" />
							<?php if (validation_errors()): ?>
								<?php echo form_error('password', '<div class="error-message text-small text-danger" id="error-password">', '</div>'); ?>
							<?php endif; ?>
							<!--end::Input-->
						</div>
						<!--end::Input group-->
						<!--begin::Actions-->
						<div class="text-center">
							<!--begin::Submit button-->
							<button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary-bs w-100 mb-5">
								<span class="indicator-label">Login</span>
								<span class="indicator-progress">Please wait...
								<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
							</button>
							<!--end::Submit button-->
							<!--begin::Separator-->
							<!-- <div class="text-center text-muted text-uppercase fw-bolder mb-5">or</div> -->
							<!--end::Separator-->
							<!--begin::Google link-->
							<!-- <a href="#" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
							<img alt="Logo" src="assets/media/svg/brand-logos/google-icon.svg" class="h-20px me-3" />Continue with Google</a> -->
							<!--end::Google link-->
							<!--begin::Google link-->
							<!-- <a href="#" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
							<img alt="Logo" src="assets/media/svg/brand-logos/facebook-4.svg" class="h-20px me-3" />Continue with Facebook</a> -->
							<!--end::Google link-->
							<!--begin::Google link-->
							<!-- <a href="#" class="btn btn-flex flex-center btn-light btn-lg w-100">
							<img alt="Logo" src="assets/media/svg/brand-logos/apple-black.svg" class="h-20px me-3" />Continue with Apple</a> -->
							<!--end::Google link-->
						</div>
						<!--end::Actions-->
					</form>
					<!--end::Form-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Content-->
		</div>
		<!--end::Body-->
	</div>
	<!--end::Authentication - Sign-in-->
</div>
