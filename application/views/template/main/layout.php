<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
		<base href="<?= base_url() ?>">
    	<title>PerforMe <?= isset($title) ? ' - ' . $title : '' ?></title>
		<meta name="description" content="PerforMe is a performance management application specifically designed for Bank Sumut. This application helps in monitoring, measuring, and improving employee performance effectively and efficiently." />
		<meta name="keywords" content="PerforMe, performance management, Bank Sumut, employee performance, performance monitoring, performance measurement, performance improvement, HR management, employee evaluation, KPI tracking, performance analytics, employee productivity, performance dashboard" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta charset="utf-8" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="PerforMe <?= isset($title) ? ' - ' . $title : '' ?>" />
		<meta property="og:url" content="https://www.banksumut.co.id" />
		<meta property="og:site_name" content="PerforMe" />
		<link rel="canonical" href="https://www.banksumut.co.id" />
		<link rel="shortcut icon" href="assets/media/logo-blue-mini.png" />
		<!--begin::Fonts-->
		<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" /> -->
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(used by all pages)-->
		<link href="assets/vendors/metronic-admin/dist/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="assets/vendors/metronic-admin/dist/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/custom.css" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
		<script>
			var siteUrl = "<?= site_url(); ?>";
		</script>
		<!--begin::Global Javascript Bundle(used by all pages)-->
		<script src="assets/vendors/metronic-admin/dist/assets/plugins/global/plugins.bundle.js"></script>
		<script src="assets/vendors/metronic-admin/dist/assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="bg-body">
		<!--begin::Main-->
		<?php echo isset($body) ? $body : 'Page Not Found'; ?>
		<!--end::Main-->
		<!--begin::Javascript-->
		<!--begin::Page Vendors Javascript(used by this page)-->
		<script src="assets/vendors/lodash/lodash.min.js"></script>
		<!--end::Page Vendors Javascript-->
		<!--begin::Page Custom Javascript(used by this page)-->
		<?php if (isset($js) && is_array($js)): ?>
			<?php foreach ($js as $script): ?>
				<script src="<?= base_url('assets/') ?>js/<?= $script ?>"></script>
			<?php endforeach; ?>
		<?php endif; ?>
		<!--end::Page Custom Javascript-->
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>