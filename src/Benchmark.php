<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug;

final class Benchmark
{
    /** @var int */
    private $start;

    /** @var integer */
    private $finish;

    /** @var string */
    private $caller;

    /** @var string */
    private $event;

    /** @var mixed */
    private $context;

    /**
     * @param string $caller
     * @param string $event
     * @param mixed  $context
     */
    public function __construct(string $caller, string $event, $context)
    {
        $this->start = microtime(true);
        $this->caller = $caller;
        $this->event = $event;
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getFinish(): ?int
    {
        return $this->finish;
    }

    /**
     * @return string
     */
    public function getCaller(): string
    {
        return $this->caller;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Indicates that benchmark is complete.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return !empty($this->finish);
    }

    /**
     * Complete benchmark record.
     *
     * @return int Returns elapsed time.
     */
    public function complete(): int
    {
        $this->finish = microtime(true);

        return $this->getElapsed();
    }

    /**
     * Returns elapsed time of given record. Will return null if record was not complete.
     *
     * @return int|null
     */
    public function getElapsed(): ?int
    {
        if (!$this->isComplete()) {
            return null;
        }

        return $this->finish - $this->start;
    }
}