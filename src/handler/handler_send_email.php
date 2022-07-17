<?php

declare(strict_types=1);

namespace Handler;

function sendEmail(): string {
    $content = <<<HTML
    <style>
        h1 {
            color:#333;
            text-align: center;
        }
    </style>
    <h1>Send Email</h1>
    HTML;

    return $content;
}