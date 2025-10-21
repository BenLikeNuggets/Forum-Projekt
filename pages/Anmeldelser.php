<?php
    require_once __DIR__ . '/../bootstrap.php';

    // Tilføj kommentar til anmeldelse
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_review_comment') {
        $reviewId = intval($_POST['review_id'] ?? 0);
        $cUser = trim($_POST['comment_username'] ?? '');
        $cText = trim($_POST['comment_text'] ?? '');
        if ($reviewId > 0 && $cUser !== '' && $cText !== '') {
            $stmt = $conn->prepare('INSERT INTO review_comments (review_id, username, comment) VALUES (?,?,?)');
            if ($stmt) {
                $stmt->bind_param('iss', $reviewId, $cUser, $cText);
                $stmt->execute();
                $stmt->close();
            }
            header('Location: Anmeldelser.php?' . http_build_query($_GET));
            exit();
        } else {
            $comment_error = 'Ugyldigt kommentar input.';
        }
    }

    // Sortering og filtrering
    $sort = $_GET['sort'] ?? 'newest';
    $genreFilter = trim($_GET['genre'] ?? '');

    $orderClause = match ($sort) {
        'title_az' => 'ORDER BY r.film_title ASC, r.created_at DESC',
        'title_za' => 'ORDER BY r.film_title DESC, r.created_at DESC',
        'oldest'   => 'ORDER BY r.created_at ASC',
        default    => 'ORDER BY r.created_at DESC',
    };

    // Hent genre til dropdown
    $genres = [];
    $gRes = $conn->query("SELECT DISTINCT genre FROM reviews WHERE genre IS NOT NULL AND genre <> '' ORDER BY genre ASC");
    if ($gRes) {
        while ($gRow = $gRes->fetch_assoc()) {
            $genres[] = $gRow['genre'];
        }
    }

    $where = '';
    if ($genreFilter !== '') {
        $safe = $conn->real_escape_string($genreFilter);
        $where = "WHERE r.genre = '$safe'";
    }

    // Hent anmeldelser
    $sql = "SELECT r.* FROM reviews r $where $orderClause";
    $res = $conn->query($sql);
    $reviews = [];
    $reviewIds = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $reviews[] = $row;
            $reviewIds[] = (int)$row['id'];
        }
    }

    // Hent kommentarer til anmeldelser
    $commentsByReview = [];
    if ($reviewIds) {
        $idList = implode(',', $reviewIds);
        $cRes = $conn->query("SELECT * FROM review_comments WHERE review_id IN ($idList) ORDER BY created_at ASC");
        if ($cRes) {
            while ($cRow = $cRes->fetch_assoc()) {
                $commentsByReview[(int)$cRow['review_id']][] = $cRow;
            }
        }
    }
?>

<?php include PROJECT_ROOT . '/includes/head.php'; ?>
<?php include PROJECT_ROOT . '/includes/header.php'; ?>

