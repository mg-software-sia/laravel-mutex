<?php


namespace MgSoftware\MySQLMutex;


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
     * Acquires a lock by name.
     * @param string $name of the lock to be acquired. Must be unique.
     * @param int $timeout time (in seconds) to wait for lock to be released. Defaults to zero meaning that method will return
     * false immediately in case lock was already acquired.
     * @return bool lock acquiring result.
     */
    public function acquire(string $name, int $timeout = 0): bool
    {
        $this->_locks[] = $this->hashLockName($name);

        return true;
    }


    /**
     * Releases acquired lock. This method will return false in case the lock was not found.
     * @param string $name of the lock to be released. This lock must already exist.
     * @return bool lock release result: false in case named lock was not found..
     */
    public function release(string $name): bool
    {
//        if ($this->releaseLock($name)) {
//            $index = array_search($name, $this->_locks);
//            if ($index !== false) {
//                unset($this->_locks[$index]);
//            }
//
//            return true;
//        }

        return false;
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
}