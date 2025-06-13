<?php
// controllers/car.php




$car_id = intval($_GET['id'] ?? 0);
if ($car_id <= 0) {
    echo '<div class="alert alert-danger">Hibás autóazonosító!</div>';
    return;
}

$stmt = $dbh->prepare("
    SELECT c.*, u.login
    FROM cars c
    JOIN users u ON c.user_id = u.id
    WHERE c.id = ?
");
$stmt->execute([$car_id]);
$auto = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$auto) {
    echo '<div class="alert alert-danger">Ez az autó nem létezik!</div>';
    return;
}

$stmt2 = $dbh->prepare("
    SELECT * FROM car_images
    WHERE car_id = ?
    ORDER BY id
");
$stmt2->execute([$car_id]);
$kepek = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mt-4">
  <div class="row">
    <div class="col-lg-7">
  <?php if (count($kepek) > 0): ?>
    <div id="carCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
      <div class="carousel-inner">
        <?php foreach ($kepek as $i => $kep): ?>
          <div class="carousel-item<?= $i === 0 ? ' active' : '' ?>">
            <img src="uploads/autos/<?= htmlspecialchars($kep['filename']) ?>"
                 class="d-block w-100"
                 style="object-fit:contain; max-height:500px;">
          </div>
        <?php endforeach; ?>
      </div>
      <!-- Indikátorok (kis pontok) -->
      <div class="carousel-indicators">
        <?php foreach ($kepek as $i => $kep): ?>
          <button type="button" data-bs-target="#carCarousel" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Kép <?= $i+1 ?>"></button>
        <?php endforeach; ?>
      </div>
      <!-- Vezérlő nyilak -->
      <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Előző</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Következő</span>
      </button>
    </div>
  <?php endif; ?>
</div>


    <div class="col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title"><?= htmlspecialchars($auto['title']) ?></h3>
          <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item">
              <strong>Ár:</strong> <?= number_format($auto['price'],0,'',' ') ?> Ft
            </li>
            <li class="list-group-item">
              <strong>Évjárat:</strong> <?= htmlspecialchars($auto['year']) ?>
            </li>
            <li class="list-group-item">
              <strong>Feltöltő:</strong> <?= htmlspecialchars($auto['login']) ?>
            </li>
          </ul>
          <p><?= nl2br(htmlspecialchars($auto['description'])) ?></p>

          <?php if (isset($_SESSION['user']['id']) &&
                    $_SESSION['user']['id'] == $auto['user_id']): ?>
            <a href="index.php?page=edit_car&id=<?= $auto['id'] ?>"
               class="btn btn-warning me-2">Szerkesztés</a>
            <a href="index.php?page=delete_car&id=<?= $auto['id'] ?>"
               class="btn btn-danger"
               onclick="return confirm('Biztos törlöd ezt az autót?');">
               Törlés
            </a>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>
