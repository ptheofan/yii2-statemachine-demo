<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\fsm\yii2\example\SwitchModel;
use app\fsm\providers\StateMachineProviderArray;
use app\fsm\providers\StateMachineProviderXML;
use app\fsm\yii2\StateMachineViewerText;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * @throws \app\fsm\EInvalidConfiguration
     * @throws \app\fsm\EInvalidParameter
     * @throws \app\fsm\EStateNotFound
     * @throws \app\fsm\ETransitionNotFound
     * @throws \app\fsm\viewers\ENoStateMachineLoaded
     */
    public function actionIndex()
    {
        // Using XML Configuration
        echo "Using Provider ";
        echo Console::ansiFormat("StateMachineProviderXML\n", [Console::BOLD]);
        $provider = StateMachineProviderXML::fromFile(Yii::getAlias('@app/fsm/yii2/example/flip.xml'));
        echo (new StateMachineViewerText())->setStateMachine($provider->createStateMachine('flip'))->render();

        // XML also contains the example StateMachine which is more loaded. Uncomment the following line to see it
        // echo (new StateMachineViewerText())->setStateMachine($provider->createStateMachine('example'))->render();
        echo "\n";


        // Using Array Configuration
        echo "\n\nUsing Provider ";
        echo Console::ansiFormat("StateMachineProviderArray\n", [Console::BOLD]);
        $sm = (new StateMachineProviderArray())->createStateMachine('flip', [
            // This is how the configuration of the dummy StateMachine found in example.xml looks
            // like if it would have been defined in PHP array
            // State Machine Name (we can have multiple state machines configs in a single array)
            'flip' => [
                // InitialState
                '@initialState' => 'off',

                // StateMachine States. This StateMachine has 2 states
                // State ON (value on)
                'on' => [

                    // Defining transition with value Off (full format)
                    'switch_off' => [

                        // Setting transition target (target state value)
                        'target' => 'off',

                        // Additional Custom Data - go crazy
                        'name' => 'Off',
                    ],
                ],

                // State OFF (value off)
                'off' => [

                    // Defining transition (quick way)
                    // This is a transition with value = on and target state value = on
                    'switch_on' => 'on',
                ],
            ],
        ]);
        echo (new StateMachineViewerText())->setStateMachine($sm)->render();
        echo "\n";
    }

    /**
     * @throws \app\fsm\EInvalidConfiguration
     * @throws \app\fsm\EInvalidParameter
     * @throws \app\fsm\EStateNotFound
     * @throws \app\fsm\ETransitionNotFound
     * @throws \yii\base\InvalidConfigException
     */
    public function actionTrigger()
    {
        $model = new SwitchModel();
        printf("Current Status is %s\n", Console::ansiFormat($model->status, [Console::BOLD]));

        $transitions = $model->status->getTransitions();
        echo "Available Transitions\n";
        foreach ($transitions as $transition) {
            printf("  > %s\n", Console::ansiFormat($transition->getValue(), [Console::FG_GREEN]));
        }

        $transition = $transitions->first();
        printf("Triggering %s\n", Console::ansiFormat($transition->getValue(), [Console::FG_YELLOW]));

        $model->status->trigger($transition->getValue());
        printf("New Status is %s\n", Console::ansiFormat($model->status, [Console::BOLD, Console::FG_GREEN]));
    }

    /**
     * @throws \app\fsm\EInvalidConfiguration
     * @throws \app\fsm\EInvalidParameter
     * @throws \app\fsm\EStateNotFound
     * @throws \app\fsm\ETransitionNotFound
     * @throws \yii\base\InvalidConfigException
     */
    public function actionMax()
    {
        $model = new SwitchModel();
        $c = $model->status->trigger('switch_on');
        echo "Triggered (switch_on), Status = " . $c->getStatus() . "\n";

        $c = $model->status->trigger('switch_off');
        echo "Triggered (switch_off), Status = " . $c->getStatus() . "\n";

        // This on-enter command should issue an ABORT of the transition
        $c = $model->status->trigger('switch_on');
        echo "Triggered (switch_on), Status = " . $c->getStatus() . "\n";

        $c = $model->status->trigger('switch_off');
        echo "Triggered (switch_off), Status = " . $c->getStatus() . "\n";

        $c = $model->status->trigger('switch_on');
        echo "Triggered (switch_on), Status = " . $c->getStatus() . "\n";

        $model->status->trigger('switch_off');
        echo "Triggered (switch_off), Status = " . $c->getStatus() . "\n";
    }
}
