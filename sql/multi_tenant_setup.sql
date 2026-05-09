-- Update websites table (already exists, but ensure these columns)
ALTER TABLE websites ADD COLUMN IF NOT EXISTS total_visits INT DEFAULT 0;
ALTER TABLE websites ADD COLUMN IF NOT EXISTS total_pageviews INT DEFAULT 0;
ALTER TABLE websites ADD COLUMN IF NOT EXISTS last_activity DATETIME NULL;

-- Create indexes for better performance
CREATE INDEX idx_website_api ON websites(api_key);
CREATE INDEX idx_activity_website ON activity_logs(website_id, timestamp);
CREATE INDEX idx_login_website ON login_logs(website_id, login_time);

-- Create website_stats table for aggregated data
CREATE TABLE IF NOT EXISTS website_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    date DATE NOT NULL,
    visits INT DEFAULT 0,
    pageviews INT DEFAULT 0,
    unique_visitors INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
    UNIQUE KEY unique_daily_stats (website_id, date),
    INDEX idx_date (date)
);

-- Create user_activity table for individual user tracking per website
CREATE TABLE IF NOT EXISTS user_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    user_email VARCHAR(255),
    user_ip VARCHAR(45),
    first_visit DATETIME,
    last_visit DATETIME,
    total_visits INT DEFAULT 1,
    total_time_spent INT DEFAULT 0,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
    INDEX idx_user_email (user_email),
    INDEX idx_website_user (website_id, user_email)
);