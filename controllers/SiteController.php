<?php

namespace app\controllers;

use app\components\Messages;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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

        return $this->render('index', [
            'user' => $user,
            'role' => $asRole,
            'sm' => Yii::$app->smUserAccountStatus,
            'messages' => Messages::get(),
        ]);
    }

    public function actionTrigger()
    {
        $asRole = Yii::$app->getRequest()->getQueryParam('role', 'guest');
        $trigger = Yii::$app->getRequest()->getQueryParam('event');
        $tx = User::getDb()->beginTransaction();
        /** @var User $user */
        $user = User::find()->andWhere(['email' => 'test@example.com'])->one();
        $context = $user->status->trigger($trigger, $asRole);
        $tx->commit();

        return $this->render('index', [
            'user' => $user,
            'role' => $asRole,
            'sm' => Yii::$app->smUserAccountStatus,
            'messages' => Messages::get(),
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
