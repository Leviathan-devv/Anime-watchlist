<?php
include '../connection/connection.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $type = $_POST['anime_type'] ?? 'TV';
    $status = $_POST['watch_status'] ?? 'Plan to Watch';
    $current_ep = intval($_POST['current_episode'] ?? '');
    $total_ep = intval($_POST['total_episodes'] ?? '');
    $rating = !empty($_POST['my_rating']) ? floatval($_POST['my_rating']) : null;
    $release_year = !empty($_POST['release_year']) ? floatval($_POST['release_year']) : null;
    $studio = trim($_POST['studio'] ?? '');
    $cover = trim($_POST['cover'] ?? '');
    $genres = $_POST['genres'] ?? [];

    if (empty($title)) {
        echo "Title is required.";
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('INSERT INTO anime (title, anime_type, watch_status, current_episode, total_episodes, my_rating, release_year, studio, cover) 
        VALUES (:title, :anime_type, :watch_status, :current_episode, :total_episodes, :my_rating, :release_year, :studio, :cover)');

        $stmt->execute(
            [
                ':title' => $title,
                ':anime_type' => $type,
                ':watch_status' => $status,
                ':current_episode' => $current_ep,
                ':total_episodes' => $total_ep,
                ':my_rating' => $rating,
                ':release_year' => $release_year,
                ':studio' => $studio,
                ':cover' => $cover
            ]
        );

        $anime_id = $pdo->lastInsertId();

        if (!empty($genres) && is_array($genres)) {
            $stmt_genres = $pdo->prepare('INSERT INTO anime_genres (anime_id, genre_id) VALUES (:anime_id, :genre_id)');
            foreach ($genres as $genre) {
                $stmt_genres->execute([':anime_id' => $anime_id, ':genre_id' => $genre]);
            }
        }

        $pdo->commit();

        header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
