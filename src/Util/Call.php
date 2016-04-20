<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Call
{
    /**
     * @param callable $callable
     *
     * @return Call
     */
    public static function begin(callable $callable): Call
    {
        return new static($callable);
    }

    /** @var callable[] */
    private $catchers = [];
    /** @var string */
    private $current;
    /** @var callable */
    private $wrapped;

    /**
     * @param string $exceptionClass
     * @param callable|null $catcher
     *
     * @return $this
     *
     * @api
     */
    public function rescue(string $exceptionClass = \Exception::class, callable $catcher = null)
    {
        $this->catchers[$exceptionClass][] = $catcher !== null
            ? $catcher
            : function () {
                // do nothing, it is rescue
            };
        $this->current = $exceptionClass;
        return $this;
    }

    /**
     * @param int $times to repeat
     * @param int $timeout in milliseconds between repeats
     *
     * @return $this
     *
     * @api
     */
    public function retry(int $times = 1, int $timeout = 0)
    {
        if ($this->current !== null) {
            $this->catchers[$this->current][] = function (...$args) use ($times, $timeout) {
                static $lTimes;
                if ($lTimes === null) {
                    $lTimes = $times;
                }
                // the first end() is already invoked
                $lTimes--;
                if ($lTimes > 0) {
                    usleep($timeout);
                    return $this->end(...$args);
                }
                return null;
            };
        }
        return $this;
    }

    /**
     * @param array ...$args
     *
     * @return mixed
     *
     * @throws \Throwable
     *
     * @api
     */
    public function end(...$args)
    {
        try {
            $callback = $this->wrapped;
            return $callback(...$args);
        } catch (\Throwable $e) {
            $class = get_class($e);
            if (array_key_exists($class, $this->catchers)) {
                $latest = null;
                foreach ($this->catchers[$class] as $catcher) {
                    $latest = $catcher(...$args);
                }
                return $latest;
            } else {
                throw $e;
            }
        }
    }

    /**
     * @param callable $callable
     */
    private function __construct(callable $callable)
    {
        $this->wrapped = $callable;
    }
}
