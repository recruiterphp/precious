<?php

namespace Precious;

use Exception;

trait SingletonScaffold
{
    protected static ?self $instance = null;

    private function __construct()
    {
        // nothing is good
    }

    /**
     * @throws Exception
     */
    final public function __clone()
    {
        throw new Exception('You can not clone a singleton');
    }

    /**
     * @throws Exception
     */
    final public function __sleep()
    {
        throw new Exception('You can not serialize a singleton');
    }

    /**
     * @throws Exception
     */
    final public function __wakeup()
    {
        throw new Exception('You can not deserialize a singleton');
    }

    public static function instance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
