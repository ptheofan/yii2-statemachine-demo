<?php

/** @var yii\web\View $this */
/** @var \app\components\Message[] $messages */
/** @var \ptheofan\statemachine\StateMachine $sm */
/** @var \app\models\User $user */
/** @var string $role */

use ptheofan\helpers\SystemHelper;

$graphViz = new \ptheofan\statemachine\GraphViz([
    'profile' => require(Yii::getAlias('@vendor/ptheofan/yii2-statemachine/profiles/default.php')),
]);

$this->title = 'Yii2-StateMachine example';
?>

<div class="row">
    <div class="col-md-6">
        <p>State Machine - <?= $sm->name; ?></p>
        <div id="dot-graph">
            <?= SystemHelper::call('/usr/local/bin/dot', ['-Tsvg'], $graphViz->render($sm), $exitCode); ?>
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
            <?php foreach ($user->status->getTriggers($role) as $event) { ?>
            <li>
                <?= \yii\bootstrap\Html::a($event->getLabel(), ['/', 'event' => $event->getLabel(), 'role' => $role], [
                    'class' => 'btn btn-default',
                ]); ?>
            </li>
            <?php } ?>
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