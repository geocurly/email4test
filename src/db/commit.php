<?php

declare(strict_types=1);

namespace DB;

function commit(object $conn): bool
{
    return pg_query($conn, "COMMIT") !== false;
}