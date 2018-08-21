<?php
declare(strict_types=1);

namespace InputGuard;

use InputGuard\Guards\BoolGuard;
use InputGuard\Guards\ErrorMessagesBase;
use InputGuard\Guards\FloatGuard;
use InputGuard\Guards\Guard;
use InputGuard\Guards\InListGuard;
use InputGuard\Guards\InstanceOfGuard;
use InputGuard\Guards\IntGuard;
use InputGuard\Guards\IterableFloatGuard;
use InputGuard\Guards\IterableGuard;
use InputGuard\Guards\IterableIntGuard;
use InputGuard\Guards\IterableStringableGuard;
use InputGuard\Guards\IterableStringGuard;
use InputGuard\Guards\StringableGuard;
use InputGuard\Guards\StringGuard;

/** @noinspection EfferentObjectCouplingInspection */

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InputGuard implements GuardChain
{
    use ErrorMessagesBase;

    /**
     * @var Guard[]
     */
    private $guards = [];

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var bool|null
     */
    private $validated;

    /**
     * Allows for injection of a Configurable object.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        $this->configuration = $configuration ?? new DefaultConfiguration();
    }

    /**
     * Reset all properties except the configuration to there default values.
     */
    public function __clone()
    {
        $this->errorMessages = [];
        $this->guards        = [];
        $this->validated     = null;
    }

    /**
     * Check for success on all the guard objects.
     *
     * @return bool
     */
    public function success(): bool
    {
        if ($this->validated !== null) {
            return $this->validated;
        }

        // Pass a local error messages variable to avoid merging arrays inside a loop.
        $error_messages = [];
        $success        = array_reduce(
            $this->guards,
            function (bool $success, Guard $guard) use (&$error_messages): bool {
                // Check for success/failure for all collected Val's.
                if ($guard->success() === false) {
                    $error_messages[] = $guard->pullErrorMessages();
                    $success          = false;
                }

                return $success;
            },
            true
        );

        if ($error_messages) {
            // Merge the errors, remove duplicates, and reset the keys.
            $this->errorMessages = array_values(array_unique(array_merge($this->errorMessages, ...$error_messages)));
        }

        $this->validated = $success;

        return $success;
    }

    /**
     * Return the current instance.
     *
     * @return InputGuard
     */
    public function value(): InputGuard
    {
        return $this;
    }

    /**
     * Allow for the injection of any Guard object.
     *
     * @param Guard $val
     *
     * @return Guard
     */
    public function add(Guard $val): Guard
    {
        $this->validated = null;
        $this->guards[]  = $val;

        return $val;
    }

    public function bool($input): BoolGuard
    {
        $val = new BoolGuard($input, $this->configuration->defaultValue(BoolGuard::class));
        $this->add($val);

        return $val;
    }

    public function int($input): IntGuard
    {
        $val = new IntGuard($input, $this->configuration->defaultValue(IntGuard::class));
        $this->add($val);

        return $val;
    }

    public function float($input): FloatGuard
    {
        $val = new FloatGuard($input, $this->configuration->defaultValue(FloatGuard::class));
        $this->add($val);

        return $val;
    }

    public function instanceOf($input, string $className): InstanceOfGuard
    {
        $val = new InstanceOfGuard($input, $className, $this->configuration->defaultValue(StringGuard::class));
        $this->add($val);

        return $val;
    }

    public function string($input): StringGuard
    {
        $val = new StringGuard($input, $this->configuration->defaultValue(StringGuard::class));
        $this->add($val);

        return $val;
    }

    public function stringable($input): StringableGuard
    {
        $val = new StringableGuard($input, $this->configuration->defaultValue(StringableGuard::class));
        $this->add($val);

        return $val;
    }

    /**
     * Wrapper to iterable.
     *
     * @param $input
     *
     * @return IterableGuard
     */
    public function array($input): IterableGuard
    {
        return $this->iterable($input);
    }

    public function iterable($input): IterableGuard
    {
        $val = new IterableGuard($input, $this->configuration->defaultValue(IterableGuard::class));
        $this->add($val);

        return $val;
    }

    public function iterableInt($input): IterableIntGuard
    {
        $val = new IterableIntGuard($input, $this->configuration->defaultValue(IterableIntGuard::class));
        $this->add($val);

        return $val;
    }

    public function iterableFloat($input): IterableFloatGuard
    {
        $val = new IterableFloatGuard($input, $this->configuration->defaultValue(IterableFloatGuard::class));
        $this->add($val);

        return $val;
    }

    public function iterableString($input): IterableStringGuard
    {
        $val = new IterableStringGuard($input, $this->configuration->defaultValue(IterableStringGuard::class));
        $this->add($val);

        return $val;
    }

    public function iterableStringable($input): IterableStringableGuard
    {
        $val = new IterableStringableGuard($input, $this->configuration->defaultValue(IterableStringableGuard::class));
        $this->add($val);

        return $val;
    }

    public function inList($input, iterable $list): InListGuard
    {
        $val = new InListGuard(
            $input,
            $list,
            $this->configuration->defaultValue(InListGuard::class),
            $this->configuration->defaultStrict(InListGuard::class)
        );

        $this->add($val);

        return $val;
    }
}
