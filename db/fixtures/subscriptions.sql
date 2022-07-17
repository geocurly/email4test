INSERT INTO users (username, email)
SELECT name, name || '@gmail.com' as email FROM (
    SELECT md5(generate_series(1, 1000000)::TEXT) as name
) a;

INSERT INTO subscriptions (user_id, validts)
SELECT id, NOW() - INTERVAL '2 month' + (random() * (NOW()+'90 days' - NOW())) + '30 days' FROM users;

INSERT INTO emails (email, valid, confirmed)
SELECT email, true, (random() > 0.5)::BOOLEAN FROM users;