<?php

declare(strict_types=1);

arch()
    ->expect('Ozzie\Html')
    ->toUseStrictEquality()
    ->toUseStrictTypes()
    ->toBeCasedCorrectly()
    ->not->toHaveSuspiciousCharacters()
    ->not->toUse([
        'die', 'eval',
        'dd', 'print_r', 'var_dump', 'var_export',
        'date', 'gmdate', 'time',
        'compact', 'extract',
    ]);

arch()->preset()->php();
arch()->preset()->security();
