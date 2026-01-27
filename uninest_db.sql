-- Create database
CREATE DATABASE IF NOT EXISTS uninest_db;
USE uninest_db;

-- Students table
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reg_no VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    room_number VARCHAR(10),
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    faculty VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rooms table
CREATE TABLE rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_number VARCHAR(10) UNIQUE NOT NULL,
    capacity INT DEFAULT 4,
    occupied INT DEFAULT 0,
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    floor INT,
    hostel_block VARCHAR(20)
);

-- Complaints table
CREATE TABLE complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_reg_no VARCHAR(20),
    complaint_type VARCHAR(50),
    description TEXT,
    status ENUM('pending', 'in-progress', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (student_reg_no) REFERENCES students(reg_no) ON DELETE CASCADE
);

-- Payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_reg_no VARCHAR(20),
    amount DECIMAL(10,2) NOT NULL,
    payment_type VARCHAR(50),
    status ENUM('paid', 'pending', 'overdue') DEFAULT 'pending',
    due_date DATE,
    paid_date DATE NULL,
    FOREIGN KEY (student_reg_no) REFERENCES students(reg_no) ON DELETE CASCADE
);

-- Admin users table (for future admin panel)
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'staff') DEFAULT 'staff'
);

-- Insert sample admin
INSERT INTO admin_users (username, password, email, role) 
VALUES ('admin', '$2y$10$YourHashedPasswordHere', 'admin@uninest.lk', 'admin');

-- Insert sample rooms
INSERT INTO rooms (room_number, capacity, floor, hostel_block) VALUES
('101', 4, 1, 'A'),
('102', 4, 1, 'A'),
('201', 4, 2, 'A'),
('202', 4, 2, 'A'),
('103', 4, 1, 'B'),
('104', 4, 1, 'B');

-- Room requests table
CREATE TABLE IF NOT EXISTS room_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_reg_no VARCHAR(20),
    requested_room VARCHAR(10),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    admin_notes TEXT,
    FOREIGN KEY (student_reg_no) REFERENCES students(reg_no) ON DELETE CASCADE
);

-- Maintenance issues table
CREATE TABLE IF NOT EXISTS maintenance_issues (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_number VARCHAR(10),
    issue_description TEXT,
    reported_by VARCHAR(100),
    status ENUM('reported', 'in-progress', 'resolved') DEFAULT 'reported',
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    assigned_to VARCHAR(100),
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium'
);

-- Add complaint categories if not already defined
ALTER TABLE complaints 
ADD COLUMN IF NOT EXISTS complaint_type ENUM('Electrical', 'Plumbing', 'Cleaning', 'Furniture', 'Internet', 'Security', 'Other') DEFAULT 'Other';

-- Or if you want to keep it as VARCHAR but with predefined categories
ALTER TABLE complaints 
MODIFY COLUMN complaint_type ENUM('Electrical', 'Plumbing', 'Cleaning', 'Furniture', 'Internet', 'Security', 'Other') DEFAULT 'Other';



-- Update admin_users table with proper password hashing
ALTER TABLE admin_users 
MODIFY COLUMN password VARCHAR(255) NOT NULL;

-- Insert a default admin (password: admin123)
INSERT INTO admin_users (username, password, email, role) 
VALUES 
('admin', '$2y$10$Lx5Jg7m9XbQ6H8T3V2W1Ue4R5Y6T7U8I9O0P1A2S3D4F5G6H7J8K9L0M1N2O', 'admin@uninest.lk', 'admin'),
('staff', '$2y$10$Lx5Jg7m9XbQ6H8T3V2W1Ue4R5Y6T7U8I9O0P1A2S3D4F5G6H7J8K9L0M1N2O', 'staff@uninest.lk', 'staff')
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Add admin permissions table
CREATE TABLE IF NOT EXISTS admin_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    can_manage_students BOOLEAN DEFAULT TRUE,
    can_manage_rooms BOOLEAN DEFAULT TRUE,
    can_manage_complaints BOOLEAN DEFAULT TRUE,
    can_manage_payments BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
);

-- Add default permissions
INSERT INTO admin_permissions (admin_id) 
SELECT id FROM admin_users;

INSERT INTO admin_users (username, password, email, role) 
VALUES ('admin', 'admin123', 'admin@uninest.lk', 'admin')
ON DUPLICATE KEY UPDATE password = 'admin123';