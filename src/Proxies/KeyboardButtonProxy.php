<?php

/** @noinspection PhpDocMissingThrowsInspection */

/** @noinspection PhpUnhandledExceptionInspection */

namespace Praskovi04\Telegrand\Proxies;

use Praskovi04\Telegrand\Exceptions\KeyboardException;
use Praskovi04\Telegrand\Keyboard\Button;
use Praskovi04\Telegrand\Keyboard\Keyboard;

/**
 * @internal
 *
 * @mixin Button
 */
class KeyboardButtonProxy extends Keyboard
{
    private Button $button;

    public function __construct(Keyboard $proxyed, Button $button)
    {
        parent::__construct();
        $this->rtl = $proxyed->rtl;
        $this->button = $button;
        $this->buttons = $proxyed->buttons;
    }

    /**
     * @param array<array-key, mixed> $arguments
     */
    public function __call(string $name, array $arguments): KeyboardButtonProxy
    {
        if (!method_exists($this->button, $name)) {
            throw KeyboardException::undefinedMethod($name);
        }

        $clone = $this->clone();

        $clone->button->$name(...$arguments);

        return $clone;
    }

    protected function clone(): KeyboardButtonProxy
    {
        return new self(parent::clone(), $this->button);
    }
}
