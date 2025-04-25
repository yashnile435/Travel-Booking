-- Create the database
CREATE DATABASE IF NOT EXISTS bappa_travels;
USE bappa_travels;

-- Create admin table if not exists
CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- First, clear existing admin data
DELETE FROM admin WHERE username = 'admin';

-- Then insert a fresh admin account with a known password hash
-- This password hash is for the password 'password'
INSERT INTO admin (username, password, email, fullname) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@bappatravels.com', 'Admin User');


-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile VARCHAR(15) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);  

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    fullname VARCHAR(100) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    car_name VARCHAR(50) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    pickup_location TEXT NOT NULL,
    dropoff_location TEXT NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create cars table
CREATE TABLE IF NOT EXISTS cars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    car_name VARCHAR(100) NOT NULL,
    description TEXT,
    passengers INT NOT NULL,
    image_url VARCHAR(255),
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample cars
INSERT INTO cars (car_name, description, passengers, image_url, is_available) VALUES
('Swift Dzire', 'Comfortable ride for 4 passengers', 4, 'assets/images/Swift_Dzire.jpg', 1),
('Tavera', 'Spacious vehicle for up to 7 passengers', 7, 'assets/images/Tavera.jpg', 1),
('Tempo Traveller', 'Perfect for group travel up to 12 passengers', 12, 'assets/images/Tempo_Traveller.jpg', 1),
('Innova Crysta', 'Premium comfort for 7 passengers', 7, 'assets/images/Innova.jpg', 1),
('Ertiga', 'Efficient family car for 7 passengers', 7, 'assets/images/Ertiga.jpg', 1);

-- Create destinations table
CREATE TABLE IF NOT EXISTS destinations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    visit_date DATE NOT NULL,
    highlights TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample destinations
INSERT INTO destinations (title, location, description, image_url, visit_date, highlights, is_featured) VALUES
('Scenic Beach Getaway', 'Goa', 'Beautiful beach tour with our customers enjoying the sunset views and water activities.', 'assets/images/destinations/goa_beach.jpg', '2023-10-15', 'Beach activities, Water sports, Seaside dining', 1),
('Mountain Adventure', 'Manali', 'Exciting mountain trip with breathtaking views of the snow-capped peaks and adventure activities.', 'assets/images/destinations/manali_hills.jpg', '2023-08-22', 'Trekking, Snow activities, Mountain views', 1),
('Historical Tour', 'Jaipur', 'Cultural experience exploring the historical forts and palaces of the Pink City.', 'assets/images/destinations/jaipur_fort.jpg', '2023-09-05', 'Fort visits, Cultural experiences, Historical monuments', 0),
('Temple Heritage Trip', 'Tirupati', 'Spiritual journey to one of India\'s most sacred temples with comfortable transportation.', 'assets/images/destinations/tirupati_temple.jpg', '2023-11-12', 'Temple visits, Spiritual experience, Cultural insights', 1);

-- Note: Make sure to create the destinations directory
-- Run the following command before importing this SQL:
-- mkdir -p assets/images/destinations 