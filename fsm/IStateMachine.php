<?php
namespace app\fsm;

use app\fsm\providers\IStateMachineProvider;
use Illuminate\Support\Collection;

/**
 * Interface IStateMachine
 *
 * @package app\fsm
 */
interface IStateMachine
{
    /**
     * StateMachine constructor.
     *
     * @param IStateMachineProvider|null $provider
     */
    public function __construct($provider = null);

    /**
     * @param string $value
     * @return IStateMachineState
     * @throws EInvalidConfiguration
     * @throws EStateNotFound
     * @throws EInvalidParameter
     */
    public function getState($value);

    /**
     * @return IStateMachineState[]|Collection
     * @throws EInvalidConfiguration
     * @throws EStateNotFound
     * @throws EInvalidParameter
     */
    public function getStates();

    /**
     * @param IStateMachineState[] $states
     * @return $this
     */
    public function setStates($states);

    /**
     * @param IStateMachineState $state
     * @return $this
     */
    public function addState(IStateMachineState $state);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getInitialStateValue();

    /**
     * @param string $initialStateValue
     * @return $this
     */
    public function setInitialStateValue($initialStateValue);
}