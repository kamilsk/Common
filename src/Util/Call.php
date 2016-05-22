<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Call
{
    /** @var array<string,callable[]> */
    private $catchers = [];
    /** @var string */
    private $current;
    /** @var array<string,bool> */
    private $parents = [];
    /** @var callable */
    private $wrapped;

    /**
     * @param callable $callable
     */
    private function __construct(callable $callable)
    {
        $this->wrapped = $callable;
    }

    /**
     * @param callable $callable
     *
     * @return Call
     *
     * @api
     */
    public static function begin(callable $callable): Call
    {
        return new self($callable);
    }

    /**
     * @param callable $callable
     *
     * @return array
     *
     * @api
     */
    public static function go(callable $callable): array
    {
        try {
            return [$callable(), null];
        } catch (\Throwable $e) {
            return [null, $e];
        }
    }

    /**
     * @param mixed[] ...$args
     *
     * @return mixed
     *
     * @throws \Throwable
     *
     * @api
     */
    public function __invoke(...$args)
    {
        return $this->end(...$args);
    }

    /**
     * @param mixed[] ...$args
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
            return ($this->wrapped)(...$args);
        } catch (\Throwable $e) {
            $class = get_class($e);
            if (array_key_exists($class, $this->catchers)) {
                return $this->catch($class, $args);
            } else {
                foreach ($this->parents as $class => $check) {
                    if ($check && is_subclass_of($e, $class, false)) {
                        return $this->catch($class, $args);
                    }
                }
                throw $e;
            }
        }
    }

    /**
     * @param string $exceptionClass
     * @param callable|null $catcher
     * @param bool $checkSubclasses
     *
     * @return Call
     *
     * @api
     */
    public function rescue(
        string $exceptionClass = \Exception::class,
        callable $catcher = null,
        bool $checkSubclasses = false
    ): Call
    {
        $this->catchers[$exceptionClass][] = $catcher ?? function () {
                // do nothing, it is rescue
            };
        $this->parents[$exceptionClass] = $checkSubclasses;
        $this->current = $exceptionClass;
        return $this;
    }

    /**
     * @param int $times to repeat
     * @param int $timeout in milliseconds between repeats
     *
     * @return Call
     *
     * @api
     */
    public function retry(int $times = 1, int $timeout = 0): Call
    {
        assert('$times >= 1 && $timeout >= 0');
        assert('$this->current !== null');
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
     * @param string $class
     * @param array $args
     *
     * @return mixed
     */
    private function catch (string $class, array $args)
    {
        $latest = null;
        foreach ($this->catchers[$class] as $catcher) {
            $latest = $catcher(...$args);
        }
        return $latest;
    }
}
