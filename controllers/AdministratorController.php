<?php

namespace app\controllers;
use app\models\ColorProjects;
use app\models\MaterialCategories;
use app\models\MaterialTags;
use app\models\Projects;
use app\models\SimpleImage;
use app\models\Subscribers;
use app\models\SubscribersCategory;
use app\models\UploadsFileMaterialLog;
use Yii;
use app\models\Users;

use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\db\Expression;

//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdministratorController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            throw new HttpException(517 ,'Сессия закончилась. Выполните повторно вход.');
        }
        if(Yii::$app->user->identity->role != Users::ROLE_ADMIN)
        {
            $this->redirect(['/']);
        }
        else
        {
            if(is_null(Yii::$app->session->get('access_token')))
            {
                Yii::$app->session->set('access_token',md5(time()));
            }
            $this->layout = 'main';
        }

        return true;
    }

//    /**
//     * @return array
//     */
//    public function actions()
//    {
//        return [
//            'uploadPhoto' => [
//                'class' => 'budyaga\cropper\actions\UploadAction',
//                'url' =>  '/uploads/users',
//                'path' => 'uploads/users',
//            ]
//        ];
//    }

    public function actionAllForms()
    {
        return $this->render('allForms');
    }

    public function actionAllSubscribers($email=null)
    {
        $query = Subscribers::find();

        if(!is_null($email) && $email != '')
        {
            $query->filterWhere(['like', 'LOWER(email)', mb_strtolower($email)]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort(['attributes' =>
            [
                'email',
                'time_created',
                'status',
                'time_send'
            ]
        ]);

        return $this->render('allSubscribers', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAddSubscribers()
    {
        $model = new Subscribers();

        $category_id = [];

        $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
        $json = json_encode(MaterialCategories::CreateTree($material_categories,0, $category_id));

        $subscribers = Yii::$app->getRequest()->post('Subscribers');

        if($subscribers)
        {
            $model->setAttributes($subscribers);

            $model['time_created'] = date("Y-m-d H:i:s",strtotime($model['time_created']));

            if($model['time_send'] != '')
            {
                $model['time_send'] = date("Y-m-d H:i:s",strtotime($model['time_send']));
            }

            if($model->save())
            {
                SubscribersCategory::deleteAll(['user_id'=>$model['id']]);

                foreach ($subscribers['category_id'] as $post_value)
                {
                    $subscribers = new SubscribersCategory();
                    $subscribers['user_id'] = $model['id'];
//                    $subscribers['school'] = $model['school'];
                    $subscribers['category_id'] = $post_value;
                    $subscribers['time_created'] = date("Y-m-d H:i:s",time());

                    $subscribers->save();
                }

                return $this->redirect(['/administrator/all-subscribers']);
            }
            else
            {
                $model['time_created'] = date("d.m.Y H:i:s",strtotime($model['time_created']));
                if($model['time_send'] != '')
                {
                    $model['time_send'] = date("d.m.Y H:i:s",strtotime($model['time_send']));
                }
            }

        }

        return $this->render('addSubscribers',[
            'model'  => $model,
            'json' => $json,
        ]);
    }

    public function actionUpdateSubscribers($id)
    {
        $model = Subscribers::find()->where(['id'=>$id])->one();
        if(is_null($model))
        {
            throw new HttpException(517 ,'Подписчик не найден');
        }

        $user_groups_rights = SubscribersCategory::find()->where(['user_id'=>$model['id']])->all();
//
        $category_id = [];
        foreach ($user_groups_rights as $value)
        {
            $category_id[] = $value['category_id'];
        }

        $model['time_created'] = date("d.m.Y H:i:s",strtotime($model['time_created']));
        if($model['time_send'] != '')
        {
            $model['time_send'] = date("d.m.Y H:i:s",strtotime($model['time_send']));
        }

        $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
        $json = json_encode(MaterialCategories::CreateTree($material_categories,0, $category_id));

        $subscribers = Yii::$app->getRequest()->post('Subscribers');

        if($subscribers)
        {

            if(!isset($subscribers['is_deleted']) || $subscribers['is_deleted'] == false)
            {
                $model->setAttributes($subscribers);

                $model['time_created'] = date("Y-m-d H:i:s",strtotime($model['time_created']));

                if($model['time_send'] != '')
                {
                    $model['time_send'] = date("Y-m-d H:i:s",strtotime($model['time_send']));
                }

                if($model->save())
                {
                    SubscribersCategory::deleteAll(['user_id'=>$model['id']]);

                    foreach ($subscribers['category_id'] as $post_value)
                    {
                        $subscribers = new SubscribersCategory();
                        $subscribers['user_id'] = $model['id'];
//                        $subscribers['school'] = $model['school'];
                        $subscribers['category_id'] = $post_value;
                        $subscribers['time_created'] = date("Y-m-d H:i:s",time());

                        $subscribers->save();
                    }

                    return $this->redirect(['/administrator/all-subscribers']);
                }
                else
                {
                    $model['time_created'] = date("d.m.Y H:i:s",strtotime($model['time_created']));
                    if($model['time_send'] != '')
                    {
                        $model['time_send'] = date("d.m.Y H:i:s",strtotime($model['time_send']));
                    }
                }
            }
            else
            {
                SubscribersCategory::deleteAll(['user_id'=>$model['id']]);
                Subscribers::deleteAll(['id'=>$model['id']]);
                return $this->redirect(['/administrator/all-subscribers']);
            }

        }

        return $this->render('addSubscribers',[
            'model'  => $model,
            'json' => $json,
        ]);
    }

    public function actionAllTags($name=null)
    {
        $query = MaterialTags::find();

        if(!is_null($name) && $name != '')
        {
            $query->filterWhere(['like', 'LOWER(name)', mb_strtolower($name)]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort(['attributes' =>
            [
                'name',
                'published'
            ]
        ]);



//        Url::remember();
        return $this->render('allTags', [
            'dataProvider' => $dataProvider,
        ]);
    }
//
//
//
//    public function actionAllProjects($in_archive=false,$title=null)
//    {
//        $query = Projects::find()->where(['in_archive'=>$in_archive]);
//
//        if(!is_null($title) && $title != '')
//        {
//            $query->filterWhere(['like', 'LOWER(title)', mb_strtolower($title)]);
//        }
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => $query,
//        ]);
//
////        $dataProvider->setSort(['attributes' =>
////            [
////                'title',
////                'published'
////            ]
////        ]);
//
//        Url::remember();
//        return $this->render('allProjects', [
//            'dataProvider' => $dataProvider,
//        ]);
//    }
//
//
//    public function actionAddProjects()
//    {
//        $model = new Projects();
//        $color_projects = [];
//        $color_projects_query = ColorProjects::find()->all();
//
//        if(!empty($color_projects_query))
//        {
//            foreach($color_projects_query as $color_projects_query_val)
//            {
//                $color_projects[$color_projects_query_val['id']] = $color_projects_query_val['name'];
//            }
//        }
//
//        if(Yii::$app->getRequest()->post())
//        {
//            $post = Yii::$app->getRequest()->post('Projects');
//            $model->setAttributes($post);
//
//            $model['time_create'] =   date("Y-m-d H:i:s",strtotime($post['time_create']));
//
//
////            $model->background_input = UploadedFile::getInstance($model, 'background_input');
////
////            if(!is_null($model->background_input))
////            {
////                $file_path = 'uploads/image_for_projects/background_'.Projects::transliterate($model['title']).'_'.md5(time()).'_'. $model->background_input->baseName . '.' . $model->background_input->extension;
////                if($model->background_input->saveAs($file_path))
////                {
////                    $model['background'] = $file_path;
////                }
////                else
////                {
////                    throw new HttpException(517 ,var_export($model->background_input->getErrors(),true) );
////                }
////            }
//
//            $model->logo_input = UploadedFile::getInstance($model, 'logo_input');
//
//            if(!is_null($model->logo_input))
//            {
//                $file_path = 'uploads/image_for_projects/logo_'.Projects::transliterate($model['title']).'_'.md5(time()).'_'. $model->logo_input->baseName . '.' . $model->logo_input->extension;
//                if($model->logo_input->saveAs($file_path))
//                {
//                    $model['logo'] = $file_path;
//                }
//                else
//                {
//                    throw new HttpException(517 ,var_export($model->logo_input->getErrors(),true) );
//                }
//            }
//
////            $model['time_create'] = new Expression('NOW()');
//
//            if($model->save())
//            {
//                return $this->redirect([Url::previous()]);
//            }
//        }
//
//        return $this->render('addProjects',[
//            'model'  => $model,
//            'color_projects'  => $color_projects,
//        ]);
//    }
//
//    public function actionUpdateProjects($id)
//    {
//        $model = Projects::find()->where(['id'=>$id])->one();
//
//        $color_projects = [];
//        $color_projects_query = ColorProjects::find()->all();
//
//        if(!empty($color_projects_query))
//        {
//            foreach($color_projects_query as $color_projects_query_val)
//            {
//                $color_projects[$color_projects_query_val['id']] = $color_projects_query_val['name'];
//            }
//        }
//
//        $model['time_create'] =  date("d.m.Y H:i:s",strtotime($model['time_create']));
//
//        if(Yii::$app->getRequest()->post())
//        {
//            $post = Yii::$app->getRequest()->post('Projects');
//            $model->setAttributes($post);
//            $model['time_create'] =   date("Y-m-d H:i:s",strtotime($post['time_create']));
////            $model->background_input = UploadedFile::getInstance($model, 'background_input');
////
////            if(!is_null($model->background_input))
////            {
////                $file_path = 'uploads/image_for_projects/background_'.Projects::transliterate($model['title']).'_'.md5(time()).'_'. $model->background_input->baseName . '.' . $model->background_input->extension;
////                if($model->background_input->saveAs($file_path))
////                {
////                    $model['background'] = $file_path;
////                }
////                else
////                {
////                    throw new HttpException(404 ,var_export($model->background_input->getErrors(),true) );
////                }
////            }
//
//            $model->logo_input = UploadedFile::getInstance($model, 'logo_input');
//
//            if(!is_null($model->logo_input))
//            {
//                $file_path = 'uploads/image_for_projects/logo_'.Projects::transliterate($model['title']).'_'.md5(time()).'_'. $model->logo_input->baseName . '.' . $model->logo_input->extension;
//                if($model->logo_input->saveAs($file_path))
//                {
//                    $model['logo'] = $file_path;
//                }
//                else
//                {
//                    throw new HttpException(517 ,var_export($model->logo_input->getErrors(),true) );
//                }
//            }
//
//            $model['time_create'] = new Expression('NOW()');
//
//            if($model->save())
//            {
//                return $this->redirect([Url::previous()]);
//            }
//            else
//            {
//                $model['time_create'] =  date("d.m.Y H:i:s",strtotime($model['time_create']));
//            }
//        }
//
//        return $this->render('addProjects',[
//            'model'  => $model,
//            'color_projects'  => $color_projects,
//        ]);
//    }

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

            if($model->save())
            {
                $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
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

            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();

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

            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
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
        $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
        $json = json_encode(MaterialCategories::CreateTreeCategory($material_categories,0));
        return $json;
    }

    public function actionAllCategories($in_archive = 0)
    {
        $model = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => $in_archive])->all();
        $json = json_encode(MaterialCategories::CreateTreeCategory($model,0));

//        $subscribers = Yii::$app->getRequest()->post('Subscribers');

//        if($subscribers)
//        {
//            $model->setAttributes($subscribers);
//
//            if($model->save())
//            {
//                SubscribersCategory::deleteAll(['user_id'=>$model['id']]);
//
//                foreach ($subscribers['category_id'] as $post_value)
//                {
//                    $subscribers = new SubscribersCategory();
//                    $subscribers['user_id'] = $model['id'];
//                    $subscribers['school'] = $model['school'];
//                    $subscribers['category_id'] = $post_value;
//                    $subscribers['time_created'] = date("Y-m-d H:i:s",time());
//
//                    $subscribers->save();
//                }
//
//                return $this->redirect(['/administrator/all-subscribers']);
//            }
//
//        }

        return $this->render('allCategories', [
            'json' => $json,
        ]);

    }

    public function actionImagesUploadUrl($guid)
    {

        $ImagesUploadUrl = [];
        if($_FILES['file']['name'] != '' && $_FILES["file"]["error"] == UPLOAD_ERR_OK)
        {
            if($_FILES['file']['type'] != 'image/jpeg' && $_FILES['file']['type'] != 'image/jpg' && $_FILES['file']['type'] != 'image/png')
            {
                echo json_encode('Расширение файла не подходит, можно загружать только *.jpeg или *.jpg, или *.png',JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                return false;
            }
            $tmp_name = $_FILES["file"]["tmp_name"];
            $path_info = pathinfo($_FILES["file"]["name"]);
            $rand = md5(rand(1,2147483647));

            $path_image_thumbs = 'uploads/files_for_material/'.Yii::$app->user->identity->guid.'/thumbs/'.$path_info['filename'].'_'.$rand.'.'.$path_info['extension'];

            $image = new SimpleImage();
            $image->load($tmp_name);
            $image->resizeToWidth(160);
            $image->save($path_image_thumbs);

            $model = new UploadsFileMaterialLog();
            $model['guid_material'] = $guid;
            $model['url_file_material'] = $path_image_thumbs;
            $model['user_id'] = Yii::$app->user->identity->id;
            $model['date_uploads'] = new Expression('NOW()');

            if(!$model->save())
            {
                throw new HttpException(517 ,var_export($model->getErrors(),true));
            }

            $path_image_source = 'uploads/files_for_material/'.Yii::$app->user->identity->guid.'/source/' .$path_info['filename'].'_'.$rand.'.'.$path_info['extension'];

            if(!move_uploaded_file($tmp_name, $path_image_source))
            {
                echo json_encode('Файл не переместился в папку '.'uploads/files_for_material/'.Yii::$app->user->identity->guid.'/source/',JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
//                var_dump('Файл не переместился в папку '.'uploads/files_for_material/'.Yii::$app->user->identity->guid.'/source/');
                return false;
            }
            else
            {
                $model_1 = new UploadsFileMaterialLog();
                $model_1['guid_material'] = $guid;
                $model_1['url_file_material'] = $path_image_source;
                $model_1['user_id'] = Yii::$app->user->identity->id;
                $model_1['date_uploads'] = new Expression('NOW()');

                if(!$model_1->save())
                {
                    throw new HttpException(517 ,var_export($model_1->getErrors(),true));
                }
            }

            $ImagesUploadUrl = ['location' => Url::to($path_image_source,true)];
        }

        return json_encode($ImagesUploadUrl);
    }

    public function actionAddTag()
    {
        $model = new MaterialTags();

        if(Yii::$app->getRequest()->post())
        {
            $post = Yii::$app->getRequest()->post('MaterialTags');
            $model->setAttributes($post);

            if($model->save())
            {
                return $this->redirect(['/administrator/all-tags']);
            }
        }

        return $this->render('addTags',[
            'model'  => $model
        ]);
    }

    public function actionUpdateTag($id)
    {
        $model = MaterialTags::find()->where(['id'=>$id])->one();

        if(Yii::$app->getRequest()->post())
        {
            $post = Yii::$app->getRequest()->post('MaterialTags');
            $model->setAttributes($post);

            if($model->save())
            {
                return $this->redirect(['/administrator/all-tags']);
            }
        }

        return $this->render('addTags',[
            'model'  => $model
        ]);
    }
}
