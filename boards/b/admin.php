<?php
// Configuration (must match index.php)
$data_file = 'posts.json';
$upload_dir = 'uploads/';
$admin_password_hash = password_hash('your_admin_password', PASSWORD_DEFAULT); // Use the same hash as in index.php

// Function to load posts
function load_posts($file) {
    if (file_exists($file)) {
        $data = file_get_contents($file);
        return json_decode($data, true) ?: [];
    }
    return [];
}

// Function to save posts
function save_posts($file, $posts) {
    file_put_contents($file, json_encode($posts, JSON_PRETTY_PRINT));
}

session_start();

// Handle login
if (isset($_POST['admin_login'])) {
    if (password_verify($_POST['password'], $admin_password_hash)) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        echo "<p style='color: red;'>Incorrect password.</p>";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}



// Check admin status
if (!isset($_SESSION['admin_logged_in'])) {
    // Show login form
    ?>
    <div class = "mainarea">
    <span style="color: white;">
    <h1>Admin Login</h1>
    <form method="POST" action="admin.php">
        Password: <input type="password" name="password">
        <input type="submit" name="admin_login" value="Login">
    </form>
     </span>
    </div>
      <style>
      body {
  /* Dark Grey Steel Gradient */
  background-color: #2c3e50; /* Fallback */
  background-image: linear-gradient(135deg, #434343 0%, #000000 100%);
  
  /* Ensure full screen coverage */
  min-height: 100vh;
  margin: 0;
  background-attachment: fixed;
}
        .mainarea {
          /* Ensure the container covers the desired area */
  width: 40%;
  height: 190px;  /* Full viewport height */
  /* Define the "steel" gradient with shades of gray/silver */
  /* Define a sharp metallic linear gradient */
  background: linear-gradient(135deg, 
    #000000 0%, 
    #434343 25%, 
    #ffffff 50%, 
    #434343 75%, 
    #000000 100%
  );
  
  /* Make background larger than container to allow movement */
  background-size: 400% 400%;
  
  /* Animate the movement */
  animation: metalGradientAnimation 10s ease infinite;
}

@keyframes metalGradientAnimation {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
 
      input {
  background-color: black; /* Or use the hex code #000000 */
  color: white; /* Change the text color for better visibility on a black background */
}
    </style>
    <?php
    exit;
}

// Admin is logged in, handle deletion
if (isset($_GET['delete_id'])) {
    $post_id_to_delete = (int)$_GET['delete_id'];
    $posts = load_posts($data_file);

    if (isset($posts[$post_id_to_delete])) {
        // Optional: delete associated image file
        if ($posts[$post_id_to_delete]['image'] && file_exists($posts[$post_id_to_delete]['image'])) {
            unlink($posts[$post_id_to_delete]['image']);
        }
        unset($posts[$post_id_to_delete]);
        
        // Also delete all replies to this post
        foreach ($posts as $id => $post) {
            if ($post['parent_id'] == $post_id_to_delete) {
                // This is a simple flat-file; a more complex system would handle nested replies better
                // For simplicity here, just delete immediate children
                if ($post['image'] && file_exists($post['image'])) {
                    unlink($post['image']);
                }
                unset($posts[$id]);
            }
        }

        save_posts($data_file, $posts);
        echo "<p>Post $post_id_to_delete and its immediate replies deleted.</p>";
    } else {
        echo "<p>Post not found.</p>";
    }
}
?>

 <div class = "mainarea2">
 <span style="color: white;">
<h1>Admin Panel</h1>
 </span>
<p><a href="index.php">View site</a> | <a href="admin.php?logout=true">Logout</a></p>
<p>Use the delete links on the main site (visible to admin when logged in) to manage posts.</p>
     </div>
    <style>
    body {
  /* Dark Grey Steel Gradient */
  background-color: #2c3e50; /* Fallback */
  background-image: linear-gradient(135deg, #434343 0%, #000000 100%);
  
  /* Ensure full screen coverage */
  min-height: 100vh;
  margin: 0;
  background-attachment: fixed;
}
        .mainarea2 {
          /* Ensure the container covers the desired area */
  width: 40%;
  height: 190px;  /* Full viewport height */
  /* Define the "steel" gradient with shades of gray/silver */
  /* Define a sharp metallic linear gradient */
  background: linear-gradient(135deg, 
    #000000 0%, 
    #434343 25%, 
    #ffffff 50%, 
    #434343 75%, 
    #000000 100%
  );
  
  /* Make background larger than container to allow movement */
  background-size: 400% 400%;
  
  /* Animate the movement */
  animation: metalGradientAnimation 10s ease infinite;
}

@keyframes metalGradientAnimation {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
 
      input {
  background-color: black; /* Or use the hex code #000000 */
  color: white; /* Change the text color for better visibility on a black background */
}
    </style>
