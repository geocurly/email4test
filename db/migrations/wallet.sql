CREATE TABLE wallet (
    id BIGSERIAL PRIMARY KEY,
    email TEXT NOT NULL,
    amount BIGINT NOT NULL DEFAULT 0
);

CREATE INDEX wallet_email_idx ON wallet (email);

COMMENT ON TABLE wallet IS 'Кошелек пользователя';
COMMENT ON COLUMN wallet.email IS 'Email пользователя';
COMMENT ON COLUMN wallet.amount IS 'Состояние кошелька в центах';


UPDATE wallet SET amount = amount - :cost WHERE email = :email AND amount - :cost >= 0;