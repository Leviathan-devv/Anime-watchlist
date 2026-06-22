<?php
// 1. Include your database connection
include './connection/connection.php';
// 2. Validate and get the anime ID from the URL string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$anime_id = intval($_GET['id']);

try {
    // 3. Fetch the specific anime details
    $stmt = $pdo->prepare("SELECT * FROM anime WHERE id = :id");
    $stmt->execute([':id' => $anime_id]);
    $anime = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$anime) {
        die("<div class='container my-5 text-white'>Anime profile entry not found.</div>");
    }

    // 4. Fetch assigned genres for this anime
    $genre_stmt = $pdo->prepare("
        SELECT g.name
        FROM genres g
        JOIN anime_genres ag ON g.id = ag.genre_id
        WHERE ag.anime_id = :anime_id
    ");
    $genre_stmt->execute([':anime_id' => $anime_id]);
    $genres = $genre_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Calculations for calculations & fallbacks
$current = (int)$anime['current_episode'];
$total = (int)$anime['total_episodes'];
$progress_percent = ($total > 0) ? ($current / $total) * 100 : 0;
$rating_val = !empty($anime['my_rating']) ? (float)$anime['my_rating'] : 0.0;
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="./assets/css/main.css" rel="stylesheet">
    <link href="./assets/css/extra.css" rel="stylesheet">
    <title><?= htmlspecialchars($anime['title']); ?> - Details</title>
    <style>
    .detail-banner {
        background: linear-gradient(180deg, rgba(26, 29, 32, 0.4) 0%, rgba(26, 29, 32, 0.95) 100%),
            url('<?= !empty($anime['cover']) ? htmlspecialchars($anime['cover']) : "./assets/img/default-cover.jpg"; ?>');
        background-size: cover;
        background-position: center;
        min-height: 380px;
    }

    .poster-img {
        margin-top: -180px;
        border: 4px solid #FFD700;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        max-width: 100%;
    }
    </style>
</head>

<body style="background-color: #1a1d20; color: #fff;">
    <header class="fixed-top mb-5">
        <?php include './component/navbar.php' ?>
    </header>

    <div class="detail-banner d-flex align-items-end pt-5">
        <div class="container pb-4">
            <div class="row align-items-end">
                <div class="col-md-3 d-none d-md-block">
                </div>
                <div class="col-md-9 text-start">
                    <span
                        class="badge bg-warning text-dark mb-2 fw-bold text-uppercase"><?= htmlspecialchars($anime['anime_type']); ?></span>
                    <h1 class="display-4 fw-bold text-white mb-2"><?= htmlspecialchars($anime['title']); ?></h1>
                    <p class="text-muted fs-5 mb-0">Studio: <span
                            class="text-light"><?= htmlspecialchars($anime['studio']); ?></span> • Released: <span
                            class="text-light"><?= htmlspecialchars($anime['release_year']); ?></span></p>
                </div>
            </div>
        </div>
    </div>

    <main class="container my-5">
        <div class="row">
            <div class="col-md-3 text-center mb-4 positional-column">
                <img src="<?= !empty($anime['cover']) ? htmlspecialchars($anime['cover']) : "./assets/img/default-cover.jpg"; ?>"
                    class="img-fluid poster-img mb-3" alt="<?= htmlspecialchars($anime['title']); ?> Poster">

                <div class="p-3 border border-secondary rounded text-start" style="background-color: #212529;">
                    <div class="mb-2">
                        <small class="text-muted d-block">WATCH STATUS</small>
                        <span
                            class="badge w-100 py-2 fs-6 mt-1 <?= $anime['watch_status'] === 'Completed' ? 'bg-success' : 'bg-primary'; ?>">
                            <?= htmlspecialchars($anime['watch_status']); ?>
                        </span>
                    </div>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">SCORE</small>
                            <span class="fs-4 fw-bold text-warning">★ <?= number_format($rating_val, 1); ?></span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">EPISODES</small>
                            <span class="fs-4 fw-bold text-white"><?= $current; ?> / <?= $total; ?></span>
                        </div>
                    </div>
                </div>

                <a href="https://aniwaves.ru/home" class="btn btn-warning w-100 mt-3 btn-sm fw-bold text-dark">
                    <i class="fas fa-play me-2"></i>Watch more at Aniwave
                </a>

                <a href="index.php" class="btn btn-outline-secondary w-100 mt-2 btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Back to Watchlist
                </a>
            </div>

            <div class="col-md-9 ps-md-4">
                <div class="mb-4">
                    <h3 class="h4 text-warning border-bottom border-secondary pb-2 mb-3">Genres</h3>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (!empty($genres)): ?>
                        <?php foreach ($genres as $g): ?>
                        <span class="badge border border-secondary px-3 py-2 fs-6 bg-dark text-light rounded-pill">
                            <?= htmlspecialchars($g); ?>
                        </span>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <span class="text-muted">No genres selected for this title.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4 mt-5">
                    <h3 class="h4 text-warning border-bottom border-secondary pb-2 mb-3">Watch Progress Tracking</h3>
                    <div class="d-flex justify-content-between mb-1 small text-muted">
                        <span>Completion Rate</span>
                        <span><?= number_format($progress_percent, 0); ?>% Finished</span>
                    </div>
                    <div class="progress mb-3" style="height: 12px; background-color: #343a40;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                            style="background-color: #FFD700; width: <?= $progress_percent; ?>%;"></div>
                    </div>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-sm-6">
                        <div class="p-3 border border-secondary rounded h-100" style="background-color: #212529;">
                            <span class="text-muted small d-block">Production Studio</span>
                            <span
                                class="fs-5 fw-semibold text-white"><?= htmlspecialchars($anime['studio'] ?? 'Unknown'); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 border border-secondary rounded h-100" style="background-color: #212529;">
                            <span class="text-muted small d-block">Premiered Year</span>
                            <span
                                class="fs-5 fw-semibold text-white"><?= htmlspecialchars($anime['release_year'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer style="background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%); color: #000;" class="mt-5">
        <?php include './component/footer.php' ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
</body>

</html>