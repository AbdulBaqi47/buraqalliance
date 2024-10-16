<!DOCTYPE html>

<html lang="en">

	<!-- begin::Head -->
	<head>
		<base href="../../">
		<meta charset="utf-8" />
		<title>Login | Kinglimousine</title>
		<meta name="description" content="We provide luxury transport. With a fleet of extravagant vehicles, professional chauffeur drivers, and impeccable service">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />

		
        <!--begin::Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

        <!--end::Fonts -->

        <!--begin::Page Custom Styles(used by this page) -->
        <link href="<?php echo e(asset('theme/css/pages/login/login-v2.css')); ?>" rel="stylesheet" type="text/css" />

        <!--end::Page Vendors Styles -->

        <!--begin::Global Theme Styles(used by all pages) -->
        <link href="<?php echo e(asset('theme/plugins/global/plugins.bundle.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('theme/css/style.bundle.css')); ?>" rel="stylesheet" type="text/css" />

        <!--end::Global Theme Styles -->

        <!--begin::Layout Skins(used by all pages) -->
        <link href="<?php echo e(asset('theme/css/skins/header/base/navy.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('theme/css/skins/header/menu/light.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('theme/css/skins/brand/navy.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('theme/css/skins/aside/light.css')); ?>" rel="stylesheet" type="text/css" />

        <!--end::Layout Skins -->
        <link rel="shortcut icon" href="<?php echo e(asset('media/logo/company-logo.png')); ?>" />
	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body class="kt-login-v2--enabled kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-aside--enabled kt-aside--fixed kt-page--loading">

		<!-- begin:: Page -->
		<div class="kt-grid kt-grid--ver kt-grid--root">
			<div class="kt-grid__item   kt-grid__item--fluid kt-grid  kt-grid kt-grid--hor kt-login-v2" id="kt_login_v2">

				<!--begin::Item-->
				<div class="kt-grid__item  kt-grid--hor">

					<!--begin::Heade-->
					<div class="kt-login-v2__head">
						<div class="kt-login-v2__logo">
							<a href="#" class="text-dark h5 mb-0">
								<img src="<?php echo e(asset('media/logo/company-logo.png')); ?>" alt="" class="w-25" />

							</a>
						</div>
					</div>

					<!--begin::Head-->
				</div>

				<!--end::Item-->

				<!--begin::Item-->
				<div class="kt-grid__item  kt-grid  kt-grid--ver  kt-grid__item--fluid">

					<!--begin::Body-->
					<div class="kt-login-v2__body">

						<!--begin::Wrapper-->
						<div class="kt-login-v2__wrapper">
							<div class="kt-login-v2__container">
								<div class="kt-login-v2__title">
									<h3>Sign to Account</h3>
								</div>

								<!--begin::Form-->
								<form class="kt-login-v2__form kt-form" method="POST" action="<?php echo e(route('central.admin.login')); ?>" autocomplete="off">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group">
                                        <input class="form-control" type="email" placeholder="Email" name="email" required autocomplete="email" autofocus>
                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
									</div>
									<div class="form-group">
                                        <input class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" type="password" placeholder="Password" name="password" required autocomplete="current-password">
                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong><?php echo e($message); ?></strong>
                                            </span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
									</div>

									<!--begin::Action-->
									<div class="kt-login-v2__actions">
                                        <?php if(Route::has('password.request')): ?>
                                            <a href="<?php echo e(route('password.request')); ?>" class="kt-link kt-link--brand">
                                                Forgot Password ?
                                            </a>
                                        <?php endif; ?>

										<button type="submit" class="btn btn-brand btn-elevate btn-pill">Sign In</button>
									</div>

									<!--end::Action-->
								</form>

								<!--end::Form-->
							</div>
						</div>

						<!--end::Wrapper-->

						<!--begin::Image-->
						<div class="kt-login-v2__image">
							<img src="<?php echo e(asset('theme/media/misc/bg_icon.svg')); ?>" alt="">
						</div>

						<!--begin::Image-->
					</div>

					<!--begin::Body-->
				</div>

				<!--end::Item-->

			</div>
		</div>

		<!-- end:: Page -->

		<!-- begin::Global Config(global config for global JS sciprts) -->
		<script>
			var KTAppOptions = {
				"colors": {
					"state": {
						"brand": "#5d78ff",
						"metal": "#c4c5d6",
						"light": "#ffffff",
						"accent": "#00c5dc",
						"primary": "#5867dd",
						"success": "#34bfa3",
						"info": "#36a3f7",
						"warning": "#ffb822",
						"danger": "#fd3995",
						"focus": "#9816f4"
					},
					"base": {
						"label": [
							"#c5cbe3",
							"#a1a8c3",
							"#3d4465",
							"#3e4466"
						],
						"shape": [
							"#f0f3ff",
							"#d9dffa",
							"#afb4d4",
							"#646c9a"
						]
					}
				}
			};
		</script>

		<!-- end::Global Config -->

		<!--begin::Global Theme Bundle(used by all pages) -->
		<script src="<?php echo e(asset('theme/plugins/global/plugins.bundle.js')); ?>" type="text/javascript"></script>
        <script src="<?php echo e(asset('theme/js/scripts.bundle.js')); ?>" type="text/javascript"></script>

		<!--end::Global Theme Bundle -->

		<!--begin::Page Scripts(used by this page) -->
		<script src="<?php echo e(asset('theme/js/pages/custom/user/login.js')); ?>" type="text/javascript"></script>

		<!--end::Page Scripts -->
	</body>

	<!-- end::Body -->
</html>
<?php /**PATH C:\Users\DELL 5300\Documents\buraqallience\resources\views/Central/auth/login.blade.php ENDPATH**/ ?>