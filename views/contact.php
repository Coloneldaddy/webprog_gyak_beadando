<h2>Kapcsolat</h2>
<form id="contactForm" method="post" action="index.php?page=contact">
    <div class="mb-3">
        <label for="name" class="form-label">Név</label>
        <input type="text" class="form-control" id="name" name="name"
        value="<?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['vezeteknev'] . ' ' . $_SESSION['user']['keresztnev']) : '' ?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">E-mail (opcionális)</label>
        <input type="email" class="form-control" id="email" name="email">
    </div>
    <div class="mb-3">
        <label for="message" class="form-label">Üzenet</label>
        <textarea class="form-control" id="message" name="message"></textarea>
    </div>
    <div id="formError" class="text-danger mb-2"></div>
    <button type="submit" class="btn btn-primary">Küldés</button>
</form>
