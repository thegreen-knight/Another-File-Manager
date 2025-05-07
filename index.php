<?php
/**
 * Copyright (c) 2025 Josh, The Green Knight
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * GitHub: https://github.com/thegreen-knight
 * Support: https://buymeacoffee.com/gr33nknight
 */

//error_reporting(E_ALL);

//ini_set('display_errors', '1');
// File management actions
$dirName = dirname(__FILE__);
$current_dir = '.';
if (isset($_GET['cd'])) {
    $current_dir .= '/' . trim($_GET['cd'], '/');
}

// Helper function to sanitize input
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["uploadedFile"])) {
    if ($_FILES["uploadedFile"]["error"] == 0) {
        $filename = basename($_FILES["uploadedFile"]["name"]);
        move_uploaded_file($_FILES["uploadedFile"]["tmp_name"], "$current_dir/$filename");
        echo "File uploaded successfully.<br>";
    } else {
        echo "Error uploading file.<br>";
    }
}

// Handle file deletion
if (isset($_POST['delete']) && $_POST['delete'] === 'Delete') {
    $file_to_delete = $current_dir . '/' . sanitize_input($_POST['filename']);
    if (file_exists($file_to_delete)) {
        unlink($file_to_delete);
        echo "File deleted successfully.<br>";
    } else {
        echo "File does not exist.<br>";
    }
}

// Handle file rename
if (isset($_POST['rename']) && $_POST['rename'] === 'Rename') {
    $old_name = $current_dir . '/' . sanitize_input($_POST['oldname']);
    $new_name = $current_dir . '/' . sanitize_input($_POST['newname']);
    if (file_exists($old_name)) {
        rename($old_name, $new_name);
        echo "File renamed successfully.<br>";
    } else {
        echo "File does not exist.<br>";
    }
}

if (isset($_POST['createFolder']) && $_POST['createFolder'] === 'Create') {
    $newDir = $current_dir . '/' . sanitize_input($_POST['fileName']);
    if (!is_dir($newDir)) {
        if (mkdir($newDir)) {
            echo "Directory created successfully.<br>";
        } else {
            echo "Failed to create directory.<br>";
        }
    } else {
        echo "Directory already exists.<br>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #333; /* Dark grey background */
        color: #ccc; /* Light grey text color */
        padding: 20px;
        margin: 0;
    }

    table {
        width: 100%;
        margin-bottom: 20px;
        border: 1px solid #444; /* Darker grey border for table */
    }

    table, th, td {
        border-collapse: collapse;
        padding: 8px;
        text-align: left;
        background-color: #222; /* Very dark grey for table cells */
        border: 1px solid #444; /* Darker grey borders */
    }

    input[type="text"], input[type="file"] {
        width: 250px;
        padding: 8px;
        margin-top: 5px;
        margin-bottom: 10px;
        border: 1px solid #555; /* Medium dark grey border */
        background-color: #222; /* Very dark grey background */
        color: #ddd; /* Light grey text color */
        box-sizing: border-box; /* Include padding and border in the element's width and height */
    }

    input[type="submit"] {
        background-color: #555; /* Medium dark grey background */
        color: #fff; /* White text for contrast */
        border: none;
        padding: 10px 20px;
        text-transform: uppercase;
        cursor: pointer;
        margin-top: 5px;
    }

    input[type="submit"]:hover {
        background-color: #666; /* Slightly lighter grey for hover */
    }

    ul {
        list-style-type: none;
        padding: 0;
    }

    li {
        padding: 8px;
        background-color: #222; /* Very dark grey for list items */
        border-bottom: 1px solid #444; /* Darker grey border */
        color: #ddd; /* Light grey text color */
    }

    li:last-child {
        border-bottom: none;
    }

    form {
        display: inline;
    }
    a { /* New style for links */
        color: #0f0; /* Bright green */
        text-decoration: none; /* No underline */
    }

    a:hover { /* Style for hovering over links */
        color: #0c0; /* Slightly darker green */
        text-decoration: underline; /* Underline on hover for emphasis */
    }
    footer {
        text-align: center;
    }
</style>


</head>
<body>
<a href="../">Go Up a Directory</a>
    <h2>Files in: <?php echo $dirName; ?></h2>
    <table>
    <tr>
        <td>
        <h2>Create Folder</h2>
        <form method="post">
        <input type="text" name="fileName">
        <input type="submit" name="createFolder" value="Create">
        </form>
        </td>
        <td>
            <h2>Upload a file</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="uploadedFile">
        <input type="submit" value="Upload File">
    </form>
        </td>
    </tr>
    </table>
    <ul>
        <?php
        $files = scandir($current_dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'index.php') continue;
            echo '<li>' . htmlspecialchars($file);
            if (is_dir("$current_dir/$file")) {
                echo " (dir) - <a href='?cd=$current_dir/$file'>Change Directory</a>";
            } else {
                echo " - <a href='$current_dir/$file' target='_blank'>View</a>"; // Open file in a new tab
                echo " - <a href='$current_dir/$file' download>Download</a>";
                echo " - <form style='display:inline;' method='post'>
                    <input type='hidden' name='filename' value='$file'>
                    <input type='submit' name='delete' value='Delete'>
                </form>";
                echo " - <form style='display:inline;' method='post'>
                    <input type='hidden' name='oldname' value='$file'>
                    <input type='text' name='newname' required>
                    <input type='submit' name='rename' value='Rename'>
                </form>";
            }
            echo '</li>';
        }
        ?>
    </ul>

    <footer>
        <script type="text/javascript" src="https://cdnjs.buymeacoffee.com/1.0.0/button.prod.min.js" data-name="bmc-button" data-slug="gr33nknight" data-color="#FFDD00" data-emoji=""  data-font="Cookie" data-text="Buy me a coffee" data-outline-color="#000000" data-font-color="#000000" data-coffee-color="#ffffff" ></script>
        <h4>Created by The Green Knight</h4>
        <a href="https://greenknightdigital.com" target="_blank">My Website</a>
        <a href="https://github.com/thegreen-knight" target="_blank">My Github</a>
        <a href="https://github.com/thegreen-knight/Another-File-Manager" target="_blank">Code Repository</a>
    </footer>

</body>
</html>
