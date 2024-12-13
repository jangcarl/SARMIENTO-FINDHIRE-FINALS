<?php
require_once '../core/dbConfig.php';

function authenticateUser($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

function registerUser($username, $password, $role) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        return false; 
    }
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword,
        'role' => $role
    ]);

    return $pdo->lastInsertId();
}

function fetchJobPosts() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM job_posts");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchApplications() {
  global $pdo;
  $stmt = $pdo->prepare("
      SELECT 
          applications.*, 
          job_posts.title AS job_title, 
          users.username AS applicant_name
      FROM 
          applications
      JOIN 
          job_posts ON applications.job_post_id = job_posts.id
      JOIN 
          users ON applications.applicant_id = users.id
  ");
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchMessages($user_id) {
  global $pdo;
  $stmt = $pdo->prepare("
      SELECT m.*, u1.username AS sender_name, u2.username AS receiver_name
      FROM messages m
      JOIN users u1 ON m.sender_id = u1.id
      JOIN users u2 ON m.receiver_id = u2.id
      WHERE m.sender_id = :user_id OR m.receiver_id = :user_id
      ORDER BY m.sent_at DESC
  ");
  $stmt->execute(['user_id' => $user_id]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function sendMessage($sender_id, $receiver_username, $content) {
  global $pdo;
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
  } else {
      echo "User not found!";
  }
}




function addJobPost($title, $description) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO job_posts (title, description) VALUES (:title, :description)");
    $stmt->execute([
        'title' => $title,
        'description' => $description
    ]);
}

function applyToJob($applicant_id, $job_post_id, $resume_path, $message) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO applications (applicant_id, job_post_id, resume_path, message, status) VALUES (:applicant_id, :job_post_id, :resume_path, :message, 'PENDING')");
    $stmt->execute([
        'applicant_id' => $applicant_id,
        'job_post_id' => $job_post_id,
        'resume_path' => $resume_path,
        'message' => $message
    ]);
}

function updateApplicationStatus($application_id, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE applications SET status = :status WHERE id = :application_id");
    $stmt->execute([
        'status' => $status,
        'application_id' => $application_id
    ]);
}

function getApplicationDetails($application_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM applications JOIN job_posts ON applications.job_post_id = job_posts.id WHERE applications.id = :application_id");
    $stmt->execute(['application_id' => $application_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserDetails($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
