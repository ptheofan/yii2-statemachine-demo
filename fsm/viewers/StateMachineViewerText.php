<?php
namespace app\fsm\viewers;

use app\fsm\IStateMachine;

/**
 * Class StateMachineViewerText
 * A very basic textual representation of a state machine
 *
 * @package app\fsm\viewers
 */
class StateMachineViewerText implements IStateMachineViewer
{
    /**
     * @var IStateMachine
     */
    private $sm;

    /**
     * @return IStateMachine
     */
    public function getStateMachine()
    {
        return $this->sm;
    }

    /**
     * @param IStateMachine $sm
     * @return $this
     */
    public function setStateMachine($sm)
    {
        $this->sm = $sm;
        return $this;
    }

    /**
     * @return string
     * @throws ENoStateMachineLoaded
     * @throws \app\fsm\EInvalidConfiguration
     * @throws \app\fsm\EInvalidParameter
     * @throws \app\fsm\EStateNotFound
     * @throws \app\fsm\ETransitionNotFound
     */
    public function render()
    {
        if (!$this->sm instanceof IStateMachine) {
            throw new ENoStateMachineLoaded("No StateMachine loaded.");
        }

        $rVal = [
            'StateMachine: ' . $this->sm->getName(),
            'InitialState: ' . $this->sm->getInitialStateValue(),
            '',
        ];
        foreach ($this->sm->getStates() as $state) {
            $rVal[] = sprintf("[%s]", $state->getValue());
            foreach ($state->getEnterCommands() as $command) {
                $rVal[] = sprintf("@on-enter %s", get_class($command));
            }
            foreach ($state->getExitCommands() as $command) {
                $rVal[] = sprintf("@on-enter %s", get_class($command));
            }
            foreach ($state->getTransitions() as $event) {
                $rVal[] = sprintf("[%s] ==> (%s) ==> [%s]", $state->getValue(), $event->getValue(), $event->getTarget());
            }
        }

        return implode("\n", $rVal);
    }
}