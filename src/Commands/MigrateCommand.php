<?php

namespace MgSoftware\Mutex\Commands;

use MgSoftware\Mutex\MySQLMutex;

class MigrateCommand extends \Illuminate\Database\Console\Migrations\MigrateCommand
{
    protected $signature = 'migrate:with-mutex {--database= : The database connection to use}
                {--force : Force the operation to run when in production}
                {--path=* : The path(s) to the migrations files to be executed}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--schema-path= : The path to a schema dump file}
                {--pretend : Dump the SQL queries that would be run}
                {--seed : Indicates if the seed task should be re-run}
                {--step : Force the migrations to be run so they can be rolled back individually}';

    protected $description = 'Run the database migrations synchronously. Useful for replicated services.';

    public function handle(): int
    {
        /** @var MySQLMutex $mutex */
        $mutex = app('mutex');
        $timeout = 6 * 60 * 60; // 6 hours

        $this->info('Running migrations with mutex...');
        return $mutex->perform('migrate:with-mutex', $timeout, fn() => parent::handle());
    }
}