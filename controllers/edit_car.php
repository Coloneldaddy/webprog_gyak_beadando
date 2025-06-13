<?php
// controllers/edit_car.php
$hiba    = '';
$success = '';

// 1) Autó betöltése és jogosultság ellenőrzés
$id    = intval($_GET['id'] ?? 0);
$stmt  = $dbh->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$auto  = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$auto || $auto['user_id'] !== $_SESSION['user']['id']) {
    echo '<div class="alert alert-danger">Nincs jogosultságod szerkeszteni ezt az autót.</div>';
    return;
}

// 2) Kép törlése (GET param)
if (isset($_GET['delete_image'])) {
    $img_id = intval($_GET['delete_image']);
    // Lekérdezzük az adott képet, és ellenőrizzük, hogy ehhez az autóhoz tartozik
    $s = $dbh->prepare("SELECT filename FROM car_images WHERE id = ? AND car_id = ?");
    $s->execute([$img_id, $id]);
    if ($row = $s->fetch(PDO::FETCH_ASSOC)) {
        @unlink(__DIR__ . "/../uploads/autos/{$row['filename']}");
        $dbh->prepare("DELETE FROM car_images WHERE id = ?")->execute([$img_id]);
    }
    // PRG: vissza a szerkesztő oldalra
    header("Location: index.php?page=edit_car&id={$id}&deleted=1");
    exit;
}

// 3) Új képek feltöltése (POST mező 'new_images')
if (isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3a) szöveges mezők mentése
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $year        = intval($_POST['year']);
    $price       = intval($_POST['price']);
    if (!$title || !$description || !$year || !$price) {
        $hiba = 'A szöveges mezők kitöltése kötelező!';
    } else {
        $stmt2 = $dbh->prepare(
            "UPDATE cars SET title=?, description=?, year=?, price=? WHERE id=?"
        );
        $stmt2->execute([$title, $description, $year, $price, $id]);
        $success = 'Az autó adatai sikeresen frissítve!';
    }

    // 3b) új képek mentése, ha választottál ki
    if (!empty($_FILES['new_images']) && $_FILES['new_images']['error'][0] === 0) {
        $uploadDir = __DIR__ . '/../uploads/autos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $allowed = ['image/jpeg','image/png','image/gif'];
        foreach ($_FILES['new_images']['tmp_name'] as $i => $tmp) {
            if ($_FILES['new_images']['error'][$i] !== 0) continue;
            $type = mime_content_type($tmp);
            if (!in_array($type, $allowed, true)) continue;
            $ext     = pathinfo($_FILES['new_images']['name'][$i], PATHINFO_EXTENSION);
            $newName = uniqid('car_', true) . ".$ext";
            $target  = $uploadDir . $newName;
            if (move_uploaded_file($tmp, $target)) {
                $dbh->prepare(
                    "INSERT INTO car_images (car_id, filename) VALUES (?, ?)"
                )->execute([$id, $newName]);
                $success = 'Új képek sikeresen feltöltve!';
            }
        }
        // PRG: frissítés GET-re, hogy ne duplikálódjon
        header("Location: index.php?page=edit_car&id={$id}&updated=1");
        exit;
    }
}

// 4) Visszajelzések GET-ből
if (isset($_GET['deleted']) && $_GET['deleted'] === '1') {
    $success = 'Kép sikeresen törölve.';
}
if (isset($_GET['updated']) && $_GET['updated'] === '1') {
    $success = $success ?: 'Frissítés sikeres!';
}

// 5) Meglévő képek lekérdezése
$stmtImg = $dbh->prepare("SELECT * FROM car_images WHERE car_id = ? ORDER BY id");
$stmtImg->execute([$id]);
$images = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
  <h2>Autó szerkesztése</h2>

  <?php if ($hiba): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($hiba) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="row g-3 mb-4">
    <!-- Szöveges mezők -->
    <div class="col-md-6">
      <label class="form-label">Autó márka / modell</label>
      <input name="title" class="form-control" required
             value="<?= htmlspecialchars($auto['title']) ?>">
    </div>
    <div class="col-md-2">
      <label class="form-label">Évjárat</label>
      <input type="number" name="year" class="form-control"
             min="1950" max="<?= date('Y') ?>" required
             value="<?= htmlspecialchars($auto['year']) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Ár (Ft)</label>
      <input type="number" name="price" class="form-control" min="0" required
             value="<?= htmlspecialchars($auto['price']) ?>">
    </div>
    <div class="col-12">
      <label class="form-label">Leírás</label>
      <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($auto['description']) ?></textarea>
    </div>

    <!-- Meglévő képek -->
    <?php if ($images): ?>
      <div class="col-12">
        <label class="form-label">Meglévő képek</label>
        <div class="row g-2">
          <?php foreach ($images as $img): ?>
            <div class="col-auto position-relative">
              <img src="uploads/autos/<?= htmlspecialchars($img['filename']) ?>"
                   class="img-thumbnail"
                   style="width:120px; height:80px; object-fit:cover;">
              <a href="index.php?page=edit_car&id=<?= $id ?>&delete_image=<?= $img['id'] ?>"
                 class="btn btn-sm btn-danger position-absolute top-0 end-0"
                 onclick="return confirm('Biztos törlöd ezt a képet?');">
                &times;
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Új képek feltöltése -->
    <div class="col-12">
      <label class="form-label">Új képek feltöltése</label>
      <input type="file" name="new_images[]" class="form-control" accept="image/*" multiple>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-primary">Mentés</button>
      <a href="index.php?page=car&id=<?= $id ?>" class="btn btn-secondary">Mégse</a>
    </div>
  </form>
</div>
