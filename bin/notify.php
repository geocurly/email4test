<?php

declare(strict_types=1);

require_once __DIR__ . "/../src/core/core.php";

set_time_limit(0);

//TODO если планируется запуск нескольких экземпляров сприпта (например в разных подах)
//  нужно продумывать распределенную блокировку, чтобы не было повторных отправок

//Nested Loop  (cost=1.28..8998.54 rows=500 width=83) (actual time=0.080..22.615 rows=493 loops=1)
//  ->  Nested Loop  (cost=0.86..8475.37 rows=1000 width=83) (actual time=0.033..4.203 rows=1000 loops=1)
//        ->  Limit  (cost=0.43..770.87 rows=1000 width=16) (actual time=0.026..1.120 rows=1000 loops=1)
//              ->  Index Scan using subscriptions_validts_idx on subscriptions s  (cost=0.43..26701.46 rows=34657 width=16) (actual time=0.026..1.026 rows=1000 loops=1)
//                    Index Cond: ((validts >= (now() + '12:00:00'::interval)) AND (validts <= (now() + '3 days 12:00:00'::interval)))
//        ->  Index Scan using users_pkey on users u  (cost=0.42..7.69 rows=1 width=83) (actual time=0.003..0.003 rows=1 loops=1000)
//              Index Cond: (id = s.user_id)
//    ->  Index Scan using emails_pkey on emails e  (cost=0.42..0.52 rows=1 width=43) (actual time=0.018..0.018 rows=0 loops=1000)
//        Index Cond: (email = u.email)
//        Filter: (valid AND confirmed)
//        Rows Removed by Filter: 1
//Planning Time: 0.428 ms
//Execution Time: 22.671 ms

$query = <<<SQL
    SELECT u.id, u.username, u.email, u.timezone FROM users u
    JOIN emails e ON u.email = e.email
    JOIN (
        SELECT user_id, validts FROM subscriptions s
        WHERE validts BETWEEN (NOW() + INTERVAL '12 hours') AND (NOW() + INTERVAL '3 days 12 hours')
        LIMIT $1
    ) rfe ON rfe.user_id = u.id
    WHERE e.valid
      AND e.confirmed;
    SQL;

$users = \DB\exec($query, [1000]);

// TODO решить проблему повторных отправок.
//  Стоит реализовать outbox с асинхронным разгребанием очереди
foreach ($users as $user) {
//    $sent = \Mail\send(
//        $user['email'],
//        "{$user['username']}, your subscription is expiring soon.",
//        'Subscription expiration',
//    );
    echo "{$user['email']}. {$user['username']}, your subscription is expiring soon.\n";
}