<nav class="navbar-sidebar2">
    <ul class="list-unstyled navbar__list">
    
        <?php foreach($USER_MENU_ARR as $key => $arr) : ?>
            <?php if (!(in_array($key,array_keys($USER_MENU)) || in_array($key,$TAG_ARR))){ continue; } ?>

            <?php if ($arr[2] == 'false' || $arr[4] !== ''){ continue; } ?>

            <li class="nav-item <?php echo ($USER_CMD[$cmd][1] == $key) ? 'active' : ''?>">
                <?php if ($arr[5] == true): ?>
                    <a data-toggle="collapse" href="#<?= $key; ?>" aria-expanded="true" class="nav-link">
                <?php else: ?>
                    <a href="index.php?cmd=<?=$key;?>">
                <?php endif; ?>

                    <i class="<?=$arr[1];?>"></i>
                    <?=$arr[0];?>

                    <?php if ($arr[5] == true): ?>
                    <i class="fa fa-angle-down"></i>
                    <?php endif; ?>
                </a>
                <?php if ($arr[5] == true): ?>
                    <div class="collapse" id="<?= $key; ?>">
                        <ul class="nav">
                            <?php foreach($USER_MENU_ARR as $k => $userMenu) : ?>
                                <?php if ($key == $userMenu[4]): ?>
                                <li class="nav-item ">
                                    <a class="nav-link" href="index.php?cmd=<?= $k; ?>">
                                        <i class="fa fa-pencil-square-o" style="opacity: 0;"></i>
                                        <?=$userMenu[0];?>
                                    </a>
                                </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>