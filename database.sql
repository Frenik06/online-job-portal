CREATE DATABASE IF NOT EXISTS careerconnect
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE careerconnect;

DROP TABLE IF EXISTS applications;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('seeker', 'giver') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE jobs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  giver_id INT UNSIGNED NOT NULL,
  title VARCHAR(150) NOT NULL,
  company VARCHAR(150) NOT NULL,
  location VARCHAR(150) NOT NULL,
  type ENUM('Full Time', 'Part Time', 'Internship') NOT NULL,
  salary VARCHAR(100) NOT NULL,
  skills VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_jobs_giver
    FOREIGN KEY (giver_id) REFERENCES users(id)
    ON DELETE CASCADE
);

CREATE TABLE applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_id INT UNSIGNED NOT NULL,
  seeker_id INT UNSIGNED NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  education VARCHAR(150) NOT NULL,
  skills VARCHAR(255) NOT NULL,
  message TEXT NULL,
  status ENUM('Applied', 'Interview Scheduled') NOT NULL DEFAULT 'Applied',
  interview_date DATE NULL,
  interview_time TIME NULL,
  interview_mode ENUM('Online', 'Offline', 'Phone Call') NULL,
  interview_note TEXT NULL,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_applications_job
    FOREIGN KEY (job_id) REFERENCES jobs(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_applications_seeker
    FOREIGN KEY (seeker_id) REFERENCES users(id)
    ON DELETE CASCADE
);

INSERT INTO users (id, name, email, password, role) VALUES
(1, 'Demo Job Seeker', 'seeker@example.com', '123456', 'seeker'),
(2, 'Demo Job Giver', 'giver@example.com', '123456', 'giver');

INSERT INTO jobs (id, giver_id, title, company, location, type, salary, skills, description) VALUES
(1, 2, 'manager', 'ABCDEFGHI', 'surat', 'Full Time', '5-7 lpa', 'javascript', 'sdjfkasdhfjsdfhsdkfhasdfk'),
(2, 2, 'iHR', 'sagfuiadgf', 'hdasdgf', 'Internship', '85', 'ijfdd', 'dfjkhsjdf');

INSERT INTO applications (
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
  interview_date,
  interview_time,
  interview_mode,
  interview_note,
  applied_at
) VALUES
(1, 1, 1, 'frenik', 'freniksmangukiya1954@gmail.com', '2342342342', 'ca', 'javascript', 'ugdafghsjdgfjsdfsd', 'Interview Scheduled', '2026-04-22', '17:00:00', 'Online', 'jksgfjshd', '2026-04-29 19:28:31'),
(2, 2, 1, 'Demo Job Seeker', 'seeker@example.com', 'jhsgdjad', 'jasdjsad', 'sdasjdasjkd', 'sjdajkdnasdsad', 'Applied', NULL, NULL, NULL, NULL, '2026-04-29 19:48:45');

ALTER TABLE users AUTO_INCREMENT = 3;
ALTER TABLE jobs AUTO_INCREMENT = 3;
ALTER TABLE applications AUTO_INCREMENT = 3;
