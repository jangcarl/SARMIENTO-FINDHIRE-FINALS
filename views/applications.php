<?php
require_once '../core/dbConfig.php';
require_once '../core/models.php';

session_start();

if ($_SESSION['role'] !== 'HR') {
    echo "Access Denied.";
    exit;
}

$applications = getApplicationsByHR($pdo, $_SESSION['user']['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications - FindHire</title>
    <link rel="stylesheet" href="../styles/applications.css">
</head>
<body>
    <div class="container">
        <h1>Applications</h1>
        
        <?php foreach ($applications as $app): ?>
            <div class="application">
                <h3>Job: <?= htmlspecialchars($app['title']); ?></h3>
                <p>Applicant: <?= htmlspecialchars($app['applicant_name']); ?></p>
                <p>Status: <?= htmlspecialchars($app['status']); ?></p>
                <p>Cover Letter: <?= htmlspecialchars($app['cover_letter']); ?></p>
                <a href="../resumes/<?= htmlspecialchars($app['resume']); ?>" download>Download Resume</a>
                <form action="../core/handleforms.php" method="POST">
                    <input type="hidden" name="application_id" value="<?= $app['application_id']; ?>">
                    <select name="status">
                        <option value="accepted">Accept</option>
                        <option value="rejected">Reject</option>
                    </select>
                    <button type="submit" name="updateApplicationStatus">Update</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
