
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="<?= THEME_ROOT ?>/img/icons/icon-48x48.png" />

    <link rel="canonical" href="https://demo-basic.adminkit.io/pages-sign-in.html" />

    <title><?=  $pageTitle . " : ". APP_NAME ?></title>

    <link href="<?= THEME_ROOT ?>/css/app.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet" />
    <?php
        if (isset($header_styles)):
            foreach ($header_styles as $header_style):
                echo "<link href='$header_style' rel='stylesheet' type='text/css' />";
            endforeach;
        endif;
        if (isset($header_scripts)):
            foreach ($header_scripts as $script):
                echo "<script src='$script'></script>";
            endforeach;
        endif;
    ?>
</head>

<body>
<main class="d-flex w-100">
    <?= $this->section("content") ?>
</main>

<script src="<?= THEME_ROOT ?>/js/app.js"></script>

<?php
    if (isset($footer_scripts)):
        foreach ($footer_scripts as $script):
            echo "<script src='$script'></script>";
        endforeach;
    endif;
?>


</body>

</html>