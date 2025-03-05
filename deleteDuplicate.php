<?php
// Directory where the files are located
$directory = 'OrderBook/';

// Open the directory
if ($handle = opendir($directory)) {
    // Loop through the files in the directory
    while (false !== ($file = readdir($handle))) {
        // Check if the file ends with "(1).json"
        if (preg_match('/\(1\)\.json$/', $file)) {
            // Construct full file path
            $filePath = $directory . $file;
            // Check if the file exists and is a file (not a directory)
            if (is_file($filePath)) {
                // Delete the file
                if (unlink($filePath)) {
                    echo "Deleted: $file\n";
                } else {
                    echo "Failed to delete: $file\n";
                }
            }
        }
    }
    // Close the directory handle
    closedir($handle);
}
?>