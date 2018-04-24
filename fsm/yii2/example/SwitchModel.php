<?php
namespace app\fsm\yii2\example;

use app\fsm\providers\StateMachineProviderXML;
use app\fsm\yii2\StateMachineHandlerModelBehavior;
use Yii;
use yii\base\Model;

/**
 * Class DummyModel
 *
 * @package app\fsm\yii2\example
 *
 * @property StateMachineHandlerModelBehavior $status
 */
class SwitchModel extends Model
{
    public $sm_status;

    /**
     * @return array
     * @throws \app\fsm\EInvalidConfiguration
     */
    public function behaviors()
    {
        return [
            [
                'class' => StateMachineHandlerModelBehavior::class,
                'attr' => 'sm_status',
                'virtualAttr' => 'status',
                'stateMachine' => StateMachineProviderXML::fromFile(Yii::getAlias('@app/fsm/yii2/example/flip.xml'))->createStateMachine('flip'),
            ],
        ];
    }

    /**
     *
     */
    public function init()
    {
        // If we had multiple state machines attached that depend
        // one on another, we initialise only the one that should
        // get initialised so you don't have to worry about initialState
        // on attributes you don't want to be touched just yet
        $this->status->initAttribute();
    }
}