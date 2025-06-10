-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert default admin user (username: admin, password: admin123)
-- The password is hashed using PHP's password_hash() function
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$8K1p/a0dR1Ux5Y5Y5Y5Y5O5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y5Y'); 