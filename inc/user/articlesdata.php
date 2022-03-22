<?php

    $articles = $DB->query("SELECT article_id, article_name, article_image, article_headline, article_subheadline, article_button_text  FROM {$dbprefix}articles WHERE campaigns_id = '{$_GET["campaigns_id"]}'");
?>