-- Create table for website users (each website has its own users)
CREATE TABLE IF NOT EXISTS website_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    status ENUM('active', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
    UNIQUE KEY unique_website_email (website_id, email),
    INDEX idx_email (email)
);

-- Insert test users for your website
-- First, get your website ID
-- Then insert test users

-- Example: Replace YOUR_WEBSITE_ID with actual ID
-- INSERT INTO website_users (website_id, email, password, name) 
-- VALUES (YOUR_WEBSITE_ID, 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User');