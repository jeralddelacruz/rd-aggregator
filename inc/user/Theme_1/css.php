<!-- Fontfaces CSS-->
<link href="/themes/css/font-face-<?php echo fontTheme(); ?>.css" rel="stylesheet" media="all">
<link href="/themes/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
<link href="/themes/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
<link href="/themes/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

<!-- Bootstrap CSS-->
<link href="/themes/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

<!-- Vendor CSS-->
<link href="/themes/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
<link href="/themes/vendor/wow/animate.css" rel="stylesheet" media="all">
<link href="/themes/vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
<link href="/themes/vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

<!-- Main CSS-->
<link href="/themes/css/Theme_1/theme_1.css?v=<?php echo rand(1, 1000); ?>" rel="stylesheet" media="all">

<!-- From original -->
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="/css/jquery-ui-timepicker-addon.min.css" />
<link rel="stylesheet" href="/css/spectrum.css" />
<link rel="stylesheet" href="/assets/css/pe-icon-7-stroke.css" />
<link rel="stylesheet" href="/js/fancybox/jquery.fancybox.css" />

<!-- Custom CSS-->
<!-- Custom Color Theme -->
<?php echo colorTheme(); ?>

<?php if (fontTheme() == 'raleway') : ?>
<style>
    body, h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6, p, .navbar, .brand, .btn-simple, .alert, a, .td-name, td, button.close { font-family: "Raleway", sans-serif; }
</style>
<?php elseif (fontTheme() == 'oswald') : ?>
<style>
    body, h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6, p, .navbar, .brand, .btn-simple, .alert, a, .td-name, td, button.close { font-family: "Oswald", sans-serif; }
</style>
<?php elseif (fontTheme() == 'josefin') : ?>
<style>
    body, h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6, p, .navbar, .brand, .btn-simple, .alert, a, .td-name, td, button.close { font-family: "Josefin Sans", sans-serif; }
</style>
<?php elseif (fontTheme() == 'livvic') : ?>
<style>
    body, h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6, p, .navbar, .brand, .btn-simple, .alert, a, .td-name, td, button.close { font-family: "Livvic", sans-serif; }
</style>
<?php elseif (fontTheme() == 'nunito') : ?>
<style>
    body, h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6, p, .navbar, .brand, .btn-simple, .alert, a, .td-name, td, button.close { font-family: "Nunito", sans-serif; }
</style>
<?php elseif (fontTheme() == 'teko') : ?>
<style>
    body, h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6, p, .navbar, .brand, .btn-simple, .alert, a, .td-name, td, button.close { font-family: "Teko", sans-serif; }
 
    .navbar-sidebar2 .navbar__list li a {
        font-size: 20px;
        padding: 10px 35px;
    }
</style>
<?php endif; ?>

<!-- Jquery JS-->
<script src="../themes/vendor/jquery-3.2.1.min.js"></script>
