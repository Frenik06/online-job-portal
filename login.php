<?php
require_once 'functions.php';

$role = $_GET['role'] ?? 'seeker';
if (!in_array($role, ['seeker', 'giver'], true)) {
    $role = 'seeker';
}

$error = '';
$notice = $_GET['registered'] ?? '' ? 'Account created successfully. Please login with your email and password.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? 'seeker';

    if (login_user($_POST['email'] ?? '', $_POST['password'] ?? '', $role)) {
        header('Location: ' . ($role === 'seeker' ? 'seeker_dashboard.php' : 'giver_dashboard.php'));
        exit;
    }

    $error = 'Invalid email, password, or role.';
}

$demoEmail = $role === 'seeker' ? 'seeker@example.com' : 'giver@example.com';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($role); ?> Login - CareerConnect</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo filemtime('css/style.css'); ?>">
</head>
<body class="auth-body">
    <main class="auth-page compact-auth">
        <section class="auth-panel">
            <div class="auth-header">
                <a class="logo" href="index.php">CareerConnect</a>
                <span><?php echo $role === 'seeker' ? 'Candidate login' : 'Employer login'; ?></span>
            </div>

            <div class="auth-title-row">
                <div>
                    <p class="eyebrow">Sign in</p>
                    <h1><?php echo $role === 'seeker' ? 'Find your next role' : 'Manage your hiring'; ?></h1>
                </div>
                <a class="text-link" href="index.php">Home</a>
            </div>

            <div class="role-tabs" aria-label="Choose login role">
                <a class="<?php echo $role === 'seeker' ? 'active' : ''; ?>" href="login.php?role=seeker">Job Seeker</a>
                <a class="<?php echo $role === 'giver' ? 'active' : ''; ?>" href="login.php?role=giver">Job Giver</a>
            </div>

            <?php if ($notice): ?>
                <div class="alert success-alert"><?php echo $notice; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert error-alert"><?php echo $error; ?></div>
            <?php endif; ?>

            <form class="form-card auth-form" method="POST">
                <input type="hidden" name="role" value="<?php echo $role; ?>">
                <label>Email address <input type="email" name="email" value="<?php echo $demoEmail; ?>" required></label>
                <label>Password <input type="password" name="password" value="123456" required></label>
                <button class="primary-btn" type="submit">Login</button>
            </form>

            <div class="auth-footer-row">
                <p>New here? <a href="register.php?role=<?php echo $role; ?>">Create an account</a></p>
                <p class="demo-note">Demo: <?php echo $demoEmail; ?> / 123456</p>
            </div>
        </section>
    </main>
</body>
</html>
