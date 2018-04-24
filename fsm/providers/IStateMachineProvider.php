<?php
namespace app\fsm\providers;

use app\fsm\ETransitionNotFound;
use app\fsm\EInvalidConfiguration;
use app\fsm\EStateNotFound;
use app\fsm\IStateMachine;
use app\fsm\IStateMachineTransition;
use app\fsm\IStateMachineState;

/**
 * Interface IStateMachineProvider
 *
 * @package app\fsm\providers
 */
interface IStateMachineProvider
{
    /**
     * @return string
     */
    public function getDefaultStateMachineClass();

    /**
     * @param string $defaultStateMachineClass
     * @return $this
     */
    public function setDefaultStateMachineClass($defaultStateMachineClass);

    /**
     * @return string
     */
    public function getDefaultStateClass();

    /**
     * @param string $defaultStateClass
     * @return $this
     */
    public function setDefaultStateClass($defaultStateClass);

    /**
     * @return string
     */
    public function getDefaultTransitionClass();

    /**
     * @param string $defaultEventClass
     * @return $this
     */
    public function setDefaultTransitionClass($defaultEventClass);

    /**
     * @return string
     */
    public function getDefaultCommandClass();

    /**
     * @param string $defaultCommandClass
     * @return $this
     */
    public function setDefaultCommandClass($defaultCommandClass);

    /**
     * @param string $name
     * @return IStateMachine
     * @throws EInvalidConfiguration
     */
    public function createStateMachine($name);

    /**
     * Enumerate the states of this StateMachine
     *
     * @return string[]
     */
    public function enumStates();

    /**
     * Enumerate the events of a State
     *
     * @return string[]
    */
    public function enumTransitions();

    /**
     * @param string $stateValue
     * @return IStateMachineState
     * @throws EInvalidConfiguration
     * @throws EStateNotFound
    */
    public function createState($stateValue);

    /**
     * @param string $transitionValue
     * @return IStateMachineTransition
     * @throws EInvalidConfiguration
     * @throws ETransitionNotFound
     */
    public function createTransition($transitionValue);
}