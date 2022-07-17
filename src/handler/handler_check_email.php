<?php

declare(strict_types=1);

namespace Handler;

use function DB\commit;

const CHECK_COST_CENT = 10;
const TIME_LIMIT = 60;

function checkEmail(): string {
    set_time_limit(TIME_LIMIT);

    $cost = CHECK_COST_CENT . "cent";
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return <<<HTML
        <style>
            form {
                color:#333;
                text-align: center;
            }
        </style>
        <form method="post" action="/check_email">
            <p>Check email $cost</p>
            <input type="text" name="email" placeholder="email for check">
            <input type="submit" content="check">
        </form>
        HTML;
    }

    $email = (string) $_POST['email'];
    $valid = \Mail\check($email);

    $conn = \DB\begin();
    try {
        $query = <<<SQL
        UPDATE wallet SET amount = amount - $1 WHERE email = $2 AND amount - $1 >= 0 RETURNING amount
        SQL;

        $amount = \DB\txExec($conn, $query, [CHECK_COST_CENT, $email]);

        if ($amount === []) {
            $message = "$email. You don't have enough money";
        } else if ($valid) {
            $message = "$email is valid. You paid " . CHECK_COST_CENT . " cent. You have {$amount[0]['amount']} cent";
            $query = <<<SQL
                INSERT INTO emails (email, valid, checked) VALUES ($1, $2, TRUE) 
                ON CONFLICT (email) DO UPDATE SET valid = $2 RETURNING email      
                SQL;

            \DB\txExec($conn, $query, [$email, $valid]);
        } else {
            $message = "$email is not valid. You paid " . CHECK_COST_CENT . " cent. You have {$amount[0]['amount']} cent";
        }

        commit($conn);
    } catch (\Throwable $e) {
        \DB\rollback($conn);
    }

    return <<<HTML
    <style>
        h1 {
            color:#333;
            text-align: center;
        }
    </style>
    <h1>$message</h1>
    HTML;
}
