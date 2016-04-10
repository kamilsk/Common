<?php

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class CallableSugar
{
    /**
     * @param callable $callable
     *
     * @return $this
     */
    public static function begin(callable $callable)
    {
        return new static($callable);
    }

    /** @var array */
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
    public function rescue($exceptionClass = \Exception::class, callable $catcher = null)
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
    public function retry($times = 1, $timeout = 0)
    {
        if ($this->current !== null) {
            $this->catchers[$this->current][] = function () use ($times, $timeout) {
                static $lTimes;
                if ($lTimes === null) {
                    $lTimes = $times;
                }
                // the first was an end() call
                $lTimes--;
                if ($lTimes > 0) {
                    usleep($timeout);
                    return call_user_func_array([$this, 'end'], func_get_args());
                }
                return null;
            };
        }
        return $this;
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     *
     * @api
     */
    public function end()
    {
        $args = func_get_args();
        try {
            return call_user_func_array($this->wrapped, $args);
        } catch (\Exception $e) {
            $class = get_class($e);
            if (array_key_exists($class, $this->catchers)) {
                $latest = null;
                foreach ($this->catchers[$class] as $catcher) {
                    $latest = call_user_func_array($catcher, $args);
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
