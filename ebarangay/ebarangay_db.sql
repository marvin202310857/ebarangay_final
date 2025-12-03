-- ebarangay_db.sql
CREATE DATABASE ebarangay_db;
USE ebarangay_db;

-- Users Table
CREATE TABLE users (
    user_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    user_type ENUM('admin', 'resident') DEFAULT 'resident',
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Residents Profile Table
CREATE TABLE residents (
    resident_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11),
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    suffix VARCHAR(10),
    birthdate DATE,
    gender ENUM('Male', 'Female', 'Other'),
    civil_status ENUM('Single', 'Married', 'Widowed', 'Separated'),
    contact_number VARCHAR(20),
    address TEXT,
    purok VARCHAR(50),
    profile_photo VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Barangay Officials Table
CREATE TABLE barangay_officials (
    official_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    position VARCHAR(100) NOT NULL,
    term_start DATE,
    term_end DATE,
    contact_number VARCHAR(20),
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transactions Table
CREATE TABLE transactions (
    transaction_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    resident_id INT(11),
    transaction_type VARCHAR(100) NOT NULL,
    purpose TEXT,
    status ENUM('pending', 'approved', 'rejected', 'processing') DEFAULT 'pending',
    requested_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_date TIMESTAMP NULL,
    processed_by INT(11),
    remarks TEXT,
    FOREIGN KEY (resident_id) REFERENCES residents(resident_id),
    FOREIGN KEY (processed_by) REFERENCES users(user_id)
);

-- Blotter Reports Table
CREATE TABLE blotter_reports (
    blotter_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    resident_id INT(11),
    incident_type VARCHAR(100) NOT NULL,
    incident_date DATETIME NOT NULL,
    reported_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    location TEXT,
    description TEXT NOT NULL,
    complainant_name VARCHAR(200),
    respondent_name VARCHAR(200),
    status ENUM('new', 'ongoing', 'resolved', 'closed') DEFAULT 'new',
    admin_response TEXT,
    resolved_date TIMESTAMP NULL,
    resolved_by INT(11),
    FOREIGN KEY (resident_id) REFERENCES residents(resident_id),
    FOREIGN KEY (resolved_by) REFERENCES users(user_id)
);

-- Announcements Table
CREATE TABLE announcements (
    announcement_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(50),
    posted_by INT(11),
    posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('published', 'draft', 'archived') DEFAULT 'published',
    FOREIGN KEY (posted_by) REFERENCES users(user_id)
);

-- Pet Registration Table
CREATE TABLE pet_registrations (
    pet_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    resident_id INT(11),
    pet_name VARCHAR(100) NOT NULL,
    pet_type VARCHAR(50) NOT NULL,
    breed VARCHAR(100),
    color VARCHAR(50),
    age INT(3),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    vaccination_status VARCHAR(100),
    pet_photo VARCHAR(255),
    FOREIGN KEY (resident_id) REFERENCES residents(resident_id)
);

-- Barangay Information Table
CREATE TABLE barangay_info (
    info_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    barangay_name VARCHAR(200) NOT NULL,
    municipality VARCHAR(100),
    province VARCHAR(100),
    phone_number VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    logo VARCHAR(255)
);

-- Insert Default Admin Account (password: admin123)
INSERT INTO users (username, password, email, user_type, status) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@ebarangay.ph', 'admin', 'active');

-- Insert Default Barangay Info
INSERT INTO barangay_info (barangay_name, municipality, province, phone_number, email, address) 
VALUES ('Barangay Sample', 'Sample Municipality', 'Sample Province', '(123) 456-7890', 'info@ebarangay.ph', 'Sample Address, Philippines');
