<?php
namespace app\fsm;

use Illuminate\Support\Collection;

/**
 * Interface IStateMachineContext
 *
 * @package app\fsm
 */
interface IStateMachineContext
{
    // Continue context execution as planned
    const STATUS_CONTINUE = 'continue';

    // A failure has occurred. StateMachine will halt
    // the context execution.
    const STATUS_FAILED = 'failed';

    // No error has occurred but for whatever reason
    // abort the context execution.
    const STATUS_ABORT = 'abort';

    // This event fires just before a transition is ready to start.
    // At this point a transition context exists. Nothing else has been done.
    const EVT_BEFORE_TRANSITION = 'beforeTransition';

    // This event fires after any ON-EXIT commands have been executed. State is
    // not yet updated and it's same as on BEFORE_TRANSITION
    const EVT_AFTER_STATE_EXIT = 'afterStateExit';

    // This event fires after state has been updated but before any ON-ENTER
    // commands have been executed
    const EVT_BEFORE_STATE_ENTER = 'beforeStateEnter';

    // This event will fire after state is updated and any ON-ENTER commands
    // have been properly executed
    const EVT_AFTER_TRANSITION = 'afterTransition';

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
     * @return IStateMachineTransition
     */
    public function getTransition();

    /**
     * @param IStateMachineTransition $transition
     * @return $this
     */
    public function setTransition($transition);

    /**
     * @return IStateMachineState
     */
    public function getFrom();

    /**
     * @param IStateMachineState $fromState
     * @return $this
     */
    public function setFrom($fromState);

    /**
     * @return IStateMachineState
     */
    public function getTo();

    /**
     * @param IStateMachineState $toState
     * @return $this
     */
    public function setTo($toState);

    /**
     * @return IStateMachine
     */
    public function getStateMachine();

    /**
     * @param IStateMachine $stateMachine
     * @return $this
     */
    public function setStateMachine($stateMachine);

    /**
     * @return IStateMachineHandler
     */
    public function getHandler();

    /**
     * @param IStateMachineHandler $subject
     * @return $this
     */
    public function setHandler(IStateMachineHandler $subject);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getChainTransitionValue();

    /**
     * @param string $chainTransitionValue
     * @return $this
     */
    public function setChainTransitionValue($chainTransitionValue);

    /**
     * Executing the context will attempt to perform
     * the configured transition
     */
    public function execute();
}