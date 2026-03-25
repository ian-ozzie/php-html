<?php declare(strict_types = 1);

namespace Ozzie\Html;

use Ozzie\Html\Element\Html;

class Page extends Html {

    /**
     * Page instances
     *
     * @var array<string, static> $attributes
     */
    private static array $pages = [];

    private function __construct()
    {
        parent::__construct();
    }

    public static function get_instance(string $key): static
    {
        if (isset(static::$pages[$key]) === false) {
            static::$pages[$key] = new static();
        }

        return static::$pages[$key];
    }

}
