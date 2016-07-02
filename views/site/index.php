<?php

/* @var $this yii\web\View */

use ptheofan\helpers\SystemHelper;

$profile = require(Yii::getAlias('@vendor/ptheofan/yii2-statemachine/profiles/default.php'));
$this->title = 'Yii2-StateMachine example';
?>


<div id="dot-graph">
    <?= SystemHelper::call('/usr/local/bin/dot', ['-Tsvg'], Yii::$app->smUserAccountStatus->dot($profile), $exitCode); ?>
</div>
