<?php
require_once 'db.php';

$sessionPath = __DIR__ . '/data/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);
session_start();

function clean_input($value) {
    return trim(htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'));
}

function get_users() {
    $statement = db()->query('SELECT * FROM users ORDER BY id');
    return $statement->fetchAll();
}

function get_jobs() {
    $statement = db()->query('SELECT * FROM jobs ORDER BY id');
    return $statement->fetchAll();
}

function get_applications() {
    $statement = db()->query(
        "SELECT
            id,
            job_id,
            seeker_id,
            name,
            email,
            phone,
            education,
            skills,
            message,
            status,
            COALESCE(DATE_FORMAT(interview_date, '%Y-%m-%d'), '') AS interview_date,
            COALESCE(TIME_FORMAT(interview_time, '%H:%i'), '') AS interview_time,
            COALESCE(interview_mode, '') AS interview_mode,
            COALESCE(interview_note, '') AS interview_note,
            DATE_FORMAT(applied_at, '%Y-%m-%d %H:%i:%s') AS applied_at
        FROM applications
        ORDER BY id"
    );

    return $statement->fetchAll();
}

function find_user($user_id) {
    $statement = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $statement->execute([(int) $user_id]);
    $user = $statement->fetch();

    return $user ?: null;
}

function find_job($job_id) {
    $statement = db()->prepare('SELECT * FROM jobs WHERE id = ? LIMIT 1');
    $statement->execute([(int) $job_id]);
    $job = $statement->fetch();

    return $job ?: null;
}

function current_user() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return find_user($_SESSION['user_id']);
}

function is_logged_in() {
    return current_user() !== null;
}

function is_seeker() {
    $user = current_user();
    return $user && $user['role'] === 'seeker';
}

function is_giver() {
    $user = current_user();
    return $user && $user['role'] === 'giver';
}

function user_email_exists($email) {
    $statement = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $statement->execute([clean_input($email)]);

    return (bool) $statement->fetch();
}

function register_user($post) {
    $name = clean_input($post['name'] ?? '');
    $email = clean_input($post['email'] ?? '');
    $password = clean_input($post['password'] ?? '');
    $confirmPassword = clean_input($post['confirm_password'] ?? '');
    $role = clean_input($post['role'] ?? 'seeker');

    if (!$name || !$email || !$password || !$confirmPassword || !in_array($role, ['seeker', 'giver'], true)) {
        return ['success' => false, 'message' => 'Please fill all registration details.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Please enter a valid email address.'];
    }

    if ($password !== $confirmPassword) {
        return ['success' => false, 'message' => 'Passwords do not match.'];
    }

    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters.'];
    }

    if (user_email_exists($email)) {
        return ['success' => false, 'message' => 'This email already exists. Please login instead.'];
    }

    $statement = db()->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
    $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);

    $_SESSION['user_id'] = db()->lastInsertId();

    return ['success' => true, 'role' => $role, 'message' => 'Registration successful.'];
}

function login_user($email, $password, $role) {
    $statement = db()->prepare('SELECT * FROM users WHERE email = ? AND role = ? LIMIT 1');
    $statement->execute([clean_input($email), $role]);
    $user = $statement->fetch();
    $enteredPassword = clean_input($password);

    if ($user && (password_verify($enteredPassword, $user['password']) || $enteredPassword === $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return true;
    }

    return false;
}

function require_role($role) {
    $user = current_user();

    if (!$user || $user['role'] !== $role) {
        header('Location: login.php?role=' . $role);
        exit;
    }
}

function dashboard_link() {
    if (is_seeker()) {
        return 'seeker_dashboard.php';
    }
    if (is_giver()) {
        return 'giver_dashboard.php';
    }

    return 'login.php';
}

function add_job($post) {
    if (!is_giver()) {
        return false;
    }

    $user = current_user();
    $statement = db()->prepare(
        'INSERT INTO jobs (giver_id, title, company, location, type, salary, skills, description)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );

    return $statement->execute([
        (int) $user['id'],
        clean_input($post['title']),
        clean_input($post['company']),
        clean_input($post['location']),
        clean_input($post['type']),
        clean_input($post['salary']),
        clean_input($post['skills']),
        clean_input($post['description'])
    ]);
}

function add_application($post) {
    if (!is_seeker()) {
        return false;
    }

    $user = current_user();
    $statement = db()->prepare(
        'INSERT INTO applications (job_id, seeker_id, name, email, phone, education, skills, message)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );

    return $statement->execute([
        (int) $post['job_id'],
        (int) $user['id'],
        clean_input($post['name']),
        clean_input($post['email']),
        clean_input($post['phone']),
        clean_input($post['education']),
        clean_input($post['skills']),
        clean_input($post['message'])
    ]);
}

function get_applications_for_seeker($seeker_id) {
    return array_values(array_filter(get_applications(), fn($application) => (int) $application['seeker_id'] === (int) $seeker_id));
}

function get_jobs_for_giver($giver_id) {
    $statement = db()->prepare('SELECT * FROM jobs WHERE giver_id = ? ORDER BY id');
    $statement->execute([(int) $giver_id]);

    return $statement->fetchAll();
}

function get_applications_for_giver($giver_id) {
    $statement = db()->prepare(
        "SELECT
            applications.id,
            applications.job_id,
            applications.seeker_id,
            applications.name,
            applications.email,
            applications.phone,
            applications.education,
            applications.skills,
            applications.message,
            applications.status,
            COALESCE(DATE_FORMAT(applications.interview_date, '%Y-%m-%d'), '') AS interview_date,
            COALESCE(TIME_FORMAT(applications.interview_time, '%H:%i'), '') AS interview_time,
            COALESCE(applications.interview_mode, '') AS interview_mode,
            COALESCE(applications.interview_note, '') AS interview_note,
            DATE_FORMAT(applications.applied_at, '%Y-%m-%d %H:%i:%s') AS applied_at
        FROM applications
        INNER JOIN jobs ON jobs.id = applications.job_id
        WHERE jobs.giver_id = ?
        ORDER BY applications.id"
    );
    $statement->execute([(int) $giver_id]);

    return $statement->fetchAll();
}

function schedule_interview($post) {
    if (!is_giver()) {
        return false;
    }

    $user = current_user();
    $check = db()->prepare(
        'SELECT applications.id
        FROM applications
        INNER JOIN jobs ON jobs.id = applications.job_id
        WHERE applications.id = ?
          AND jobs.giver_id = ?
        LIMIT 1'
    );
    $check->execute([(int) $post['application_id'], (int) $user['id']]);

    if (!$check->fetch()) {
        return false;
    }

    $statement = db()->prepare(
        "UPDATE applications
        SET
            status = 'Interview Scheduled',
            interview_date = ?,
            interview_time = ?,
            interview_mode = ?,
            interview_note = ?
        WHERE id = ?"
    );
    $statement->execute([
        clean_input($post['interview_date']),
        clean_input($post['interview_time']),
        clean_input($post['interview_mode']),
        clean_input($post['interview_note']),
        (int) $post['application_id']
    ]);

    return true;
}
?>
