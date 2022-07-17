<?php

declare(strict_types=1);

namespace Mail;

function send(string $to, string $body, string $subject): bool
{
    // Лучше всего перейти на стороннее решение типа PHPMailer или других
    return mail($to, $subject, $body);
}
