<?php

declare(strict_types=1);

namespace Handler;

function notFound(): string {
    return <<<HTML
    <style>
        h1 {
            color:#333;
            text-align: center;
        }
    </style>
    <h1>Unknown method called</h1>
    HTML;
}