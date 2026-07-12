<?php

namespace Tests\Fixtures;

class PreservesRouteParameterMiddleware
{
    /**
     * @var array<int, string>
     */
    public static array $parameters = [];

    /**
     * @param array<int, string> $parameters
     */
    public function setParameters(array $parameters): void
    {
        self::$parameters = $parameters;
    }

    public function handle(): ?string
    {
        return null;
    }
}
