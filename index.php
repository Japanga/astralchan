<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AstralChan</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; }
        .board-list a { display: block; padding: 5px 0; }
    </style>
</head>
<body>
    <h1>Content Index</h1>
    <p>Welcome to the index page. Below are the available boards:</p>

<div class="content-box">
    <h2>Boards</h2>
     <ul class="board-list">
        <?php
        $boardsDir = 'boards';
        // Get all directories inside the 'boards' folder
        $folders = glob($boardsDir . '/*' , GLOB_ONLYDIR);

        foreach ($folders as $folder) {
            $folderName = basename($folder);
            $titleFile = $folder . '/title.txt';
            $title = $folderName; // Default title

            // Fetch title from title.txt if it exists
            if (file_exists($titleFile)) {
                $title = file_get_contents($titleFile);
            }

            echo "<li><a href='{$folder}/'>{$title}</a></li>";
        }
        ?>
    </ul>
</div>
    
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; }
        .board-list {
            width: 80%;
            margin: 0 auto;
            border: 1px solid #AAA;
            background-color: #F0E0D6;
            padding: 10px;
        }
       body {
            background-color: #FFFFEE;
            color: #800000;
            font-family: arial,helvetica,sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
          /* Content Element */
.content-box {
    max-width: 800px;
    margin: 0 auto;
    background: #FFF;
    border: 1px solid #CCC;
    padding: 10px;
}

/* Board List - Top to Bottom */
.board-list {
    list-style-type: none; /* Remove bullets [7] */
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column; /* Stack vertically [2] */
}

.board-list li {
    padding: 5px;
    border-bottom: 1px solid #EEE;
}

.board-list a {
    text-decoration: none;
    color: #0000EE;
}

/* Hover Effect [7] */
.board-list a:hover {
    color: #AF0A0F;
    background-color: #EEEEEE;
}

    </style>
    
</body>
</html>