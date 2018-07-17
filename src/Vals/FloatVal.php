<?php
declare(strict_types=1);

namespace InVal\Vals;

use InVal\Configurable;

class FloatVal implements Valadatable
{
    /**
     * @var mixed
     */
    private $input;

    /**
     * @var int|null
     */
    private $value;

    /**
     * @var bool|null
     */
    private $validated;

    public function __construct($input, Configurable $configuration)
    {
        $this->input = $input;
        $this->value = $configuration->defaultValue(self::class);
    }

    public function success(): bool
    {
        if ($this->validated === null) {
            $options = [
                'options' => [
                    'min_range' => PHP_FLOAT_MIN,
                    'max_range' => PHP_FLOAT_MAX,
                ],
            ];

            $value = filter_var($this->input, FILTER_VALIDATE_FLOAT, $options);
            if ($value === false) {
                $this->validated = false;
            } /** @noinspection InvertedIfElseConstructsInspection */ else {
                $this->validated = true;
                $this->value     = $value;
            }
        }

        return $this->validated;
    }

    public function value(): ?float
    {
        $this->success();

        return $this->value;
    }
}
