<?php

declare(strict_types=1);

namespace DB;

/**
 *
 * @param string $query
 * @param array $params
 * @return array|bool
 */
function exec(string $query, array $params): array|bool
{
    try {
        $conn = connect();
        $result = pg_query_params($conn, $query, $params);
        if ($result !== false) {
            return pg_fetch_all($result);
        }
    } finally {
        close($conn);
    }

    return false;
}
