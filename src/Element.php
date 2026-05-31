<?php

declare(strict_types=1);

namespace Ozzie\Html;

class Element extends Component implements ElementInterface
{
    use ElementTrait;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        public readonly string $tag,
        array $attributes = [],
        mixed $content = null,
    ) {
        if (in_array($this->tag, static::VOID_TAGS) === true) {
            $this->controls['void'] = true;
        }

        $this->set_attributes($attributes);
        if (isset($content) === true) {
            $this->add_content($content);
        }
    }
}
