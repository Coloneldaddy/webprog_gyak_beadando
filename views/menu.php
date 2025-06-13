<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="index.php?page=home">
      <?= htmlspecialchars($config['site_title']) ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php foreach ($menu as $slug => $label): ?>
          <li class="nav-item">
            <a class="nav-link<?php if ($page === $slug) echo ' active'; ?>"
               href="index.php?page=<?= htmlspecialchars($slug) ?>">
              <?= htmlspecialchars($label) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
