<header class="header-mobile d-block d-lg-none">
    <div class="header-mobile__bar">
        <div class="container-fluid">
            <div class="header-mobile-inner">
            <?php if($WEBSITE["logo"]) : ?>
                <a class="logo" href="index.php?cmd=home">
                    <img src="../img/<?php echo $WEBSITE["logo"];?>" />
                </a>
            <?php endif; ?>
                
                <button class="hamburger hamburger--slider" type="button">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <nav class="navbar-mobile">
        <div class="container-fluid">
            <ul class="navbar-mobile__list list-unstyled">
            <?php foreach($USER_MENU_ARR as $key=>$arr) : ?>

                <?php if(!(in_array($key,array_keys($USER_MENU))||in_array($key,$TAG_ARR))){continue;}?>

                <li<?php echo ($USER_CMD[$cmd][1]==$key)?" class=\"active\"":"";?>>
                    <a href="index.php?cmd=<?php echo $key;?>"><i class="<?php echo $arr[1];?>"></i> <?php echo $arr[0];?></a>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</header>
