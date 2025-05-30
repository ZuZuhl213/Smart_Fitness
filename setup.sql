-- Create database
CREATE DATABASE IF NOT EXISTS smartfit_db;
USE smartfit_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create workout_history table
CREATE TABLE IF NOT EXISTS workout_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    age INT NOT NULL,
    height FLOAT NOT NULL,
    weight FLOAT NOT NULL,
    duration FLOAT NOT NULL,
    heart_rate FLOAT NOT NULL,
    body_temp FLOAT NOT NULL,
    calories INT NOT NULL,
    goal VARCHAR(20) NOT NULL, -- mục tiêu cá nhân: tăng_cơ, giảm_cân, ...
    burned_calories FLOAT NOT NULL, -- lượng calo đã đốt
    menu JSON, -- lưu thực đơn gợi ý dạng JSON (tên món, dinh dưỡng, v.v.)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);