<?php

declare(strict_types=1);

namespace Ozzie\Html;

use Ozzie\Html\Element\Html;

class Page extends Html
{
    /**
     * Page instances
     *
     * @var array<string, self>
     */
    private static array $pages = [];

    private function __construct()
    {
        parent::__construct();
    }

    public static function get_instance(string $key): self
    {
        return self::$pages[$key] ??= new self;
    }
}
