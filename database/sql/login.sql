CREATE TABLE login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,             -- Ensures no duplicate emails
    password VARCHAR(255) NULL,
    otp VARCHAR(6),                                 -- OTPs are usually 4-6 digits
    rights ENUM('U', 'A', 'SA') NOT NULL,      -- U = User, R = Reviewer, A = Admin, SA = Super Admin
    password_reset_token VARCHAR(255) UNIQUE,       -- Prevent token collisions
    token_expiry TIMESTAMP NULL,
    salt VARCHAR(255),                              -- For password hashing
    hash_password VARCHAR(255),                     -- Hashed password
    en_password VARCHAR(255),                       -- Encrypted password (if used)
    remarks TEXT,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdby VARCHAR(255),
    updatedby VARCHAR(255)
);
