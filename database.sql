-- Drop the database if it exists (be careful: this deletes all data!)
DROP DATABASE IF EXISTS laburger;

-- Create and use the database
CREATE DATABASE laburger;
USE laburger;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT
);

-- Menu Items Table
CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255)
);

-- Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


select * from laburger.order_items

-- Order Items Table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
);

-- Optional: Sample Data for menu_items
INSERT INTO menu_items (name, description, price, image_url) VALUES
('Classic Burger', 'A juicy beef patty, fresh lettuce, tomatoes, and our signature sauce.', 8.99, 'burger1.jpeg'),
('Cheese Explosion', 'Double layers of cheddar cheese, crispy onions, and a special sauce.', 10.99, 'burger2.jpeg'),
('Spicy Inferno', 'Spicy grilled chicken, jalapeños, and a fiery hot sauce.', 9.99, 'burger3.jpeg'),
('Veggie Delight', 'A plant-based patty with fresh avocado, lettuce, and tomato.', 8.49, 'burger4.jpeg'),
('Bacon Bliss', 'Smoky bacon strips layered on a beef patty with melted cheddar.', 10.49, 'burger5.jpeg'),
('Mushroom Melt', 'Grilled mushrooms, Swiss cheese, and a creamy garlic sauce.', 9.79, 'burger6.jpeg'),
('Crunchy Chicken', 'Crispy fried chicken fillet with tangy mayo and pickles.', 8.99, 'burger7.jpeg'),
('BBQ Beef Stack', 'Double beef patties glazed with BBQ sauce and caramelized onions.', 11.49, 'burger8.jpeg'),
('Avocado Supreme', 'Fresh avocado slices, grilled chicken, and chipotle aioli.', 9.89, 'burger9.jpeg'),
('Triple Tower', 'Three layers of beef, cheese, and flavor-packed toppings.', 12.99, 'burger10.jpeg'),
('Greek Lamb Burger', 'Juicy lamb patty, tzatziki sauce, and Mediterranean herbs.', 10.99, 'burger11.jpeg'),
('Kids Mini Burger', 'Perfectly sized for little hands with classic toppings.', 6.49, 'burger12.jpeg');



-- Create contact_us table
CREATE TABLE contact_us (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(100) NOT NULL,
  message       TEXT NOT NULL,
  submitted_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (email, password) VALUES ('admin@laburger.com', 'adminpass123');

DELETE FROM admins WHERE email = 'admin@laburger.com';
INSERT INTO admins (email, password) VALUES ('admin@laburger.com', 'adminpass123');
SELECT id, email, password FROM admins;
SELECT * FROM admins;

DESCRIBE admins;
SELECT * FROM admins;

DESCRIBE users;
DESCRIBE admins;

ALTER TABLE users CHANGE password_hash password VARCHAR(255);