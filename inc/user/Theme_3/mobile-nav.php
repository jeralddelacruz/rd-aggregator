<div id="theme-3-mobile-nav">
    <div class="header-button-item js-item-menu">
        <i class="zmdi zmdi-menu"></i>
        <div class="notifi-dropdown js-dropdown">
            <div class="notifi__title">
                <p>Menu</p>
            </div>
            <ul>
                <?php foreach($USER_MENU_ARR as $key=>$arr): ?>
                    <?php if(!(in_array($key,array_keys($USER_MENU))||in_array($key,$TAG_ARR))): ?>
                        <?php continue; ?>
                    <?php endif ?>
                    <li <?= ($USER_CMD[$cmd][1]==$key)?" class=\"active\"":"";?>>
                        <a href="index.php?cmd=<?= $key;?>">
                            <i class="<?= $arr[1] ?>"></i>
                            <?= $arr[0] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>