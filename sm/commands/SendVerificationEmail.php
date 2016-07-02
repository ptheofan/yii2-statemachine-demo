<?php
/**
 * User: Paris Theofanidis
 * Date: 02/07/16
 * Time: 14:30
 */
namespace app\sm\commands;

use app\components\Messages;
use app\models\User;
use ptheofan\statemachine\commands\Command;
use ptheofan\statemachine\interfaces\StateMachineContext;

class SendVerificationEmail extends Command
{
    /**
     * Execute the command on the $context
     *
     * @param StateMachineContext $context
     * @return bool
     */
    public function execute(StateMachineContext $context)
    {
        /** @var User $user */
        $user = $context->getModel();
        Messages::add("Sending Verification Email to {$user->email}");
        return true;
    }
}