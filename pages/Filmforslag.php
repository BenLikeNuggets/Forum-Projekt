<?php
    require_once __DIR__ . '/../bootstrap.php';

    // Send kommentarer til db
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_thread_comment') {
        $threadId = intval($_POST['thread_id'] ?? 0);
        $cUser = trim($_POST['comment_username'] ?? '');
        $cText = trim($_POST['comment_text'] ?? '');
        if ($threadId > 0 && $cUser !== '' && $cText !== '') {
            $stmt = $conn->prepare('INSERT INTO thread_comments (thread_id, username, comment) VALUES (?,?,?)');
            if ($stmt) {
                $stmt->bind_param('iss', $threadId, $cUser, $cText);
                $stmt->execute();
                $stmt->close();
            }
            header('Location: Filmforslag.php?' . http_build_query($_GET));
            exit();
        } else {
            $comment_error = 'Ugyldigt kommentar input.';
        }
    }

    // Sortering generelt og sortering efter genre
    $order = (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'ASC' : 'DESC';
    $genreFilter = trim($_GET['genre'] ?? '');

    // Genre dropdown liste
    $genres = [];
    $gRes = $conn->query("SELECT DISTINCT genre FROM threads WHERE genre IS NOT NULL AND genre <> '' ORDER BY genre ASC");
    if ($gRes) { while ($gRow = $gRes->fetch_assoc()) { $genres[] = $gRow['genre']; } }

    $where = $genreFilter !== '' ? "WHERE genre = '" . $conn->real_escape_string($genreFilter) . "'" : '';
    $threadsResult = $conn->query("SELECT * FROM threads $where ORDER BY created_at $order");

    // Oplæg (Threads)
    $threads = [];
    $threadIds = [];
    if ($threadsResult) {
        while ($rowTmp = $threadsResult->fetch_assoc()) {
            $threadIds[] = (int)$rowTmp['id'];
            $threads[] = $rowTmp;
        }
    }

    // Kommentarer
    $commentsByThread = [];
    if ($threadIds) {
        $idList = implode(',', $threadIds);
        $cRes = $conn->query("SELECT * FROM thread_comments WHERE thread_id IN ($idList) ORDER BY created_at ASC");
        if ($cRes) {
            while ($cRow = $cRes->fetch_assoc()) {
                $commentsByThread[(int)$cRow['thread_id']][] = $cRow;
            }
        }
    }
?>

<?php include PROJECT_ROOT . '/includes/head.php'; ?>
<?php include PROJECT_ROOT . '/includes/header.php'; ?>

<body>
    <section class="layout__header">
        <h1 class="forum__title">Filmforslag</h1>
    </section>
    <main class="forum">
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
        <section class="layout__col">
            <?php if (!empty($comment_error)): ?>
                <p class="forum__error"><?= htmlspecialchars($comment_error) ?></p>
            <?php endif; ?>

            <?php if ($threads): ?>
                <?php foreach ($threads as $thread): ?>
                    <article class="thread">
                        <header class="thread__header">
                            <h2 class="thread__title">
                                <?= htmlspecialchars($thread['title']) ?>
                            </h2>
                            <?php if (!empty($thread['genre'])): ?>
                                <small class="thread__genre">Genre: [<?= htmlspecialchars($thread['genre']) ?>]</small>
                            <?php endif; ?>
                        </header>
                            <small class="thread__meta">
                                <p class="thread__username"><?= htmlspecialchars($thread['username']) ?></p>
                                <p class="thread__date"><?= $thread['created_at'] ?></p>
                            </small>

                        <div class="thread__content">
                            <?= nl2br(htmlspecialchars($thread['content'])) ?>
                        </div>
                        <details class="comment__container" closed>
                            <summary class="comment__summary">
                                Se Kommentarer Her (<?= isset($commentsByThread[$thread['id']]) ? count($commentsByThread[$thread['id']]) : 0 ?>)
                            </summary>
                            <div class="comment__list">
                                <?php if (!empty($commentsByThread[$thread['id']])): ?>
                                    <?php foreach ($commentsByThread[$thread['id']] as $comment): ?>
                                        <div class="comment">
                                            <div class="comment__meta">
                                                <b class="comment__username"><?= htmlspecialchars($comment['username']) ?></b>
                                                <div class="comment__date"><?= $comment['created_at'] ?></div>
                                            </div>
                                            <div class="comment__text"><?= nl2br(htmlspecialchars($comment['comment'])) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="comment__empty">Ingen kommentarer endnu.</p>
                                <?php endif; ?>
                            </div>

                            <form class="form__container" method="post" action="">
                                <p class="form__header">Opret en kommentar</p>
                                <input type="hidden" name="action" value="add_thread_comment">
                                <input type="hidden" name="thread_id" value="<?= (int)$thread['id'] ?>">
                                <input class="form__username" type="text" name="comment_username" placeholder="Brugernavn" required>
                                <textarea class="form__comment" type="text" name="comment_text" placeholder="Kommentar" required></textarea>
                                <button class="form__button" type="submit">Post Kommentar</button>
                            </form>
                        </details>
                    </article>

                <?php endforeach; ?>
            <?php else: ?>
                <p class="forum__empty">
                    Intet oppe endnu. Læg det første <a href="/pages/OpretFilmforslag.php">opslag</a> op!
                </p>
            <?php endif; ?>
        </section>
        <!-- Højre Layout -->
        <section class="layout__col layout__col--right">
            <form class="filter" method="get">
                <div class="filter__group">
                    <h1 class="filter__label">Sortering</h1>
                    <a class="reset-link" href="?sort=newest<?= $genreFilter !== '' ? '&genre=' . urlencode($genreFilter) : '' ?>">Nyeste Opslag</a>
                    <a class="reset-link" href="?sort=oldest<?= $genreFilter !== '' ? '&genre=' . urlencode($genreFilter) : '' ?>">Ældste Opslag</a>
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

                <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort'] ?? 'newest') ?>">
            </form>
            <nav class="forum__opret-opslag">
                <a class="reset-link" href="/pages/OpretFilmforslag.php"><h1>Opret Opslag</h1></a>
            </nav>
        </section>
    </main>
</body>
</html>