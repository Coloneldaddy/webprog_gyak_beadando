<?php
// controllers/cars.php

$hiba = '';
$success = '';

// Törlés utáni sikerüzenet
if (isset($_GET['deleted']) && $_GET['deleted'] === '1') {
    $success = 'Autó sikeresen törölve.';
}

// PRG utáni feltöltés sikerüzenet
if (isset($_GET['success']) && $_GET['success'] === '1') {
    $success = 'Sikeres feltöltés!';
}

// Az autos könyvtár ellenőrzése/létrehozása
$uploadDir = __DIR__ . '/../uploads/autos/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $year        = intval($_POST['year'] ?? 0);
    $price       = intval($_POST['price'] ?? 0);
    $files       = $_FILES['kepek'] ?? null;

    if (!$title || !$description || !$year || !$price || !$files || $files['error'][0] !== 0) {
        $hiba = 'Minden mezőt és legalább egy képet kötelező kitölteni!';
    } else {
        $dbh->beginTransaction();
        $stmt = $dbh->prepare("
            INSERT INTO cars (user_id, title, description, year, price)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user']['id'], $title, $description, $year, $price
        ]);
        $car_id        = $dbh->lastInsertId();
        $allowed       = ['image/jpeg', 'image/png', 'image/gif'];
        $success_count = 0;

        foreach ($files['tmp_name'] as $i => $tmp_name) {
            if ($files['error'][$i] !== 0) continue;
            $filetype = mime_content_type($tmp_name);
            if (!in_array($filetype, $allowed, true)) continue;
            $ext     = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $newName = uniqid('car_', true) . '.' . $ext;
            $target  = $uploadDir . $newName;
            if (move_uploaded_file($tmp_name, $target)) {
                $stmt2 = $dbh->prepare("
                    INSERT INTO car_images (car_id, filename)
                    VALUES (?, ?)
                ");
                $stmt2->execute([$car_id, $newName]);
                $success_count++;
            }
        }

        if ($success_count > 0) {
            $dbh->commit();
            header('Location: index.php?page=cars&success=1');
            exit;
        } else {
            $dbh->rollBack();
            $hiba = 'Hiba a képek mentésekor!';
        }
    }
}

// Autók lekérdezése az indexkép feltöltő nevével
$sql   = "
    SELECT c.*, u.login,
           (SELECT filename FROM car_images WHERE car_id=c.id ORDER BY id LIMIT 1) AS indexkep
    FROM cars c
    JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC
";
$autok = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-3">Új autó feltöltése</h2>

    <?php if ($hiba): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($hiba) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['user'])): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Autó márka / modell</label>
                        <input type="text" name="title" class="form-control" required maxlength="80">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Évjárat</label>
                        <input type="number" name="year" class="form-control"
                               min="1950" max="<?= date('Y') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ár (Ft)</label>
                        <input type="number" name="price" class="form-control" min="0" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Leírás</label>
                        <textarea name="description" class="form-control"
                                  rows="3" maxlength="500" required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Autó képei (többet is kijelölhetsz)</label>
                        <input type="file" name="kepek[]" class="form-control"
                               accept="image/*" multiple required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Feltöltés</button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Csak bejelentkezve adhatsz fel új autót!</div>
    <?php endif; ?>
</div>

<div class="container">
    <h2 class="mb-3">Eladó autók</h2>
    <div class="row g-4">
        <?php foreach ($autok as $auto): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow">
                    <?php if ($auto['indexkep']): ?>
                        <img src="uploads/autos/<?= htmlspecialchars($auto['indexkep']) ?>"
                             class="card-img-top"
                             style="height:200px;object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($auto['title']) ?></h5>
                        <div class="mb-1 text-muted">
                            Feltöltő: <?= htmlspecialchars($auto['login']) ?>
                        </div>
                        <div class="mb-2">
                            <b>Évjárat:</b> <?= htmlspecialchars($auto['year']) ?>
                            | <b>Ár:</b> <?= number_format($auto['price'],0,'',' ') ?> Ft
                        </div>
                        <p class="card-text">
                            <?= htmlspecialchars(mb_substr($auto['description'],0,100)) ?>...
                        </p>
                        <?php if (isset($_SESSION['user']['id']) &&
                                  $_SESSION['user']['id'] == $auto['user_id']): ?>
                            <a href="index.php?page=car&id=<?= $auto['id'] ?>"
                               class="btn btn-outline-primary w-100 mb-2">Részletek</a>
                            <a href="index.php?page=edit_car&id=<?= $auto['id'] ?>"
                               class="btn btn-warning w-100 mb-2">Szerkesztés</a>
                            <a href="index.php?page=delete_car&id=<?= $auto['id'] ?>"
                               class="btn btn-danger w-100"
                               onclick="return confirm('Biztos törlöd ezt az autót?');">
                               Törlés
                            </a>
                        <?php else: ?>
                            <a href="index.php?page=car&id=<?= $auto['id'] ?>"
                               class="btn btn-outline-primary w-100">Részletek</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
