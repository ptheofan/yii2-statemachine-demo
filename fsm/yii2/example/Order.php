<?php
/**
 * User: Paris Theofanidis
 * Date: 23.11.17
 * Time: 21:48
 */

namespace app\fsm\yii2\example;

use app\fsm\providers\StateMachineProviderXML;
use app\fsm\yii2\StateMachineHandlerModelBehavior;
use Yii;
use yii\base\Model;

class Order extends Model
{
    public function behaviors()
    {
        $smProvider = StateMachineProviderXML::fromFile(Yii::getAlias('@app/fsm/yii2/example/order.xml'));
        return [
            [
                'class' => StateMachineHandlerModelBehavior::class,
                'attr' => 'sm_checkout_status',
                'virtualAttr' => 'checkoutStatus',
                'stateMachine' => $smProvider->createStateMachine('checkout'),
            ],
            [
                'class' => StateMachineHandlerModelBehavior::class,
                'attr' => 'sm_online_payment_status_1',
                'virtualAttr' => 'onlinePayment1',
                'stateMachine' => $smProvider->createStateMachine('online_payment'),
            ],
            [
                'class' => StateMachineHandlerModelBehavior::class,
                'attr' => 'sm_online_payment_status_2',
                'virtualAttr' => 'onlinePayment2',
                'stateMachine' => $smProvider->createStateMachine('online_payment'),
            ],
            [
                'class' => StateMachineHandlerModelBehavior::class,
                'attr' => 'sm_bank_transfer_status',
                'virtualAttr' => 'bankTransfer',
                'stateMachine' => $smProvider->createStateMachine('bank_transfer'),
            ],
        ];
    }
}