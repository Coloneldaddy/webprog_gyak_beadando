<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title><?= $config['site_title'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <!-- BOOTSTRAP CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Saját stíluslap -->
  <link href="assets/style.css" rel="stylesheet">




</head>
<body>
<header>
    <h1><?= $config['site_title'] ?></h1>
    <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']['login'])): ?>
        <div class="user">
            Bejelentkezett:
            <?= htmlspecialchars($_SESSION['user']['vezeteknev']) ?>
            <?= htmlspecialchars($_SESSION['user']['keresztnev']) ?>
            (<?= htmlspecialchars($_SESSION['user']['login']) ?>)
        </div>
    <?php endif; ?>
</header>
