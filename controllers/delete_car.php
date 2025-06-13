<?php
// controllers/delete_car.php
$id = intval($_GET['id'] ?? 0);
// 1) Jogosultság ellenőrzése
$stmt = $dbh->prepare("SELECT user_id FROM cars WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row || $row['user_id'] != $_SESSION['user']['id']) {
  echo '<div class="alert alert-danger">Nincs jogosultságod törölni ezt az autót.</div>';
  return;
}

// 2) Törlés (képek fájlrendszerből és adatbázisból)
$imgs = $dbh->prepare("SELECT filename FROM car_images WHERE car_id=?");
$imgs->execute([$id]);
foreach($imgs->fetchAll() as $img) {
  @unlink(__DIR__.'/../uploads/autos/'.$img['filename']);
}
$dbh->prepare("DELETE FROM car_images WHERE car_id=?")->execute([$id]);
$dbh->prepare("DELETE FROM cars WHERE id=?")->execute([$id]);

header('Location: index.php?page=cars&deleted=1');
exit;
