<?php
/**
 * User: Paris Theofanidis
 * Date: 23.11.17
 * Time: 02:15
 */

namespace app\fsm\yii2\example\commands;

use app\fsm\IStateMachineContext;
use app\fsm\StateMachineCommand;

/**
 * Class Max
 *
 * @package app\fsm\yii2\example\commands
 */
class Max extends StateMachineCommand
{
    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $counter = 0;

    /**
     * @param IStateMachineContext $context
     */
    public function run(IStateMachineContext $context)
    {
        if ($this->counter > $this->limit) {
            $context->setStatus(IStateMachineContext::STATUS_ABORT);
            $context->setData('error', "Limit of {$this->limit} reached.");
        } else {
            $this->counter++;
        }
    }
}