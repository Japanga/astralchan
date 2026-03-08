<?php
function handlePostUpload() {
    $id = time() . mt_rand(100, 999); // Unique numeric ID
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $tripcode = filter_input(INPUT_POST, 'content3', FILTER_SANITIZE_STRING);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $timestamp = date('Y-m-d H:i:s');
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $fileSize = $_FILES['image']['size'];
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    // Ensure uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate a unique name for the image to prevent overwrites and security issues
    $uniqueFileName = $id . '.' . $imageFileType;
    $targetFilePath = $uploadDir . $uniqueFileName;

    // Validate and move file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        $newPost = [
            'id' => $id,
            'description' => $description,
            'username' => $username,
            'title' => $title,
            'tripcode' => $tripcode,
            'image_path' => $targetFilePath,
            'timestamp' => $timestamp,
            'file_size' => $fileSize
        ];

        // Read existing posts, add new one, and save back to JSON file
        $posts = file_exists('posts.json') ? json_decode(file_get_contents('posts.json'), true) : [];
        $posts[] = $newPost;
        file_put_contents('posts.json', json_encode($posts, JSON_PRETTY_PRINT));

        // Create a new replies file for this thread
        $repliesDir = 'replies/';
        if (!is_dir($repliesDir)) {
            mkdir($repliesDir, 0755, true);
        }
        file_put_contents($repliesDir . $id . '.json', json_encode([])); // Initialize with empty array

    } else {
        echo "Error uploading file.";
    }
}
?>