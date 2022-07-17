<?php

declare(strict_types=1);

require_once __DIR__ . "/../src/core/core.php";

set_time_limit(0);

const NOTIFICATION_LIMIT = 100;

$notified = [];
$subIds = [];

try {
    $users = getUsersForNotification(NOTIFICATION_LIMIT);
    $subIds = array_map(
        static fn(array $user): int =>  (int) $user['sub_id'],
        $users,
    );

    foreach ($users as $user) {
        $sent = \Mail\send(
            $user['email'],
            "{$user['username']}, your subscription is expiring soon.",
            'Subscription expiration',
        );

        if (!$sent) {
            echo "ERROR: {$user['email']}. Email was not sent.\n";
            continue;
        }

        echo "OK: {$user['email']}. Email was sent.\n";
        $notified[] = (int) $user['sub_id'];
    }
} catch (\Throwable $throwable) {
    rollbackNotifications(array_diff($subIds, $notified));
    throw  $throwable;
}

$diff = array_diff($subIds, $notified);
if ($diff !== []) {
    rollbackNotifications($diff);
}


/**
 * @param int $limit
 * @return array
 */
function getUsersForNotification(int $limit): array
{
    //CTE Scan on ready_for_notification  (cost=10674.78..10684.84 rows=503 width=112) (actual time=8.958..18.970 rows=122 loops=1)
    //  CTE ready_for_notification
    //    ->  Nested Loop  (cost=1.28..9008.95 rows=503 width=124) (actual time=8.955..18.897 rows=122 loops=1)
    //          ->  Nested Loop  (cost=0.86..8485.78 rows=1000 width=124) (actual time=0.033..8.659 rows=1000 loops=1)
    //                ->  Limit  (cost=0.43..781.28 rows=1000 width=16) (actual time=0.019..3.869 rows=1000 loops=1)
    //                      ->  Index Scan using subscriptions_validts_idx on subscriptions ss  (cost=0.43..27700.15 rows=35474 width=16) (actual time=0.019..3.775 rows=1000 loops=1)
    //                            Index Cond: ((validts >= (now() + '12:00:00'::interval)) AND (validts <= (now() + '3 days 12:00:00'::interval)))
    //                ->  Index Scan using users_pkey on users u  (cost=0.42..7.69 rows=1 width=116) (actual time=0.004..0.004 rows=1 loops=1000)
    //                      Index Cond: (id = ss.user_id)
    //    ->  Index Scan using emails_pkey on emails e  (cost=0.42..0.52 rows=1 width=43) (actual time=0.010..0.010 rows=0 loops=1000)
    //                Index Cond: (email = u.email)
    //                Filter: (valid AND confirmed)
    //                Rows Removed by Filter: 1
    //  CTE updated
    //    ->  Update on subscriptions  (cost=11.74..1665.83 rows=503 width=63) (actual time=20.548..20.548 rows=0 loops=1)
    //          ->  Nested Loop  (cost=11.74..1665.83 rows=503 width=63) (actual time=0.093..6.318 rows=122 loops=1)
    //                ->  HashAggregate  (cost=11.32..13.32 rows=200 width=40) (actual time=0.079..0.131 rows=122 loops=1)
    //                      Group Key: ready_for_notification_1.sub_id
    //                      ->  CTE Scan on ready_for_notification ready_for_notification_1  (cost=0.00..10.06 rows=503 width=40) (actual time=0.007..0.042 rows=122 loops=1)
    //                ->  Index Scan using subscriptions_pkey on subscriptions  (cost=0.42..8.26 rows=1 width=30) (actual time=0.050..0.050 rows=1 loops=122)
    //                      Index Cond: (id = ready_for_notification_1.sub_id)
    //Planning Time: 4.805 ms
    //Execution Time: 39.583 ms


    // Здесь сразу указываем что уже было отправлено
    // Это спасет нас от повторных отправок, но не спасет, если скрипт умрет после получения данных
    $query = <<<SQL
    WITH ready_for_notification AS (
        SELECT u.id, u.username, u.email, u.timezone, rfe.id AS sub_id FROM users u
            JOIN emails e ON u.email = e.email
            JOIN (
                SELECT ss.id, user_id FROM subscriptions ss
                WHERE ss.notified IS DISTINCT FROM TRUE
                  AND validts BETWEEN (NOW() + INTERVAL '12 hours') AND (NOW() + INTERVAL '3 days 12 hours')
                LIMIT $1
            ) rfe ON rfe.user_id = u.id
        WHERE e.valid AND confirmed
    ), updated AS (
        UPDATE subscriptions SET notified = TRUE WHERE id IN (SELECT sub_id FROM ready_for_notification)
    ) SELECT rfn.id, rfn.username, rfn.email, rfn.timezone, rfn.sub_id FROM ready_for_notification AS rfn
    SQL;

    // Хорошо бы фильтровать сообщения которые приходят не в рабочее время для пользователей у которых указана timezone
    return \DB\exec($query, [$limit]);
}

function rollbackNotifications(array $subscriptionIds): void
{
    $query = <<<SQL
    UPDATE subscriptions SET notified = FALSE WHERE id = ANY($1)
    SQL;

    \DB\exec($query, [ "{" . implode(',', $subscriptionIds) . "}"]);
}