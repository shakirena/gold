-- Test fixtures for yii2_basic_tests database.
-- Applied by tests/setup-test-db.sh after schema copy.
-- Contains ONLY test data — no production records.

-- Minimal role required by users.id_role FK
INSERT INTO roles (id_role, role)
VALUES (1, 'manager')
ON DUPLICATE KEY UPDATE role = VALUES(role);

-- Test user: login=demo, password=demo (bcrypt)
-- Used by LoginFormTest::testLoginCorrect
INSERT INTO users (id_user, fio, login, password, id_role)
VALUES (1, 'Demo User', 'demo', '$2y$10$QC7SqVckYeLkTc97CU5PGu9W9LcYHyGYQ5x3XKMWNhdfywNJ9lSvu', 1)
ON DUPLICATE KEY UPDATE login = VALUES(login);
