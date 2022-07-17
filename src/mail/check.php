<?php

declare(strict_types=1);

namespace Mail;

function check(string $email): bool
{
    // Не самая идеальная проверка. Нужно явно понимать что мы хотим.
    // RFC слишком вольно формулируют требования, не все чаще почтовые сервисы делаают более жесткие требования.
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
