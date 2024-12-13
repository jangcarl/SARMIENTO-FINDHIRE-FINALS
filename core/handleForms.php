<?php
require_once '../dbConfig.php';
require_once '../models.php';
session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Handle login
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $user = authenticateUser($username, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            header("Location: ../" . ($user['role'] === 'HR' ? 'views/hr_dashboard.php' : 'views/applicant_dashboard.php'));
        } else {
            header('Location: ../authentication/login.php?error=Invalid username or password');
        }
        exit;
    }

    // Handle logout
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header('Location: ../authentication/login.php');
        exit;
    }

    // Handle registration
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingUser) {
            header('Location: ../authentication/register.php?error=Username already taken');
            exit;
        }
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword,
            'role' => $role
        ]);

        $_SESSION['user'] = [
            'username' => $username,
            'role' => $role,
            'id' => $pdo->lastInsertId()
        ];

        header("Location: ../views/" . ($role === 'HR' ? 'hr_dashboard.php' : 'applicant_dashboard.php'));
        exit;
    }

    // Handle adding job post
    if (isset($_POST['add_job_post'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        addJobPost($title, $description);
        header('Location: ../views/hr_dashboard.php');
        exit;
    }

    // Handle job application
    if (isset($_POST['apply_to_job'])) {
        $applicant_id = $_SESSION['user']['id'];
        $job_post_id = $_POST['job_post_id'];
        $message = $_POST['message'];
        $resumeFileName = $_FILES['resume']['name'];
        $tempFileName = $_FILES['resume']['tmp_name'];
        $fileExtension = pathinfo($resumeFileName, PATHINFO_EXTENSION);

        $uniqueID = sha1(md5(rand(1,9999999)));
        $resumeFileNameToSave = $uniqueID . "." . $fileExtension;
        $resumeFolder = "../resumes/" . $resumeFileNameToSave;

        if (!is_dir("../resumes")) {
            mkdir("../resumes", 0777, true);
        }

        if (move_uploaded_file($tempFileName, $resumeFolder)) {
            applyToJob($applicant_id, $job_post_id, $resumeFileNameToSave, $message);
            header('Location: ../views/applicant_dashboard.php');
            exit;
        } else {
            echo "Error uploading the resume. Please try again.";
        }
    }

    // Handle updating application status
    if (isset($_POST['update_application_status'])) {
        $application_id = $_POST['application_id'];
        $status = $_POST['status'];
        updateApplicationStatus($application_id, $status);
        header('Location: ../views/hr_dashboard.php');
        exit;
    }

    // Handle sending a message
    if (isset($_POST['send_message'])) {
        $sender_id = $_SESSION['user']['id'];
        $receiver_username = $_POST['receiver_username'];
        $content = $_POST['content'];

        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $receiver_username]);
        $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($receiver) {
            $receiver_id = $receiver['id'];
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content, sent_at)
                                   VALUES (:sender_id, :receiver_id, :content, NOW())");
            $stmt->execute([
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'content' => $content
            ]);
            header('Location: ../views/messages.php');
        } else {
            echo "Error: Receiver not found!";
        }
    }
}
?>
