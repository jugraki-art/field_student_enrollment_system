-- Kinondoni Municipal Council HQ - Field Students Database Schema
-- Database: kinondoni_pt_db

CREATE DATABASE IF NOT EXISTS kinondoni_pt_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kinondoni_pt_db;

CREATE TABLE IF NOT EXISTS field_students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    institution VARCHAR(150) NOT NULL,
    edu_level ENUM('Certificate', 'Diploma', 'Degree') NOT NULL,
    year_of_study ENUM('Year 1', 'Year 2', 'Year 3', 'Year 4') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add this to schema.sql
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    position VARCHAR(50) DEFAULT 'Training Officer',
    phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users
   (username, password_hash, position, phone_number)
   values
   ('admin', 'admin123', 'Admin', '0712345678'); -- Replace with a secure hashed password in production