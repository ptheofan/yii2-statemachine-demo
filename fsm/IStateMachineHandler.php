<?php
namespace app\fsm;

/**
 * Interface IStateMachineHandler
 *
 * @package app\fsm
 */
interface IStateMachineHandler
{
    /**
     * Set the current state of the managed object. This
     * does not perform any validations and will not.
     * execute any commands or even update any journal.
     * To properly move from one state to another use the
     * trigger function
     *
     * @param IStateMachineState $state
     * @return $this
     */
    public function setState(IStateMachineState $state);

    /**
     * Get the current state of the managed object
     *
     * @return IStateMachineState|null
     * @throws EInvalidConfiguration
     * @throws EInvalidParameter
     * @throws EStateNotFound
     */
    public function getState();

    /**
     * @return mixed
     */
    public function getAttributeValue();

    /**
     * @param mixed $value
     * @return $this
     */
    public function setAttributeValue($value);

    /**
     * @return IStateMachine
     */
    public function getStateMachine();

    /**
     * @param IStateMachine $stateMachine
     * @return $this
     */
    public function setStateMachine(IStateMachine $stateMachine);

    /**
     * Get the transition under the current state represented
     * with the $transitionValue
     *
     * @param string $transitionValue
     * @return IStateMachineTransition|mixed
     * @throws EInvalidConfiguration
     * @throws EInvalidParameter
     * @throws EStateNotFound
     * @throws ETransitionNotFound
     */
    public function getTransition($transitionValue);

    /**
     * @return IStateMachineTransition[]|\Illuminate\Support\Collection
     * @throws EInvalidConfiguration
     * @throws EInvalidParameter
     * @throws EStateNotFound
     * @throws ETransitionNotFound
     */
    public function getTransitions();

    /**
     * Trigger a transition. This will perform a proper
     * transition from the current state to the transitions
     * target state.
     *
     * @param string $transitionValue
     * @return IStateMachineContext
     * @throws EInvalidConfiguration
     * @throws EInvalidParameter
     * @throws EStateNotFound
     * @throws ETransitionNotFound
     * @throws \yii\base\InvalidConfigException
     */
    public function trigger(string $transitionValue);

    /**
     * Initialise the managed objects property value to
     * the StateMachines initial value
     */
    public function initAttribute();

    /**
     * Quickly returns the value of the current state
     * @return string
     */
    public function __toString();

    /**
     * @param string $eventName
     * @param IStateMachineContext $context
     */
    public function raiseStateMachineEvent($eventName, IStateMachineContext $context);
}