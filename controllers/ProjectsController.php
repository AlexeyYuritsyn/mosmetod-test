<?php

namespace app\controllers;
use app\models\ColorProjects;
use app\models\Projects;
use Yii;
use app\models\Users;

use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\db\Expression;

class ProjectsController extends \yii\web\Controller
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

    public function actionAllProjects($outdated=false,$title=null)
    {
        $query = Projects::find()->where(['outdated'=>$outdated,'in_archive'=>false]);

        if(!is_null($title) && $title != '')
        {
            $query->andFilterWhere(['like', 'LOWER(title)', mb_strtolower($title)]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        Url::remember();
        return $this->render('allProjects', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFileLogoRemoveProjects($id)
    {
        $model = Projects::find()->where(['id'=>$id])->one();

        if(!is_null($model))
        {
            unlink($model['logo']);
            $model['logo'] = null;

            return $model->save();
        }
        echo 'Файл не найден';
        return false;
    }

    public function actionAddProjects()
    {
        $model = new Projects();
        $color_projects = [];
        $color_projects_query = ColorProjects::find()->all();

        if(!empty($color_projects_query))
        {
            foreach($color_projects_query as $color_projects_query_val)
            {
                $color_projects[$color_projects_query_val['id']] = $color_projects_query_val['name'];
            }
        }

        if(Yii::$app->getRequest()->post())
        {
            $post = Yii::$app->getRequest()->post('Projects');
            $model->setAttributes($post);

            $model['time_create'] =   date("Y-m-d H:i:s",strtotime($post['time_create']));

            $model->logo_input = UploadedFile::getInstance($model, 'logo_input');

            if(!is_null($model->logo_input))
            {
                $file_path = 'uploads/image_for_projects/logo_'.Projects::transliterate($model['title']).'_'.md5(time()).'_'. $model->logo_input->baseName . '.' . $model->logo_input->extension;
                if($model->logo_input->saveAs($file_path))
                {
                    $model['logo'] = $file_path;
                }
                else
                {
                    throw new HttpException(517 ,var_export($model->logo_input->getErrors(),true) );
                }
            }

            if(!is_null(Yii::$app->getRequest()->post('delete-project')))
            {
                $model['in_archive'] = true;
            }

            if($model->save())
            {
                return $this->redirect([Url::previous()]);
            }
        }

        return $this->render('addProjects',[
            'model'  => $model,
            'color_projects'  => $color_projects,
        ]);
    }

    public function actionUpdateProjects($id)
    {
        $model = Projects::find()->where(['id'=>$id])->one();

        $color_projects = [];
        $color_projects_query = ColorProjects::find()->all();

        if(!empty($color_projects_query))
        {
            foreach($color_projects_query as $color_projects_query_val)
            {
                $color_projects[$color_projects_query_val['id']] = $color_projects_query_val['name'];
            }
        }

        $model['time_create'] =  date("d.m.Y H:i:s",strtotime($model['time_create']));

        if(Yii::$app->getRequest()->post())
        {
            $post = Yii::$app->getRequest()->post('Projects');

            $model->setAttributes($post);
            $model['time_create'] =   date("Y-m-d H:i:s",strtotime($post['time_create']));

            $model->logo_input = UploadedFile::getInstance($model, 'logo_input');

            if(!is_null($model->logo_input))
            {
                $file_path = 'uploads/image_for_projects/logo_'.Projects::transliterate($model['title']).'_'.md5(time()).'_'. $model->logo_input->baseName . '.' . $model->logo_input->extension;
                if($model->logo_input->saveAs($file_path))
                {
                    $model['logo'] = $file_path;
                }
                else
                {
                    throw new HttpException(517 ,var_export($model->logo_input->getErrors(),true) );
                }
            }

            if(!is_null(Yii::$app->getRequest()->post('delete-project')))
            {
                $model['in_archive'] = true;
            }

            if($model->save())
            {
                return $this->redirect([Url::previous()]);
            }
            else
            {
                $model['time_create'] =  date("d.m.Y H:i:s",strtotime($model['time_create']));
            }
        }

        return $this->render('addProjects',[
            'model'  => $model,
            'color_projects'  => $color_projects,
        ]);
    }
}
