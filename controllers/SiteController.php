<?php

namespace app\controllers;


use app\models\Materials;
use app\models\Notifications;
use app\models\Subscribers;
use Yii;
use yii\debug\panels\EventPanel;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Users;
use yii\helpers\Url;

use yii\web\HttpException;
use app\models\SubscribersCategory;
use app\models\MaterialCategories;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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

    public function beforeAction($action)
    {
//        if (!Yii::$app->user->isGuest)
//        {
//            $this->layout = 'main';
////            $role = Yii::$app->user->identity->role;
////            if ($role == Users::ROLE_ADMIN) {
////                $this->redirect(['/administrator/all-materials','in_archive' => false]);
////            }
////            else if($role == Users::ROLE_METHODIST)
////            {
////                $this->redirect(['/methodist/all-materials','in_archive' => false]);
////            }
//            $this->redirect(['/materials/all-materials']);
//        }
//        else

        if($this->action->id == 'login' || $this->action->id == 'createuser' || $this->action->id == 'change-password' || $this->action->id == 'show-model')
        {
            $this->layout = 'main-login';
//            $this->redirect(['/site/login']);
        }
        else if($this->action->id == 'update-material-categories')
        {
            $this->layout = 'update-material-categories';
        }
        else if($this->action->id == 'error')
        {
            $this->layout = 'main-site';
            $alias = $this->action->controller->module->requestedRoute;
            $alias = explode('/',$alias);

            if($alias[0]=='news-feed')
            {
               unset($alias[0]);
            }
            $alias_material = array_pop($alias);

            $material_categories = 0;
            if(!empty($alias))
            {
                foreach ($alias as $alias_val)
                {
                    $material_categories = MaterialCategories::find()->where(['alias'=>$alias_val,'parent'=>$material_categories])->one()['id'];
//                    var_dump($material_categories);
                }
            }

            if($material_categories != 0)
            {
                $materials = Materials::find()->where(['alias'=>$alias_material,'material_categories_id'=>$material_categories,'status'=>Materials::PUBLISHED])->one();

                if(!is_null($materials))
                {
                    $this->redirect(['/news-feed/'.$materials['id']]);

                    return false;
                }
                else
                {
                    return true;
//                    return  new HttpException(517 ,'Материал не найден или устарел');
                }
            }
            else
            {
                if(Yii::$app->response->statusCode == '517')
                {
                    $this->layout = 'main';
                }
            }
        }
        else
        {
            $this->layout = 'main-site';
        }

        return true;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionUpdateMaterialCategories($email,$guid)
    {
        $model = Subscribers::find()->where(['email'=>$email])->one();
        if(is_null($model))
        {
            throw new HttpException(517 ,'Подписчик не найден');
        }

        $code = md5($model['id'] . $model['email'] . $model['time_created']);
        if($code == $guid)
        {
            if($model['status'] == Subscribers::CONFIRMATION_SENT)
            {
                $model['status'] = Subscribers::CONFIRMATION;
                $model['time_send'] = date('Y-m-d H:i:s',time());

                $model->save();
            }

            $user_groups_rights = SubscribersCategory::find()->where(['user_id'=>$model['id']])->all();
//
            $category_id = [];
            foreach ($user_groups_rights as $value)
            {
                $category_id[] = $value['category_id'];
            }

            $methodical_space = MaterialCategories::find()->where(['LOWER(title)'=>mb_strtolower('Методическое пространство'),'parent'=>0,'in_archive'=>false])->one();
            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();


            $json = json_encode(MaterialCategories::CreateTree($material_categories,$methodical_space['id'], $category_id));

            $subscribers = Yii::$app->getRequest()->post('Subscribers');

            if($subscribers)
            {

                if(!isset($subscribers['is_deleted']) || $subscribers['is_deleted'] == false)
                {
                    $model->setAttributes($subscribers);

                    if($model->save())
                    {
                        SubscribersCategory::deleteAll(['user_id'=>$model['id']]);

                        foreach ($subscribers['category_id'] as $post_value)
                        {
                            $subscribers = new SubscribersCategory();
                            $subscribers['user_id'] = $model['id'];
//                            $subscribers['school'] = $model['school'];
                            $subscribers['category_id'] = $post_value;
                            $subscribers['time_created'] = date("Y-m-d H:i:s",time());

                            $subscribers->save();
                        }

                        return $this->redirect(['/site/update-material-categories','email'=>$email, 'guid'=>$guid]);
                    }
                }
                else
                {
                    SubscribersCategory::deleteAll(['user_id'=>$model['id']]);
                    Subscribers::deleteAll(['id'=>$model['id']]);
                    return $this->redirect(['/']);
                }

            }

            return $this->render('addSubscribers',[
                'model'  => $model,
                'json' => $json,
            ]);
        }
        else
        {
            throw new HttpException(517 ,'Email не прошел валидацию');
        }
    }


    public function actionCreateuser()
    {
        echo 'pass = '.password_hash('49oSnb',PASSWORD_DEFAULT);
    }

    public function actionShowPreliminaryMaterial()
    {
        echo 'pass = '.password_hash('49oSnb',PASSWORD_DEFAULT);
        die;
    }


    public function actionHello()
    {
        $sSql = 'SELECT * FROM doc_index_tags WHERE MATCH('.Yii::$app->sphinx->quoteValue('дни').') ORDER BY hits DESC, published_date DESC limit 30000 option max_matches=30000'; //WHERE MATCH('.Yii::$app->sphinx->quoteValue('проект').') , published_date DESC
        $ids = Yii::$app->sphinx->createCommand($sSql)->queryAll();
        var_dump($ids); die();
    }


//    public function actionUpdateMaterialCategories($email,$guid)
//    {
//        if(md5($email.'123') == $guid)
//        {
//            $model = Subscribers::find()->where(['email'=>$email])->one();
//            if(is_null($model))
//            {
//                throw new HttpException(517 ,'Подписчик не найден');
//            }
//
//            $user_groups_rights = SubscribersCategory::find()->where(['user_id'=>$model['id']])->all();
////
//            $category_id = [];
//            foreach ($user_groups_rights as $value)
//            {
//                $category_id[] = $value['category_id'];
//            }
//
//            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
//            $json = json_encode(MaterialCategories::CreateTree($material_categories,0, $category_id));
//
////            $subscribers = Yii::$app->getRequest()->post('Subscribers');
////
////            if($subscribers)
////            {
////
////                if(!isset($subscribers['is_deleted']) || $subscribers['is_deleted'] == false)
////                {
////                    $model->setAttributes($subscribers);
////
////                    if($model->save())
////                    {
////                        SubscribersCategory::deleteAll(['user_id'=>$model['id']]);
////
////                        foreach ($subscribers['category_id'] as $post_value)
////                        {
////                            $subscribers = new SubscribersCategory();
////                            $subscribers['user_id'] = $model['id'];
////                            $subscribers['school'] = $model['school'];
////                            $subscribers['category_id'] = $post_value;
////                            $subscribers['time_created'] = date("Y-m-d H:i:s",time());
////
////                            $subscribers->save();
////                        }
////
////                        return $this->redirect(['/administrator/all-subscribers']);
////                    }
////                }
////                else
////                {
////                    SubscribersCategory::deleteAll(['user_id'=>$model['id']]);
////                    Subscribers::deleteAll(['id'=>$model['id']]);
////                    return $this->redirect(['/administrator/all-subscribers']);
////                }
////
////            }
//
//            return $this->render('addSubscribers',[
//                'model'  => $model,
//                'json' => $json,
//            ]);
//        }
//        else
//        {
//            throw new HttpException(517 ,'Email не прошел валидацию');
//        }
////        $subscribers = \app\models\UserGroupsRightsMaterialCategories::find()->where(['user_groups_id'=>1])->all();
////
////        $category_id_tree_total = [];
////        foreach ($subscribers as $value) {
////            $category_id_tree_total[] = $value['category_id'];
////        }
//
//
////        $category_id_tree = [];
////
////        $subscribers_category = SubscribersCategory::find()->select('category_id')->where(['user_id' => $value['id']])->all();
////
////        if (!empty($subscribers_category)) {
////            foreach ($subscribers_category as $subscribers_category_key => $subscribers_category_val) {
////                $category_id_tree[] = $subscribers_category_val['category_id'];
////            }
////        }array_unique
//
////        $category_id_tree = MaterialCategories::CreateTreeMaterialsUserChild(1679);
////            $array_diff = array_diff(MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree), $category_id_tree);
////            $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree);
////        var_dump(phpinfo());
////            throw new HttpException(517 ,'Ошибка при обновлении правил группы категорий подписчиков. id группы = ');
//
//        die;
//    }


//    public function action!!!!!!!!!!!!!!!!!!!!АВТОРИЗАЦИЯ ЧЕРЕЗ ГУГЛ()
//    {
//        if (!empty($_GET['code'])) {
//            // Отправляем код для получения токена (POST-запрос).
//            $params = array(
//                'client_id'     => '5130350111-l1i743n6f87750h737t5cdeitk1akgjp.apps.googleusercontent.com',
//                'client_secret' => 'eL43FxXlmPWRPN3RrXXyljsK',
//                'redirect_uri'  => 'https://museumday.mosmetod.ru/site/test-test',
//                'grant_type'    => 'authorization_code',
//                'code'          => $_GET['code']
//            );
//
//            $ch = curl_init('https://accounts.google.com/o/oauth2/token');
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($ch, CURLOPT_HEADER, false);
//            $data = curl_exec($ch);
//            curl_close($ch);
//
//            $data = json_decode($data, true);
//            if (!empty($data['access_token'])) {
//                // Токен получили, получаем данные пользователя.
//                $params = array(
//                    'access_token' => $data['access_token'],
//                    'id_token'     => $data['id_token'],
//                    'token_type'   => 'Bearer',
//                    'expires_in'   => 3599
//                );
//
//                $info = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?' . urldecode(http_build_query($params)));
//                $info = json_decode($info, true);
//                print_r($info);
//                die;
//            }
//        }
//    }

    public function actionTestTest()
    {
// !!!!!!!!!!!!!!!! АВТОРИЗАЦИЯ ЧЕРЕЗ ГУГЛ
//$model = new LoginForm();
//        $model->email = 'i255fsadfasdfsad85@yandex.ru';
//        $model->loginGoogle();
//var_dump(Yii::$app->user->identity);


//
//            $role = Yii::$app->user->identity->role;

//var_dump(Yii::$app->request->post());
//var_dump(Yii::$app->request->post('WidgetAccordion'));
die;

//        $subscribers = \app\models\UserGroupsRightsMaterialCategories::find()->where(['user_groups_id'=>1])->all();
//
//        $category_id_tree_total = [];
//        foreach ($subscribers as $value) {
//            $category_id_tree_total[] = $value['category_id'];
//        }


//        $category_id_tree = [];
//
//        $subscribers_category = SubscribersCategory::find()->select('category_id')->where(['user_id' => $value['id']])->all();
//
//        if (!empty($subscribers_category)) {
//            foreach ($subscribers_category as $subscribers_category_key => $subscribers_category_val) {
//                $category_id_tree[] = $subscribers_category_val['category_id'];
//            }
//        }array_unique

//        $category_id_tree = MaterialCategories::CreateTreeMaterialsUserChild(1679);
//            $array_diff = array_diff(MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree), $category_id_tree);
//            $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree);
//        var_dump(phpinfo());
//            throw new HttpException(517 ,'Ошибка при обновлении правил группы категорий подписчиков. id группы = ');

//        die;
    }

    public function actionChangePassword($email)
    {
        $model = Users::find()->where(['email'=>$email,'password'=>'0'])->one();

        if(!is_null($model))
        {
            $model->setScenario('change_new_password_user');
            if ($model->load(Yii::$app->request->post()))
            {
                $model->setAttributes(Yii::$app->request->post('Users'));

                if($model->validate())
                {
                    $model['new_password_email'] = password_hash($model['new_password'],PASSWORD_DEFAULT);

                    if($model->save(false))
                    {
                        $ContactForm =  new ContactForm();

//                        $form['email'] = $model['email'];
//                        $form['subject_email'] = 'Восстановления пароля';
                        $param['<%link%>'] = Html::a('Для подтвреждения учетной записи пройдите по ссылке', Url::to(['/site/confirmation-password', 'guid' => $model['new_password_email']],true));


                       $ContactForm->SendMail($model['email'],Notifications::RECORD_ACCOUNT_RECOVERY,$param);
                        return $this->redirect(['/site/show-model',
                            'type' => 1
                        ]);
                    }
                }
            }

            return $this->render('change-password', [
                'model' => $model,
            ]);
        }
    }

    public function actionConfirmationPassword($guid)
    {
        $model = Users::find()->where(['new_password_email'=>$guid,'password'=>'0'])->one();

        if(!is_null($model))
        {
            $model['password'] = $model['new_password_email'];

            if($model->save(false))
            {
                return $this->redirect(['/site/show-model',
                    'type' => 2
                ]);
            }
        }
        else
        {
            throw new HttpException(517 ,'Пользователь не найден');
        }
     }

    public function actionShowModel($type)
    {
        $text = '';
        if($type == 1)
        {
            $text = 'Изменения пароля прошло успешно, вам нужно подтвердить изменения пройдя по ссылке в письме, которое мы отправили';
        }
        else if($type == 2)
        {
            $text = 'Подтверждения пароля прошло успешно';
        }

        return $this->render('show-model', [
            'text' => $text,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {

            Users::updateAll(['last_visit_date'=>date('Y-m-d H:i:s',time())],['id'=>Yii::$app->user->identity->id]);

            $role = Yii::$app->user->identity->role;
            if ($role == Users::ROLE_USER) {
                return  $this->redirect(['/users/index']);
            }
            else
            {
                return $this->redirect(['/materials/all-materials','not_in_archive' => true]);
            }

//            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login())
        {
            Users::updateAll(['last_visit_date'=>date('Y-m-d H:i:s',time())],['id'=>Yii::$app->user->identity->id]);

            $role = Yii::$app->user->identity->role;
            if ($role == Users::ROLE_USER) {
                return  $this->redirect(['/users/index']);
            }
            else
            {
                return  $this->redirect(['/materials/all-materials','not_in_archive' => true]);
            }

//            $role = Yii::$app->user->identity->role;
//            if ($role == Users::ROLE_ADMIN) {
//                return  $this->redirect(['/administrator/all-materials','in_archive' => false]);
//            }
//            elseif($role == Users::ROLE_METHODIST){
//                //return $this->redirect(['logout']);
//                return $this->redirect(['/methodist/profile']);
//            }
//            elseif($role == Users::ROLE_EMPLOYEE_MUSEUM){
//                //return $this->redirect(['logout']);
//                return $this->redirect(['/museum/excursion']);
//            }
//            elseif($role == Users::ROLE_TEACHER)
//            {
//                return $this->redirect(['/teacher/confirmapplication']);
//            }
//            elseif($role == Users::ROLE_STUDENT)
//            {
//                return $this->redirect(['/student/confirmapplication']);
//            }

//            return $this->goBack();
        }
        else if($model->load(Yii::$app->request->post()) && !$model->login())
        {
            $post = Yii::$app->request->post('LoginForm');
            $user = Users::find()->where(['email'=>$post['email'],'password'=>'0','in_archive'=>false])->one();

            if(!is_null($user))
            {
                return $this->redirect(['/site/change-password', 'email' => $user['email']]);
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
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

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
