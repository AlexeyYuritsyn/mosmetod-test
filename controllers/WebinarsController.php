<?php

namespace app\controllers;
use app\models\ColorProjects;
use app\models\UserGroups;
use app\models\Webinars;
use Yii;
use app\models\Users;

use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\db\Expression;

class WebinarsController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            throw new HttpException(517 ,'Сессия закончилась. Выполните повторно вход.');
        }
        if(Yii::$app->user->identity->role != Users::ROLE_ADMIN && Yii::$app->user->identity->role != Users::ROLE_MODERATOR)
        {
            $this->redirect(['/']);
        }

        return true;
    }

    public function actionAllWebinars($user_groups_id=0,$title=null)
    {
        $query = Webinars::find()->select('webinars.*, user_groups.name AS user_groups_name')->where(['webinars.in_archive'=>false]);

        $query->innerJoin(UserGroups::tableName(),'user_groups_id = user_groups.id');

        if(!is_null($title) && $title != '')
        {
            $query->filterWhere(['like', 'LOWER(title)', mb_strtolower($title)]);
        }

        if($user_groups_id > 0)
        {
            $query->andWhere(['user_groups_id'=>$user_groups_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort(['attributes' =>
            [
                'title',
                'user_groups_name',
                'time_created'
            ]
        ]);

        $user_groups_array[0] = 'Все категории...';
        $user_groups = UserGroups::find()->where(['in_archive'=>false])->all();

        if(!empty($user_groups))
        {
            foreach ($user_groups as $user_groups_val)
            {
                $user_groups_array[$user_groups_val['id']] =  $user_groups_val['name'];
            }
        }

        Url::remember();
        return $this->render('allWebinars', [
            'dataProvider' => $dataProvider,
            'user_groups_array' => $user_groups_array,
        ]);
    }

    public function actionAddWebinars()
    {
        $model = new Webinars();

        $user_groups_array = [];
        $user_groups = UserGroups::find()->where(['in_archive'=>false])->all();

        if(!empty($user_groups))
        {
            foreach ($user_groups as $user_groups_val)
            {
                $user_groups_array[$user_groups_val['id']] =  $user_groups_val['name'];
            }
        }

        if(Yii::$app->getRequest()->post())
        {
            $post = Yii::$app->getRequest()->post('Webinars');
            $model->setAttributes($post);

            $model['time_created'] = date("Y-m-d H:i:s",strtotime($post['time_created']));
            $model['users_id'] = Yii::$app->user->identity->id;

            if($model->save())
            {
                return $this->redirect([Url::previous()]);
            }
            else
            {
                $model['time_created'] = date("d.m.Y H:i:s",strtotime($model['time_created']));
            }
        }

        return $this->render('addWebinars',[
            'model'  => $model,
            'user_groups_array'  => $user_groups_array
        ]);
    }

    public function actionUpdateWebinars($id)
    {
        $model = Webinars::find()->where(['id'=>$id])->one();
        $model['time_created'] =  date("d.m.Y H:i:s",strtotime($model['time_created']));

        $user_groups_array = [];
        $user_groups = UserGroups::find()->where(['in_archive'=>false])->all();

        if(!empty($user_groups))
        {
            foreach ($user_groups as $user_groups_val)
            {
                $user_groups_array[$user_groups_val['id']] =  $user_groups_val['name'];
            }
        }

        if(Yii::$app->getRequest()->post())
        {
            $post = Yii::$app->getRequest()->post('Webinars');
            $model->setAttributes($post);
            $model['time_created'] =   date("Y-m-d H:i:s",strtotime($post['time_created']));
            $model['users_id'] = Yii::$app->user->identity->id;

            if($model->save())
            {
                return $this->redirect([Url::previous()]);
            }
            else
            {
                $model['time_created'] =  date("d.m.Y H:i:s",strtotime($model['time_created']));
            }
        }

        return $this->render('addWebinars',[
            'model'  => $model,
            'user_groups_array' => $user_groups_array
        ]);
    }
}
