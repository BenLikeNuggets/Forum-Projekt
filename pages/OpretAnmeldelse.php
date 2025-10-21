<?php
require_once __DIR__ . '/../bootstrap.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $filmTitle = trim($_POST['film_title'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($username && $title && $filmTitle && $content) {
        $stmt = $conn->prepare('INSERT INTO reviews (username, title, film_title, genre, content) VALUES (?,?,?,?,?)');
        if ($stmt) {
            $stmt->bind_param('sssss', $username, $title, $filmTitle, $genre, $content);
            $stmt->execute();
            $stmt->close();
            header('Location: Anmeldelser.php');
            exit();
        } else {
            $error = 'Database fejl ved oprettelse.';
        }
    } else {
        $error = 'Alle felter skal udfyldes.';
    }
}
?>

<?php include PROJECT_ROOT . '/includes/head.php'; ?>
<?php include PROJECT_ROOT . '/includes/header.php'; ?>

<body>
    <div class="form form--review">
        <div class="form__wrapper">
            <h1 class="form__title">Skriv en anmeldelse</h1>
            <?php if (!empty($error)) : ?>
                <p class="form__error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form class="form__body" method="post" action="">
                <label class="form__label">Brugernavn:<br>
                    <input class="form__input" type="text" name="username" required>
                </label>
                <label class="form__label">Overskrift:<br>
                    <input class="form__input" type="text" name="title" required>
                </label>
                <label class="form__label">Filmtitel:<br>
                    <input class="form__input" type="text" name="film_title" required>
                </label>
                <label class="form__label">Genre:<br>
                    <input class="form__input" type="text" name="genre" placeholder="(valgfri)">
                </label>
                <label class="form__label">Indhold:<br>
                    <textarea class="form__textarea" name="content" rows="5" required></textarea>
                </label>
                <div class="form__actions">
                    <button class="form__button form__button--submit" type="submit">Opret Anmeldelse</button>
                    <button class="form__button form__button--reset" type="reset">Nulstil</button>
                </div>
            </form>
            <p class="form__nav">
                <a class="form__link" href="Anmeldelser.php">Tilbage til Anmeldelser</a>
            </p>
        </div>
    </div>
</body>
</html>

