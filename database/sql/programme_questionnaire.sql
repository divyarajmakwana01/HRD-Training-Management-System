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
