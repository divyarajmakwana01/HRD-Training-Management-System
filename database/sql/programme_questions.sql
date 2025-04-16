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