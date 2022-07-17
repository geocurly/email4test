<?php

declare(strict_types=1);

namespace DB;

/**
 * @param object $conn
 * @param string $query
 * @param array $params
 * @return array|bool
 */
function txExec(object $conn, string $query, array $params): array|bool
{
    $result = pg_query_params($conn, $query, $params);
    if ($result !== false) {
        return pg_fetch_all($result);
    }

    return false;
}
