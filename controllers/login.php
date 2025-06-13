<?php
$hiba = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        // Regisztráció
        $vezeteknev = trim($_POST['vezeteknev']);
        $keresztnev = trim($_POST['keresztnev']);
        $login = trim($_POST['login']);
        $jelszo = $_POST['jelszo'];
        $jelszo2 = $_POST['jelszo2'];

        if ($jelszo !== $jelszo2) {
            $hiba = 'A két jelszó nem egyezik!';
        } elseif (empty($vezeteknev) || empty($keresztnev) || empty($login) || empty($jelszo)) {
            $hiba = 'Minden mező kitöltése kötelező!';
        } else {
            // Felhasználó beszúrás
            $sql = "INSERT INTO users (vezeteknev, keresztnev, login, password_hash)
                    VALUES (?, ?, ?, ?)";
            try {
                $stmt = $dbh->prepare($sql);
                $stmt->execute([
                    $vezeteknev, $keresztnev, $login, password_hash($jelszo, PASSWORD_DEFAULT)
                ]);
                $hiba = 'Sikeres regisztráció! Most már beléphetsz.';
            } catch (PDOException $e) {
                $hiba = 'A felhasználónév már foglalt vagy hiba történt!';
            }
        }
    } elseif (isset($_POST['login'])) {
        // Bejelentkezés
        $login = trim($_POST['loginnev']);
        $jelszo = $_POST['loginjelszo'];
        $sql = "SELECT * FROM users WHERE login = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($jelszo, $user['password_hash'])) {
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit;
        } else {
            $hiba = 'Hibás felhasználónév vagy jelszó!';
        }
    }
}
?>

<h2 class="mt-4 mb-3 text-center">Belépés vagy Regisztráció</h2>

<?php if ($hiba): ?>
    <div class="alert alert-<?php echo (strpos($hiba, 'Sikeres') !== false) ? 'success' : 'danger'; ?> text-center" role="alert">
        <?= htmlspecialchars($hiba) ?>
    </div>
<?php endif; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <form method="post" class="card card-body shadow mb-3">
                <h3 class="mb-3 text-primary">Regisztráció</h3>
                <div class="mb-2">
                    <input type="text" name="vezeteknev" class="form-control" placeholder="Vezetéknév" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="keresztnev" class="form-control" placeholder="Keresztnév" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="login" class="form-control" placeholder="Felhasználónév" required>
                </div>
                <div class="mb-2">
                    <input type="password" name="jelszo" class="form-control" placeholder="Jelszó" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="jelszo2" class="form-control" placeholder="Jelszó újra" required>
                </div>
                <button type="submit" name="register" class="btn btn-success w-100">Regisztráció</button>
            </form>
        </div>

        <div class="col-md-5">
            <form method="post" class="card card-body shadow mb-3">
                <h3 class="mb-3 text-primary">Belépés</h3>
                <div class="mb-2">
                    <input type="text" name="loginnev" class="form-control" placeholder="Felhasználónév" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="loginjelszo" class="form-control" placeholder="Jelszó" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Belépés</button>
            </form>
        </div>
    </div>
</div>

