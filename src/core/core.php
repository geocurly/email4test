<?php

declare(strict_types=1);

namespace Core;

const ROOT = __DIR__ . "/../../";
const SRC = ROOT . "/src";

function init(): void {
    // Если бы делали через ООП, можно было бы сделать нормальный автолоадинг
    /** @var \SplFileObject $obj */
    foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(SRC)) as $obj) {
        if (
            $obj->isDir() ||
            $obj->getExtension() !== 'php' ||
            $obj->getRealPath() === __FILE__
        ) {
            continue;
        }

        require_once $obj->getRealPath();
    }
}

init();