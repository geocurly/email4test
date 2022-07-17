<?php

declare(strict_types=1);

namespace DB;

/**
 *
 * @param string $query
 * @param array $params
 * @return array|bool
 */
function exec(/** language postgresql */ string $query, array $params): array|bool
{
    try {
        // TODO спрятать в ENV
        $connString = "host=db port=5432 dbname=email4test user=email4test password=email4test";
        $conn = pg_connect($connString);
        $result = pg_query_params($conn, $query, $params);
        if ($result !== false) {
            return pg_fetch_all($result);
        }
    } finally {
        pg_close($conn);
    }

    return false;
}
