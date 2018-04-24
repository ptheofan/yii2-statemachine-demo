<?php
namespace app\fsm;

use Illuminate\Support\Collection;

/**
 * Class StateMachineCommand
 *
 * @package app\fsm
 */
abstract class StateMachineCommand implements IStateMachineCommand
{
    use TStateMachineDataSetAccessibility;

    /**
     * StateMachineCommand constructor.
     */
    public function __construct()
    {
        $this->dataSet = new Collection();
    }

    /**
     * @param IStateMachineContext $context
     */
    abstract public function run(IStateMachineContext $context);
}