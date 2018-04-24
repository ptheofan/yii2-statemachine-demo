<?php
namespace app\fsm;

/**
 * Trait TStateMachineContext
 * The class the uses the trait must implement IStateMachineContext
 *
 * @package app\fsm
 */
trait TStateMachineContext
{
    /**
     * @var IStateMachineTransition
     */
    private $transition;

    /**
     * @var IStateMachineState
     */
    private $from;

    /**
     * @var IStateMachineState
     */
    private $to;

    /**
     * @var IStateMachine
     */
    private $stateMachine;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $chainTransitionValue;

    /**
     * @var IStateMachineHandler
     */
    private $handler;

    /**
     * @return string
     */
    public function getChainTransitionValue()
    {
        return $this->chainTransitionValue;
    }

    /**
     * @param string $chainTransitionValue
     * @return $this
     */
    public function setChainTransitionValue($chainTransitionValue)
    {
        $this->chainTransitionValue = $chainTransitionValue;
        return $this;
    }

    /**
     * @return IStateMachineTransition
     */
    public function getTransition()
    {
        return $this->transition;
    }

    /**
     * @param IStateMachineTransition $transition
     * @return $this
     */
    public function setTransition($transition)
    {
        $this->transition = $transition;
        return $this;
    }

    /**
     * @return IStateMachineState
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param IStateMachineState $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return IStateMachineState
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param IStateMachineState $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return IStateMachine
     */
    public function getStateMachine()
    {
        return $this->stateMachine;
    }

    /**
     * @param IStateMachine $stateMachine
     * @return $this
     */
    public function setStateMachine($stateMachine)
    {
        $this->stateMachine = $stateMachine;
        return $this;
    }

    /**
     * @return IStateMachineHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param IStateMachineHandler $handler
     * @return $this
     */
    public function setHandler(IStateMachineHandler $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Executing the context will attempt to perform
     * the configured transition
     */
    public function execute()
    {
        if (!$this instanceof IStateMachineContext) {
            $class = get_class($this);
            throw new EStateMachine("{$class} must implement interface ISateMachineContext (trait TStateMachineContext requirement)");
        }

        // This event fires just before a transition is ready to start.
        // At this point a transition context exists. Nothing else has been done.
        $this->getHandler()->raiseStateMachineEvent(IStateMachineContext::EVT_BEFORE_TRANSITION, $this);
        if ($this->getStatus() !== IStateMachineContext::STATUS_CONTINUE) {
            return;
        }

        // Execute on-exit commands
        foreach ($this->getFrom()->getExitCommands() as $command) {
            $command->run($this);
            if ($this->getStatus() !== IStateMachineContext::STATUS_CONTINUE) {
                return;
            }
        }

        // This event fires after any ON-EXIT commands have been executed. State is
        // not yet updated and it's same as on BEFORE_TRANSITION
        $this->getHandler()->raiseStateMachineEvent(IStateMachineContext::EVT_AFTER_STATE_EXIT, $this);
        if ($this->getStatus() !== IStateMachineContext::STATUS_CONTINUE) {
            return;
        }

        // Change the state to the new state
        $this->getHandler()->setState($this->getTo());

        // This event fires after state has been updated but before any ON-ENTER
        // commands have been executed
        $this->getHandler()->raiseStateMachineEvent(IStateMachineContext::EVT_BEFORE_STATE_ENTER, $this);
        if ($this->getStatus() !== IStateMachineContext::STATUS_CONTINUE) {
            // Revert state change and exit
            $this->getHandler()->setState($this->getFrom());
            return;
        }

        // Execute on-enter commands
        foreach ($this->getTo()->getEnterCommands() as $command) {
            $command->run($this);
            if ($this->getStatus() !== IStateMachineContext::STATUS_CONTINUE) {
                // Revert state change and exit
                $this->getHandler()->setState($this->getFrom());
                return;
            }
        }

        // This event will fire after state is updated and any ON-ENTER commands
        // have been properly executed
        $this->getHandler()->raiseStateMachineEvent(IStateMachineContext::EVT_AFTER_TRANSITION, $this);
        if ($this->getStatus() !== IStateMachineContext::STATUS_CONTINUE) {
            // Revert state change and exit
            $this->getHandler()->setState($this->getFrom());
            return;
        }
    }
}