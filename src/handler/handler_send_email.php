<?php

declare(strict_types=1);

namespace Handler;

const CHECK_SEND_LIMIT = 600;

function sendEmail(): string {
    set_time_limit(CHECK_SEND_LIMIT);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return <<<HTML
        <style>
            form {
                color:#333;
                text-align: center;
            }
        </style>
        <form method="post" action="/send_email">
            <p>Send email</p>
            <input type="text" name="email" placeholder="email">
            <input type="text" name="subject" placeholder="subject">
            <input type="text" name="body" placeholder="body">
            <input type="submit" content="send">
        </form>
        HTML;
    }

    $email = (string) $_POST['email'];
    $body = (string) $_POST['body'];
    $subject = (string) $_POST['subject'];
    // Так как мы монетизируем проверку валидности email нам не стоит сообщать пользователю что он отправил некорректный email
    if (\Mail\check($email)) {
        \Mail\send($email, $body, $subject);
    }

    return <<<HTML
    <style>
        h1 {
            color:#333;
            text-align: center;
        }
    </style>
    <h1>We start to send your message email to $email. If something wrong please check email by <a href="check_email">Check Email method</a></h1>
    HTML;
}