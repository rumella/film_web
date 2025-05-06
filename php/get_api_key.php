<?php
header('Content-Type: application/json');
$apiKey = trim(file_get_contents('../api_key.txt'));
echo json_encode(['apiKey' => $apiKey]);
