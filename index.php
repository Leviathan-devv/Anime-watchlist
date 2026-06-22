<?php
include './connection/connection.php';

try {
    $query = "SELECT id, title, anime_type, watch_status, current_episode, total_episodes, my_rating, release_year, studio, cover FROM anime ORDER BY id ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $anime_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}
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
    <title>Anime Watchlist</title>
</head>

<body class="bg-light">
    <header class="fixed-top mb-5">
        <?php include './component/navbar.php' ?>
    </header>

    <main class="py-5" style="background-color: #1a1d20;">

        <div class="py-5" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);">
            <div class="container">
                <h1 class="display-5 fw-bold text-dark mb-2">
                    <i class="fas fa-star me-3"></i>My Anime Watchlist
                </h1>
            </div>
        </div>

        <div class="container my-5">
            <div class="my-4">
                <form class="d-flex" onsubmit="event.preventDefault();">
                    <input id="searchInput" class="form-control" type="search" placeholder="Search anime..."
                        aria-label="Search">
                </form>
            </div>

            <div id="noResultsMessage" class="text-center py-5 d-none">
                <p class="text-muted fs-5">No matching anime titles found.</p>
            </div>

            <div class="row g-4" id="anime">
                <?php if (!empty($anime_list)): ?>
                    <?php foreach ($anime_list as $row):
                        $current = (int)$row['current_episode'];
                        $total = (int)$row['total_episodes'];
                        $progress_percent = ($total > 0) ? ($current / $total) * 100 : 0;
                        $genre_names = [];
                        try {
                            // Note: Adjusted schema target key dynamically inside the join framework
                            $genre_stmt = $pdo->prepare("
                                SELECT g.name 
                                FROM genres g
                                JOIN anime_genres ag ON g.id = ag.genre_id
                                WHERE ag.anime_id = :anime_id
                            ");
                            $genre_stmt->execute([':anime_id' => $row['id']]);
                            $genre_names = $genre_stmt->fetchAll(PDO::FETCH_COLUMN);
                        } catch (PDOException $e) {
                            // Fallback array mapping
                        }
                        $genre_display = !empty($genre_names) ? implode(' • ', array_map('htmlspecialchars', $genre_names)) : 'No Genre';

                        $rating_val = !empty($row['my_rating']) ? (float)$row['my_rating'] : 0.0;
                        $star_rating = $rating_val / 2;
                    ?>
                        <div class="col-md-6 col-lg-4 anime-item-card"
                            data-title="<?= strtolower(htmlspecialchars($row['title'])); ?>"
                            data-studio="<?= strtolower(htmlspecialchars($row['studio'] ?? '')); ?>"
                            data-genres="<?= strtolower(htmlspecialchars($genre_display)); ?>">
                            <div class="card border-0 shadow anime-card h-100">
                                <div class="anime-card-image"
                                    style="background-image: url('<?= !empty($row['cover']) ? htmlspecialchars($row['cover']) : './assets/img/default-cover.jpg'; ?>'); background-size: cover; background-position: center; position: relative; min-height: 250px;">
                                    <?php if (empty($row['cover'])): ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 text-secondary"
                                            style="background-color: #2b3035;">
                                            <i class="fas fa-film fa-3x"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="anime-card-overlay">
                                        <p style="font-size: 12px; margin: 0;">Episode <?= $current; ?>/<?= $total; ?></p>
                                        <div style="font-size: 24px; font-weight: bold; line-height: 1.2;">
                                            <?= htmlspecialchars($row['title']); ?></div>
                                    </div>
                                </div>
                                <div class="anime-card-content d-flex flex-column justify-content-between p-3"
                                    style="background-color: #212529; color: #fff;">
                                    <div>
                                        <div class="anime-card-title fw-bold fs-5 text-truncate"
                                            title="<?= htmlspecialchars($row['title']); ?>">
                                            <?= htmlspecialchars($row['title']); ?></div>
                                        <div class="anime-card-genre text-muted small mb-2"><?= $genre_display; ?></div>

                                        <div class="anime-card-rating d-flex align-items-center gap-2 mb-3">
                                            <div class="stars text-warning small">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($star_rating >= $i) {
                                                        echo '<i class="fas fa-star"></i>';
                                                    } elseif ($star_rating >= ($i - 0.5)) {
                                                        echo '<i class="fas fa-star-half-alt"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star"></i>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="score fw-bold text-warning"><?= number_format($rating_val, 1); ?></div>
                                        </div>

                                        <div class="progress mb-3" style="height: 6px; background-color: #343a40;">
                                            <div class="progress-bar"
                                                style="background-color: #FFD700; width: <?= $progress_percent; ?>%;"></div>
                                        </div>
                                    </div>

                                    <div class="anime-card-footer d-flex gap-2 mt-auto">
                                        <a href="detail.php?id=<?= $row['id']; ?>"
                                            class="anime-card-btn primary btn btn-warning btn-sm w-100 text-dark fw-bold"
                                            title="View details for <?= htmlspecialchars($row['title']); ?>">
                                            <i class="fas fa-play me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted fs-5">No anime found in your watchlist database.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer style="background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%); color: #000;">
        <?php include './component/footer.php' ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>

    <script>
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.anime-item-card');
            const noResultsMessage = document.getElementById('noResultsMessage');
            let visibleCount = 0;

            cards.forEach(card => {
                const title = card.getAttribute('data-title');
                const studio = card.getAttribute('data-studio');
                const genres = card.getAttribute('data-genres');

                // Allows user to search by Title, Studio name, or Genre tags smoothly
                if (title.includes(query) || studio.includes(query) || genres.includes(query)) {
                    card.classList.remove('d-none');
                    visibleCount++;
                } else {
                    card.classList.add('d-none');
                }
            });

            // Toggle "No matching results found" message if grid filter matches nothing
            if (visibleCount === 0 && query !== '') {
                noResultsMessage.classList.remove('d-none');
            } else {
                noResultsMessage.classList.add('d-none');
            }
        });
    </script>
</body>

</html>