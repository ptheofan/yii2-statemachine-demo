<?php
/**
 * User: Paris Theofanidis
 * Date: 02/07/16
 * Time: 14:46
 */
namespace app\sm\commands;

use app\components\Messages;
use ptheofan\statemachine\commands\Command;
use ptheofan\statemachine\interfaces\StateMachineContext;

class ReportStateTransition extends Command
{
    public $verb = 'Entering';

    /**
     * Execute the command on the $context
     *
     * @param StateMachineContext $context
     * @return bool
     */
    public function execute(StateMachineContext $context)
    {
        if (!$context->getEvent()) {
            Messages::add("{$this->verb} state {$context->getSm()->getInitialStateValue()} - (StateMachine Initialization)");
        } elseif ($this->verb === 'Entering') {
            Messages::add("{$this->verb} state {$context->getEvent()->getTargetState()}");
        } elseif ($this->verb === 'Leaving') {
            Messages::add("{$this->verb} state {$context->getEvent()->getState()}");
        }

        return true;
    }
}