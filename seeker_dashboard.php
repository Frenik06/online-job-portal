<?php
require_once 'functions.php';
require_role('seeker');

$user = current_user();
$applications = get_applications_for_seeker($user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Dashboard - CareerConnect</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="page-header">
        <nav class="navbar">
            <a class="logo" href="index.php">CareerConnect</a>
            <div class="nav-links">
                <a href="index.php#jobs">Browse Jobs</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
        <div>
            <p class="eyebrow">Job Seeker Dashboard</p>
            <h1>Welcome, <?php echo $user['name']; ?></h1>
            <p class="hero-text">Track all jobs you applied for and see interview updates from employers.</p>
        </div>
    </header>

    <main>
        <section class="stats-grid">
            <div class="mini-stat"><span><?php echo count($applications); ?></span><p>Total Applications</p></div>
            <div class="mini-stat"><span><?php echo count(array_filter($applications, fn($app) => $app['status'] === 'Interview Scheduled')); ?></span><p>Interviews Scheduled</p></div>
            <div class="mini-stat"><span><?php echo count(array_filter($applications, fn($app) => $app['status'] === 'Applied')); ?></span><p>Waiting Response</p></div>
        </section>

        <section class="panel">
            <p class="eyebrow">My Applications</p>
            <h2>Application Tracker</h2>

            <?php if (empty($applications)): ?>
                <p>You have not applied for any job yet.</p>
                <a class="primary-btn" href="index.php#jobs">Browse Jobs</a>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Interview</th>
                                <th>Applied On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_reverse($applications) as $application): ?>
                                <?php $job = find_job($application['job_id']); ?>
                                <tr>
                                    <td><?php echo $job ? $job['title'] : 'Job removed'; ?></td>
                                    <td><?php echo $job ? $job['company'] : '-'; ?></td>
                                    <td><span class="status-pill"><?php echo $application['status']; ?></span></td>
                                    <td>
                                        <?php if ($application['status'] === 'Interview Scheduled'): ?>
                                            <strong><?php echo $application['interview_date']; ?> at <?php echo $application['interview_time']; ?></strong><br>
                                            <span><?php echo $application['interview_mode']; ?></span><br>
                                            <small><?php echo $application['interview_note']; ?></small>
                                        <?php else: ?>
                                            Not scheduled yet
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $application['applied_at']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
