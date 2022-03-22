<header class="header-desktop4">
    <div class="container">
        <div class="header4-wrap">
            <div class="header__logo">
                <a href="#">
                    <img src="../img/<?= $WEBSITE["logo"] ?>" class="img-responsive"/>
                </a>
            </div>
            <div class="header__tool">
                
                <div class="header-button-item has-noti js-item-menu">
                    <i class="zmdi zmdi-notifications"></i>
                    <div class="notifi-dropdown js-dropdown">
                        <div class="notifi__title">
                            <p>You have <span id="mes_num"></span> unread notifications</p>
                        </div>

                        <div id="theme-3-notifcations">
                            <?php
                                $res = $DB->query("select m.*,(select count(mesview_id) from $dbprefix"."mesview v where v.mes_id=m.mes_id and v.user_id='$UserID') as view from $dbprefix"."mes m order by mes_rd desc limit 0, 5");
                            ?>
                            <?php if(sizeof($res)): ?>
                                <?php foreach($res as $row): ?>
                                    <div class="notifi__item" style="background:<?= $row['view'] ? '#fff' : '#DEECFF' ; ?>">
                                        <div class="bg-c1 img-cir img-40" style="background:<?= $row['view'] ? (($_GET["id"] == $row['mes_id']) ? 'red' : '#919090') : (($_GET["id"] == $row['mes_id']) ? 'red' : '#00AD5F'); ?>">
                                            <?= $row['view'] ? '<i class="zmdi zmdi-email-open"></i>' : '<i class="zmdi zmdi-email"></i>';?>
                                        </div>
                                        <div class="content">
                                            <p class="notifcation-title">
                                                <a href="index.php?cmd=mesview&id=<?= $row["mes_id"] ?>">
                                                    <?= $row["mes_title"] ?>
                                                </a>
                                            </p>
                                            <span class="date"><?= date("Y-m-d H:i:s", $row["mes_rd"]) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="notifi__footer">
                            <a href="index.php?cmd=mes">All notifications</a>
                        </div>
                    </div>
                </div>

                <div class="header-button-item js-item-menu">
                    <i class="zmdi zmdi-settings"></i>
                    <div class="setting-dropdown js-dropdown">
                        <div class="account-dropdown__body">
                            <div class="account-dropdown__item">
                                <a href="index.php?cmd=eswipes">
                                    <i class="zmdi zmdi-email"></i>Email Swipes</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="account-wrap">
                    <div class="account-item account-item--style2 clearfix js-item-menu">
                        <div class="image">
                            <img src="<?= ($cur_user["user_avatar"]) ? "../upload/avatar/" . $cur_user["user_avatar"] : "../themes/images/icon/avatar.gif"?>" alt="<?= $cur_user["user_fname"] ?> <?= $cur_user["user_lname"] ?>" />
                        </div>
                        <div class="content">
                            <a class="js-acc-btn" href="#"><?= $cur_user["user_fname"] ?> <?= $cur_user["user_lname"] ?></a>
                        </div>
                        <div class="account-dropdown js-dropdown">
                            <div class="info clearfix">
                                <div class="image">
                                    <a href="#">
                                        <img src="<?= ($cur_user["user_avatar"]) ? "../upload/avatar/" . $cur_user["user_avatar"] : "../themes/images/icon/avatar.gif"?>" alt="<?= $cur_user["user_fname"] ?> <?= $cur_user["user_lname"] ?>" />
                                    </a>
                                </div>
                                <div class="content">
                                    <h5 class="name">
                                        <a href="#"><?= $cur_user["user_fname"] ?> <?= $cur_user["user_lname"] ?></a>
                                    </h5>
                                    <span class="email"><?= $cur_user["user_email"] ?></span>
                                </div>
                            </div>

                            <div class="account-dropdown__body">
                                <div class="account-dropdown__item">
                                    <a href="index.php?cmd=info">
                                        <i class="zmdi zmdi-account"></i>Account</a>
                                </div>
                            </div>
                            <div class="account-dropdown__footer">
                                <a href="./">
                                    <i class="zmdi zmdi-power"></i>Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MOBILE NAV -->
                <?php include("mobile-nav.php"); ?>
                <!-- END MOBILE NAV -->
            </div>
        </div>
    </div>
</header>