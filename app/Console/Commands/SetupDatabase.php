<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Creates Kickback's own Postgres schema (DB_SCHEMA), then rebuilds its tables and demo data inside it.
 * Only that schema is touched, so other apps living in the same database (e.g. in "public") are safe.
 */
class SetupDatabase extends Command
{
    protected $signature = 'kickback:setup-database {--no-seed : Create the tables without demo data}';

    protected $description = 'Create the Kickback schema, tables and demo data (only inside DB_SCHEMA)';

    public function handle(): int
    {
        $schema = (string) config('database.connections.pgsql.search_path');

        if (! preg_match('/^[a-z_][a-z0-9_]*$/', $schema) || $schema === 'public') {
            $this->error("DB_SCHEMA must be set to a dedicated schema name (e.g. kickback), got \"{$schema}\".");
            $this->line('This guard prevents wiping tables of other apps that live in "public".');

            return self::FAILURE;
        }

        DB::statement("create schema if not exists \"{$schema}\"");
        $this->info("Schema \"{$schema}\" ready.");

        return $this->call('migrate:fresh', [
            '--force' => true,
            '--seed' => ! $this->option('no-seed'),
        ]);
    }
}
