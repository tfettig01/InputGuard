<?php
declare(strict_types=1);

namespace InputGuardTests\Guards;

use InputGuard\Guards\IterableStringableGuard;
use PHPUnit\Framework\TestCase;
use stdClass;

class IterableStringableGuardTest extends TestCase
{
    /**
     * @dataProvider successProvider
     *
     * @param            $input
     * @param string     $message
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSuccess($input, string $message): void
    {
        $val = new IterableStringableGuard($input);

        self::assertTrue($val->success(), $message);
        self::assertSame($input, $val->value(), $message);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function successProvider(): array
    {
        $object = new class()
        {
            public function __toString()
            {
                return 'success';
            }
        };

        return [
            [[], 'An empty array'],
            [[$object, 'two', 1, true, 234.23], 'Array of types that can be cast to strings.'],
        ];
    }


    /**
     * @dataProvider failureProvider
     *
     * @param            $input
     * @param string     $message
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testFailure($input, string $message): void
    {
        $val = new IterableStringableGuard($input);

        self::assertFalse($val->success(), $message);
        self::assertNull($val->value(), $message);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function failureProvider(): array
    {
        return [
            [[new stdClass()], 'A class without toString.'],
            [[[1], [1,3,4]], 'An iterable of arrays'],
        ];
    }
}