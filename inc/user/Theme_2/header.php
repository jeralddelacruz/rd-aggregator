<?php
    $user = $DB->info("user", "user_id = '$UserID'");
    $userCampaign = $DB->info("campaigns", "user_id = '$UserID'");
    $campaignID = $userCampaign['campaigns_id'];
    // exit;
    // add/news.php?campaigns_id=23
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('meta.php'); ?>
    
    <?php include('css.php'); ?>
</head>
<body class="animsition">
    <div class="page-wrapper">
        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar2">
            <div class="logo">
                <a href="#">
                    <img src="../img/<?php echo $WEBSITE["logo"];?>" alt="<?php echo $WEBSITE['site_name']; ?>" class="img-logo" />
                </a>
                <i id="sidebar__menuBtn" onclick="toggleNav()" class="fa fa-bars"></i>
            </div>
            <div id="menu-sidebar2__content" class="menu-sidebar2__content js-scrollbar1">
                <div class="account2">
                    <div class="image img-cir img-m-120">
                        <img src="/themes/images/icon/avatar-big-01.jpg" alt="<?php echo $_SESSION["user_name"]; ?>" />
                    </div>
                    <h4 class="name"><?php echo $_SESSION["user_name"]; ?></h4>
                    <a href="./"> <i class="fa fa-sign-out"></i> &nbsp;Sign out</a>
                </div>
                
                <?php include('sidenav.php'); ?>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div id="page-container2" class="page-container2">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop2">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap2">
                            <!--<div class="logo d-block d-lg-none">-->
                            <!--    <a href="#">-->
                            <!--        <img src="../img/<?php //echo $WEBSITE["logo"];?>" alt="<?php //echo $WEBSITE['site_name']; ?>" class="img-logo" />-->
                            <!--    </a>-->
                            <!--</div>-->
                            <div class="header-button mr-auto">
                                <h3>Welcome to <?php echo $WEBSITE['sitename']; ?>!</h3>
                            </div>
                            <div class="header-button2">
                                <!--<a href="https://dummy.newsmaximizer.com/add/news.php?campaigns_id=<?= $campaignID ?>" class="btn btn-primary mr-4" target="_blank"><i class="fa fa-eye"></i> View Your Site</a>-->
                                <div class="header-button-item js-item-menu" id="mes_body">
                                    
                                </div>
                                <div id="profile_settings" class="header-button-item mr-0 js-sidebar-btn">
                                    <div class="pr-3 bd-highlight"><img class="header-img" src="/themes/images/icon/avatar-big-01.jpg" alt="<?php echo $_SESSION["user_name"]; ?>" /></div>
                                    <div class="bd-highlight"><i class="fa fa-gear"></i></div>
                                </div>
                                <?php include('nav.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <aside class="menu-sidebar2 js-right-sidebar d-block d-lg-none">
                <div class="logo">
                    <a href="#">
                        <img src="../img/<?php echo $WEBSITE["logo"];?>" alt="<?php echo $WEBSITE['site_name']; ?>" class="img-logo" />
                    </a>
                </div>
                <div class="menu-sidebar2__content js-scrollbar2">
                    <div class="account2">
                        <div class="image img-cir img-m-120">
                            <img src="/themes/images/icon/avatar-big-01.jpg" alt="<?php echo $_SESSION["user_name"]; ?>" />
                        </div>
                        <h4 class="name"><?php echo $_SESSION["user_name"]; ?></h4>
                        <a href="./">Sign out</a>
                    </div>
                    
                    <?php //include('nav.php'); ?>
                    <?php include('sidenav.php'); ?>
                </div>
            </aside>
            <!-- END HEADER DESKTOP-->

            <script>
                function toggleNav() {

                    if(document.getElementById("menu-sidebar2__content").style.marginLeft == "-300px") {
                        document.getElementById("menu-sidebar2__content").style.width = "300px";
                        document.getElementById("menu-sidebar2__content").style.marginLeft = "0";
                        document.getElementById("page-container2").style.paddingLeft = "300px";
                    } else {
                        document.getElementById("menu-sidebar2__content").style.width = "0";
                        document.getElementById("menu-sidebar2__content").style.marginLeft = "-300px";
                        document.getElementById("page-container2").style.paddingLeft = "0";
                    }
                }
            </script>

            <section class="p-t-10">
                <div class="container-fluid">
