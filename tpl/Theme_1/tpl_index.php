<?php include('inc/user/Theme_1/login-header.php'); ?>

                    <div class="login-wrap p-0">
                        <div class="login-content">
                            <div class="login-logo">
                                <a href="/">
                                <?php if ($WEBSITE["logo"]) : ?>
                                    <img src="../img/<?php echo $WEBSITE["logo"];?>" class="img-responsive" style="margin:0 auto;float:none;" />
                                <?php endif; ?>
                                </a>
                            </div>
                            <div class="login-form">
                                <div class="social-login-content">
                                    <div class="social-button">
                                        <a href="<?php echo $WEBSITE["sales_page_link"];?>" class="au-btn au-btn--block au-btn--blue m-b-20 text-center">Not yet a member?</a>
                                        <a href="<?php echo $USERDIR; ?>" class="au-btn au-btn--block au-btn--blue2 btn-fill text-center">Already a member? Sign in here</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

<?php include('inc/user/Theme_1/login-footer.php'); ?>