<?php

declare(strict_types=1);

require_once __DIR__ . "/../src/core/core.php";

use function Route\route;
use function Handler\{checkEmail, notFound, sendEmail};
use const Route\{CHECK, NOT_FOUND, SEND, VERIFY};

echo route(
    handlers: [
        CHECK => static fn(): string => checkEmail(),
        SEND => static fn(): string => sendEmail(),
        NOT_FOUND => static fn(): string => notFound(),
        VERIFY => static fn(): string => "<h1>Your email verification is finished</h1>",
    ],
);