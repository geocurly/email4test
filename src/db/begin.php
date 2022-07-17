<?php

declare(strict_types=1);

namespace DB;

function begin(): object
{
    $conn = \DB\connect();
    pg_query($conn, "BEGIN");
    return $conn;
}