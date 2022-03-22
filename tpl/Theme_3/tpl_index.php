<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        
        <title><?php echo $index_title." &lsaquo; ".$WEBSITE["sitename"]." &ndash; ";?>Member CP</title>
        
        <?php if ($WEBSITE["icon"]) : ?>
        <link rel="shortcut icon" href="../img/<?php echo $WEBSITE["icon"];?>" />
        <?php endif; ?>
        
        <?php include_once('inc/user/Theme_3/css.php'); ?>
    </head>
    <body class="animsition">
        <div class="page-wrapper">
            <div class="page-content--bge5">
                <div class="container h-100 d-flex align-items-center">
                    <div class="login-wrap p-0">
                        <div class="login-content">
                            <div class="login-logo">
                                <a href="#">
                                <?php if ($WEBSITE["logo"]) : ?>
                                    <img src="../img/<?php echo $WEBSITE["logo"];?>" class="img-responsive" style="margin:0 auto;float:none;" />
                                <?php endif; ?>
                                </a>
                            </div>
                            <div class="login-form">
                                <div class="social-login-content">
                                    <div class="social-button">
                                        <a href="<?php echo $WEBSITE["sales_page_link"];?>" class="au-btn au-btn--block au-btn--blue m-b-20 text-center">Not yet a member?</a>
                                        <a href="<?php echo $USERDIR; ?>" class="au-btn au-btn--block au-btn--blue2 text-center">Already a member? Sign in here</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php include_once('inc/user/Theme_2/scripts.php'); ?>
    </body>
</html>