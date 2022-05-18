CREATE TABLE users
(
    user_id INTEGER NOT NULL,
    PRIMARY KEY (user_id)
);

CREATE TABLE user_tokens
(
    user_id    INTEGER NOT NULL,
    token_type TEXT DEFAULT 'access' NOT NULL,
    token      TEXT    NOT NULL,
    PRIMARY KEY (user_id, token_type, token)
);
