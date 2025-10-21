<?php
require_once __DIR__ . '/../bootstrap.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if ($username && $title && $content) {
        $stmt = $conn->prepare('INSERT INTO threads (username, title, genre, content) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $username, $title, $genre, $content);
        $stmt->execute();
        $stmt->close();
        header('Location: Filmforslag.php');
        exit();
    } else {
        $error = 'Alle felter skal udfyldes.';
    }
}
?>

<?php include PROJECT_ROOT . '/includes/head.php'; ?>
<?php include PROJECT_ROOT . '/includes/header.php'; ?>

<body>
    <div class="form form--thread">
        <div class="form__wrapper">
            <h1 class="form__title">Opret et nyt oplæg</h1>
            <?php if (!empty($error)) : ?>
                <p class="form__error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form class="form__body" method="post" action="">
                <label class="form__label">Brugernavn:<br>
                    <input class="form__input" type="text" name="username" required>
                </label>
                <label class="form__label">Titel:<br>
                    <input class="form__input" type="text" name="title" required>
                </label>
                <label class="form__label">Genre:<br>
                    <input class="form__input" type="text" name="genre" placeholder="f.eks. Sci-Fi, Drama, Action osv.">
                </label>
                <label class="form__label">Indhold:<br>
                    <textarea class="form__textarea" name="content" rows="5" required></textarea>
                </label>
                <div class="form__actions">
                    <button class="form__button form__button--submit" type="submit">Opret Oplæg</button>
                    <button class="form__button form__button--reset" type="reset">Nulstil</button>
                </div>
            </form>
            <p class="form__nav">
                <a class="form__link" href="Filmforslag.php">Tilbage til Forum</a>
            </p>
        </div>
    </div>
</body>
</html>

