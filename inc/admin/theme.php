<?php
    $themes = $DB->query("SELECT * FROM $dbprefix" . "themes ORDER BY name ASC");
    $colors = $DB->query("SELECT * FROM $dbprefix" . "colors ORDER BY name ASC");
    $fonts = $DB->query("SELECT * FROM $dbprefix" . "fonts ORDER BY name ASC");
?>

<?php

    if (isset($_POST['submit'])) {
        $theme = strip($_POST['theme'], 0);
        $font = strip($_POST['font'], 0);
        $color = strip($_POST['color'], 0);

        $DB->query("UPDATE $dbprefix" . "themes SET selected = 0");
        $DB->query("UPDATE $dbprefix" . "themes SET selected = 1 WHERE id = '$theme' ");

        $DB->query("UPDATE $dbprefix" . "fonts SET selected = 0");
        $DB->query("UPDATE $dbprefix" . "fonts SET selected = 1 WHERE id = '$font' ");

        $DB->query("UPDATE $dbprefix" . "colors SET selected = 0");
        $DB->query("UPDATE $dbprefix" . "colors SET selected = 1 WHERE id = '$color' ");

    	redirect("index.php?cmd=theme&ok=1");
    }
?>

<h2><?php echo $index_title;?> Settings</h2>
<p><code>NOTE: When "Old Theme is selected fonts and color will not work."</code></p>

<?php if ($_GET["ok"]) : ?>
<div class="ok">Theme Settings <strong>saved</strong>.</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" action="index.php?cmd=theme">
    <table class="tbl_form">
        <tr>
            <th>Theme</th>
            <td>
                <select name="theme" id="" class="text_l">
                    <option value="">Select Theme</option>
                    <?php foreach ($themes as $theme) : ?>
                    <option value="<?=$theme['id']?>" <?php echo ($theme['selected'] ? 'selected' : ''); ?>><?=$theme['name']?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Font</th>
            <td>
                <select name="font" id="" class="text_l">
                    <option value="">Select Font</option>
                    <?php foreach ($fonts as $font) : ?>
                    <option value="<?=$font['id']?>" <?php echo ($font['selected'] ? 'selected' : ''); ?>><?=$font['name']?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Color</th>
            <td>
                <select name="color" id="" class="text_l">
                    <option value="">Select Color</option>
                    <?php foreach ($colors as $color) : ?>
                    <option value="<?=$color['id']?>" <?php echo ($color['selected'] ? 'selected' : ''); ?>><?=$color['name']?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    
    <div class="submit"><input type="submit" name="submit" value="Save Changes" class="button" /></div>
</form>