<?php

declare(strict_types=1);

namespace Handler;

function checkEmail(): string {
    $content = <<<HTML
    <style>
        h1 {
            color:#333;
            text-align: center;
        }
    </style>
    <h1>Check Email</h1>
    HTML;

    return $content;
}