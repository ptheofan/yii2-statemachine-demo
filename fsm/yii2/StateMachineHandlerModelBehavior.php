<?php
namespace app\fsm\yii2;

use app\fsm\IStateMachine;
use app\fsm\IStateMachineContext;
use app\fsm\IStateMachineHandler;
use app\fsm\StateMachineContext;
use app\fsm\TStateMachineHandler;
use Yii;
use yii\base\Behavior;
use yii\base\Event;

/**
 * Class StateMachineModelBehavior
 *
 * @package app\fsm\yii2
 */
class StateMachineHandlerModelBehavior extends Behavior implements IStateMachineHandler
{
    use TStateMachineHandler;

    /**
     * This can be anything BaseYii::createObject() would accept as parameter
     * @see BaseYii::createObject()
     *
     * @var string|array
     */
    public $contextClass = StateMachineContext::class;

    /**
     * @var string
     */
    public $attr;

    /**
     * @var string
     */
    public $virtualAttr;

    /**
     * @param string $name
     * @param bool $checkVars
     * @return bool
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $name === $this->virtualAttr ? true : parent::canGetProperty($name, $checkVars);
    }

    /**
     * @param string $name
     * @param bool $checkVars
     * @return bool
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return $name === $this->virtualAttr ? false : parent::canGetProperty($name, $checkVars);
    }

    /**
     * @param string $name
     * @return StateMachineHandlerModelBehavior|mixed
     * @throws \yii\base\UnknownPropertyException
     */
    public function __get($name)
    {
        return $name === $this->virtualAttr ? $this : parent::__get($name);
    }

    /**
     * @return mixed
     */
    public function getAttributeValue()
    {
        return $this->owner->{$this->attr};
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setAttributeValue($value)
    {
        $this->owner->{$this->attr} = $value;
        return $this;
    }

    /**
     * @return IStateMachineContext
     * @throws \yii\base\InvalidConfigException
     */
    public function createContext()
    {
        return Yii::createObject($this->contextClass);
    }

    /**
     * @param string $eventName
     * @param IStateMachineContext $context
     */
    public function raiseStateMachineEvent($eventName, IStateMachineContext $context)
    {
        $event = new Event();
        $event->data = $context;
        $this->owner->trigger($eventName, $event);
    }
}