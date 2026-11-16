CREATE DATABASE IF NOT EXISTS kriya CHARACTER SET utf8mb4;
USE kriya;
CREATE TABLE users(
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('client','agency','owner') NOT NULL DEFAULT 'client',
  phone VARCHAR(30), city VARCHAR(80),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE cars(
  id INT AUTO_INCREMENT PRIMARY KEY,
  owner_id INT NOT NULL,
  brand VARCHAR(60) NOT NULL, model VARCHAR(60) NOT NULL, year SMALLINT,
  city VARCHAR(80) NOT NULL, price_per_day DECIMAL(8,2) NOT NULL,
  fuel ENUM('Petrol','Diesel','Hybrid','Electric') DEFAULT 'Petrol',
  transmission ENUM('Manual','Automatic') DEFAULT 'Manual',
  seats TINYINT DEFAULT 5, description TEXT, image VARCHAR(200),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE bookings(
  id INT AUTO_INCREMENT PRIMARY KEY,
  car_id INT NOT NULL, client_id INT NOT NULL,
  start_date DATE NOT NULL, end_date DATE NOT NULL, total DECIMAL(10,2),
  status ENUM('pending','accepted','refused') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(car_id) REFERENCES cars(id) ON DELETE CASCADE,
  FOREIGN KEY(client_id) REFERENCES users(id) ON DELETE CASCADE
);
-- demo data (demo accounts can't log in; register your own to test)
INSERT INTO users(name,email,password,role,phone,city) VALUES
('Atlas Drive Agency','agency@demo.com','!locked','agency','+212600000001','Tangier'),
('Youssef B.','owner@demo.com','!locked','owner','+212600000002','Casablanca');
INSERT INTO cars(owner_id,brand,model,year,city,price_per_day,fuel,transmission,seats,description) VALUES
(1,'Dacia','Logan',2022,'Tangier',250,'Diesel','Manual',5,'Reliable and economical, perfect for the coast road to Asilah.'),
(1,'Renault','Clio 5',2023,'Tangier',320,'Petrol','Manual',5,'Compact city car, easy to park in the medina.'),
(1,'Hyundai','Tucson',2022,'Tetouan',550,'Hybrid','Automatic',5,'Comfortable SUV for mountain trips to Chefchaouen.'),
(2,'Peugeot','208',2021,'Casablanca',300,'Petrol','Manual',5,'Clean, fun to drive, great fuel economy.'),
(2,'Volkswagen','Golf 8',2023,'Rabat',480,'Diesel','Automatic',5,'Smooth highway cruiser with all the comfort.');
CREATE TABLE IF NOT EXISTS subscriptions(
  user_id INT PRIMARY KEY, plan ENUM('starter','pro','agency') NOT NULL,
  status ENUM('active','expired') DEFAULT 'active', price DECIMAL(8,2) DEFAULT 0,
  started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
CREATE TABLE IF NOT EXISTS car_images(
  id INT AUTO_INCREMENT PRIMARY KEY, car_id INT NOT NULL, filename VARCHAR(200) NOT NULL,
  FOREIGN KEY(car_id) REFERENCES cars(id) ON DELETE CASCADE);
-- give the demo accounts a plan
INSERT IGNORE INTO subscriptions(user_id,plan) SELECT id,'agency' FROM users WHERE role='agency';
INSERT IGNORE INTO subscriptions(user_id,plan) SELECT id,'starter' FROM users WHERE role='owner';
