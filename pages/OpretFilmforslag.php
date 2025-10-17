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
    <div class="alt">
        <div class="center-wrapper">
            <h1>Opret et nyt oplæg</h1>
            <?php if (!empty($error)) echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>'; ?>
            <form method="post" action="">
                <label>Brugernavn:<br><input type="text" name="username" required></label>
                <label>Titel:<br><input type="text" name="title" required></label>
                <label>Genre:<br><input type="text" name="genre" placeholder="f.eks. Sci-Fi, Drama, Action osv."></label>
                <label>Indhold:<br><textarea name="content" rows="5" cols="40" required></textarea></label>
                <div style="text-align:center; margin-top:12px;">
                    <button type="submit">Opret oplæg</button>
                    <button type="reset">Nulstil</button>
                </div>
            </form>
            <p><a href="Filmforslag.php">Tilbage til Forum</a></p>
        </div>
    </div>
</body>
</html>

