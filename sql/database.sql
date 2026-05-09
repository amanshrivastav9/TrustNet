-- Create database
CREATE DATABASE IF NOT EXISTS trustnet_db;
USE trustnet_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    email_verified BOOLEAN DEFAULT FALSE,
    otp_code VARCHAR(6),
    otp_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Websites table
CREATE TABLE IF NOT EXISTS websites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    website_name VARCHAR(255) NOT NULL,
    website_url VARCHAR(255) NOT NULL,
    api_key VARCHAR(100) UNIQUE NOT NULL,
    secret_key VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Login logs table
CREATE TABLE IF NOT EXISTS login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    user_identifier VARCHAR(255),
    ip_address VARCHAR(45),
    browser VARCHAR(100),
    device VARCHAR(100),
    os VARCHAR(100),
    location VARCHAR(255),
    login_time DATETIME,
    logout_time DATETIME,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE
);

-- Failed attempts table
CREATE TABLE IF NOT EXISTS failed_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45),
    email VARCHAR(255),
    attempt_count INT DEFAULT 1,
    blocked_until DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- API user blocks table (IMPORTANT for 4 failed attempts = 5 hours block)
CREATE TABLE IF NOT EXISTS api_user_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    failed_attempts INT DEFAULT 0,
    is_blocked BOOLEAN DEFAULT FALSE,
    blocked_until DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
    INDEX idx_user_email (user_email),
    INDEX idx_blocked_until (blocked_until)
);

-- Activity logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    activity_type VARCHAR(100),
    activity_details TEXT,
    ip_address VARCHAR(45),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE
);

-- API requests table
CREATE TABLE IF NOT EXISTS api_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    api_key VARCHAR(100),
    endpoint VARCHAR(255),
    request_ip VARCHAR(45),
    response_time INT,
    status_code INT,
    request_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE
);

-- IP blacklist table
CREATE TABLE IF NOT EXISTS ip_blacklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) UNIQUE NOT NULL,
    reason TEXT,
    blocked_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (blocked_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: Admin@123)
INSERT INTO users (name, email, password, role, email_verified) VALUES 
('Administrator', 'admin@trustnet.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);