-- Active sessions table for real-time tracking
CREATE TABLE IF NOT EXISTS active_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    session_id VARCHAR(100) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    session_start DATETIME,
    last_activity DATETIME,
    session_end DATETIME,
    total_duration INT DEFAULT 0,
    page_count INT DEFAULT 1,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id),
    INDEX idx_last_activity (last_activity)
);

-- Page time tracking
CREATE TABLE IF NOT EXISTS page_time_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    ip_address VARCHAR(45),
    page_url TEXT,
    total_time INT DEFAULT 0,
    last_update DATETIME,
    UNIQUE KEY unique_page_ip (website_id, ip_address, page_url(255)),
    INDEX idx_website_ip (website_id, ip_address)
);

-- Custom events tracking
CREATE TABLE IF NOT EXISTS custom_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    event_name VARCHAR(100),
    event_data JSON,
    ip_address VARCHAR(45),
    session_id VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
    INDEX idx_event_name (event_name),
    INDEX idx_created_at (created_at)
);

-- Real-time visitors
CREATE TABLE IF NOT EXISTS realtime_visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    session_id VARCHAR(100),
    ip_address VARCHAR(45),
    current_page VARCHAR(500),
    last_activity DATETIME,
    first_seen DATETIME,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
    INDEX idx_last_activity (last_activity),
    INDEX idx_website (website_id)
);