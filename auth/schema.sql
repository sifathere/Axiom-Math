CREATE DATABASE IF NOT EXISTS axiommath_db;
USE axiommath_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,       -- stores a bcrypt hash from password_hash(), never plain text
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
