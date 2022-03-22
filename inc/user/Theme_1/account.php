<div class="account-wrap">
    <div class="account-item clearfix js-item-menu">
        <div class="image">
            <img src="<?php echo ($cur_user["user_avatar"]) ? "../upload/avatar/" . $cur_user["user_avatar"] : "../themes/images/icon/avatar.gif"?>" />
        </div>
        <div class="content">
            Welcome back, <a class="js-acc-btn" href="#"><?php echo $cur_user["user_fname"];?></a>
        </div>
        <div class="account-dropdown js-dropdown">
            <div class="info clearfix">
                <div class="image">
                    <a href="#">
                        <img src="<?php echo ($cur_user["user_avatar"]) ? "../upload/avatar/" . $cur_user["user_avatar"] : "../themes/images/icon/avatar.gif"?>" />
                    </a>
                </div>
                <div class="content">
                    <h5 class="name">
                        <a href="#"><?php echo $cur_user["user_fname"] . " " . $cur_user["user_lname"];?></a>
                    </h5>
                    <span class="email"><?php echo $cur_user["user_email"];?></span>
                </div>
            </div>
            <div class="account-dropdown__body">
                <div class="account-dropdown__item">
                    <a href="index.php?cmd=info">
                        <i class="zmdi zmdi-account"></i>Account</a>
                    <a href="./">
                        <i class="zmdi zmdi-power"></i>Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>