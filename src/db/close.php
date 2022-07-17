<?php

declare(strict_types=1);

namespace DB;

function close(object $conn): bool
{
    return pg_close($conn);
}