<?php
namespace app\fsm;

/**
 * Trait TStateMachineHandler
 * Class getting this trait MUST implement the IStateMachineHandler
 *
 * @package app\fsm
 */
trait TStateMachineHandler
{
    /**
     * @var IStateMachine
     */
    private $sm;

    /**
     * @return IStateMachineContext
     */
    abstract public function createContext();

    /**
     * @return IStateMachine
     */
    public function getStateMachine()
    {
        return $this->sm;
    }

    /**
     * @param IStateMachine $stateMachine
     * @return $this
     */
    public function setStateMachine(IStateMachine $stateMachine)
    {
        $this->sm = $stateMachine;
        return $this;
    }

    /**
     * Quickly returns the value of the current state
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getAttributeValue();
    }

    /**
     * Initialise the attribute to the initial state
     */
    public function initAttribute()
    {
        $this->setAttributeValue($this->getStateMachine()->getInitialStateValue());
    }

    /**
     * @return IStateMachineState|null
     * @throws EInvalidConfiguration
     * @throws EInvalidParameter
     * @throws EStateNotFound
     */
    public function getState()
    {
        return $this->getStateMachine()->getState($this->getAttributeValue());
    }

    /**
     * @param IStateMachineState $state
     * @return $this
     */
    public function setState(IStateMachineState $state)
    {
        $this->setAttributeValue($state->getValue());
        return $this;
    }

    /**
     * @param string $transitionValue
     * @return IStateMachineTransition|mixed
     * @throws EInvalidConfiguration
     * @throws EInvalidParameter
     * @throws EStateNotFound
     * @throws ETransitionNotFound
     */
    public function getTransition($transitionValue)
    {
        return $this->getState()->getTransition($transitionValue);
    }

    /**
     * @return IStateMachineTransition[]|\Illuminate\Support\Collection
     * @throws EInvalidConfiguration
     * @throws EInvalidParameter
     * @throws EStateNotFound
     * @throws ETransitionNotFound
     */
    public function getTransitions()
    {
        return $this->getState()->getTransitions();
    }


    /**
     * @param string $transitionValue
     * @return IStateMachineContext
     * @throws EInvalidConfiguration
     * @throws EInvalidParameter
     * @throws EStateNotFound
     * @throws ETransitionNotFound
     */
    public function trigger(string $transitionValue)
    {
        /** @var IStateMachineContext $context */
        $context = $this->createContext();

        if (!$context instanceof IStateMachineContext) {
            $class = get_class($context);
            throw new EInvalidConfiguration("{$class} must implement IStateMachineContext");
        }

        while($transitionValue) {
            $context->setFrom($this->getState())
                ->setTransition($this->getTransition($transitionValue))
                ->setTo($this->getStateMachine()->getState($this->getTransition($transitionValue)->getTarget()))
                ->setStateMachine($this->getStateMachine())
                ->setHandler($this)
                ->execute();

            // Check for transition chaining. If non set clear $transitionValue so
            // loop will end. Otherwise, load the chained transition value so process
            // will repeat automatically until no more chainTransitions are set.
            if ($context->getChainTransitionValue()) {
                $transitionValue = $context->getChainTransitionValue();
                $context->setChainTransitionValue(null);
            } else {
                $transitionValue = null;
            }
        }

        return $context;
    }
}