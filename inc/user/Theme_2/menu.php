<?php foreach($USER_MENU_ARR as $key => $arr) : ?>
    <?php if (!(in_array($key,array_keys($USER_MENU)) || in_array($key,$TAG_ARR))){ continue; } ?>

    <li <?php echo ($USER_CMD[$cmd][1] == $key) ? " class=\"active\"" : "";?>>
        <a href="index.php?cmd=<?=$key;?>"><i class="<?=$arr[1];?>"></i><p><?=$arr[0];?></p></a>
    </li>
<?php endforeach; ?>