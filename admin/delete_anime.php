<?php
include '../connection/connection.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $anime_id = intval($_GET['id']);

    try {
        $sql = "DELETE FROM anime WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $anime_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: index.php?status=deleted");
            exit();
        } else {
            echo "Oops! Something went wrong trying to delete the item.";
        }
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit();
}
