<?php

declare(strict_types=1);

namespace DB;

function connect(): ?object
{
    // TODO спрятать в ENV
    $connString = "host=db port=5432 dbname=email4test user=email4test password=email4test";
    return pg_connect($connString) ?: null;
}