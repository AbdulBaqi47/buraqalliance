<?php

namespace App\Drivers;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Exceptions\NoConnectionSetException;

class MySQLDatabaseManager implements TenantDatabaseManager
{
    /** @var string */
    protected $connection;

    protected function database(): Connection
    {
        if ($this->connection === null) {
            throw new NoConnectionSetException(static::class);
        }

        return DB::connection($this->connection);
    }

    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        $name = $tenant->database()->getName();

        $database = $this->database()->getMongoClient()->{$name};

        $database->createCollection("template");

        return (bool) isset($database);
    }

    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {

        $databaseName = $tenant->database()->getName();

        $this->database()->getMongoClient()->$databaseName->drop();

        return ! $this->databaseExists($tenant->database()->getName());
    }

    public function databaseExists(string $name): bool
    {
        $list = $this->database()->getMongoClient()->listDatabases();

        $collection = collect($list);

        return (bool) $collection->contains("name", $name);
    }

    public function makeConnectionConfig(array $baseConfig, string $databaseName): array
    {
        $baseConfig['database'] = $databaseName;

        return $baseConfig;
    }
}
