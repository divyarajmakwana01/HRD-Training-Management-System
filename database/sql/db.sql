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


CREATE TABLE programme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    programmeBrief TEXT,
    brochure_link VARCHAR(255),
    programmeType ENUM('1', '2', '3','4','5') NOT NULL COMMENT '1-Webinar, 2-User Awareness, 3-Workshop',
    programmeVenue VARCHAR(255),
    questionnaire TEXT,
    startdate DATE NOT NULL,
    enddate DATE NOT NULL,
    startTime TIME,
    endTime TIME,
    fees VARCHAR(20),
    fees_with_acc VARCHAR(20),
    fees_exemption INT(2),
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdby VARCHAR(255),
    updatedby VARCHAR(255)
);

CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login_id INT NOT NULL,
    image VARCHAR(255),
    prefix VARCHAR(255),
    fname VARCHAR(255) NOT NULL,
    lname VARCHAR(255),
    designation VARCHAR(255),
    gender VARCHAR(10),
    institute_name VARCHAR(255),
    address TEXT,
    city TEXT,
    pincode VARCHAR(20), -- Added pincode column
    district INT,
    state INT,
    country VARCHAR(255),
    mobile VARCHAR(20),
    mobile_verified TINYINT(1) DEFAULT 0,
    email VARCHAR(255) NOT NULL,
    email_verified TINYINT(1) DEFAULT 0,
    facebook VARCHAR(255),
    linkedin VARCHAR(255),
    twitter VARCHAR(255),
    orcid VARCHAR(255),
    biography TEXT,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdby VARCHAR(255),
    updatedby VARCHAR(255),
    FOREIGN KEY (login_id) REFERENCES login(id) ON DELETE CASCADE
);



CREATE TABLE registration (
    id INT AUTO_INCREMENT PRIMARY KEY,
    programme_id INT NOT NULL,
    participant_id INT NOT NULL,
    reg_type ENUM('1', '2') NOT NULL COMMENT '1-Individual, 2-Group',
    reg_group_name VARCHAR(255),
    accommodation ENUM('Yes', 'No'),
    category VARCHAR(50),
    passno VARCHAR(255),
    passv DATE,
    pob VARCHAR(255),
    nation VARCHAR(255),
    pno VARCHAR(255),
    pbank VARCHAR(255),
    pdate DATE,
    pamt VARCHAR(255),
    payment ENUM('1', '2') NOT NULL COMMENT '1-Offline, 2-Online',
    payment_verification ENUM('0', '1', '2', '3') DEFAULT '0' COMMENT '0-Unverified, 1-Verified, 2-Hold, 3-Reject',
    payment_remarks TEXT,
    acc_cat INT(3),
    acc_place ENUM('1', '2', '3', '4', '5') COMMENT '1-Univ, 2-L D, 3-IUCTE, 4-Yug, 5-Faculty',
    acc_roomno VARCHAR(255),
    acc_remarks TEXT,
    adate DATETIME,
    rdate DATETIME,
    mode VARCHAR(50),
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdby VARCHAR(255),
    updatedby VARCHAR(255),
    FOREIGN KEY (programme_id) REFERENCES programme(id) ON DELETE CASCADE,
    FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE
);

CREATE TABLE coordinators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login_id INT NOT NULL,
    name VARCHAR(255) NULL,
    designation VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    mobile VARCHAR(20),
    contact_no VARCHAR(20),
    division VARCHAR(255),
    facebook VARCHAR(255),
    linkedin VARCHAR(255),
    twitter VARCHAR(255),
    orcid VARCHAR(255),
    biography TEXT,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdby VARCHAR(255),
    updatedby VARCHAR(255),
    FOREIGN KEY (login_id) REFERENCES login(id) ON DELETE CASCADE
);

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

CREATE TABLE programme_questionnaire (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    programme_id INT NOT NULL,
    question_id INT UNSIGNED NOT NULL,
    sub_question_id VARCHAR(100),
    sequence INT,
    mandatory ENUM('Yes', 'No') NOT NULL DEFAULT 'No',
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    createdby VARCHAR(255),
    updatedby VARCHAR(255),
    FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);


CREATE TABLE programme_questions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    questions VARCHAR(255),
    sub_question INT(11) NULL,
    answerType INT(10) NOT NULL DEFAULT 0 COMMENT '1-Checkbox, 2-Radio Button, 3-Text, 4-Likert',
    answerOption VARCHAR(255) NULL,
    answerValidation VARCHAR(100) NULL,
    maxResponse VARCHAR(100) NULL,
    active TINYINT(2) NOT NULL DEFAULT 0,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    createdby VARCHAR(50) NULL,
    updatedby VARCHAR(50) NULL
);

CREATE TABLE `programme_responses` (
    `id` INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `programme_id` INT(10) NOT NULL,
    `question_id` INT(10) UNSIGNED NOT NULL,
    `participant_id` INT(10) NOT NULL,
    `response` TEXT DEFAULT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 0,
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `createdby` VARCHAR(255) NOT NULL,
    `updatedby` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `fk_programme_responses_programme` FOREIGN KEY (`programme_id`) REFERENCES `programme`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_programme_responses_questions` FOREIGN KEY (`question_id`) REFERENCES `programme_questions`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_programme_responses_participants` FOREIGN KEY (`participant_id`) REFERENCES `participants`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
