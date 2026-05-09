-- Update api_user_blocks table for better tracking
ALTER TABLE api_user_blocks 
ADD COLUMN IF NOT EXISTS block_reason VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS unblocked_at DATETIME NULL;

-- Create security_events table for all security-related activities
CREATE TABLE IF NOT EXISTS security_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    user_identifier VARCHAR(255),
    ip_address VARCHAR(45),
    location VARCHAR(255),
    user_agent TEXT,
    event_details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_website (website_id),
    INDEX idx_ip (ip_address),
    INDEX idx_created (created_at),
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE
);

-- Create ip_location_cache table for caching location data
CREATE TABLE IF NOT EXISTS ip_location_cache (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) UNIQUE NOT NULL,
    country VARCHAR(100),
    country_code VARCHAR(5),
    region VARCHAR(100),
    city VARCHAR(100),
    lat DECIMAL(10,6),
    lon DECIMAL(10,6),
    isp VARCHAR(255),
    is_proxy BOOLEAN DEFAULT FALSE,
    is_vpn BOOLEAN DEFAULT FALSE,
    risk_score INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ip (ip_address)
);

-- Create failed_login_attempts table with better tracking
CREATE TABLE IF NOT EXISTS failed_login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    user_identifier VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    location VARCHAR(255),
    user_agent TEXT,
    attempt_time DATETIME,
    INDEX idx_website_user (website_id, user_identifier),
    INDEX idx_ip (ip_address),
    INDEX idx_time (attempt_time)
);