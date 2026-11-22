-- ITLAW701 | Inventory Management System | Group 2
-- Import this file in phpMyAdmin AFTER creating/selecting your database.
-- (Local XAMPP: CREATE DATABASE inventory_db; then import into it.)

SET NAMES utf8mb4;

DROP TABLE IF EXISTS requests;
DROP TABLE IF EXISTS equipment;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(120) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('logistic','coordinator','hod') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE equipment (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  category VARCHAR(60) NOT NULL,
  serial_number VARCHAR(80) NOT NULL UNIQUE,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  condition_status ENUM('New','Good','Fair','Needs repair') NOT NULL DEFAULT 'New',
  location VARCHAR(100) DEFAULT NULL,
  description TEXT,
  registered_by INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (registered_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  equipment_name VARCHAR(120) NOT NULL,
  category VARCHAR(60) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  priority ENUM('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  justification TEXT NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  requested_by INT UNSIGNED NOT NULL,
  decided_by INT UNSIGNED DEFAULT NULL,
  decided_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (requested_by) REFERENCES users(id),
  FOREIGN KEY (decided_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default accounts (passwords stored with password_hash / bcrypt)
INSERT INTO users (full_name, username, email, password, role) VALUES
('Logistic Officer', 'logistic',    'logistic@ngoma.local',    '$2y$10$kT/8Fkke1XfghFF17Ch85OO5NOk1ux/k14xyHPL9mSOlXU8Pgnggq', 'logistic'),
('Coordinator',      'coordinator', 'coordinator@ngoma.local', '$2y$10$6vtVZpLFJ5GAsIH9ub6/tOVlZ6AiPZBMDZA5jNpZJt741mHErDXaq', 'coordinator'),
('Head of Department','hod',        'hod@ngoma.local',         '$2y$10$Qlj3cwxSpLDQJ9Nq.Dwupuju9GmwVcLaTRy1odpR7nXRAIPq/0KIa', 'hod');

-- Sample data so pagination is visible from the first login
INSERT INTO equipment (name, category, serial_number, quantity, condition_status, location, description, registered_by) VALUES
('Dell OptiPlex 7090 Desktop','Computers','SN-PC-0001',20,'Good','ICT Lab 1','Core i5, 8GB RAM',1),
('HP LaserJet Pro M404','Printers','SN-PR-0002',2,'Good','Admin Block','Mono laser printer',1),
('Epson EB-X49 Projector','Audio-Visual','SN-AV-0003',4,'New','Seminar Room','3600 lumens',1),
('Cisco Catalyst 2960 Switch','Networking','SN-NW-0004',3,'Good','Server Room','24-port managed switch',1),
('TP-Link Archer C6 Router','Networking','SN-NW-0005',5,'New','ICT Lab 2','Dual-band Wi-Fi router',1),
('Lenovo ThinkPad E14','Computers','SN-PC-0006',10,'New','Staff Room','Core i5, 16GB RAM',1),
('Office Chair (ergonomic)','Furniture','SN-FU-0007',30,'Fair','ICT Lab 1','Adjustable height',1),
('Whiteboard 120x240','Furniture','SN-FU-0008',6,'Good','Classrooms','Magnetic whiteboard',1),
('APC Back-UPS 650VA','Electrical','SN-EL-0009',12,'Good','ICT Lab 1','Backup power unit',1),
('Canon EOS 250D Camera','Audio-Visual','SN-AV-0010',1,'Needs repair','Media Room','Lens mount issue',1),
('Raspberry Pi 4 Kit','Electronics','SN-EL-0011',15,'New','Embedded Lab','4GB model with case',1),
('Multimeter DT-830','Electronics','SN-EL-0012',25,'Fair','Embedded Lab','Digital multimeter',1);

INSERT INTO requests (equipment_name, category, quantity, priority, justification, requested_by) VALUES
('Network cable tester','Networking',5,'Medium','Needed for the practical networking sessions.',2),
('Wireless presenter clickers','Audio-Visual',3,'Low','For lecturers during presentations.',2),
('Gaming-grade GPU workstations','Computers',4,'High','Required for the machine learning module labs.',2);
