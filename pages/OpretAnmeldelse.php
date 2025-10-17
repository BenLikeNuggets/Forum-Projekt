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
    <div class="alt">
        <div class="center-wrapper">
            <h1>Skriv en anmeldelse</h1>
            <?php if (!empty($error)) echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>'; ?>
            <form method="post" action="">
                <label>Brugernavn:<br><input type="text" name="username" required></label>
                <label>Rubrik:<br><input type="text" name="title" required></label>
                <label>Filmtitel:<br><input type="text" name="film_title" required></label>
                <label>Genre:<br><input type="text" name="genre" placeholder="(valgfri)"></label>
                <label>Indhold:<br><textarea name="content" rows="5" cols="40" required></textarea></label>
                <div style="text-align:center; margin-top:12px;">
                    <button type="submit">Opret anmeldelse</button>
                    <button type="reset">Nulstil</button>
                </div>
            </form>
            <p><a href="Anmeldelser.php">Tilbage til Anmeldelser</a> | <a href="Filmforslag.php">Forum</a></p>
        </div>
    </div>
</body>
</html>

