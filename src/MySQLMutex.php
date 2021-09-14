<?php


namespace MgSoftware\Mutex;

use DB;

class MySQLMutex
{
    /**
     * @var string[] names of the locks acquired by the current PHP process.
     */
    private array $_locks = [];

    public function __construct()
    {
        register_shutdown_function(function () {
            foreach ($this->_locks as $lock) {
                $this->release($lock);
            }
        });
    }

    /**
     * @param string $name
     * @param int $timeout
     * @param callable $callback
     * @return mixed
     * @throws FailedException
     */
    public function perform(string $name, int $timeout, callable $callback)
    {
        $this->acquire($name, $timeout);
        try {
            return $callback();
        } finally {
            $this->release($name);
        }
    }

    /**
     * Acquires a lock by name.
     * @param string $name of the lock to be acquired. Must be unique.
     * @param int $timeout time (in seconds) to wait for lock to be released. Defaults to zero meaning that method will return
     * false immediately in case lock was already acquired.
     * @return bool lock acquiring result.
     */
    public function acquire(string $name, int $timeout = 0): bool
    {
        $this->_locks[] = $name;

        // Acquire lock
        if (!$this->acquireLock($name, $timeout)) {
            throw new FailedException();
        }

        return true;
    }


    /**
     * Releases acquired lock. This method will return false in case the lock was not found.
     * @param string $name of the lock to be released. This lock must already exist.
     * @return bool lock release result: false in case named lock was not found..
     */
    public function release(string $name): bool
    {
        if ($this->releaseLock($name)) {
            $index = array_search($name, $this->_locks);
            if ($index !== false) {
                unset($this->_locks[$index]);
            }

            return true;
        }

        return false;
    }


    /**
     * Acquires lock by given name.
     * @param string $name of the lock to be acquired.
     * @param int $timeout time (in seconds) to wait for lock to become released.
     * @return bool acquiring result.
     * @see https://dev.mysql.com/doc/refman/8.0/en/locking-functions.html#function_get-lock
     */
    protected function acquireLock(string $name, int $timeout = 0)
    {
        $this->supported();
        $result = DB::selectOne('SELECT GET_LOCK(?, ?) as `acquired`', [
            $this->hashLockName($name),
            $timeout,
        ], false);
        return (bool)$result->acquired;
    }

    /**
     * Releases lock by given name.
     * @param string $name of the lock to be released.
     * @return bool release result.
     * @see https://dev.mysql.com/doc/refman/8.0/en/locking-functions.html#function_release-lock
     */
    protected function releaseLock(string $name): bool
    {
        $this->supported();
        $result = DB::selectOne('SELECT RELEASE_LOCK(?) as `released`', [
            $this->hashLockName($name),
        ], false);
        return (bool)$result->released;
    }

    /**
     * Generate hash for lock name to avoid exceeding lock name length limit.
     * @param string $name
     * @return string
     */
    protected function hashLockName(string $name): string
    {
        return sha1($name);
    }

    protected function supported(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            throw new \Exception('In order to use MysqlMutex connection must be configured to use MySQL database.');
        }
    }
}