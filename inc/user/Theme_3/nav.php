<div id="theme-3-sidebar">
    <aside class="menu-sidebar3 js-spe-sidebar">
        <nav class="navbar-sidebar2 navbar-sidebar3">
            <ul class="list-unstyled navbar__list">
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
        </nav>
    </aside>
</div>