<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the uploaded file and action type
    $originalFile = $_FILES['file']['name'];
    $actionType = $_POST['action_type'];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($originalFile);

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        require_once 'codec.php'; // Include Huffman encoding class
        $codec = new Codec();

        if ($actionType === 'compress') {
            $data = file_get_contents($targetFile);
            list($compressedData, $message) = $codec->encode($data);
            $compressedFile = $targetDir . "compressed_" . $originalFile;
            file_put_contents($compressedFile, $compressedData);
            echo "File compressed and saved as: " . $compressedFile;
        } elseif ($actionType === 'decompress') {
            $data = file_get_contents($targetFile);
            list($decompressedData, $message) = $codec->decode($data);
            $decompressedFile = $targetDir . "decompressed_" . $originalFile;
            file_put_contents($decompressedFile, $decompressedData);
            echo "File decompressed and saved as: " . $decompressedFile;
        }
    } else {
        echo "Error uploading file.";
    }
}
