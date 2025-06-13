<?php
// controllers/contact.php

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Ha be van jelentkezve, felülírjuk a nevet:
    if (isset($_SESSION['user'])) {
        $name = $_SESSION['user']['vezeteknev'] . ' ' . $_SESSION['user']['keresztnev'];
    }
    if (!$name) $name = "Vendég";

    // Szerveroldali ellenőrzés
    if (strlen($name) < 2) $errors[] = "A név legalább 2 karakter legyen!";
    if (strlen($message) < 5) $errors[] = "Az üzenet legalább 5 karakter legyen!";
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Az e-mail cím nem megfelelő!";

    // Ha nincs hiba, mentés DB-be
    if (!$errors) {
        require_once(__DIR__ . '/../db/db.php'); // ha még nem lenne bent
        $stmt = $dbh->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        $success = true;
    }
}
?>

<h2>Kapcsolat</h2>
<?php if ($success): ?>
    <div class="alert alert-success">Az üzenet elküldve! Köszönjük a visszajelzést.</div>
<?php endif; ?>
<?php if ($errors): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?>
            <div><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form id="contactForm" method="post" action="index.php?page=contact" novalidate>
    <div class="mb-3">
        <label for="name" class="form-label">Név</label>
        <input type="text" class="form-control" id="name" name="name"
            value="<?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['vezeteknev'] . ' ' . $_SESSION['user']['keresztnev']) : htmlspecialchars($_POST['name'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">E-mail (opcionális)</label>
        <input type="email" class="form-control" id="email" name="email"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="message" class="form-label">Üzenet</label>
        <textarea class="form-control" id="message" name="message"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
    </div>
    <div id="formError" class="text-danger mb-2"></div>
    <button type="submit" class="btn btn-primary">Küldés</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    if (form) {
        form.onsubmit = function(e) {
            let errors = [];
            let name = document.getElementById('name').value.trim();
            let message = document.getElementById('message').value.trim();
            if (name.length < 2) errors.push("A név legalább 2 karakter legyen!");
            if (message.length < 5) errors.push("Az üzenet legalább 5 karakter legyen!");
            if (errors.length) {
                document.getElementById('formError').innerHTML = errors.join('<br>');
                e.preventDefault();
            }
        };
    }
});
</script>
