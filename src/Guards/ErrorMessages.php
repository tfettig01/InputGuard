<?php

declare(strict_types=1);

namespace InputGuard\Guards;

interface ErrorMessages
{
    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    /**
     * Add an error message that will be retrieved on validation failure.
     *
     * @param string $message
     *
     * @return $this
     */
    public function errorMessage(string $message);

    /**
     * Pull all the error messages.
     *
     * @return array
     */
    public function pullErrorMessages(): array;
}
