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