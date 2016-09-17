INSERT INTO loginlib.`accounts` VALUES (0, 'test', 'test@email.com', '12345', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO loginlib.`login_tokens` VALUES (0, 0, '12345', CURRENT_TIMESTAMP, NULL);

SELECT * FROM loginlib.`account` WHERE 1;
SELECT * FROM loginlib.`login_tokens` WHERE 1;

TRUNCATE loginlib.`accounts`;
TRUNCATE loginlib.`login_tokens`;

DROP TABLE loginlib.`accounts`;
DROP TABLE loginlib.`login_tokens`;