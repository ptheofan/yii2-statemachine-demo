<?php

namespace app\controllers;

use app\components\Messages;
use app\models\User;
use ptheofan\statemachine\exceptions\EventNotFoundException;
use yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $user = User::find()->andWhere(['email' => 'test@example.com'])->one();
        if (!$user) {
            $tx = User::getDb()->beginTransaction();
            $user = new User();
            $user->email = 'test@example.com';
            $user->setPassword('123');
            $user->save();
            $tx->commit();
        }

        $asRole = Yii::$app->getRequest()->getQueryParam('role', 'guest');
        $trigger = Yii::$app->getRequest()->getQueryParam('event');
        $tx = User::getDb()->beginTransaction();
        /** @var User $user */
        $user = User::find()->andWhere(['email' => 'test@example.com'])->one();
        try {
            $context = $user->status->trigger($trigger, $asRole);
        } catch (EventNotFoundException $e) {
            Messages::add($e->getMessage(), Messages::TYPE_ERROR);
        } finally {
            if (!$user->hasErrors()) {
                $tx->commit();
            } else {
                foreach ($user->getErrors() as $attr => $errors) {
                    Messages::add(array_merge(["ERRORS `{$attr}`"], $errors), Messages::TYPE_ERROR);
                }
            }
        }

        $asRole = Yii::$app->getRequest()->getQueryParam('role', 'guest');

        return $this->render('index', [
            'user' => $user,
            'role' => $asRole,
            'sm' => Yii::$app->smUserAccountStatus,
            'messages' => Messages::get(),
        ]);
    }
}
