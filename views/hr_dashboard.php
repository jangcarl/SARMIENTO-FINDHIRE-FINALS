<?php
require_once '../core/dbConfig.php';
require_once '../core/models.php';

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'HR') {
    header('Location: ../authentication/login.php');
    exit;
}

$jobPosts = fetchJobPosts();
$applications = fetchApplications();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/hr_dashboard.css">
    <title>HR Dashboard - FindHire</title>
</head>
<body>
    <div class="container">
        <h1>HR Dashboard</h1>
        <form action="../core/handleforms.php" method="POST">
            <button type="submit" name="logout" class="logout-button">Logout</button>
        </form>

        <h2>Create Job Post</h2>
        <form action="../core/handleforms.php" method="POST">
            <label for="title">Job Title:</label>
            <input type="text" name="title" id="title" required>
            <br>
            <label for="description">Job Description:</label>
            <textarea name="description" id="description" required></textarea>
            <br>
            <button type="submit" name="add_job_post">Post Job</button>
        </form>

        <a href="../views/messages.php" class="message-link">Go to Messages</a>

        <h2>Job Applications</h2>
        <table>
            <tr>
                <th>Job Title</th>
                <th>Applicant</th>
                <th>Message</th>
                <th>Resume</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?= htmlspecialchars($application['job_title']) ?></td>
                    <td><?= htmlspecialchars($application['applicant_name']) ?></td>
                    <td><?= htmlspecialchars($application['message']) ?></td>
                    <td><a href="../resumes/<?= htmlspecialchars($application['resume_path']) ?>">Download</a></td>
                    <td><?= htmlspecialchars($application['status']) ?></td>
                    <td>
                        <form action="../core/handleforms.php" method="POST">
                            <input type="hidden" name="application_id" value="<?= $application['id'] ?>">
                            <select name="status">
                                <option value="ACCEPTED">Accept</option>
                                <option value="REJECTED">Reject</option>
                            </select>
                            <button type="submit" name="update_application_status">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
