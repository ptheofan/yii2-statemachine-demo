<?php
namespace app\fsm;

use app\fsm\providers\IStateMachineProvider;
use Illuminate\Support\Collection;

/**
 * Interface IStateMachineState
 *
 * @package app\fsm
 */
interface IStateMachineState
{
    /**
     * StateMachineState constructor.
     *
     * @param IStateMachineProvider $provider
     */
    public function __construct($provider = null);

    /**
     * @return IStateMachineTransition[]|Collection
     * @throws EInvalidConfiguration
     * @throws ETransitionNotFound
     * @throws EInvalidParameter
     */
    public function getTransitions();

    /**
     * @param string $value - the value of the event
     * @return IStateMachineTransition|mixed
     * @throws ETransitionNotFound
     * @throws EInvalidConfiguration
     * @throws EInvalidParameter
     */
    public function getTransition($value);

    /**
     * @param IStateMachineTransition[]|Collection $events
     * @return $this
     */
    public function setTransitions($events);

    /**
     * @param IStateMachineTransition $transition
     * @return $this
     */
    public function addTransition(IStateMachineTransition $transition);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getData($key, $default = null);

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value);

    /**
     * @return Collection
     */
    public function getDataSet();

    /**
     * @param Collection|array|null $dataSet
     */
    public function setDataSet($dataSet);

    /**
     * @return IStateMachineCommand[]
     */
    public function getEnterCommands();

    /**
     * @param IStateMachineCommand[] $enterCommands
     * @return $this
     */
    public function setEnterCommands(array $enterCommands);

    /**
     * @return IStateMachineCommand[]
     */
    public function getExitCommands();

    /**
     * @param IStateMachineCommand[] $exitCommands
     * @return $this
     */
    public function setExitCommands(array $exitCommands);
}