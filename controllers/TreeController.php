<?php

namespace app\controllers;
use app\models\MaterialCategories;
use Yii;
use app\models\Users;

use yii\web\HttpException;


class TreeController extends \yii\web\Controller
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
        else
        {
            $this->layout = 'main';
        }

        return true;
    }

    public function actionSaveDataTree()
    {
        $post = Yii::$app->getRequest()->post();
        if($post)
        {

            if(preg_match("/j/", $post['edit_id']))
            {
                $post['edit_id'] = 0;
            }
            $model = MaterialCategories::find()->where(['id'=>$post['edit_id']])->one();

            if(is_null($model))
            {
                $model = new MaterialCategories();
            }

            $model['title'] = $post['edit_name'];//edit_name
            $model['parent'] = $post['edit_parent'] == '#' ? 0:$post['edit_parent'];
            $model['order_categories'] = ((int) $post['edit_order']) == 0?1:$post['edit_order'];
            $model['exclude_from_search'] = $post['exclude_from_search'] == 'true'?true : false;

            if($model->save())
            {
                $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->orderBy('order_categories')->all();
                $json = json_encode(MaterialCategories::CreateTreeCategory($material_categories,0));
                return $json;
            }
            else
            {
                throw new HttpException(517 ,var_export($model->getErrors(),true));
            }
        }
        else
        {
            throw new HttpException(517 ,'Запрос не определен');
        }
    }

    public function actionDeleteDataTree()
    {
        $post = Yii::$app->getRequest()->post();
        if($post)
        {

            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->orderBy('order_categories')->all();

            $array_delete[0] = $post['edit_id'];

            $array_walk_recursive = MaterialCategories::CreateTreeCategory($material_categories,$post['edit_id']);

            array_walk_recursive($array_walk_recursive,function  ($item, $key){
                if($key == 'id')
                {
                    Yii::$app->params['id_material_categories_for_delete'][] = $item;
                }
            });

            if(!empty(Yii::$app->params['id_material_categories_for_delete']))
            {
                foreach (Yii::$app->params['id_material_categories_for_delete'] as $val)
                {
                    $array_delete[] = $val;
                }
            }

            MaterialCategories::updateAll(['in_archive' => true],['id'=>$array_delete]);
            MaterialCategories::updateAll(['parent'=>0],['id'=>$array_delete[0]]);

            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->orderBy('order_categories')->all();
            $json = json_encode(MaterialCategories::CreateTreeCategory($material_categories,0));
            return $json;

        }
        else
        {
            throw new HttpException(517 ,'Запрос не определен');
        }
    }

    public function actionRestoreDataTree()
    {
        $post = Yii::$app->getRequest()->post();
        if($post)
        {

            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => true])->all();

            $array_delete[0] = $post['edit_id'];

            $array_walk_recursive = MaterialCategories::CreateTreeCategory($material_categories,$post['edit_id']);

            array_walk_recursive($array_walk_recursive,function  ($item, $key){
                if($key == 'id')
                {
                    Yii::$app->params['id_material_categories_for_delete'][] = $item;
                }
            });

            if(!empty(Yii::$app->params['id_material_categories_for_delete']))
            {
                foreach (Yii::$app->params['id_material_categories_for_delete'] as $val)
                {
                    $array_delete[] = $val;
                }
            }

            if($post['edit_parent'] != '#')
            {
                $nested_node = MaterialCategories::find()->select('id ')->where(['in_archive' => false, 'id'=>$post['edit_parent']])->one();
                if(is_null($nested_node))
                {
                    $restore_node_delete = MaterialCategories::find()->select('id , title, parent')->where(['id'=>$post['edit_id']])->one();
                    $restore_node_delete['parent'] = 0;
                    $restore_node_delete['title'] = $post['edit_name'];

                    $restore_node_delete->save();
                }
            }

            MaterialCategories::updateAll(['in_archive' => false],['id'=>$array_delete]);

            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => true])->all();
            $json = json_encode(MaterialCategories::CreateTreeCategory($material_categories,0));
            return $json;

        }
        else
        {
            throw new HttpException(517 ,'Запрос не определен');
        }
    }

    public function actionRefreshDataTree()
    {
        $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->orderBy('order_categories')->all();
        $json = json_encode(MaterialCategories::CreateTreeCategory($material_categories,0));
        return $json;
    }

    public function actionGetDataTree()
    {
        $post = Yii::$app->getRequest()->post();
        if($post)
        {
            $model = MaterialCategories::find()->select('id , title, parent, order_categories, exclude_from_search')->where(['id' => $post['edit_id']])->one();

            if(!is_null($model))
            {
                return  json_encode($model->getAttributes());
            }
            else
            {
                throw new HttpException(517 ,'Категория не найдена');
            }
        }
    }

    public function actionAllCategories($in_archive = 0)
    {
        $model = MaterialCategories::find()
            ->select('id , title, parent')
            ->where(['in_archive' => $in_archive])
            ->orderBy('order_categories')
            ->all();
        $json = json_encode(MaterialCategories::CreateTreeCategory($model,0));

        return $this->render('allCategories', [
            'json' => $json,
        ]);

    }
}
