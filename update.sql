USE kriya;
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
