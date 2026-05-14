<?php
require_once 'functions.php';

$role = $_GET['role'] ?? 'seeker';
if (!in_array($role, ['seeker', 'giver'], true)) {
    $role = 'seeker';
}

$error = '';
$old = [
    'name' => '',
    'email' => '',
    'role' => $role
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name'] = clean_input($_POST['name'] ?? '');
    $old['email'] = clean_input($_POST['email'] ?? '');
    $old['role'] = clean_input($_POST['role'] ?? 'seeker');
    $result = register_user($_POST);

    if ($result['success']) {
        header('Location: ' . ($result['role'] === 'seeker' ? 'seeker_dashboard.php' : 'giver_dashboard.php'));
        exit;
    }

    $error = $result['message'];
    $role = in_array($old['role'], ['seeker', 'giver'], true) ? $old['role'] : 'seeker';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - CareerConnect</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo filemtime('css/style.css'); ?>">
</head>
<body class="auth-body">
    <main class="auth-page compact-auth">
        <section class="auth-panel register-panel">
            <div class="auth-header">
                <a class="logo" href="index.php">CareerConnect</a>
                <span>New account</span>
            </div>

            <div class="auth-title-row">
                <div>
                    <p class="eyebrow">Register</p>
                    <h1>Create your account</h1>
                    <p class="small-copy">Choose the correct role. Your email must be unique and will be used for login.</p>
                </div>
                <a class="text-link" href="login.php?role=<?php echo $role; ?>">Login</a>
            </div>

            <div class="role-tabs" aria-label="Choose account role">
                <a class="<?php echo $role === 'seeker' ? 'active' : ''; ?>" href="register.php?role=seeker">Job Seeker</a>
                <a class="<?php echo $role === 'giver' ? 'active' : ''; ?>" href="register.php?role=giver">Job Giver</a>
            </div>

            <?php if ($error): ?>
                <div class="alert error-alert">
                    <?php echo $error; ?>
                    <?php if (strpos($error, 'already exists') !== false): ?>
                        <a href="login.php?role=<?php echo $role; ?>">Login here</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form class="form-card auth-form" method="POST">
                <input type="hidden" name="role" value="<?php echo $role; ?>">

                <div class="two-fields">
                    <label>Full name <input name="name" value="<?php echo $old['name']; ?>" placeholder="Your name" required></label>
                    <label>Email address <input type="email" name="email" value="<?php echo $old['email']; ?>" placeholder="you@example.com" required></label>
                </div>

                <div class="two-fields">
                    <label>Password <input type="password" name="password" minlength="6" placeholder="Minimum 6 characters" required></label>
                    <label>Confirm password <input type="password" name="confirm_password" minlength="6" placeholder="Repeat password" required></label>
                </div>

                <button class="primary-btn" type="submit">Create <?php echo $role === 'seeker' ? 'seeker' : 'giver'; ?> account</button>
            </form>

            <div class="auth-footer-row">
                <p>Already registered? <a href="login.php?role=<?php echo $role; ?>">Login instead</a></p>
                <p class="demo-note">Use seeker for applying, giver for posting jobs.</p>
            </div>
        </section>
    </main>
</body>
</html>
