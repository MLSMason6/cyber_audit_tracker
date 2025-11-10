-- Users table
CREATE TABLE Users ( 
    user_id INT AUTO_INCREMENT PRIMARY KEY, 
    username VARCHAR(50) NOT NULL UNIQUE, 
    email VARCHAR(100) NOT NULL, 
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'analyst') DEFAULT 'analyst', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

-- Vulnerabilities table 
CREATE TABLE Vulnerabilities ( 
    vuln_id INT AUTO_INCREMENT PRIMARY KEY, 
    title VARCHAR(255) NOT NULL, 
    severity ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL,
    status ENUM('Open', 'In Progress', 'Resolved') DEFAULT 'Open',
    system_affected VARCHAR (100), 
    description TEXT,
    date_found DATE NOT NULL,
    date_resolved DATE NULL, 
    created_by INT, 
    FOREIGN KEY (created_by) REFERENCES Users(user_id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Actions table (for mitigation steps or updates)
CREATE TABLE Actions( 
    action_id INT AUTO_INCREMENT PRIMARY KEY, 
    vuln_id INT NOT NULL, 
    user_id INT NOT NULL, 
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    notes TEXT, 
    FOREIGN KEY (vuln_id) REFERENCES Vulnerabilities(vuln_id) ON DELETE CASCADE, 
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Audit Log Table 
CREATE TABLE AuditLog ( 
    log_id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    action VARCHAR(255), 
    details TEXT, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    FOREIGN KEY(user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);