<html>
<body>
    <section class="layout__header">
        <h1 class="forum__title">Anmeldelser</h1>
    </section>

    <main class="reviews">
        <section class="layout__col layout__col--left">
            <article class="menu-options__container">
            <div class="menu-options__container--categories">
                <h1 class="menu-options__title">Kategorier</h1>
                <h2 class="menu-options__subtitle"><a href="/pages/Filmforslag.php" class="reset-link menu-options__link">Filmforslag</a></h2>
                <h2 class="menu-options__subtitle"><a href="/pages/Anmeldelser.php" class="reset-link menu-options__link">Anmeldelser</a></h2>
            </div>
            </article>
        </section>

        <!-- Middle -->
        <section class="layout__col" closed>
            <?php if (!empty($comment_error)): ?>
                <p class="forum__error"><?= htmlspecialchars($comment_error) ?></p>
            <?php endif; ?>

            <?php if ($reviews): ?>
                <?php foreach ($reviews as $rev): ?>
                    <article class="thread">
                        <header class="thread__header">
                            <h2 class="thread__title">
                                <?= htmlspecialchars($rev['title']) ?>
                            </h2>
                            <small class="thread__subtitle">Titel: (<?= htmlspecialchars($rev['film_title']) ?>)</small>
                            <?php if (!empty($rev['genre'])): ?>
                                    <small class="thread__genre">Genre: [<?= htmlspecialchars($rev['genre']) ?>]</small>
                            <?php endif; ?>
                        </header>
                        <small class="thread__meta">
                            <p class="thread__username"><?= htmlspecialchars($rev['username']) ?></p>
                            <p class="thread__username"><?= $rev['created_at'] ?></p>
                        </small>

                        <div class="thread__content">
                            <?= nl2br(htmlspecialchars($rev['content'])) ?>
                        </div>

                        <details class="comment__container" closed>
                            <summary class="comment__summary">
                                Se Kommentarer Her (<?= isset($commentsByReview[$rev['id']]) ? count($commentsByReview[$rev['id']]) : 0 ?>)
                            </summary>

                            <div class="comment__list">
                                <?php if (!empty($commentsByReview[$rev['id']])): ?>
                                    <?php foreach ($commentsByReview[$rev['id']] as $c): ?>
                                        <div class="comment">
                                            <div class="comment__meta">
                                                <b class="comment__username"><?= htmlspecialchars($c['username']) ?></b>
                                                <div class="comment__date"><?= $c['created_at'] ?></div>
                                            </div>
                                            <div class="comment__text"><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <h1 class="comment__empty">Ingen kommentarer endnu.</h1>
                                <?php endif; ?>
                            </div>
                            <form class="form__container" method="post" action="">
                                <p class="form__header">Opret en kommentar</p>
                                <input type="hidden" name="action" value="add_review_comment">
                                <input type="hidden" name="review_id" value="<?= (int)$rev['id'] ?>">
                                <input class="form__username" type="text" name="comment_username" placeholder="Brugernavn" required>
                                <textarea class="form__comment" type="text" name="comment_text" placeholder="Kommentar" required></textarea>
                                <button class="form__button" type="submit">Post Kommentar</button>
                            </form>
                        </details>
                    </article>

                <?php endforeach; ?>
            <?php else: ?>
                <p class="forum__empty">
                    Ingen anmeldelser endnu. Skriv den første <a href="/pages/OpretAnmeldelse.php">anmeldelse</a>.
                </p>
            <?php endif; ?>
        </section>

        <!-- Højre Layout -->
        <section class="layout__col layout__col--right">
            <form class="filter" method="get">
                <div class="filter__group">
                    <h1 class="filter__label">Sortering</h1>
                    <a class="reset-link" href="?sort=newest<?= $genreFilter !== '' ? '&genre=' . urlencode($genreFilter) : '' ?>">Nyeste</a>
                    <a class="reset-link" href="?sort=oldest<?= $genreFilter !== '' ? '&genre=' . urlencode($genreFilter) : '' ?>">Ældste</a>
                    <a class="reset-link" href="?sort=title_az<?= $genreFilter !== '' ? '&genre=' . urlencode($genreFilter) : '' ?>">Titel A-Z</a>
                    <a class="reset-link" href="?sort=title_za<?= $genreFilter !== '' ? '&genre=' . urlencode($genreFilter) : '' ?>">Titel Z-A</a>
                </div>

                <div class="filter__group">
                    <h1 class="filter__label">Genre</h1>
                    <select class="filter__select" name="genre" onchange="this.form.submit()">
                        <option value="">Alle</option>
                        <?php foreach ($genres as $g): ?>
                            <option value="<?= htmlspecialchars($g) ?>" <?= $g === $genreFilter ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">


            </form>
            <nav class="forum__opret-opslag">
                <a class="reset-link" href="/pages/OpretAnmeldelse.php"><h1>Opret Anmeldelse</h1></a>
            </nav>
        </section>
    </main>
</body>
</html>