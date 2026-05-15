<?php
require_once 'functions.php';
require_role('giver');

$user = current_user();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'post_job') {
        $message = add_job($_POST) ? 'Job posted successfully.' : 'Unable to post job.';
    }

    if (($_POST['action'] ?? '') === 'schedule_interview') {
        $message = schedule_interview($_POST) ? 'Interview scheduled successfully.' : 'Unable to schedule interview.';
    }
}

$myJobs = get_jobs_for_giver($user['id']);
$myApplications = get_applications_for_giver($user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Giver Dashboard - CareerConnect</title>
    <link rel="stylesheet" href="css/style.css?v=20260515">
</head>
<body class="dashboard-page giver-page">
    <header class="page-header">
        <nav class="navbar">
            <a class="logo" href="index.php">CareerConnect</a>
            <div class="nav-links">
                <a href="index.php#jobs">Public Jobs</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
        <div>
            <p class="eyebrow">Job Giver Dashboard</p>
            <h1>Welcome, <?php echo $user['name']; ?></h1>
            <p class="hero-text">Post jobs, track applications for your openings, and schedule interviews.</p>
        </div>
    </header>

    <main>
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <section class="stats-grid">
            <div class="mini-stat"><span><?php echo count($myJobs); ?></span><p>My Jobs</p></div>
            <div class="mini-stat"><span><?php echo count($myApplications); ?></span><p>Applications Received</p></div>
            <div class="mini-stat"><span><?php echo count(array_filter($myApplications, fn($app) => $app['status'] === 'Interview Scheduled')); ?></span><p>Interviews Scheduled</p></div>
        </section>

        <section class="split-section dashboard-split">
            <form class="panel form-card" method="POST">
                <p class="eyebrow">Employer Module</p>
                <h2>Post a New Job</h2>
                <input type="hidden" name="action" value="post_job">
                <label>Job Title <input name="title" required></label>
                <label>Company Name <input name="company" required></label>
                <label>Location <input name="location" required></label>
                <label>Job Type
                    <select name="type" required>
                        <option>Full Time</option>
                        <option>Part Time</option>
                        <option>Internship</option>
                    </select>
                </label>
                <label>Salary <input name="salary" placeholder="Example: 3 - 5 LPA" required></label>
                <label>Required Skills <input name="skills" placeholder="PHP, JavaScript, SQL" required></label>
                <label>Description <textarea name="description" rows="4" required></textarea></label>
                <button class="primary-btn" type="submit">Post Job</button>
            </form>

            <section class="panel">
                <p class="eyebrow">My Openings</p>
                <h2>Posted Jobs</h2>
                <?php if (empty($myJobs)): ?>
                    <p>No jobs posted yet.</p>
                <?php else: ?>
                    <div class="application-list">
                        <?php foreach (array_reverse($myJobs) as $job): ?>
                            <div class="application-item">
                                <strong><?php echo $job['title']; ?></strong>
                                <span><?php echo $job['company']; ?> | <?php echo $job['location']; ?> | <?php echo $job['type']; ?></span>
                                <p><?php echo $job['description']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </section>

        <section class="panel">
            <p class="eyebrow">Application Management</p>
            <h2>Applications Received</h2>

            <?php if (empty($myApplications)): ?>
                <p>No candidate has applied for your jobs yet.</p>
            <?php else: ?>
                <div class="application-list">
                    <?php foreach (array_reverse($myApplications) as $application): ?>
                        <?php $job = find_job($application['job_id']); ?>
                        <article class="application-item manager-card">
                            <div>
                                <strong><?php echo $application['name']; ?></strong>
                                <span><?php echo $application['email']; ?> | <?php echo $application['phone']; ?></span>
                                <p><strong>Applied for:</strong> <?php echo $job ? $job['title'] : 'Job removed'; ?></p>
                                <p><strong>Education:</strong> <?php echo $application['education']; ?></p>
                                <p><strong>Skills:</strong> <?php echo $application['skills']; ?></p>
                                <p><strong>Message:</strong> <?php echo $application['message']; ?></p>
                                <p><strong>Status:</strong> <span class="status-pill"><?php echo $application['status']; ?></span></p>
                            </div>

                            <form class="schedule-form" method="POST">
                                <input type="hidden" name="action" value="schedule_interview">
                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                <label>Date <input type="date" name="interview_date" value="<?php echo $application['interview_date']; ?>" required></label>
                                <label>Time <input type="time" name="interview_time" value="<?php echo $application['interview_time']; ?>" required></label>
                                <label>Mode
                                    <select name="interview_mode" required>
                                        <option <?php echo $application['interview_mode'] === 'Online' ? 'selected' : ''; ?>>Online</option>
                                        <option <?php echo $application['interview_mode'] === 'Offline' ? 'selected' : ''; ?>>Offline</option>
                                        <option <?php echo $application['interview_mode'] === 'Phone Call' ? 'selected' : ''; ?>>Phone Call</option>
                                    </select>
                                </label>
                                <label>Note <textarea name="interview_note" rows="2" placeholder="Meeting link or office address"><?php echo $application['interview_note']; ?></textarea></label>
                                <button class="secondary-btn" type="submit">Schedule Interview</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
