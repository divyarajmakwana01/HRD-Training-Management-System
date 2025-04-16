CREATE TABLE programme_coordinators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    programme_id INT NOT NULL,
    coordinator_id INT NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdby VARCHAR(255),
    updatedby VARCHAR(255),
    FOREIGN KEY (programme_id) REFERENCES programme(id) ON DELETE CASCADE,
    FOREIGN KEY (coordinator_id) REFERENCES coordinators(id) ON DELETE CASCADE
);