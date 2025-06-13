<?php
// controllers/home.php
?>
<div class="container py-5">
  <!-- Hero rész -->
  <div class="bg-light p-5 rounded mb-5 text-center">
    <h1 class="display-4">Üdvözöljük az Autókereskedés Weboldalán!</h1>
    <p class="lead">
      Nálunk megtalálja az Önnek legmegfelelőbb használt és új autókat,
      kedvező finanszírozással, teljes körű ügyintézéssel.
    </p>
    <hr class="my-4">
    <a class="btn btn-primary btn-lg" href="index.php?page=cars" role="button">
      Autóink megtekintése
    </a>
  </div>

  <!-- Videók sor -->
  <div class="row g-4">
    <div class="col-md-6">
      <h3>Bemutatkozó videó (helyi fájl)</h3>
      <div class="ratio ratio-16x9 mb-3">
        <video controls class="w-100 h-100">
          <source src="uploads/intro.mp4" type="video/mp4">
          Az Ön böngészője nem támogatja a videó lejátszást.
        </video>
      </div>
    </div>
    <div class="col-md-6">
      <h3>Ismerje meg kínálatunkat (YouTube)</h3>
      <div class="ratio ratio-16x9 mb-3">
        <iframe 
          src="https://www.youtube.com/embed/py2myoi-Xuw?si=rHjtZw30GrWXhpVK
          title="YouTube videó" frameborder="0" allowfullscreen>
        </iframe>
      </div>
    </div>
  </div>

  <!-- Térkép -->
  <div class="mt-5">
    <h3>Hol talál minket?</h3>
    <div class="ratio ratio-16x9">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2726.414093200833!2d19.665893830114737!3d46.894574012192926!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4743da7a6c479e1d%3A0xc8292b3f6dc69e7f!2sNeumann%20J%C3%A1nos%20Egyetem%20GAMF%20M%C5%B1szaki%20%C3%A9s%20Informatikai%20Kar!5e0!3m2!1shu!2shu!4v1749819204990!5m2!1shu!2shu
        style="border:0;" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>
</div>
