-- ============================================================
--  Vehicle Service Management System - Database Schema
--  Compatible with MySQL 5.7+ / MariaDB / XAMPP
-- ============================================================

CREATE DATABASE IF NOT EXISTS vsms;
USE vsms;

-- ============================================================
-- TABLE 1: Customer
-- ============================================================
CREATE TABLE IF NOT EXISTS Customer (
    customer_id   INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    phone         VARCHAR(15)  NOT NULL UNIQUE,
    address       VARCHAR(255)
);

-- ============================================================
-- TABLE 2: Vehicle
-- ============================================================
CREATE TABLE IF NOT EXISTS Vehicle (
    vehicle_id     INT AUTO_INCREMENT PRIMARY KEY,
    customer_id    INT          NOT NULL,
    vehicle_number VARCHAR(20)  NOT NULL UNIQUE,
    model          VARCHAR(100) NOT NULL,
    brand          VARCHAR(100) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- ============================================================
-- TABLE 3: Mechanic
-- ============================================================
CREATE TABLE IF NOT EXISTS Mechanic (
    mechanic_id    INT AUTO_INCREMENT PRIMARY KEY,
    mechanic_name  VARCHAR(100) NOT NULL,
    phone          VARCHAR(15)  NOT NULL UNIQUE,
    specialization VARCHAR(100)
);

-- ============================================================
-- TABLE 4: Service
-- ============================================================
CREATE TABLE IF NOT EXISTS Service (
    service_id   INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(150)   NOT NULL,
    service_cost DECIMAL(10, 2) NOT NULL
);

-- ============================================================
-- TABLE 5: Service_Record
-- ============================================================
CREATE TABLE IF NOT EXISTS Service_Record (
    record_id    INT  AUTO_INCREMENT PRIMARY KEY,
    vehicle_id   INT  NOT NULL,
    mechanic_id  INT  NOT NULL,
    service_id   INT  NOT NULL,
    service_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    FOREIGN KEY (vehicle_id)  REFERENCES Vehicle(vehicle_id)  ON DELETE CASCADE,
    FOREIGN KEY (mechanic_id) REFERENCES Mechanic(mechanic_id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id)  REFERENCES Service(service_id)  ON DELETE RESTRICT
);

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Customers
INSERT INTO Customer (name, phone, address) VALUES
('Arjun Menon',     '9876543210', '12 MG Road, Kochi, Kerala'),
('Priya Nair',      '9876543211', '45 Palarivattom, Ernakulam'),
('Suresh Kumar',    '9876543212', '78 Thampanoor, Thiruvananthapuram'),
('Deepa Thomas',    '9876543213', '22 Vyttila Hub, Kochi'),
('Rahul Pillai',    '9876543214', '5 Edappally Junction, Kochi');

-- Vehicles
INSERT INTO Vehicle (customer_id, vehicle_number, model, brand) VALUES
(1, 'KL01AB1234', 'Swift Dzire', 'Maruti Suzuki'),
(2, 'KL07CD5678', 'Creta',       'Hyundai'),
(3, 'KL15EF9012', 'Nexon',       'Tata'),
(4, 'KL01GH3456', 'Innova',      'Toyota'),
(5, 'KL07IJ7890', 'Baleno',      'Maruti Suzuki'),
(1, 'KL01KL2345', 'i20',         'Hyundai');

-- Mechanics
INSERT INTO Mechanic (mechanic_name, phone, specialization) VALUES
('Raju Varma',     '8765432100', 'Engine Repair'),
('Santhosh P',     '8765432101', 'Electrical Systems'),
('Binu George',    '8765432102', 'AC & Cooling'),
('Anoop Krishnan', '8765432103', 'Body & Paint'),
('Vivek Nambiar',  '8765432104', 'General Service');

-- Services
INSERT INTO Service (service_name, service_cost) VALUES
('Full Car Service',        2500.00),
('Engine Oil Change',        800.00),
('AC Service & Recharge',   1800.00),
('Brake Pad Replacement',   1200.00),
('Tyre Rotation & Balancing', 600.00),
('Battery Replacement',     3500.00),
('Full Body Polish',        2000.00);

-- Service Records
INSERT INTO Service_Record (vehicle_id, mechanic_id, service_id, service_date) VALUES
(1, 5, 1, '2025-01-10'),
(1, 1, 2, '2025-02-15'),
(2, 3, 3, '2025-02-20'),
(3, 2, 4, '2025-03-01'),
(4, 4, 7, '2025-03-05'),
(5, 5, 5, '2025-03-10'),
(6, 1, 1, '2025-03-12'),
(2, 5, 2, '2025-03-15'),
(3, 3, 3, '2025-03-18'),
(1, 2, 6, '2025-03-19');

-- ============================================================
-- EXAMPLE QUERIES
-- ============================================================

-- 1. Display all customers
SELECT * FROM Customer;

-- 2. Display vehicles with their owner names (JOIN)
SELECT v.vehicle_number, v.brand, v.model, c.name AS owner, c.phone
FROM Vehicle v
JOIN Customer c ON v.customer_id = c.customer_id;

-- 3. Full service history with all details
SELECT
    sr.record_id,
    c.name        AS customer,
    v.vehicle_number,
    v.brand,
    m.mechanic_name,
    s.service_name,
    s.service_cost,
    sr.service_date
FROM Service_Record sr
JOIN Vehicle   v ON sr.vehicle_id  = v.vehicle_id
JOIN Customer  c ON v.customer_id  = c.customer_id
JOIN Mechanic  m ON sr.mechanic_id = m.mechanic_id
JOIN Service   s ON sr.service_id  = s.service_id
ORDER BY sr.service_date DESC;

-- 4. GROUP BY: Total revenue per mechanic
SELECT
    m.mechanic_name,
    COUNT(sr.record_id)  AS total_jobs,
    SUM(s.service_cost)  AS total_revenue
FROM Service_Record sr
JOIN Mechanic m ON sr.mechanic_id = m.mechanic_id
JOIN Service  s ON sr.service_id  = s.service_id
GROUP BY m.mechanic_id, m.mechanic_name
ORDER BY total_revenue DESC;

-- 5. GROUP BY: Services done per vehicle
SELECT
    v.vehicle_number,
    v.brand,
    COUNT(sr.record_id)  AS service_count,
    SUM(s.service_cost)  AS total_spent
FROM Service_Record sr
JOIN Vehicle v ON sr.vehicle_id = v.vehicle_id
JOIN Service s ON sr.service_id = s.service_id
GROUP BY v.vehicle_id, v.vehicle_number, v.brand
ORDER BY total_spent DESC;

-- 6. UPDATE: Update customer phone
UPDATE Customer SET phone = '9999999999' WHERE customer_id = 1;

-- 7. UPDATE: Update service cost
UPDATE Service SET service_cost = 900.00 WHERE service_id = 2;

-- 8. DELETE: Delete a service record
DELETE FROM Service_Record WHERE record_id = 10;

-- 9. ORDER BY: Services ordered by cost
SELECT service_name, service_cost FROM Service ORDER BY service_cost DESC;

-- 10. Most recent services first
SELECT sr.record_id, v.vehicle_number, s.service_name, sr.service_date
FROM Service_Record sr
JOIN Vehicle v ON sr.vehicle_id = v.vehicle_id
JOIN Service s ON sr.service_id = s.service_id
ORDER BY sr.service_date DESC
LIMIT 5;
