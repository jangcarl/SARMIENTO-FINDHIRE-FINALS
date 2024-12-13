<?php
require_once '../core/dbConfig.php';
require_once '../core/models.php';

session_start();

if (!isset($_SESSION['role'])) {
    header("Location: ../authentication/views/login.php");
    exit;
}

if ($_SESSION['role'] === 'hr') {
    header("Location: views/hr_dashboard.php");
} else {
    header("Location: views/applicant_dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire - Redirecting...</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <div class="container">
        <h1>Redirecting...</h1>
        <p>If you are not redirected automatically, <a href="views/login.php">click here</a>.</p>
    </div>
</body>
</html>
