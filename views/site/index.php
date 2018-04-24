<?php

/** @var yii\web\View $this */
/** @var \app\components\Message[] $messages */
/** @var \ptheofan\statemachine\StateMachine $sm */
/** @var \app\models\User $user */
/** @var string $role */

use giovdk21\yii2SyntaxHighlighter\SyntaxHighlighter;
use ptheofan\helpers\SystemHelper;

$graphViz = new \ptheofan\statemachine\GraphViz([
    'profile' => require(Yii::getAlias('@vendor/ptheofan/yii2-statemachine/profiles/default.php')),
]);

$this->title = 'Interactive Demo | Yii2-StateMachine';
?>
<div class="head-info">
<p>This is a demo for the <a href="https://github.com/ptheofan/yii2-statemachine">Yii2-StateMachine</a> extension.</p>
<p>The <strong>panel on the left</strong> shows you the graph representing the state machine.</p>
<p>The <strong>panel on the right</strong> allows you to modify the state of the object. The available visible
    events change according to the current object attribute state and the role of the user (ie. your role).
    <span class="text-info">For the shake of simplicity on the demo, you will not need to login but
        you can rather just click the role (on the far right) and change your current role.</span></p>
<p>At the bottom of the page you can see the xml file that represents this state machine.
    The source code of this demo site can be found at
    <a href="https://github.com/ptheofan/yii2-statemachine-demo">github - yii2-statemachine-demo</a>.</p>
</div>

<div class="row row-section">
    <div class="col-md-6">
        <p id="sm">State Machine - <?= $sm->name; ?></p>
        <div id="dot-graph">
            <?= SystemHelper::call(Yii::$app->params['graphviz']['binary'], Yii::$app->params['graphviz']['args'], $graphViz->render($sm), $exitCode); ?>
        </div>
    </div>
    <div class="col-md-6">
        <div>These are the possible events (triggers) for the state <strong><?= $user->status; ?></strong> and role <?=
            \yii\bootstrap\ButtonDropdown::widget([
                'label' => $role,
                'tagName' => 'a',
                'containerOptions' => [
                    'class' => 'dropdrown-as-textinline',
                ],
                'dropdown' => [
                    'items' => [
                        ['label' => 'guest', 'url' => ['/', 'role' => 'guest']],
                        ['label' => 'owner', 'url' => ['/', 'role' => 'owner']],
                        ['label' => 'system', 'url' => ['/', 'role' => 'system']],
                    ],
                ],
            ]);
        ?></div>
        <ul class="list-inline">
            <?php $triggers = $user->status->getTriggers($user); ?>
            <?php if (empty($triggers)) { ?>
                <p class="text-center text-info"><em>No events for <?= $role; ?> in state <?= $user->status; ?>. Try changing the role.</em></p>
            <?php } else {
                foreach ($triggers as $event) { ?>
                    <li>
                        <?= \yii\bootstrap\Html::a($event->getLabel(),
                            ['/', 'event' => $event->getLabel(), 'role' => $role], [
                                'class' => 'btn btn-default',
                            ]); ?>
                    </li>
                <?php }
            }?>
        </ul>

        <div style="margin-top: 45px;">
            <p>Transition Log</p>
            <div class="console">
                <?php foreach ($messages as $message) {
                    echo $message->toHtml();
                } ?>
            </div>
        </div>
    </div>
</div>
<div class="row-section">
    <p id="smSourceCode">State Machine Graph Source Code</p>
    <?php
    SyntaxHighlighter::begin(['brushes' => ['xml']]);
    echo SyntaxHighlighter::getBlock(file_get_contents(Yii::getAlias('@vendor/ptheofan/yii2-statemachine/example-account.xml')), 'xml');
    SyntaxHighlighter::end();
    ?>
</div>