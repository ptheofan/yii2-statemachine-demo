<?php
namespace app\fsm;

/**
 * Class StateMachineHandler
 *
 * @package app\fsm
 */
class StateMachineHandler implements IStateMachineHandler
{
    use TStateMachineHandler;

    /**
     * @var Object
     */
    private $handlingObject;

    /**
     * @var string
     */
    private $handlingProperty;

    /**
     * StateMachineHandler constructor.
     *
     * @param IStateMachine $stateMachine
     * @param $object
     * @param $property
     */
    public function __construct(IStateMachine $stateMachine, $object, $property)
    {
        $this->sm = $stateMachine;
        $this->handlingObject = $object;
        $this->handlingProperty = $property;
    }

    /**
     * @return mixed
     */
    public function getAttributeValue()
    {
        return $this->handlingObject->{$this->handlingProperty};
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setAttributeValue($value)
    {
        $this->handlingObject->{$this->handlingProperty} = $value;
        return $this;
    }

    /**
     * @param string $eventName
     * @param IStateMachineContext $context
     */
    public function raiseStateMachineEvent($eventName, IStateMachineContext $context)
    {
        // This basic handler cannot really raise any event
        // as it cannot imply any events mechanism.
        // It is recommended that you create your own handler
        // to use in your system or if you're using some
        // framework either install the relative package
        // or create one and submit it.
    }

    /**
     * @return IStateMachineContext
     */
    public function createContext()
    {
        return new StateMachineContext();
    }
}