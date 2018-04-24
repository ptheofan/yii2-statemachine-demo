<?php
namespace app\fsm\viewers;

use app\fsm\IStateMachine;

/**
 * Interface IStateMachineViewer
 *
 * @package app\fsm\viewers
 */
interface IStateMachineViewer
{
    /**
     * @return IStateMachine
     */
    public function getStateMachine();

    /**
     * @param IStateMachine $sm
     * @return $this
     */
    public function setStateMachine($sm);

    /**
     * @return mixed
     * @throws ENoStateMachineLoaded
     * @throws \app\fsm\EInvalidConfiguration
     * @throws \app\fsm\EInvalidParameter
     * @throws \app\fsm\EStateNotFound
     * @throws \app\fsm\ETransitionNotFound
     */
    public function render();
}