<?php

declare(strict_types=1);

namespace DB;

function rollback(object $conn): bool
{
    return pg_query($conn, "ROLLBACK") !== false;
}