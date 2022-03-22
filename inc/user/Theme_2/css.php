 <!-- Fontfaces CSS-->
<link href="/themes/css/font-face-<?php echo fontTheme(); ?>.css" rel="stylesheet" media="all">
<link href="/themes/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
<link href="/themes/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
<link href="/themes/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

<!-- Bootstrap CSS-->
<link href="/themes/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

<!-- Vendor CSS-->
<link href="/themes/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
<link href="/themes/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
<link href="/themes/vendor/wow/animate.css" rel="stylesheet" media="all">
<link href="/themes/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
<link href="/themes/vendor/slick/slick.css" rel="stylesheet" media="all">
<link href="/themes/vendor/select2/select2.min.css" rel="stylesheet" media="all">
<link href="/themes/vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
<link href="/themes/vendor/vector-map/jqvmap.min.css" rel="stylesheet" media="all">

<!-- Main CSS-->
<link href="/themes/css/theme.css?v=<?php echo rand(1, 1000); ?>" rel="stylesheet" media="all">

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="/css/jquery-ui-timepicker-addon.min.css" />
<link rel="stylesheet" href="/css/spectrum.css" />
<link rel="stylesheet" href="/assets/css/pe-icon-7-stroke.css" />
<link rel="stylesheet" href="/js/fancybox/jquery.fancybox.css" />

<!-- Custom Color Theme -->
<?php echo colorTheme(); ?>

<?php if (fontTheme() == 'raleway') : ?>
<style>
	body { font-family: "Raleway", sans-serif; }
</style>
<?php elseif (fontTheme() == 'oswald') : ?>
<style>
	body { font-family: "Oswald", sans-serif; }
</style>
<?php elseif (fontTheme() == 'josefin') : ?>
<style>
	body { font-family: "Josefin Sans", sans-serif; }
</style>
<?php elseif (fontTheme() == 'livvic') : ?>
<style>
	body { font-family: "Livvic", sans-serif; }
</style>
<?php elseif (fontTheme() == 'nunito') : ?>
<style>
	body { font-family: "Nunito", sans-serif; }
</style>
<?php elseif (fontTheme() == 'notoserif') : ?>
<style>
	body { font-family: "Noto Serif", sans-serif; }
</style>
<?php elseif (fontTheme() == 'bellota') : ?>
<style>
	body { font-family: "Bellota", sans-serif; }
</style>
<?php elseif (fontTheme() == 'teko') : ?>
<style>
	body { font-family: "Teko", sans-serif; }
 
	.navbar-sidebar2 .navbar__list li a {
		font-size: 20px;
		padding: 10px 35px;
	}
</style>
<?php endif; ?>

<!-- FOR WELCOME WEBSITE AT home.php -->
<style type="text/css">
	@media screen and (max-width: 768px){
		.website-welcome iframe{
			width: 100% !important;
		}
	}

	@media screen and (max-width: 1024px){
		.website-welcome iframe{
			width: 100% !important;
		}
	}
</style>

<!-- Jquery JS-->
<script src="/themes/vendor/jquery-3.2.1.min.js"></script>