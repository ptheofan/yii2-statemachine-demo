<?php
namespace app\fsm\yii2;

use app\fsm\IStateMachine;
use app\fsm\viewers\ENoStateMachineLoaded;
use yii\helpers\Console;

/**
 * Class StateMachineViewerText
 *
 * @package app\fsm\yii2\example
 */
class StateMachineViewerText extends \app\fsm\viewers\StateMachineViewerText
{
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
        $sm = $this->getStateMachine();
        if (!$sm instanceof IStateMachine) {
            throw new ENoStateMachineLoaded("No StateMachine loaded.");
        }

        $rVal = [
            'StateMachine: ' . Console::ansiFormat($sm->getName(), [Console::BOLD]),
            'InitialState: ' . Console::ansiFormat($sm->getInitialStateValue(), [Console::BOLD]),
        ];

        foreach ($sm->getStates() as $state) {
            $rVal[] = sprintf('[%s]', Console::ansiFormat($state->getValue(), [Console::BOLD, Console::FG_CYAN]));
            foreach ($state->getEnterCommands() as $command) {
                $rVal[] = sprintf("  ENTER %s", Console::ansiFormat(get_class($command), [Console::BOLD]));
            }
            foreach ($state->getExitCommands() as $command) {
                $rVal[] = sprintf("  EXIT %s", Console::ansiFormat(get_class($command), [Console::BOLD]));
            }

            foreach ($state->getTransitions() as $event) {
                $rVal[] = sprintf('  TRANSITION %s (from [%s], to [%s]',
                    Console::ansiFormat($event->getValue(), [Console::FG_GREEN, Console::BOLD]),
                    Console::ansiFormat($state->getValue(), [Console::BOLD]),
                    Console::ansiFormat($event->getTarget(), [Console::BOLD])
                );
            }
        }

        return implode("\n", $rVal);
    }
}