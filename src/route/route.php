<?php

declare(strict_types=1);

namespace Route;

const SEND = 'send_email';
const CHECK = 'check_email';
const NOT_FOUND = 'not_found';
const VERIFY = 'verify';

const AVAILABLE = [
    SEND,
    CHECK,
    VERIFY,
];

/**
 * @param array<string, callable> $handlers
 * @return string
 */
function route(array $handlers): string {
    $contentByDefault = $handlers[NOT_FOUND] ?? static fn(): string => <<<HTML
    <h4>Oops! Something wrong!</h4>
    HTML;

    $target = pathinfo($_SERVER["REQUEST_URI"], PATHINFO_FILENAME);

    if (!in_array($target, AVAILABLE, true)) {
        return $contentByDefault();
    }

    $handler = $handlers[$target];
    if (!is_callable($handler)) {
        throw new \LogicException(
            "handler must be callable, but it has type: " . gettype($handler),
        );
    }

    $content = $handler();
    if (!is_string($content)) {
        throw new \LogicException(
            "result of handler must be a string: " . gettype($handler),
        );
    }

    return $content;
}
