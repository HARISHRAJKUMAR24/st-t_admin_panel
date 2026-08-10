<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>TourVault · Admin</title>

<!-- Favicon from database -->
<?php
$favicon = getData('favicon', 'settings', 'id = 1');
if (!empty($favicon)):
?>
    <link rel="icon" type="image/x-icon" href="<?= APP_URL . $favicon ?>">
    <link rel="shortcut icon" href="<?= APP_URL . $favicon ?>">
<?php else: ?>
    <!-- Default favicon if none in database -->
    <link rel="icon" type="image/x-icon" href="<?= APP_URL ?>favicon.ico">
<?php endif; ?>

<!-- Bootstrap 5 + Icons + Google Font -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap"
    rel="stylesheet" />
<link rel="stylesheet" href="<?= APP_URL; ?>assets/css/styles.css">