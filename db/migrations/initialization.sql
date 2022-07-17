CREATE TABLE subscriptions (
    id BIGSERIAL,
    user_id BIGINT NOT NULL,
    validts TIMESTAMP NOT NULL
);

CREATE INDEX subscriptions_user_id_idx ON subscriptions (user_id);

COMMENT ON TABLE subscriptions IS 'Таблица с подписками';
COMMENT ON COLUMN subscriptions.validts IS 'Время окончания подписки в UTC';
COMMENT ON COLUMN subscriptions.user_id IS 'Ссылка подписки на пользователя';

CREATE TABLE users (
    id BIGSERIAL,
    username TEXT NOT NULL,
    email BIGINT,
    timezone TEXT
);

CREATE INDEX users_email_idx ON users (username);

COMMENT ON TABLE users IS 'Таблица с пользователями-владельцами подписки подписки';
COMMENT ON COLUMN users.id IS 'Идентификатор пользователя';
COMMENT ON COLUMN users.username IS 'Имя пользователя';
COMMENT ON COLUMN users.email IS 'Уникальный email пользователя - ссылка на таблицу emails';
COMMENT ON COLUMN users.timezone IS 'Часовая зона пользователя';

CREATE TABLE emails (
    email TEXT PRIMARY KEY,
    valid BOOLEAN NOT NULL,
    checked BOOLEAN NOT NULL DEFAULT FALSE,
    confirmed BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE INDEX emails_checked_confirmed_idx ON emails (checked, confirmed);

COMMENT ON TABLE emails IS 'Таблица с данными email пользователя';
COMMENT ON COLUMN emails.email IS 'Уникальный email пользователя';
COMMENT ON COLUMN emails.valid IS 'Корректность email';
COMMENT ON COLUMN emails.checked IS 'Была ли проверка email пользователем';
COMMENT ON COLUMN emails.confirmed IS 'Был ли подтвержден email после регистрации';
