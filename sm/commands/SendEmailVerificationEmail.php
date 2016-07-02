<?php
/**
 * User: Paris Theofanidis
 * Date: 01/07/16
 * Time: 12:56
 */
namespace app\sm\commands;

use ptheofan\statemachine\commands\Command;
use ptheofan\statemachine\interfaces\StateMachineContext;

class SendEmailVerificationEmail extends Command
{
    /**
     * Execute the command on the $context
     *
     * @param StateMachineContext $context
     * @return mixed
     */
    public function execute(StateMachineContext $context)
    {
        // TODO: Implement execute() method.
    }
}