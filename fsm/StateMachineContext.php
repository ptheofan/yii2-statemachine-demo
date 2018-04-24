<?php
namespace app\fsm;

use Illuminate\Support\Collection;

/**
 * Class StateMachineContext
 *
 * @package app\fsm
 */
class StateMachineContext implements IStateMachineContext
{
    use TStateMachineDataSetAccessibility, TStateMachineContext;

    /**
     * StateMachineContext constructor.
     */
    public function __construct()
    {
        $this->dataSet = new Collection();
        $this->status = IStateMachineContext::STATUS_CONTINUE;
    }


}