<?php
require_once 'functions.php';

$message = '';
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'apply') {
    if (add_application($_POST)) {
        $message = 'Application submitted successfully. Check your seeker dashboard for status updates.';
    } else {
        $message = 'Please login as a job seeker before applying.';
    }
}

$jobs = get_jobs();
$applications = get_applications();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareerConnect - Online Job Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="hero">
        <nav class="navbar">
            <a class="logo" href="index.php">CareerConnect</a>
            <div class="nav-links">
                <a href="#jobs">Jobs</a>
                <?php if ($user): ?>
                    <a href="<?php echo dashboard_link(); ?>">Dashboard</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php?role=seeker">Seeker Login</a>
                    <a href="login.php?role=giver">Giver Login</a>
                    <a href="register.php?role=seeker">Register</a>
                <?php endif; ?>
            </div>
        </nav>

        <section class="hero-content">
            <div>
                <p class="eyebrow">Online Job Portal Prototype</p>
                <h1>Two-side job portal for seekers and job givers.</h1>
                <p class="hero-text">Job seekers can apply and track applications. Job givers can post jobs, view candidates, and schedule interviews.</p>
                <a class="primary-btn" href="#jobs">Explore Jobs</a>
                <?php if (!$user): ?>
                    <a class="secondary-btn" href="login.php?role=seeker">Login as Seeker</a>
                    <a class="secondary-btn" href="register.php?role=seeker">Register as Seeker</a>
                    <a class="secondary-btn" href="register.php?role=giver">Register as Giver</a>
                <?php endif; ?>
            </div>
            <div class="hero-card">
                <span><?php echo count($jobs); ?></span>
                <p>Active job openings</p>
                <span><?php echo count($applications); ?></span>
                <p>Applications received</p>
            </div>
        </section>
    </header>

    <main>
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <section class="panel" id="jobs">
            <div class="section-heading">
                <p class="eyebrow">Job Listing Module</p>
                <h2>Available Jobs</h2>
            </div>

            <div class="filters">
                <input type="search" id="searchInput" placeholder="Search by title, company, location, or skills">
                <select id="typeFilter">
                    <option value="all">All Job Types</option>
                    <option value="Full Time">Full Time</option>
                    <option value="Part Time">Part Time</option>
                    <option value="Internship">Internship</option>
                </select>
            </div>

            <div class="job-grid" id="jobGrid">
                <?php foreach ($jobs as $job): ?>
                    <article class="job-card" data-search="<?php echo strtolower($job['title'] . ' ' . $job['company'] . ' ' . $job['location'] . ' ' . $job['skills']); ?>" data-type="<?php echo $job['type']; ?>">
                        <div class="job-topline">
                            <span class="job-type"><?php echo $job['type']; ?></span>
                            <span><?php echo $job['location']; ?></span>
                        </div>
                        <h3><?php echo $job['title']; ?></h3>
                        <p class="company"><?php echo $job['company']; ?></p>
                        <p><?php echo $job['description']; ?></p>
                        <p><strong>Skills:</strong> <?php echo $job['skills']; ?></p>
                        <p><strong>Salary:</strong> <?php echo $job['salary']; ?></p>
                        <?php if (is_seeker()): ?>
                            <button class="secondary-btn apply-button" data-job-id="<?php echo $job['id']; ?>" data-job-title="<?php echo $job['title']; ?>">Apply Now</button>
                        <?php elseif (is_giver()): ?>
                            <a class="secondary-btn" href="giver_dashboard.php">Manage Jobs</a>
                        <?php else: ?>
                            <a class="secondary-btn" href="login.php?role=seeker">Login to Apply</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <dialog id="applyDialog">
        <form method="POST" class="form-card modal-form">
            <button type="button" class="close-button" id="closeDialog">x</button>
            <p class="eyebrow">Candidate Application</p>
            <h2 id="modalJobTitle">Apply for Job</h2>
            <input type="hidden" name="action" value="apply">
            <input type="hidden" name="job_id" id="jobIdField">
            <label>Full Name <input name="name" value="<?php echo $user ? $user['name'] : ''; ?>" required></label>
            <label>Email <input type="email" name="email" value="<?php echo $user ? $user['email'] : ''; ?>" required></label>
            <label>Phone <input name="phone" required></label>
            <label>Education <input name="education" placeholder="BCA, B.Tech, MBA" required></label>
            <label>Your Skills <input name="skills" required></label>
            <label>Message <textarea name="message" rows="3" placeholder="Why are you suitable for this role?"></textarea></label>
            <button class="primary-btn" type="submit">Submit Application</button>
        </form>
    </dialog>

    <footer>
        <p>CareerConnect prototype built with HTML, CSS, JavaScript, PHP sessions, and MySQL storage.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
