<aside class="menu-sidebar d-none d-lg-block">
    <div class="logo">
        <?php if($WEBSITE["logo"]) : ?>
            <a href="index.php?cmd=home">
                <img src="../img/<?php echo $WEBSITE["logo"];?>" />
            </a>
        <?php endif; ?>
    </div>
    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul class="list-unstyled navbar__list">
                <?php foreach($USER_MENU_ARR as $key=>$arr):?>
                    <?php if(!(in_array($key,array_keys($USER_MENU))||in_array($key,$TAG_ARR))){continue;} ?>
                    <li<?php echo ($USER_CMD[$cmd][1]==$key)?" class=\"active\"":"";?>>
                        <a href="index.php?cmd=<?php echo $key;?>"><i class="<?php echo $arr[1];?>"></i> <?php echo $arr[0];?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>