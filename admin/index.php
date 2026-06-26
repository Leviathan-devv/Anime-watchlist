<?php
include '../connection/connection.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/main.css?v=1.0" rel="stylesheet">
    <link href="../assets/css/extra.css?v=1.1" rel="stylesheet">
    <title>Anime Watchlist</title>
</head>

<body class="bg-light">
    <header class="fixed-top mb-5">
        <?php include '../component/navbar.php' ?>
    </header>

    <main class="py-5" style="background-color: #1a1d20; min-height: 100vh; color: #fff;">

        <div class="py-4 border-bottom border-dark" style="background: #212529;">
            <div class="container d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold mb-1" style="color: #ffb703;">
                        <i class="fas fa-star me-2"></i>My Anime Watchlist
                    </h1>
                    <p class="text-muted small mb-0">Track and manage your streaming library</p>
                </div>
                <button type="button" class="btn fw-semibold" style="background-color: #ffb703; color: #000;"
                    data-bs-toggle="modal" data-bs-target="#addAnime">
                    <i class="fas fa-plus me-2"></i>Add New Anime
                </button>
            </div>
        </div>

        <div class="container my-5">
            <div class="card bg-dark border-secondary text-white shadow-lg">
                <div
                    class="card-header bg-transparent border-secondary py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-muted small uppercase tracking-wider">Current Series</h5>
                    <span class="badge rounded-pill px-3 py-2"
                        style="background-color: rgba(255,183,3,0.15); color: #ffb703;">Titles Library</span>
                </div>
                <div class="card-body p-0">
                    <?php
                    if (isset($pdo) && $pdo !== null) {
                        try {
                            // NOTE: Make sure the table names below match your phpMyAdmin tables exactly!
                            $stmt = $pdo->query('SELECT a.*, GROUP_CONCAT(g.name SEPARATOR ", ") AS genres 
                                 FROM anime a 
                                 LEFT JOIN anime_genres ag ON a.id = ag.anime_id 
                                 LEFT JOIN genres g ON ag.genre_id = g.id 
                                 GROUP BY a.id 
                                 ORDER BY a.title ASC');

                            if ($stmt->rowCount() == 0) {
                                echo '<div class="text-center text-muted p-5"><i class="fas fa-inbox fa-2x mb-2"></i><p class="mb-0">Your library is completely empty.</p></div>';
                            } else {
                                while ($anime = $stmt->fetch()) {
                    ?>
                                    <div
                                        class="d-flex align-items-center justify-content-between p-3 border-bottom border-secondary hover-row transition">
                                        <div class="d-flex align-items-center flex-grow-1" style="width: 35%;">
                                            <div class="rounded bg-secondary me-3 d-flex align-items-center justify-content-center overflow-hidden shadow"
                                                style="width: 50px; height: 70px; flex-shrink: 0;">
                                                <?php if (!empty($anime['cover'])) : ?>
                                                    <img src="<?php echo htmlspecialchars($anime['cover']); ?>" alt="Anime Cover"
                                                        class="img-fluid">
                                                <?php else : ?>
                                                    <i class="fas fa-image text-dark fa-lg"></i>
                                                <?php endif ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold text-white"><?php echo htmlspecialchars($anime['title']); ?>
                                                </h6>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <?php
                                                    if (!empty($anime['genres'])) {
                                                        $genreList = explode(',', $anime['genres']);
                                                        foreach ($genreList as $genre) {
                                                            echo '<span class="badge bg-secondary text-light ultra-small">' . htmlspecialchars(trim($genre)) . '</span>';
                                                        }
                                                    } else {
                                                        echo '<span class="badge bg-secondary text-light ultra-small">No Genre</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center text-muted small px-2" style="width: 15%;">
                                            <span class="d-block text-white-50">Studio</span>
                                            <span
                                                class="text-white"><?php echo htmlspecialchars($anime['studio'] ?? 'unknown'); ?></span>
                                        </div>

                                        <div class="text-center text-muted small px-2" style="width: 10%;">
                                            <span class="d-block text-white-50">Release</span>
                                            <span
                                                class="text-white"><?php echo htmlspecialchars($anime['release_year'] ?? 'N/A'); ?></span>
                                        </div>

                                        <div class="text-center text-muted small px-2" style="width: 10%;">
                                            <span class="d-block text-white-50">Rating</span>
                                            <span class="text-warning fw-bold"><i
                                                    class="fas fa-star fa-xs me-1"></i><?php echo htmlspecialchars($anime['my_rating']); ?></span>
                                        </div>

                                        <div class="text-center px-2" style="width: 15%;">
                                            <span class="d-block text-muted small text-white-50">Progress</span>
                                            <small class="d-block text-white-50 mt-1">
                                                <span><?php echo htmlspecialchars($anime['current_episode']); ?></span> /
                                                <span><?php echo htmlspecialchars($anime['total_episodes']); ?></span> eps
                                            </small>
                                        </div>

                                        <div class="text-end px-3" style="width: 15%;">
                                            <button type="button" class="btn btn-sm btn-outline-light me-1 edit-anime-btn"
                                                data-bs-toggle="modal" data-bs-target="#editAnime" data-id="<?php echo $anime['id']; ?>"
                                                data-title="<?php echo htmlspecialchars($anime['title']); ?>"
                                                data-studio="<?php echo htmlspecialchars($anime['studio'] ?? ''); ?>"
                                                data-type="<?php echo $anime['anime_type']; ?>"
                                                data-current="<?php echo $anime['current_episode']; ?>"
                                                data-total="<?php echo $anime['total_episodes']; ?>"
                                                data-release="<?php echo $anime['release_year'] ?? ''; ?>"
                                                data-rating="<?php echo $anime['my_rating'] ?? ''; ?>"
                                                data-status="<?php echo $anime['watch_status']; ?>"
                                                data-cover="<?php echo htmlspecialchars($anime['cover'] ?? ''); ?>" title="Edit Entry">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="delete_anime.php?id=<?= $anime['id']; ?>"
                                                onclick="deleteAnime(<?php echo $anime['id']; ?>)" class="btn btn-sm btn-outline-danger"
                                                title="Delete Entry"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </div>
                    <?php
                                }
                            }
                        } catch (PDOException $e) {
                            echo '<div class="text-center text-danger p-5"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><p>Database Query Error: ' . htmlspecialchars($e->getMessage()) . '</p></div>';
                        }
                    } else {
                        echo '<div class="text-center text-danger p-5"><p>Database connection missing or failed.</p></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Add modal -->
    <div class="modal fade" tabindex="-1" id="addAnime" aria-labelledby="addAnimeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg text-dark">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-light" id="addAnimeLabel">Add new anime</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form method="POST" action="insert_anime.php">
                    <div class="modal-body row g-3">

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-dark text-white custom-form-input"
                                    id="animeTitle" placeholder="Anime Title" name="title" required>
                                <label for="animeTitle" class="text-white-50">Anime Title</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-dark text-white custom-form-input"
                                    id="animeStudio" placeholder="Studio" name="studio">
                                <label for="animeStudio" class="text-white-50">Studio</label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <select id="watchlistStatus" class="form-select bg-dark text-white custom-form-input"
                                    name="anime_type">
                                    <option value="TV" selected>TV</option>
                                    <option value="Movie">Movie</option>
                                    <option value="OVA">OVA</option>
                                    <option value="Special">Special</option>
                                </select>
                                <label for="watchlistStatus" class="text-white-50">Type</label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label text-white-50 small fw-semibold mb-2">Genres</label>
                            <div class="p-3 rounded bg-dark border border-secondary">
                                <div class="row g-2">
                                    <?php
                                    if (isset($pdo) && $pdo !== null) {
                                        try {
                                            $genreStmt = $pdo->query('SELECT id, name FROM genres ORDER BY name ASC');
                                            while ($genre = $genreStmt->fetch()) {
                                    ?>
                                                <div class="col-6 col-sm-4">
                                                    <div class="form-check custom-genre-check">
                                                        <input class="form-check-input" type="checkbox" name="genres[]"
                                                            value="<?php echo $genre['id']; ?>"
                                                            id="genre_<?php echo $genre['id']; ?>">
                                                        <label class="form-check-label text-white small"
                                                            for="genre_<?php echo $genre['id']; ?>"><?php echo htmlspecialchars($genre['name']); ?></label>
                                                    </div>
                                                </div>
                                    <?php
                                            }
                                        } catch (PDOException $e) {
                                            echo "<div class='col-12 text-danger small'>Error fetching genres.</div>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control bg-dark text-white custom-form-input"
                                    id="currentEpisode" placeholder="0" name="current_episode" value="0" min="0">
                                <label for="currentEpisode" class="text-white-50">Current Episodes</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control bg-dark text-white custom-form-input"
                                    id="animeEpisodes" placeholder="0" name="total_episodes" value="0" min="0">
                                <label for="animeEpisodes" class="text-white-50">Total Episodes</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control bg-dark text-white custom-form-input"
                                    id="releaseYear" placeholder="Release Year" name="release_year" min="1900"
                                    max="2100">
                                <label for="releaseYear" class="text-white-50">Release Year</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" step="0.1"
                                    class="form-control bg-dark text-white custom-form-input" id="animeScore"
                                    placeholder="Score / Rating" name="my_rating" min="0" max="10">
                                <label for="animeScore" class="text-white-50">Score / Rating</label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <select id="watchStatus" class="form-select bg-dark text-white custom-form-input"
                                    name="watch_status">
                                    <option value="Watching" selected>Watching</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Plan to Watch">Plan to Watch</option>
                                    <option value="Dropped">Dropped</option>
                                </select>
                                <label for="watchStatus" class="text-white-50">Watch Status</label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-floating input-group">
                                <span class="input-group-text bg-dark border-secondary text-white-50">
                                    <i class="fas fa-link text-warning"></i>
                                </span>
                                <input type="url" class="form-control bg-dark text-white custom-form-input"
                                    id="animePosterUrl" name="cover" placeholder="">
                                <label for="animePosterUrl" class="text-white-50 ms-5">Image URL</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit modal -->
    <div class="modal fade" tabindex="-1" id="editAnime" aria-labelledby="editAnimeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg text-dark">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-light" id="editAnimeLabel">Edit anime</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form method="POST" action="edit_anime.php">
                    <input type="hidden" name="id" id="editId" value="">

                    <div class="modal-body row g-3">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-dark text-white custom-form-input"
                                    id="editAnimeTitle" placeholder="Anime Title" name="title" required>
                                <label for="editAnimeTitle" class="text-white-50">Anime Title</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-dark text-white custom-form-input"
                                    id="editAnimeStudio" placeholder="Studio" name="studio">
                                <label for="editAnimeStudio" class="text-white-50">Studio</label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <select id="editAnimeType" class="form-select bg-dark text-white custom-form-input"
                                    name="anime_type">
                                    <option value="TV">TV</option>
                                    <option value="Movie">Movie</option>
                                    <option value="OVA">OVA</option>
                                    <option value="Special">Special</option>
                                </select>
                                <label for="editAnimeType" class="text-white-50">Type</label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label text-white-50 small fw-semibold mb-2">Genres</label>
                            <div class="p-3 rounded bg-dark border border-secondary">
                                <div class="row g-2">
                                    <?php
                                    if (isset($pdo) && $pdo !== null) {
                                        try {
                                            $genreStmt = $pdo->query('SELECT id, name FROM genres ORDER BY name ASC');
                                            while ($genre = $genreStmt->fetch()) {
                                    ?>
                                                <div class="col-6 col-sm-4">
                                                    <div class="form-check custom-genre-check">
                                                        <input class="form-check-input" type="checkbox" name="genres[]"
                                                            value="<?php echo $genre['id']; ?>"
                                                            id="genre_<?php echo $genre['id']; ?>">
                                                        <label class="form-check-label text-white small"
                                                            for="genre_<?php echo $genre['id']; ?>"><?php echo htmlspecialchars($genre['name']); ?></label>
                                                    </div>
                                                </div>
                                    <?php
                                            }
                                        } catch (PDOException $e) {
                                            echo "<div class='col-12 text-danger small'>Error fetching genres.</div>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control bg-dark text-white custom-form-input"
                                    id="editCurrentEpisode" placeholder="0" name="current_episode" min="0">
                                <label for="editCurrentEpisode" class="text-white-50">Current Episodes</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control bg-dark text-white custom-form-input"
                                    id="editTotalEpisodes" placeholder="0" name="total_episodes" min="0">
                                <label for="editTotalEpisodes" class="text-white-50">Total Episodes</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control bg-dark text-white custom-form-input"
                                    id="editReleaseYear" placeholder="Release Year" name="release_year" min="1900"
                                    max="2100">
                                <label for="editReleaseYear" class="text-white-50">Release Year</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="number" step="0.1"
                                    class="form-control bg-dark text-white custom-form-input" id="editAnimeScore"
                                    placeholder="Score / Rating" name="my_rating" min="0" max="10">
                                <label for="editAnimeScore" class="text-white-50">Score / Rating</label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-floating">
                                <select id="editWatchStatus" class="form-select bg-dark text-white custom-form-input"
                                    name="watch_status">
                                    <option value="Watching">Watching</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Plan to Watch">Plan to Watch</option>
                                    <option value="Dropped">Dropped</option>
                                </select>
                                <label for="editWatchStatus" class="text-white-50">Watch Status</label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-floating input-group">
                                <span class="input-group-text bg-dark border-secondary text-white-50">
                                    <i class="fas fa-link text-warning"></i>
                                </span>
                                <input type="url" class="form-control bg-dark text-white custom-form-input"
                                    id="editAnimePosterUrl" name="cover" placeholder="">
                                <label for="editAnimePosterUrl" class="text-white-50 ms-5">Image URL</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer style="background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%); color: #000;">
        <?php include '../component/footer.php' ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script>
        function deleteAnime(id) {
            if (!confirm("Delete this anime?")) return;

            fetch("index.php?delete=" + id)
                .then(response => response.text())
                .then(() => {
                    document.getElementById("row-" + id).remove();
                })
                .catch(err => console.error(err));
        }
    </script>

    <script src="../assets/js/main.js"></script>

</body>

</html>