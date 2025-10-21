<?php require_once __DIR__ . '/bootstrap.php';?>

<?php include PROJECT_ROOT . '/includes/head.php'; ?>
<?php include PROJECT_ROOT . '/includes/header.php'; ?>

<body>

  <main class="menu-options">
    <article class="menu-options__container">
      <div class="menu-options__container--categories">
        <h1 class="menu-options__title">Kategorier</h1>
        <h2 class="menu-options__subtitle"><a href="/pages/Filmforslag.php" class="reset-link menu-options__link">Filmforslag</a></h2>
        <h2 class="menu-options__subtitle"><a href="/pages/Anmeldelser.php" class="reset-link menu-options__link">Anmeldelser</a></h2>
      </div>
    </article>

    <article class="menu-options__container">
      <div class="menu-options__container--welcome">
        <h1 class="menu-options__title">Velkommen</h1>
        <p class="menu-options__text">Vi håber det bliver hyggeligt at snakke om film osv osv osv</p>
      </div>
    </article>

    <article class="menu-options__container">
      <div class="menu-options__container--rules">
        <h1 class="menu-options__title">Regelsæt</h1>
        <p class="menu-options__text">Disse regler er lavet for at alle på forummet kan få en god oplevelse.</p>
        <ol class="menu-options__list">
          <li>Ingen mobning.</li>
          <li>Ingen bandeord.</li>
          <li>Ingen spam.</li>
          <li>Del ikke DINE eller ANDRES personlige oplysninger, såsom din adresse, personnummer osv.</li>
        </ol>
        <p class="menu-options__text"><b>Konsekvenser:</b></p>
        <p class="menu-options__text">Brud på reglerne fører til IP-ban.</p>
      </div>
    </article>
  </main>
</body>
</html>