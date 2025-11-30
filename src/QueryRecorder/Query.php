<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\QueryRecorder;

use Illuminate\Database\Events\QueryExecuted;

/**
 * This file is part of the https://packagist.org/packages/spatie/laravel-ignition
 * Given that the package is supposed to be used in dev enviroments only
 * and having it as a dependency could introduce security issues
 * we are not using the package as a dependency but rather only copying the
 * two classes we need from it.
 *
 * \Spatie\LaravelIgnition\Recorders\QueryRecorder\Query
 * \Spatie\LaravelIgnition\Recorders\QueryRecorder\QueryRecorder
 */
class Query
{
    protected float $microtime;

    /**
     * @param  array<string, string>|null  $bindings
     */
    protected function __construct(
        protected string $sql,
        protected float $time,
        protected string $connectionName,
        protected ?array $bindings = null,
        ?float $microtime = null
    ) {
        $this->microtime = $microtime ?? microtime(true);
    }

    public static function fromQueryExecutedEvent(QueryExecuted $queryExecuted, bool $reportBindings = false): self
    {
        return new self(
            $queryExecuted->sql,
            $queryExecuted->time,
            /** @phpstan-ignore-next-line  */
            $queryExecuted->connectionName ?? '',
            $reportBindings ? $queryExecuted->bindings : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'sql' => $this->sql,
            'time' => $this->time,
            'connection_name' => $this->connectionName,
            'bindings' => $this->bindings,
            'microtime' => $this->microtime,
        ];
    }
}
