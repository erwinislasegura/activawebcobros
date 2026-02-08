<?php
$queryString = $_SERVER['QUERY_STRING'] ?? '';
$destination = 'eventos-autoridades-nueva.php';
if ($queryString !== '') {
    $destination .= '?' . $queryString;
}

header('Location: ' . $destination, true, 302);
exit;
