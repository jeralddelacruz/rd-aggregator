<!DOCTYPE html>
<html lang="en">
<head>
    <!-- REQUIRED META TAGS-->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- TITLE PAGE -->
    <title><?= $index_title." &lsaquo; ".$WEBSITE["sitename"]." &ndash; ";?></title>

    <?php if($WEBSITE["icon"]): ?>
        <link rel="shortcut icon" href="../img/<?php echo $WEBSITE["icon"];?>" />
    <?php endif; ?>
    
    <?php include("css.php"); ?>

    <?php
        $selected_color = ($THEME_COLOR[0]['name'] == 'Primary') ? '#007BFF' : ( ($THEME_COLOR[0]['name'] == 'Secondary') ? '#6C757D' : 'inherit' );
    ?>
    
    <!-- Jquery JS-->
    <script src="../themes/vendor/jquery-3.2.1.min.js"></script>
</head>
<body class="animsition">
    <div class="page-wrapper">
        <!-- HEADER DESKTOP-->
        <?php include("topnav.php"); ?>
        <!-- END HEADER DESKTOP -->

        <!-- WELCOME-->
        <?php include("welcome.php"); ?>
        <!-- END WELCOME-->

        <!-- PAGE CONTENT-->
        <div class="page-container3">
            <section class="alert-wrap p-t-25 p-b-25">
                <div class="container">
                    <!-- ALERT-->
                    <?php include("notifications.php"); ?>
                    <!-- END ALERT-->
                </div>
            </section>

            <section>
                <div class="container">
                    <div class="row">
                        <div class="col-xl-3">
                            <!-- MENU SIDEBAR-->
                            <?php include("nav.php"); ?>
                            <!-- END MENU SIDEBAR-->
                        </div>

                        <div class="col-xl-9">
                            <!-- PAGE CONTENT-->
                            <div class="page-content">