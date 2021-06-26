<?php

namespace app\controllers;

use app\models\AutoSaveMaterials;
use app\models\AutoSaveWidget;
use app\models\AutoSaveWidgetAccordion;
use app\models\AutoSaveWidgetGallery;
use app\models\AutoSaveWidgetLogo;
use app\models\AutoSaveWidgetMap;
use app\models\AutoSaveWidgetTabs;
use app\models\AutoSaveWidgetYoutube;
use app\models\ContactForm;
use app\models\DuplicationMaterials;
use app\models\MaterialCategories;
use app\models\Materials;
use app\models\MaterialTags;
use app\models\MaterialTagsInMaterials;
use app\models\MaterialWatchers;
use app\models\MaterialWatchersEmail;
use app\models\Notifications;
use app\models\SendEmailMaterialPublished;
use app\models\SimpleImage;
use app\models\RoleUsersInUserGroups;
use app\models\UserGroupsRightsMaterialCategories;
use app\models\Widget;
use app\models\WidgetAccordion;
use app\models\WidgetGallery;
use app\models\WidgetLogo;
use app\models\WidgetMap;
use app\models\WidgetTabs;
use app\models\UploadsFileMaterialLog;
use app\models\Users;
use app\models\DelegationRightsInUserGroups;
use app\models\WidgetYoutube;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\Response;

use yii\helpers\Html;
use yii\web\HttpException;

use yii\helpers\Url;
use yii\web\UploadedFile;

use yii\db\Expression;


class MaterialsController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            throw new HttpException(517 ,'Сессия закончилась. Выполните повторно вход.');
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

    public function actionFileSorted($id)
    {
        if($post = Yii::$app->getRequest()->post())
        {
            $new_order = WidgetGallery::find()->where(['materials_id'=>$id,'order_id'=>$post['oldIndex']])->one();
            $old_order = WidgetGallery::find()->where(['materials_id'=>$id,'order_id'=>$post['newIndex']])->one();


            $new_order['order_id'] = $post['newIndex'];
            $new_order['modified'] = date("Y-m-d H:i:s",time());


            $old_order['order_id'] = $post['oldIndex'];
            $old_order['modified'] = date("Y-m-d H:i:s",time());

            $new_order->save();
            $old_order->save();

            return true;
        }
        return false;
    }

    public function actionAllMaterials($title=null,$fromdate=null,$todate=null,$created_by=null,$status=0,$not_in_archive=null,$category=0)
    {
        $query = Materials::find()->select([
            'materials.urgency_withdrawal',
            'materials.title',
            'materials.status',
            'mc.title AS categories_name',
            'CONCAT(uc.second_name,\' \',uc.first_name,\' \',uc.third_name) AS fio_created',
            'CONCAT(um.second_name,\' \',um.first_name,\' \',um.third_name) AS fio_modified',
            'materials.published_date',
            'materials.hits',
            'materials.id']);

        $query->leftJoin(MaterialCategories::tableName().' mc','materials.material_categories_id = mc.id');
        $query->leftJoin(Users::tableName().' uc','materials.created_by = uc.id');
        $query->leftJoin(Users::tableName().' um','materials.modified_by = um.id');

//        $json = [];
//        $json_filter = [];

        $users_array[0] = 'Автор не выбран ...';

        $users = Users::find()->select('id,second_name,first_name,third_name')
            ->where( 'role <> :role',[':role'=>Users::ROLE_USER])
            ->all();

        if(!empty($users))
        {
            foreach($users as $users_val)
            {
                $users_array[$users_val['id']] = $users_val['second_name'].' '.$users_val['first_name'].' '.$users_val['third_name'];
            }
        }

        $status_array[0] = 'Все статусы';

        foreach(Materials::$status as $status_key=>$status_val)
        {
            $status_array[$status_key] = $status_val;
        }

        $delegation_rights_in_user_groups = [];

//        if((Yii::$app->user->identity->role == Users::ROLE_ADMIN || Yii::$app->user->identity->role == Users::ROLE_MODERATOR))
//        {
////            if($status == 0)
////            {
////                $query->where(['materials.status'=>Materials::$status_query]);
////            }
////            else
////            {
////                $query->where(['materials.status'=>Materials::ARCHIVE]);
////            }
//
//
//        }
//        else

        if(Yii::$app->user->identity->role == Users::ROLE_METHODIST)
        {

            $query->where(['materials.created_by'=>Yii::$app->user->identity->id]);

//            if($status == 0)
//            {
//                $query->where(['materials.created_by'=>Yii::$app->user->identity->id,'materials.status'=>Materials::$status_query]);
////                $query->orWhere(['materials.material_categories_id'=>$category_id,'materials.status'=>Materials::$status_query]);
//            }
//            else
//            {
//                $query->where(['materials.created_by'=>Yii::$app->user->identity->id,'materials.status'=>Materials::ARCHIVE]);
////                $query->orWhere(['materials.material_categories_id'=>$category_id,'materials.status'=>Materials::ARCHIVE]);
//            }



            $delegation_rights_in_user_groups = DelegationRightsInUserGroups::find()->where(['users_id'=>Yii::$app->user->identity->id])->all();

            if(!empty($delegation_rights_in_user_groups))
            {
                $user_groups_rights_material_categories_array = [];
                foreach($delegation_rights_in_user_groups as $delegation_rights_in_user_groups_val)
                {
                    $user_groups_rights_material_categories_array[] = $delegation_rights_in_user_groups_val['user_groups_id'];
                }


                $user_groups_rights_material_categories = UserGroupsRightsMaterialCategories::find()->where(['user_groups_id'=>$user_groups_rights_material_categories_array])->all();
                $category_id = [];

                foreach($user_groups_rights_material_categories as $user_groups_rights_material_categories_key=>$user_groups_rights_material_categories_val)
                {
                    $category_id[] = $user_groups_rights_material_categories_val['category_id'];
                }

                $category_id = MaterialCategories::CreateTreeMaterialsUserParent($category_id,$category_id);

                $query->orWhere(['materials.material_categories_id'=>$category_id]);
//                if($status == 0)
//                {
//                    $query->orWhere(['materials.material_categories_id'=>$category_id,'materials.status'=>Materials::$status_query]);
//                }
//                else
//                {
//                    $query->orWhere(['materials.material_categories_id'=>$category_id,'materials.status'=>Materials::ARCHIVE]);
//                }


            }
        }
        else if(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST)
        {
            $query->where(['materials.created_by'=>Yii::$app->user->identity->id]);
//            if($status == 0)
//            {
//                $query->where(['materials.created_by'=>Yii::$app->user->identity->id,'materials.status'=>Materials::$status_query]);
//            }
//            else
//            {
//                $query->where(['materials.created_by'=>Yii::$app->user->identity->id,'materials.status'=>Materials::ARCHIVE]);
//            }


            $role_users_in_user_groups = RoleUsersInUserGroups::find()->where(['users_id'=>Yii::$app->user->identity->id])->all();

            if(!empty($role_users_in_user_groups))
            {
                $user_groups_rights_material_categories_array = [];
                foreach($role_users_in_user_groups as $role_users_in_user_groups_val)
                {
                    $user_groups_rights_material_categories_array[] = $role_users_in_user_groups_val['user_groups_id'];
                }

                $user_id_in_user_groups = RoleUsersInUserGroups::find()->where(['user_groups_id'=>$user_groups_rights_material_categories_array])->all();

                $user_id_in_user_groups_array = [];

                if(!empty($user_id_in_user_groups))
                {
                    foreach($user_id_in_user_groups as $user_id_in_user_groups_val)
                    {
                        $user_id_in_user_groups_array[] = $user_id_in_user_groups_val['users_id'];
                    }
                }


                $user_groups_rights_material_categories = UserGroupsRightsMaterialCategories::find()->where(['user_groups_id'=>$user_groups_rights_material_categories_array])->all();
                $category_id = [];

                foreach($user_groups_rights_material_categories as $user_groups_rights_material_categories_key=>$user_groups_rights_material_categories_val)
                {
                    $category_id[] = $user_groups_rights_material_categories_val['category_id'];
                }

                $category_id = MaterialCategories::CreateTreeMaterialsUserParent($category_id,$category_id);

                $query->orWhere(['materials.material_categories_id'=>$category_id,'materials.created_by'=>$user_id_in_user_groups_array]);
//                if($status == 0)
//                {
//                    $query->orWhere(['materials.material_categories_id'=>$category_id,'materials.status'=>Materials::$status_query]);
//                }
//                else
//                {
//                    $query->orWhere(['materials.material_categories_id'=>$category_id,'materials.status'=>Materials::ARCHIVE]);
//                }

            }
        }

//        $category_id_select[0] = null;
//        if($category > 0)
//        {
//            $category_id_select[0] = $category;
//        }
//        if(Yii::$app->user->identity->role == Users::ROLE_ADMIN || Yii::$app->user->identity->role == Users::ROLE_MODERATOR)
//        {
////            $category_id[0] = null;
////            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
//////            $json = MaterialCategories::CreateTreeMaterials($material_categories,0, $category_id);
////            $json_filter=[];
//////            $json_filter = MaterialCategories::CreateTreeCategory($material_categories,0,$category_id_select);
//        }
//        else
//        {
////            $role_users_in_user_groups = RoleUsersInUserGroups::find()->select('user_groups_id')->where(['users_id'=>Yii::$app->user->identity->id])->all();
////
////            $user_groups_id = [];
////
////            foreach ($role_users_in_user_groups as $role_users_in_user_groups_val)
////            {
////                $user_groups_id[] = $role_users_in_user_groups_val['user_groups_id'];
////            }
////
////            $user_groups_rights_material_categories = UserGroupsRightsMaterialCategories::find()->select('category_id')->where(['user_groups_id'=>$user_groups_id])->all();
////            $category_id_tree = [];
////
////            foreach($user_groups_rights_material_categories as $user_groups_rights_material_categories_key=>$user_groups_rights_material_categories_val)
////            {
////                $category_id_tree[] = $user_groups_rights_material_categories_val['category_id'];
////            }
////
////            $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree,$category_id_tree);
////
////            if(!empty($category_id_tree))
////            {
////                foreach($category_id_tree as $category_id_tree_val)
////                {
////                    $category_id_tree = array_merge($category_id_tree, MaterialCategories::CreateTreeMaterialsUserChild($category_id_tree_val));
////                }
////
////                $category_id_tree = array_unique($category_id_tree);
////            }
////
//////            var_dump($category_id_tree);
////            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
////
////            $json_filter = MaterialCategories::CreateTreeMaterialsUserWithoutDisabled($material_categories,0, $category_id_select,$category_id_tree);
//        }

        if(!is_null($title) && $title !='')
        {
            $query->andFilterWhere(['like', 'LOWER(materials.title)', mb_strtolower($title)]);
        }

        if(!is_null($fromdate) && $fromdate !='' && !is_null($todate) && $todate !='')
        {
            $fromdate = strtotime(date('d.m.Y 00:00:00',strtotime($fromdate)));
            $todate = strtotime(date('d.m.Y 23:59:59',strtotime($todate)));

            $query->andWhere(['between','published_date',$fromdate,$todate]);
        }

        if(!is_null($created_by) && $created_by !='' && $created_by !=0)
        {
            $query->andWhere(['materials.created_by'=>$created_by]);
        }

        if(!is_null($not_in_archive) && $not_in_archive == '1')
        {
            $query->andWhere('materials.status != :status',[':status'=>Materials::ARCHIVE]);
        }

        if($category > 0)
        {
            $category_filter_array[0] =  $category;
            $category_filter_array = MaterialCategories::CreateTreeMaterialsUserParent($category_filter_array,$category_filter_array);

            $query->andWhere(['materials.material_categories_id'=>$category_filter_array]);
        }

        if($status == Materials::NOT_PUBLISHED)
        {
            $query->andWhere(['materials.status'=>[Materials::DRAFT,Materials::SENT_FOR_CONFIRMATION,Materials::CONFIRMED,Materials::SENT_FOR_DEVELOPMENT,Materials::ARCHIVE]]);
        }
        else if(!is_null($status) && $status !='' && $status !=0)
        {
            $query->andWhere(['materials.status'=>$status]);
        }

//        $query->orderBy('id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $dataProvider->setSort(['attributes' =>
            [
                'title',
                'status',
                'categories_name',
                'fio_created',
                'fio_modified',
                'published_date',
                'hits',
                'id'
            ],
            'defaultOrder' => [ 'id' => SORT_DESC],
        ]);

        Url::remember();
        return $this->render('allMaterials', [
            'dataProvider' => $dataProvider,
            'users_array' => $users_array,
            'status_array' => $status_array,
//            'json' =>  json_encode($json),
//            'json_filter' => json_encode($json_filter),
            'delegation_rights_in_user_groups' => count($delegation_rights_in_user_groups)>0 ? true:false,
        ]);
    }

    public function actionGetJsonChangeMaterialCategory()
    {
        $json = [];

        if(Yii::$app->user->identity->role == Users::ROLE_ADMIN || Yii::$app->user->identity->role == Users::ROLE_MODERATOR)
        {
            $category_id[0] = null;

            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
            if(!empty($material_categories))
            {
                $json = MaterialCategories::CreateTreeMaterials($material_categories,0, $category_id);
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetJsonFilterMaterialCategory($category = 0)
    {
        $json = [];

        $category_id_select[0] = null;
        if($category > 0)
        {
            $category_id_select[0] = $category;
        }

        if(Yii::$app->user->identity->role == Users::ROLE_ADMIN || Yii::$app->user->identity->role == Users::ROLE_MODERATOR)
        {
            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
            $json = MaterialCategories::CreateTreeCategory($material_categories,0,$category_id_select);
        }
        else
        {
            $role_users_in_user_groups = RoleUsersInUserGroups::find()->select('user_groups_id')->where(['users_id'=>Yii::$app->user->identity->id])->all();

            $user_groups_id = [];

            foreach ($role_users_in_user_groups as $role_users_in_user_groups_val)
            {
                $user_groups_id[] = $role_users_in_user_groups_val['user_groups_id'];
            }

            $user_groups_rights_material_categories = UserGroupsRightsMaterialCategories::find()->select('category_id')->where(['user_groups_id'=>$user_groups_id])->all();
            $category_id_tree = [];

            foreach($user_groups_rights_material_categories as $user_groups_rights_material_categories_key=>$user_groups_rights_material_categories_val)
            {
                $category_id_tree[] = $user_groups_rights_material_categories_val['category_id'];
            }

            $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree,$category_id_tree);

            if(!empty($category_id_tree))
            {
                foreach($category_id_tree as $category_id_tree_val)
                {
                    $category_id_tree = array_merge($category_id_tree, MaterialCategories::CreateTreeMaterialsUserChild($category_id_tree_val));
                }

                $category_id_tree = array_unique($category_id_tree);
            }

//            var_dump($category_id_tree);
            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();

            $json = MaterialCategories::CreateTreeMaterialsUserWithoutDisabled($material_categories,0, $category_id_select,$category_id_tree);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetJsonMaterialCategory($category = 0)
    {
        $json = [];

        $category_id_select[0] = null;
        if($category > 0)
        {
            $category_id_select[0] = $category;
        }

        if(Yii::$app->user->identity->role == Users::ROLE_ADMIN || Yii::$app->user->identity->role == Users::ROLE_MODERATOR)
        {
            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
            $json = MaterialCategories::CreateTreeMaterials($material_categories,0, $category_id_select);
        }
        else
        {

            $role_users_in_user_groups = RoleUsersInUserGroups::find()->select('user_groups_id')->where(['users_id'=>Yii::$app->user->identity->id])->all();

            $user_groups_id = [];

            foreach ($role_users_in_user_groups as $role_users_in_user_groups_val)
            {
                $user_groups_id[] = $role_users_in_user_groups_val['user_groups_id'];
            }

            $user_groups_rights_material_categories = UserGroupsRightsMaterialCategories::find()->select('category_id')->where(['user_groups_id'=>$user_groups_id])->all();
            $category_id_tree = [];

            foreach($user_groups_rights_material_categories as $user_groups_rights_material_categories_key=>$user_groups_rights_material_categories_val)
            {
                $category_id_tree[] = $user_groups_rights_material_categories_val['category_id'];
            }

            $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree,$category_id_tree);

            if(!empty($category_id_tree))
            {
                foreach($category_id_tree as $category_id_tree_val)
                {
                    $category_id_tree = array_merge($category_id_tree, MaterialCategories::CreateTreeMaterialsUserChild($category_id_tree_val));
                }

                $category_id_tree = array_unique($category_id_tree);
            }

//            var_dump($category_id_tree);
            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();

            $json = MaterialCategories::CreateTreeMaterialsUser($material_categories,0, $category_id_select,$category_id_tree);


//            $role_users_in_user_groups = RoleUsersInUserGroups::find()->select('user_groups_id')->where(['users_id'=>Yii::$app->user->identity->id])->all();
//
//            $user_groups_id = [];
//
//            foreach ($role_users_in_user_groups as $role_users_in_user_groups_val)
//            {
//                $user_groups_id[] = $role_users_in_user_groups_val['user_groups_id'];
//            }
//
//            $user_groups_rights_material_categories = UserGroupsRightsMaterialCategories::find()->select('category_id')->where(['user_groups_id'=>$user_groups_id])->all();
//            $category_id_tree = [];
//
//            foreach($user_groups_rights_material_categories as $user_groups_rights_material_categories_key=>$user_groups_rights_material_categories_val)
//            {
//                $category_id_tree[] = $user_groups_rights_material_categories_val['category_id'];
//            }
//
//            $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree,$category_id_tree);
//
//            if(!empty($category_id_tree))
//            {
//                foreach($category_id_tree as $category_id_tree_val)
//                {
//                    $category_id_tree = array_merge($category_id_tree, MaterialCategories::CreateTreeMaterialsUserChild($category_id_tree_val));
//                }
//
//                $category_id_tree = array_unique($category_id_tree);
//            }
//
////            var_dump($category_id_tree);
//            $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
//
//            $json = MaterialCategories::CreateTreeMaterialsUserWithoutDisabled($material_categories,0, $category_id_select,$category_id_tree);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionUploadsFileMaterialLog()
    {
        $get = Yii::$app->getRequest()->get();

        if(!empty($get))
        {
            $model = new UploadsFileMaterialLog();
            $model['guid_material'] = $get['material_guid'];
            $model['url_file_material'] = $get['url_file_material'];
            $model['user_id'] = Yii::$app->user->identity->id;
            $model['date_uploads'] = new Expression('NOW()');

            if(!$model->save())
            {
                throw new HttpException(517 ,var_export($model->getErrors(),true));
            }
        }
        else
        {
            throw new HttpException(517 ,'Подключение POST не установлено',true);
        }
        return true;
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

    public function actionComeBack()
    {
        return $this->redirect([Url::previous()]);
    }

    public function actionChangeMaterialCategory()
    {
        $post = Yii::$app->getRequest()->post();

        if(isset($post['material_id']) && isset($post['category_id']) && $post['material_id'] != '' && $post['category_id'] != '')
        {
            $model = Materials::find()->where(['id'=>$post['material_id']])->one();

            $model['material_categories_id'] = $post['category_id'];

            if($model->save(false))
            {
                $this->AutoSaveMaterial($model['id']);
            }
            else
            {
                throw new HttpException(517 ,var_export($model->getErrors(),true));
            }
        }
        else
        {
            throw new HttpException(517 ,'Материал или категория не были выбраны');
        }
    }

    public function actionRemovePublishedMaterial()
    {
        $post = Yii::$app->getRequest()->post();

        if(isset($post['material_id']) && $post['material_id'] != '')
        {
            $model = Materials::find()->where(['id'=>$post['material_id'],'status'=>Materials::PUBLISHED])->one();

            if(!is_null($model))
            {
                $model['urgency_withdrawal'] = Materials::LOW_SPEED;
                $model['status'] = Materials::CONFIRMED;

                if($model->save(false))
                {
                    $this->AutoSaveMaterial($model['id']);
                }
                else
                {
                    throw new HttpException(517 ,var_export($model->getErrors(),true));
                }
            }
        }
        else
        {
            throw new HttpException(517 ,'Материал или категория не были выбраны');
        }
    }

    private function AutoSaveMaterial($id)
    {
        $model = Materials::find()->where(['id' => $id])->one();

        $post = Yii::$app->getRequest()->post();

        $auto_save_model = new AutoSaveMaterials();

        $auto_save_model->setAttributes($model->getAttributes());
        $auto_save_model['materials_id'] = $model['id'];
        $auto_save_model['save_date'] = new Expression('NOW()');
        $auto_save_model['users_id'] = Yii::$app->user->identity->id;
        $auto_save_model['comment'] = isset($post['Materials']['comment']) && $post['Materials']['comment'] != ''? $post['Materials']['comment']: null;


        if($auto_save_model->save())
        {
            $widget_map = WidgetMap::find()->where(['materials_id'=>$model['id']])->all();

            if(!empty($widget_map))
            {
                foreach($widget_map as $widget_map_val)
                {
                   $auto_save_widget_map =  new AutoSaveWidgetMap();
                   $auto_save_widget_map->setAttributes($widget_map_val->getAttributes());
                   $auto_save_widget_map['auto_save_materials_id'] = $auto_save_model['id'];
                    if(!$auto_save_widget_map->save())
                    {
                        throw new HttpException(517 ,var_export($auto_save_widget_map->getErrors(),true));
                    }
                }
            }

            $widget_gallery = WidgetGallery::find()->where(['materials_id'=>$model['id']])->all();

            if(!empty($widget_gallery))
            {
                foreach($widget_gallery as $widget_gallery_val)
                {
                    $auto_save_widget_gallery =  new AutoSaveWidgetGallery();
                    $auto_save_widget_gallery->setAttributes($widget_gallery_val->getAttributes());
                    $auto_save_widget_gallery['auto_save_materials_id'] = $auto_save_model['id'];
                    if(!$auto_save_widget_gallery->save())
                    {
                        throw new HttpException(517 ,var_export($auto_save_widget_gallery->getErrors(),true));
                    }
                }
            }

            $widget_accordion = WidgetAccordion::find()->where(['materials_id'=>$model['id']])->all();

            if(!empty($widget_accordion))
            {
                foreach($widget_accordion as $widget_accordion_val)
                {
                    $auto_save_widget_accordion =  new AutoSaveWidgetAccordion();
                    $auto_save_widget_accordion->setAttributes($widget_accordion_val->getAttributes());
                    $auto_save_widget_accordion['auto_save_materials_id'] = $auto_save_model['id'];
                    if(!$auto_save_widget_accordion->save())
                    {
                        throw new HttpException(517 ,var_export($auto_save_widget_accordion->getErrors(),true));
                    }
                }
            }

            $widget_tab = WidgetTabs::find()->where(['materials_id'=>$model['id']])->all();

            if(!empty($widget_tab))
            {
                foreach($widget_tab as $widget_tab_val)
                {
                    $auto_save_widget_tabs =  new AutoSaveWidgetTabs();
                    $auto_save_widget_tabs->setAttributes($widget_tab_val->getAttributes());
                    $auto_save_widget_tabs['auto_save_materials_id'] = $auto_save_model['id'];
                    if(!$auto_save_widget_tabs->save())
                    {
                        throw new HttpException(517 ,var_export($auto_save_widget_tabs->getErrors(),true));
                    }
                }
            }

            $widget_logo = WidgetLogo::find()->where(['materials_id' => $model['id']])->one();

            if(!is_null($widget_logo))
            {
                $auto_save_widget_logo = new AutoSaveWidgetLogo();
                $auto_save_widget_logo->setAttributes($widget_logo->getAttributes());
                $auto_save_widget_logo['auto_save_materials_id'] = $auto_save_model['id'];
                if(!$auto_save_widget_logo->save())
                {
                    throw new HttpException(517 ,var_export($auto_save_widget_logo->getErrors(),true));
                }
            }

            $widget_youtube = WidgetYoutube::find()->select('youtube_url')->where(['materials_id' => $model['id']])->one();

            if(!is_null($widget_youtube))
            {
                $auto_save_widget_youtube = new AutoSaveWidgetYoutube();
                $auto_save_widget_youtube->setAttributes($widget_youtube->getAttributes());
                $auto_save_widget_youtube['auto_save_materials_id'] = $auto_save_model['id'];
                if(!$auto_save_widget_youtube->save())
                {
                    throw new HttpException(517 ,var_export($auto_save_widget_youtube->getErrors(),true));
                }
            }

            $widget = Widget::find()->where(['materials_id'=>$model['id']])->all();

            if(!empty($widget))
            {
                foreach($widget as $widget_val)
                {
                    $auto_save_widget =  new AutoSaveWidget();
                    $auto_save_widget->setAttributes($widget_val->getAttributes());
                    $auto_save_widget['auto_save_materials_id'] = $auto_save_model['id'];
                    if(!$auto_save_widget->save())
                    {
                        throw new HttpException(517 ,var_export($auto_save_widget->getErrors(),true));
                    }
                }
            }
        }
        else
        {
            throw new HttpException(517 ,var_export($auto_save_model->getErrors(),true));
        }
    }


    public function actionUpdateMaterial($id,$auto_save = null)
    {

        $model = Materials::find()->where(['id' => $id])->one();

        if(is_null($model))
        {
            throw new HttpException(517 ,'Материал не найден');
        }


        $duplication_materials = DuplicationMaterials::find()->where(['materials_guid'=>$model['guid']])->one();

        if(!is_null($duplication_materials) && ($duplication_materials['time_open_material']+60)>time() && $duplication_materials['users_id'] != Yii::$app->user->identity->id)
        {
            $users = Users::find()->where(['id'=>$duplication_materials['users_id']])->one();
            throw new HttpException(517 ,'Материал заблокирован, по причине редактирование материала пользователем '.
                $users['second_name']. ' ' .$users['first_name']. ' ' .$users['third_name'].'.'.  //chr(10).chr(13).
             'Материал станет доступным в течении минуты, после выхода пользвателя из редактирования материала.');
        }

        if(is_null($duplication_materials))
        {
            $duplication_materials =  new DuplicationMaterials();
        }

        $duplication_materials['materials_guid'] = $model['guid'];
        $duplication_materials['time_open_material'] = time();
        $duplication_materials['users_id'] = Yii::$app->user->identity->id;

        if(!$duplication_materials->save())
        {
            throw new HttpException(517 , var_export($duplication_materials->getErrors(),true));
        }

        $validation = true;

        if(!is_null($auto_save))
        {
            $validation = false;
        }

        if(!is_dir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/'))
        {
            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/');
            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/source/');
            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/thumbs/');
            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/gallery/');
            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/logo/');
        }

        $users_array = [];
        $init_value_text = '';

        if(Yii::$app->user->identity->role == Users::ROLE_ADMIN || Yii::$app->user->identity->role == Users::ROLE_MODERATOR)
        {
           $users = Users::find()->where(['id'=>$model['created_by']])->one();

           if(!is_null($users))
           {
               $init_value_text = $users['second_name'].' '.$users['first_name'].' '.$users['third_name'];
           }
        }

        $widget_map = WidgetMap::find()->where(['materials_id'=>$model['id']])->all();
        $widget_gallery_image =  [];
        $widget_gallery_remove =  [];

        if(empty($widget_map))
        {
            $widget_map[0] = new WidgetMap();
        }

        $widget_gallery = WidgetGallery::find()->where(['materials_id'=>$model['id']])->orderBy('order_id')->all();

        if(!empty($widget_gallery))
        {
            foreach ($widget_gallery as $widget_gallery_val)
            {
                $widget_gallery_image[] = Url::to($widget_gallery_val['image'],true);
                $widget_gallery_remove[]['url'] = Url::to(['/materials/file-delete-material','id'=>$widget_gallery_val['id']]);
            }
        }

        $widget_accordion = WidgetAccordion::find()->where(['materials_id'=>$model['id']])->all();

        if(empty($widget_accordion))
        {
            $widget_accordion[0] = new WidgetAccordion();
        }

        $widget_tab = WidgetTabs::find()->where(['materials_id'=>$model['id']])->all();

        if(empty($widget_tab))
        {
            $widget_tab[0] = new WidgetTabs();
        }


        $widget = Widget::find()->select('type, name')->where(['materials_id'=>$model['id']])->all();

        if(!empty($widget))
        {
            foreach($widget as $widget_val)
            {
                if(isset($widget_val['type']) && $widget_val['type'] == 'accordion')
                {
                    $model['name_widget_accordion'] = $widget_val['name'];
                }

                if(isset($widget_val['type']) && $widget_val['type'] == 'gallery')
                {
                    $model['name_widget_gallery'] = $widget_val['name'];
                }

                if(isset($widget_val['type']) && $widget_val['type'] == 'map')
                {
                    $model['name_widget_map'] = $widget_val['name'];
                }

                if(isset($widget_val['type']) && $widget_val['type'] == 'tabs')
                {
                    $model['name_widget_tabs'] = $widget_val['name'];
                }

                if(isset($widget_val['type']) && $widget_val['type'] == 'youtube')
                {
                    $model['name_widget_youtube'] = $widget_val['name'];
                }
            }
        }

        $material_tags_in_materials_array = [];

        $material_tags_in_materials = MaterialTagsInMaterials::find()->select('material_tags_id')->where(['materials_id'=>$model['id']])->all();
        if(!empty($material_tags_in_materials))
        {
            foreach($material_tags_in_materials as $tags_val)
            {
                $foreach_material_tags =  MaterialTags::find()->select('name')->where(['id'=>$tags_val['material_tags_id']])->one();
                if(!is_null($foreach_material_tags))
                {
                    $material_tags_in_materials_array[] =  $foreach_material_tags['name'];
                }
            }
        }

        $model['tag'] = $material_tags_in_materials_array;

        $widget_logo = WidgetLogo::find()->where(['materials_id' => $model['id']])->one();
        if(is_null($widget_logo))
        {
            $widget_logo = new WidgetLogo();
        }

        $model['published_date'] = date("d.m.Y H:i",$model['published_date']);

        if($model['date_unpinning'] != '')
        {
            $model['date_unpinning'] = date("d.m.Y H:i",strtotime($model['date_unpinning']));
        }
        else
        {
            $model['date_unpinning'] = null;
        }


        $widget_youtube = WidgetYoutube::find()->select('youtube_url')->where(['materials_id' => $model['id']])->one();
        if(!is_null($widget_youtube))
        {
            $model['youtube_url'] = $widget_youtube['youtube_url'];
        }

        $material_watchers_array = [];
        $material_watchers = MaterialWatchers::find()->select('material_watchers_email.email as material_watchers_email')
            ->where(['material_id'=>$model['id']])
            ->innerJoin(MaterialWatchersEmail::tableName(),'material_watchers.material_watchers_email_id = material_watchers_email.id')->all();

        if(!empty($material_watchers))
        {
            foreach($material_watchers as $material_watchers_val)
            {
                $material_watchers_array[] = $material_watchers_val['material_watchers_email'];
            }

            $model['material_watchers'] = $material_watchers_array;
        }


        $materials_log = AutoSaveMaterials::find()
            ->select('id, users_id, save_date, comment,status')
            ->where(['materials_id'=>$model['id']])
            ->orderBy('save_date')->all();
        if(!empty($materials_log))
        {
            foreach($materials_log as $materials_log_val)
            {
                $user_info = Users::find()->where(['id'=>$materials_log_val['users_id']])->one();

                $user_fio = $user_info['second_name'].' '.$user_info['first_name'].' '.$user_info['third_name'];
                $model['comment_log'] .= date("d.m.Y H:i:s",strtotime($materials_log_val['save_date'])).' '.$user_fio.' '.Materials::$status_materials_log[$materials_log_val['status']].
                    ' '.Html::a('Посмотреть сохраненнную версию',   Url::to(['/show-preliminary-material/'.$materials_log_val['id'].'/'.(1).'/'.Yii::$app->session->get('access_token')],true),['target'=>'_blank'])
                    .'<br><br>';

                if($materials_log_val['comment'] != '')
                {
                    $model['comment_log'] .= 'Комментарии к изменениям:'.'<br>'
                    .'"'.$materials_log_val['comment'].'"'.'<br><br>';
                }
            }
        }

        $post = Yii::$app->getRequest()->post();

        if($post)
        {
            $ole_model = $model->getAttributes();
            $model->setAttributes($post['Materials']);

            $model['published_date'] = strtotime($model['published_date']);

            if($model['date_unpinning'] != '')
            {
                $model['date_unpinning'] = date("Y-m-d H:i", strtotime($model['date_unpinning']));
            }
            else
            {
                $model['date_unpinning'] = null;
            }

            $model['modified_by'] = Yii::$app->user->identity->id;
            $model['modified'] = date("Y-m-d H:i:s",time());

            $model['status'] = $post['Materials']['new_status'];


            if($model['status'] == Materials::PUBLISHED)
            {
                $model['publish_down'] = date("Y-m-d H:i:s",time());
            }
//            else if($model['status'] == Materials::REMOVE_PUBLICATION)
//            {
//                $model['publish_up'] = date("Y-m-d H:i:s",time());
//            }

            if($model->save($validation))
            {

                MaterialTagsInMaterials::deleteAll(['materials_id'=>$model['id']]);

                if($post['Materials']['tag'] != '')
                {
                    foreach($post['Materials']['tag'] as $tag)
                    {
                        $new_material_tags = MaterialTags::find()->where(['name'=>$tag])->one();

                        if(is_null($new_material_tags))
                        {
                            $new_material_tags = new MaterialTags();
                            $new_material_tags['name'] = $tag;
                            $new_material_tags['published'] = true;
                            $new_material_tags->save();
                        }

                        $new_material_tags_in_materials = new MaterialTagsInMaterials();

                        $new_material_tags_in_materials['material_tags_id'] = $new_material_tags['id'];
                        $new_material_tags_in_materials['materials_id'] = $model['id'];

                        $new_material_tags_in_materials->save();
                    }
                }

                Widget::deleteAll(['materials_id'=>$model['id']]);

                WidgetAccordion::deleteAll(['materials_id'=>$model['id']]);
                if(count(current($post['WidgetAccordion'])) == 1 && trim(strip_tags(current(current($post['WidgetAccordion']))['content_accordion'])) == '' && trim(strip_tags(current(current($post['WidgetAccordion']))['title_accordion'])) == '')
                {

                }
                else
                {

                    foreach(current($post['WidgetAccordion']) as $widget_accordion_value)
                    {
                        $new_widget_accordion = new WidgetAccordion();
                        if($widget_accordion_value['id_accordion'] != '')
                        {
                            $new_widget_accordion['id'] = $widget_accordion_value['id_accordion'];
                        }

                        $new_widget_accordion['title'] = $widget_accordion_value['title_accordion'];
                        $new_widget_accordion['content'] = $widget_accordion_value['content_accordion'];
                        $new_widget_accordion['materials_id'] = $model['id'];

                        if($widget_accordion_value['created_accordion'] != '')
                        {
                            $new_widget_accordion['modified'] = date("Y-m-d H:i:s",time());
                        }

                        $new_widget_accordion['created'] = $widget_accordion_value['created_accordion'] == '' ? date("Y-m-d H:i:s",time()):date("Y-m-d H:i:s",strtotime($widget_accordion_value['created_accordion']));

                        $new_widget_accordion->save();
                    }

                }

                if(isset($post['Materials']['name_widget_accordion']) && $post['Materials']['name_widget_accordion'] != '')
                {
                    $new_widget = new Widget();
                    $new_widget['type'] = 'accordion';
                    $new_widget['name'] = $post['Materials']['name_widget_accordion'];
                    $new_widget['materials_id'] = $model['id'];
                    $new_widget->save();
                }


                WidgetTabs::deleteAll(['materials_id'=>$model['id']]);
                if(count(current($post['WidgetTabs'])) == 1 && trim(strip_tags(current(current($post['WidgetTabs']))['content_tab'])) == '' && trim(strip_tags(current(current($post['WidgetTabs']))['title_tab'])) == '')
                {

                }
                else
                {

                    foreach(current($post['WidgetTabs']) as $widget_tab_value)
                    {
                        $new_widget_tab = new WidgetTabs();
                        if($widget_tab_value['id_tab'] != '')
                        {
                            $new_widget_tab['id'] = $widget_tab_value['id_tab'];
                        }

                        $new_widget_tab['title'] = $widget_tab_value['title_tab'];
                        $new_widget_tab['content'] = $widget_tab_value['content_tab'];
                        $new_widget_tab['materials_id'] = $model['id'];

                        if($widget_tab_value['created_tab'] != '')
                        {
                            $new_widget_tab['modified'] = date("Y-m-d H:i:s",time());
                        }

                        $new_widget_tab['created'] = $widget_tab_value['created_tab'] == '' ? date("Y-m-d H:i:s",time()):date("Y-m-d H:i:s",strtotime($widget_tab_value['created_tab']));

                        $new_widget_tab->save();
                    }

                }

                if(isset($post['Materials']['name_widget_tabs']) && $post['Materials']['name_widget_tabs'] != '')
                {
                    $new_widget = new Widget();
                    $new_widget['type'] = 'tabs';
                    $new_widget['name'] = $post['Materials']['name_widget_tabs'];
                    $new_widget['materials_id'] = $model['id'];
                    $new_widget->save();
                }

                WidgetMap::deleteAll(['materials_id'=>$model['id']]);
                if(count($post['WidgetMap']) == 1 && trim(current($post['WidgetMap'])['lat']) == '' && trim(current($post['WidgetMap'])['lng']) == '')
                {

                }
                else
                {
                    foreach($post['WidgetMap'] as $widget_map_value)
                    {

                        $new_widget_map = new WidgetMap();
                        if($widget_map_value['id'] != '')
                        {
                            $new_widget_map['id'] = $widget_map_value['id'];
                        }

                        $new_widget_map['name'] = $widget_map_value['name'];
                        $new_widget_map['title'] = $widget_map_value['title'];
                        $new_widget_map['lat'] = $widget_map_value['lat'];
                        $new_widget_map['lng'] = $widget_map_value['lng'];
                        $new_widget_map['materials_id'] = $model['id'];

                        if($widget_map_value['created'] != '')
                        {
                            $new_widget_map['modified'] = date("Y-m-d H:i:s",time());
                        }

                        $new_widget_map['created'] = $widget_map_value['created'] == '' ? date("Y-m-d H:i:s",time()):date("Y-m-d H:i:s",strtotime($widget_map_value['created']));

                        $new_widget_map->save();
                    }

                }

                if(isset($post['Materials']['name_widget_map']) && $post['Materials']['name_widget_map'] != '')
                {
                    $new_widget = new Widget();
                    $new_widget['type'] = 'map';
                    $new_widget['name'] = $post['Materials']['name_widget_map'];
                    $new_widget['materials_id'] = $model['id'];
                    $new_widget->save();
                }



                if(isset($_FILES['Materials']) && $_FILES['Materials']['name']['path_widget_gallery'][0] != '')
                {
                    $order_id = WidgetGallery::find()->where(['materials_id'=>$model['id']])->orderBy('order_id desc')->one();
                    if(!is_null($order_id))
                    {
                        $i_order = $order_id['order_id']+1;
                    }
                    else
                    {
                        $i_order = 0;
                    }

                    foreach($_FILES['Materials']['name']['path_widget_gallery'] as $key=>$value)
                    {
                        if ($_FILES["Materials"]["error"]['path_widget_gallery'][$key] == UPLOAD_ERR_OK)
                        {
//                            if(!is_dir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/'))
//                            {
//                                mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/');
//                            }

                            $tmp_name = $_FILES["Materials"]["tmp_name"]['path_widget_gallery'][$key];
                            $path_info = pathinfo($_FILES["Materials"]["name"]['path_widget_gallery'][$key]);
                            $path_image = 'uploads/files_for_material/'.Yii::$app->user->identity->guid.'/gallery/' .$model['guid'].'_'.md5(rand(1,2147483647)).'.'.$path_info['extension'];

                            if(move_uploaded_file($tmp_name, $path_image))
                            {
                                $gallery = new WidgetGallery();
                                $gallery['image'] = $path_image;
                                $gallery['order_id'] = $i_order;
                                $gallery['materials_id'] = $model['id'];
                                $gallery['created'] = date("Y-m-d H:i:s",time());
                                $gallery->save();
                            }
                        }

                        $i_order++;
                    }

                }

                if(isset($post['Materials']['name_widget_gallery']) && $post['Materials']['name_widget_gallery'] != '')
                {
                    $new_widget = new Widget();
                    $new_widget['type'] = 'gallery';
                    $new_widget['name'] = $post['Materials']['name_widget_gallery'];
                    $new_widget['materials_id'] = $model['id'];
                    $new_widget->save();
                }

                if($post['WidgetLogo'])
                {
                    $widget_logo->setAttributes($post['WidgetLogo']);


                    $widget_logo->path_widget_logo = UploadedFile::getInstance($widget_logo, 'path_widget_logo');

                    if(!is_null($widget_logo->path_widget_logo))
                    {
//                        if(!is_dir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/'))
//                        {
//                            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/');
//                        }
                        $file_path = 'uploads/files_for_material/'.Yii::$app->user->identity->guid.'/logo/' .$model['guid'].'_'.md5(rand(1,2147483647)).'.'.$widget_logo->path_widget_logo->extension;
//                        $file_path = 'uploads/image_for_projects/'.Projects::transliterate($widget_logo['title']).'_'.md5(time()).'_'. $widget_logo->path_widget_logo->baseName . '.' . $widget_logo->path_widget_logo->extension;
                        if($widget_logo->path_widget_logo->saveAs($file_path))
                        {
                            $widget_logo['image'] = $file_path;
                        }
                        else
                        {
                            throw new HttpException(517 ,var_export($widget_logo->path_widget_logo->getErrors(),true) );
                        }
                    }

                    if($widget_logo['created'] != '')
                    {
                        $widget_logo['modified'] = date("Y-m-d H:i:s",time());
                    }
                    else
                    {
                        $widget_logo['created'] = date("Y-m-d H:i:s",time());
                    }

                    $widget_logo['materials_id'] = $model['id'];

                    $widget_logo->save();

                }

                MaterialWatchers::deleteAll(['material_id'=>$model['id']]);

                if(!empty($post['Materials']['material_watchers']))
                {
                    foreach ($post['Materials']['material_watchers'] as $material_watchers_value)
                    {
                        $new_material_watchers_email = MaterialWatchersEmail::find()->where(['email'=>$material_watchers_value])->one();

                        if(is_null($new_material_watchers_email))
                        {
                            $new_material_watchers_email = new MaterialWatchersEmail();
                            $new_material_watchers_email['email'] = $material_watchers_value;
                            $new_material_watchers_email['in_archive'] = false;

                            $new_material_watchers_email->save();
                        }

                        $new_material_watchers = new MaterialWatchers();

                        $new_material_watchers['material_watchers_email_id'] = $new_material_watchers_email['id'];
                        $new_material_watchers['material_id'] = $model['id'];

                        $new_material_watchers->save();
                    }
                }


                WidgetYoutube::deleteAll(['materials_id'=>$model['id']]);
                if(isset($post['Materials']['youtube_url']) && $post['Materials']['youtube_url'] != '')
                {
                    $new_widget_youtube = new WidgetYoutube();
                    $new_widget_youtube['youtube_url'] = $post['Materials']['youtube_url'];
                    $new_widget_youtube['materials_id'] = $model['id'];
                    $new_widget_youtube->save();
                }
                if(isset($post['Materials']['name_widget_youtube']) && $post['Materials']['name_widget_youtube'] != '')
                {
                    $new_widget = new Widget();
                    $new_widget['type'] = 'youtube';
                    $new_widget['name'] = $post['Materials']['name_widget_youtube'];
                    $new_widget['materials_id'] = $model['id'];
                    $new_widget->save();
                }

                if(is_null($auto_save))
                {
                    if($model['status'] != Materials::DRAFT && $ole_model['status'] != $model['status'])
                    {
                        $this->sendMailUser($model);
                    }

                    if($model['status'] == Materials::PUBLISHED && $ole_model['status'] != $model['status'])
                    {
                        SendEmailMaterialPublished::deleteAll(['materials_id'=>$model['id']]);
                    }

                    $this->AutoSaveMaterial($model['id']);

                    return $this->redirect(['/materials/update-material','id'=>$model['id']]);
//                    return $this->redirect([Url::previous()]);
                }
                else
                {
                    return true;
                }
            }
            else
            {
                $model['published_date'] = date("d.m.Y H:i",$model['published_date']);

                if($model['date_unpinning'] != '')
                {
                    $model['date_unpinning'] = date("d.m.Y H:i", strtotime($model['date_unpinning']));
                }
                else
                {
                    $model['date_unpinning'] = null;
                }
            }
        }

        return $this->render('addMaterial', [
            'model' => $model,
            'widget_map' => $widget_map,
            'init_value_text' => $init_value_text,
            'widget_gallery' => $widget_gallery,
            'widget_tab' => $widget_tab,
            'widget_accordion' => $widget_accordion,
            'widget_gallery_image' => $widget_gallery_image,
            'widget_gallery_remove' => $widget_gallery_remove,
            'widget_logo' => $widget_logo,
            'users_array' => $users_array
        ]);
    }


    public function actionGetMaterialWatchers($q = '')
    {
        $json = [];

        $material_watchers_email_array = [];
        $material_watchers_email = MaterialWatchersEmail::find()->select('email')->where(['in_archive'=>false]);

        if($q != '')
        {
            $material_watchers_email->andWhere('LOWER(email) LIKE :email',[':email'=>'%'.mb_strtolower($q).'%']);
        }

        $material_watchers_email = $material_watchers_email->all();

        if(!empty($material_watchers_email))
        {
            foreach ($material_watchers_email as $material_watchers_email_val)
            {
                $material_watchers_email_array[] = ['id'=>$material_watchers_email_val['email'],'text' => $material_watchers_email_val['email']];
            }
        }

        $json['results'] = $material_watchers_email_array;

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }


    public function actionGetMaterialTag($q = '')
    {
        $json = [];

        $tags = [];
        $array_tags = MaterialTags::find()->select('name')->where(['published'=>true]);

        if($q != '')
        {
            $array_tags->andWhere('LOWER(name) LIKE :name',[':name'=>'%'.mb_strtolower($q).'%']);
        }

        $array_tags = $array_tags->all();

        if(!empty($array_tags))
        {
            foreach($array_tags as $tags_val)
            {
                $tags[] = ['id'=>$tags_val['name'],'text' => $tags_val['name']];
            }
        }

        $json['results'] = $tags;

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionFileDeleteMaterial($id)
    {
        $file_material = WidgetGallery::find()->where(['id'=>$id])->one();
        unlink($file_material['image']);
        WidgetGallery::deleteAll(['id'=>$id]);
        return true;
    }

    public function actionGetDelegationRightsInUserGroups($category)
    {
        $delegation_rights = '0';

        $delegation_rights_in_user_groups = DelegationRightsInUserGroups::find()->select('user_groups_id')->where(['users_id'=>Yii::$app->user->identity->id])->all();

        if(!empty($delegation_rights_in_user_groups))
        {
            $user_groups_rights_material_categories_array = [];
            foreach($delegation_rights_in_user_groups as $delegation_rights_in_user_groups_val)
            {
                $user_groups_rights_material_categories_array[] = $delegation_rights_in_user_groups_val['user_groups_id'];
            }

            $user_groups_rights_material_categories = UserGroupsRightsMaterialCategories::find()
                ->select('category_id')
                ->where(['user_groups_id'=>$user_groups_rights_material_categories_array,'category_id'=>$category])
                ->one();

            if(!is_null($user_groups_rights_material_categories))
            {
                $delegation_rights = '1';
            }
        }
        return $delegation_rights;
    }

    public function actionFileDeleteLogoMaterial($id)
    {
        $widget_logo = WidgetLogo::find()->where(['id'=>$id])->one();
        unlink($widget_logo['image']);
        $widget_logo['image'] = null;
        $widget_logo['modified'] = date("Y-m-d H:i:s",time());
        $widget_logo->save();
        return true;
    }



    public function actionGetUsers($q = '')
    {
        $users_array = [];
        $json = [];


        $users = Users::find()->select('id,second_name,first_name,third_name')
            ->where(['in_archive'=>false,'role'=>[Users::ROLE_ADMIN,Users::ROLE_MODERATOR,Users::ROLE_SENIOR_METHODIST,Users::ROLE_METHODIST]]);

            if($q != '')
            {
                //$query->andFilterWhere(['like', 'LOWER(materials.title)', mb_strtolower($title)]);
                $users->andWhere('LOWER(second_name) LIKE :second_name OR LOWER(first_name) LIKE :first_name OR LOWER(third_name) LIKE :third_name',
                    [':second_name'=>'%'.mb_strtolower($q).'%',':first_name'=>'%'.mb_strtolower($q).'%',':third_name'=>'%'.mb_strtolower($q).'%']);
            }

        $users = $users->all();

        if(!empty($users))
        {
            foreach($users as $users_val)
            {
                $users_array[] = ['id'=>$users_val['id'],'text' => $users_val['second_name'].' '.$users_val['first_name'].' '.$users_val['third_name']];
            }
        }
        $json['results'] = $users_array;

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }


    public function actionAddMaterial($auto_save = null)
    {

        $model = null;
        $validation = true;

        if(!is_null($auto_save))
        {
            $validation = false;
        }
        if(!is_dir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/'))
        {
            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/');
            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/source/');
            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/thumbs/');
            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/gallery/');
            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/logo/');
        }

        if(isset(Yii::$app->getRequest()->post('Materials')['guid']))
        {
            $model = Materials::find()->where(['guid'=>Yii::$app->getRequest()->post('Materials')['guid']])->one();
        }


        if(is_null($model))
        {
            $model = new Materials();
            $model['guid'] = md5(rand(1,2147483647).' '.rand(1,2147483647).' '.time());
        }

        $init_value_text = '';

        $widget_map[0] = new WidgetMap();

        $widget_accordion[0] = new WidgetAccordion();
        $widget_gallery_image =  [];
        $widget_gallery_remove =  [];
        $widget_gallery =  [];

        $widget_tab[0] = new WidgetTabs();

        $material_tags_in_materials_array = [];

        $model['tag'] = $material_tags_in_materials_array;

        $widget_logo = new WidgetLogo();

        $post = Yii::$app->getRequest()->post();


        if($post)
        {
            $model->setAttributes($post['Materials']);

            $model['created_by'] = Yii::$app->user->identity->id;
            $model['created'] = date("Y-m-d H:i:s",time());


            if(isset($model['published_date']) && $model['published_date'] != '' && !is_int($model['published_date']))
            {
                $model['published_date'] = strtotime($model['published_date']);
            }
            else
            {
                $model['published_date'] = time();
            }

            if($model['date_unpinning'] != '')
            {
                $model['date_unpinning'] = date("Y-m-d H:i", strtotime($model['date_unpinning']));
            }
            else
            {
                $model['date_unpinning'] = null;
            }


            if($model['status'] == Materials::DRAFT)
            {
                $model['publish_up'] = date("Y-m-d H:i:s",time());
            }

            $model['status'] = $post['Materials']['new_status'];

            if($model->save($validation))
            {
                Widget::deleteAll(['materials_id'=>$model['id']]);

                MaterialTagsInMaterials::deleteAll(['materials_id'=>$model['id']]);
                if($post['Materials']['tag'] != '')
                {
                    foreach($post['Materials']['tag'] as $tag)
                    {
                        $new_material_tags = MaterialTags::find()->where(['name'=>$tag])->one();


                        if(is_null($new_material_tags))
                        {
                            $new_material_tags = new MaterialTags();
                            $new_material_tags['name'] = $tag;
                            $new_material_tags['published'] = true;

                            $new_material_tags->save();
                        }

                        $new_material_tags_in_materials = new MaterialTagsInMaterials();

                        $new_material_tags_in_materials['material_tags_id'] = $new_material_tags['id'];
                        $new_material_tags_in_materials['materials_id'] = $model['id'];

                        $new_material_tags_in_materials->save();

                    }
                }


                WidgetAccordion::deleteAll(['materials_id'=>$model['id']]);
                if(count(current($post['WidgetAccordion'])) == 1 && trim(strip_tags(current(current($post['WidgetAccordion']))['content_accordion'])) == '' && trim(strip_tags(current(current($post['WidgetAccordion']))['title_accordion'])) == '')
                {

                }
                else
                {

                    foreach(current($post['WidgetAccordion']) as $widget_accordion_value)
                    {
                        $new_widget_accordion = new WidgetAccordion();
                        if($widget_accordion_value['id_accordion'] != '')
                        {
                            $new_widget_accordion['id'] = $widget_accordion_value['id_accordion'];
                        }

                        $new_widget_accordion['title'] = $widget_accordion_value['title_accordion'];
                        $new_widget_accordion['content'] = $widget_accordion_value['content_accordion'];
                        $new_widget_accordion['materials_id'] = $model['id'];

                        if($widget_accordion_value['created_accordion'] != '')
                        {
                            $new_widget_accordion['modified'] = date("Y-m-d H:i:s",time());
                        }

                        $new_widget_accordion['created'] = $widget_accordion_value['created_accordion'] == '' ? date("Y-m-d H:i:s",time()):date("Y-m-d H:i:s",strtotime($widget_accordion_value['created_accordion']));

                        $new_widget_accordion->save();
                    }

                    if(isset($post['Materials']['name_widget_accordion']) && $post['Materials']['name_widget_accordion'] != '')
                    {

                        $new_widget = new Widget();
                        $new_widget['type'] = 'accordion';
                        $new_widget['name'] = $post['Materials']['name_widget_accordion'];
                        $new_widget['materials_id'] = $model['id'];
                        $new_widget->save();
                    }

                }


                WidgetTabs::deleteAll(['materials_id'=>$model['id']]);

                if(count(current($post['WidgetTabs'])) == 1 && trim(strip_tags(current(current($post['WidgetTabs']))['content_tab'])) == '' && trim(strip_tags(current(current($post['WidgetTabs']))['title_tab'])) == '')
                {

                }
                else
                {

                    foreach(current($post['WidgetTabs']) as $widget_tab_value)
                    {
                        $new_widget_tab = new WidgetTabs();
                        if($widget_tab_value['id_tab'] != '')
                        {
                            $new_widget_tab['id'] = $widget_tab_value['id_tab'];
                        }

                        $new_widget_tab['title'] = $widget_tab_value['title_tab'];
                        $new_widget_tab['content'] = $widget_tab_value['content_tab'];
                        $new_widget_tab['materials_id'] = $model['id'];

                        if($widget_tab_value['created_tab'] != '')
                        {
                            $new_widget_tab['modified'] = date("Y-m-d H:i:s",time());
                        }

                        $new_widget_tab['created'] = $widget_tab_value['created_tab'] == '' ? date("Y-m-d H:i:s",time()):date("Y-m-d H:i:s",strtotime($widget_tab_value['created_tab']));

                        $new_widget_tab->save();
                    }

                    if(isset($post['Materials']['name_widget_tabs']) && $post['Materials']['name_widget_tabs'] != '')
                    {

                        $new_widget = new Widget();
                        $new_widget['type'] = 'tabs';
                        $new_widget['name'] = $post['Materials']['name_widget_tabs'];
                        $new_widget['materials_id'] = $model['id'];
                        $new_widget->save();
                    }
                }

                WidgetMap::deleteAll(['materials_id'=>$model['id']]);

                if(count($post['WidgetMap']) == 1 && trim(current($post['WidgetMap'])['lat']) == '' && trim(current($post['WidgetMap'])['lng']) == '')
                {

                }
                else
                {

                    foreach($post['WidgetMap'] as $widget_map_value)
                    {

                        $new_widget_map = new WidgetMap();
                        if($widget_map_value['id'] != '')
                        {
                            $new_widget_map['id'] = $widget_map_value['id'];
                        }

                        $new_widget_map['name'] = $widget_map_value['name'];
                        $new_widget_map['title'] = $widget_map_value['title'];
                        $new_widget_map['lat'] = $widget_map_value['lat'];
                        $new_widget_map['lng'] = $widget_map_value['lng'];
                        $new_widget_map['materials_id'] = $model['id'];

                        if($widget_map_value['created'] != '')
                        {
                            $new_widget_map['modified'] = date("Y-m-d H:i:s",time());
                        }

                        $new_widget_map['created'] = $widget_map_value['created'] == '' ? date("Y-m-d H:i:s",time()):date("Y-m-d H:i:s",strtotime($widget_map_value['created']));

                        $new_widget_map->save();
                    }

                    if(isset($post['Materials']['name_widget_map']) && $post['Materials']['name_widget_map'] != '')
                    {

                        $new_widget = new Widget();
                        $new_widget['type'] = 'map';
                        $new_widget['name'] = $post['Materials']['name_widget_map'];
                        $new_widget['materials_id'] = $model['id'];
                        $new_widget->save();
                    }
                }

                if(isset($_FILES['Materials']) && $_FILES['Materials']['name']['path_widget_gallery'][0] != '')
                {
                    $order_id = WidgetGallery::find()->where(['materials_id'=>$model['id']])->orderBy('order_id desc')->one();
                    if(!is_null($order_id))
                    {
                        $i_order = $order_id['order_id']+1;
                    }
                    else
                    {
                        $i_order = 0;
                    }

                    foreach($_FILES['Materials']['name']['path_widget_gallery'] as $key=>$value)
                    {
                        if ($_FILES["Materials"]["error"]['path_widget_gallery'][$key] == UPLOAD_ERR_OK)
                        {
//                            if(!is_dir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/'))
//                            {
//                                mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/');
//                            }


                            $tmp_name = $_FILES["Materials"]["tmp_name"]['path_widget_gallery'][$key];
                            $path_info = pathinfo($_FILES["Materials"]["name"]['path_widget_gallery'][$key]);
                            $path_image = 'uploads/files_for_material/'.Yii::$app->user->identity->guid.'/gallery/' .$model['guid'].'_'.md5(rand(1,2147483647)).'.'.$path_info['extension'];

                            if(move_uploaded_file($tmp_name, $path_image))
                            {
                                $gallery = new WidgetGallery();
                                $gallery['image'] = $path_image;
                                $gallery['order_id'] = $i_order;
                                $gallery['materials_id'] = $model['id'];
                                $gallery['created'] = date("Y-m-d H:i:s",time());
                                $gallery->save();
                            }
                        }

                        $i_order++;
                    }

                    if(isset($post['Materials']['name_widget_gallery']) && $post['Materials']['name_widget_gallery'] != '')
                    {
                        $new_widget = new Widget();
                        $new_widget['type'] = 'gallery';
                        $new_widget['name'] = $post['Materials']['name_widget_gallery'];
                        $new_widget['materials_id'] = $model['id'];
                        $new_widget->save();
                    }
                }

                WidgetLogo::deleteAll(['materials_id'=>$model['id']]);


                if($post['WidgetLogo'])
                {


                    $widget_logo->setAttributes($post['WidgetLogo']);

                    $widget_logo->path_widget_logo = UploadedFile::getInstance($widget_logo, 'path_widget_logo');

                    if(!is_null($widget_logo->path_widget_logo))
                    {
//                        if(!is_dir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/'))
//                        {
//                            mkdir('uploads/files_for_material/'.Yii::$app->user->identity->guid.'/');
//                        }
                        $file_path = 'uploads/files_for_material/'.Yii::$app->user->identity->guid.'/logo/' .$model['guid'].'_'.md5(rand(1,2147483647)).'.'.$widget_logo->path_widget_logo->extension;
//                        $file_path = 'uploads/image_for_projects/'.Projects::transliterate($widget_logo['title']).'_'.md5(time()).'_'. $widget_logo->path_widget_logo->baseName . '.' . $widget_logo->path_widget_logo->extension;
                        if($widget_logo->path_widget_logo->saveAs($file_path))
                        {
                            $widget_logo['image'] = $file_path;
                        }
                        else
                        {
                            throw new HttpException(517 ,var_export($widget_logo->path_widget_logo->getErrors(),true) );
                        }
                    }

                    if($widget_logo['created'] != '')
                    {
                        $widget_logo['modified'] = date("Y-m-d H:i:s",time());
                    }
                    else
                    {
                        $widget_logo['created'] = date("Y-m-d H:i:s",time());
                    }

                    $widget_logo['materials_id'] = $model['id'];

                    $widget_logo->save();
                }


                MaterialWatchers::deleteAll(['material_id'=>$model['id']]);

                if(!empty($post['Materials']['material_watchers']))
                {

                    foreach ($post['Materials']['material_watchers'] as $material_watchers_value)
                    {

                        $new_material_watchers_email = MaterialWatchersEmail::find()->where(['email'=>$material_watchers_value])->one();

                        if(is_null($new_material_watchers_email))
                        {
                            $new_material_watchers_email = new MaterialWatchersEmail();
                            $new_material_watchers_email['email'] = $material_watchers_value;
                            $new_material_watchers_email['in_archive'] = false;

                            $new_material_watchers_email->save();
                        }

                        $new_material_watchers = new MaterialWatchers();

                        $new_material_watchers['material_watchers_email_id'] = $new_material_watchers_email['id'];
                        $new_material_watchers['material_id'] = $model['id'];

                        $new_material_watchers->save();
                    }
                }


                WidgetYoutube::deleteAll(['materials_id'=>$model['id']]);

                if(isset($post['Materials']['youtube_url']) && $post['Materials']['youtube_url'] != '')
                {

                   $new_widget_youtube = new WidgetYoutube();
                   $new_widget_youtube['youtube_url'] = $post['Materials']['youtube_url'];
                   $new_widget_youtube['materials_id'] = $model['id'];
                   $new_widget_youtube->save();

                    if(isset($post['Materials']['name_widget_youtube']) && $post['Materials']['name_widget_youtube'] != '')
                    {
                        $new_widget = new Widget();
                        $new_widget['type'] = 'youtube';
                        $new_widget['name'] = $post['Materials']['name_widget_youtube'];
                        $new_widget['materials_id'] = $model['id'];
                        $new_widget->save();
                    }
                }


                if(is_null($auto_save))
                {
                    if($model['status'] != Materials::DRAFT)
                    {
                        $this->sendMailUser($model);
                    }

                    $this->AutoSaveMaterial($model['id']);

                    return $this->redirect(['/materials/update-material','id'=>$model['id']]);
                }
                else
                {
                    return true;
                }
            }
            else
            {
                $model['published_date'] = date("d.m.Y H:i",$model['published_date']);

                if($model['date_unpinning'] != '')
                {
                    $model['date_unpinning'] = date("d.m.Y H:i",strtotime($model['date_unpinning']));
                }
                else
                {
                    $model['date_unpinning'] = null;
                }
            }
        }

        return $this->render('addMaterial', [
            'model' => $model,
            'init_value_text' => $init_value_text,
            'widget_map' => $widget_map,
            'widget_gallery' => $widget_gallery,
            'widget_tab' => $widget_tab,
            'widget_accordion' => $widget_accordion,
            'widget_gallery_image' => $widget_gallery_image,
            'widget_gallery_remove' => $widget_gallery_remove,
            'widget_logo' => $widget_logo
        ]);

    }

    public function actionDuplicationMaterial($guid)
    {
         $duplication_materials = DuplicationMaterials::find()->where(['materials_guid'=>$guid])->one();

         if(is_null($duplication_materials))
         {
             $duplication_materials = new DuplicationMaterials();
         }

        $duplication_materials['materials_guid'] = $guid;
        $duplication_materials['time_open_material'] = time();
        $duplication_materials['users_id'] = Yii::$app->user->identity->id;

        if(!$duplication_materials->save())
        {
            throw new HttpException(517 , var_export($duplication_materials->getErrors(),true));
        }

        return true;
    }

    private function sendMailUser($model)
    {
        $ContactForm =  new ContactForm();
        $param['<%link%>'] = Html::a($model['title'],   Url::to(['/materials/update-material', 'id' => $model['id']],true));


        $material_categories_parant = MaterialCategories::CreateTreeMaterialsUserChild($model['material_categories_id']);

        $user_groups_rights_material_categories = UserGroupsRightsMaterialCategories::find()->where(['category_id'=>$material_categories_parant])->all();
//        var_dump($user_groups_rights_material_categories);
        $user_groups_id = [];

        if(!empty($user_groups_rights_material_categories))
        {
              foreach($user_groups_rights_material_categories as $user_groups_rights_material_categories_val)
              {
                  $user_groups_id[] = $user_groups_rights_material_categories_val['user_groups_id'];
              }

            $users_id_material_watchers = [];
            $users_id_senior_methodist = [];
            $users_id_moderator = [];

            $users_created_material = Users::find()->where(['id'=>$model['created_by'],'in_archive'=>false,'not_send_email'=>false])->one();

              $users = Users::find()
                  ->innerJoin(RoleUsersInUserGroups::tableName(),'role_users_in_user_groups.users_id = users.id')
                  ->where(['role_users_in_user_groups.user_groups_id'=>$user_groups_id,'users.in_archive'=>false,'users.not_send_email'=>false])->all();


              if(!empty($users))
              {
                  foreach($users as $users_val)
                  {
//                      if($users_val['role'] == Users::ROLE_METHODIST)
//                      {
//                          $users_id_methodist[$users_val['id']] = $users_val['email'];
//                      }
                      if($users_val['role'] == Users::ROLE_SENIOR_METHODIST)
                      {
                          $users_id_senior_methodist[$users_val['id']] = $users_val['email'];
                      }
                  }
              }

            $users_moderator = Users::find()->where(['role'=>Users::ROLE_MODERATOR,'in_archive'=>false,'not_send_email'=>false])->all();

            if(!empty($users_moderator))
            {
                foreach($users_moderator as $users_val)
                {
                    $users_id_moderator[$users_val['id']] = $users_val['email'];
                }
            }

            $users_delegation = Users::find()
                ->innerJoin(DelegationRightsInUserGroups::tableName(),'delegation_rights_in_user_groups.users_id = users.id')
                ->where(['delegation_rights_in_user_groups.user_groups_id'=>$user_groups_id,'users.in_archive'=>false,'users.not_send_email'=>false])->all();

            if(!empty($users_delegation))
            {
                foreach($users_delegation as $users_val)
                {
                    $users_id_senior_methodist[$users_val['id']] = $users_val['email'];
                }
            }


            $material_watchers_email = MaterialWatchersEmail::find()
                ->innerJoin(MaterialWatchers::tableName(),'material_watchers.material_watchers_email_id = material_watchers_email.id')
                ->where(['material_watchers.material_id'=>$model['id']])->all();


            if(!empty($material_watchers_email))
            {
                foreach($material_watchers_email as $material_watchers_email_val)
                {
                    $users_id_material_watchers[] = $material_watchers_email_val['email'];
                }
            }

            if($model['status'] == Materials::SENT_FOR_CONFIRMATION)
            {
                if(!is_null($users_created_material))
                {
                    $ContactForm->SendMail($users_created_material['email'],Notifications::MATERIAL_SENT_FOR_CONFIRMATION,$param);
                }

                if(!empty($users_id_senior_methodist))
                {
                    foreach($users_id_senior_methodist as $users_val)
                    {
                        $ContactForm->SendMail($users_val,Notifications::NEW_MATERIAL_AWAITING_CONFIRMATION,$param);
                    }
                }

                if(!empty($users_id_material_watchers))
                {
                    foreach($users_id_material_watchers as $users_val)
                    {
                        $ContactForm->SendMail($users_val,Notifications::MATERIAL_SENT_FOR_CONFIRMATION,$param);
                    }
                }
            }
            else if($model['status'] == Materials::CONFIRMED)
            {
                if(!is_null($users_created_material) && $users_created_material['role'] == Users::ROLE_METHODIST)
                {
                    $ContactForm->SendMail($users_created_material['email'],Notifications::MATERIAL_CONFIRMED_SENIOR_METHODIST,$param);
                }

                if(!empty($users_id_senior_methodist))
                {
                    foreach($users_id_senior_methodist as $users_val)
                    {
                        $ContactForm->SendMail($users_val,Notifications::MATERIAL_CONFIRMED,$param);
                    }
                }

                if(!empty($users_id_moderator))
                {
                    foreach($users_id_moderator as $users_val)
                    {
                        $ContactForm->SendMail($users_val,Notifications::NEW_MATERIAL_AWAITING_PUBLICATION,$param);
                    }
                }

                if(!empty($users_id_material_watchers))
                {
                    foreach($users_id_material_watchers as $users_val)
                    {
                        $ContactForm->SendMail($users_val,Notifications::MATERIAL_CONFIRMED_SENIOR_METHODIST,$param);
                    }
                }
            }
            else if($model['status'] == Materials::PUBLISHED)
            {
                $param_watchers['<%link%>'] = Html::a($model['title'],   Url::to(['/news-feed/'.$model['id']],true));
                if(!is_null($users_created_material) && $users_created_material['role'] == Users::ROLE_METHODIST)
                {
                    $ContactForm->SendMail($users_created_material['email'],Notifications::MATERIAL_PUBLISHED,$param);
                }

                if(!empty($users_id_senior_methodist))
                {
                    foreach($users_id_senior_methodist as $users_val)
                    {
                        $ContactForm->SendMail($users_val,Notifications::MATERIAL_PUBLISHED,$param);
                    }
                }

                if(!empty($users_id_moderator))
                {
                    foreach($users_id_moderator as $users_val)
                    {
                        $ContactForm->SendMail($users_val,Notifications::MATERIAL_PUBLISHED,$param);
                    }
                }

                if(!empty($users_id_material_watchers))
                {
                    foreach($users_id_material_watchers as $users_val)
                    {
                        $ContactForm->SendMail($users_val,Notifications::MATERIAL_PUBLISHED,$param_watchers);
                    }
                }
            }
            else if($model['status'] == Materials::SENT_FOR_DEVELOPMENT)
            {
                if(!is_null($users_created_material))
                {
                    $ContactForm->SendMail($users_created_material['email'],Notifications::MATERIAL_SENT_FOR_REVISION,$param);
                }

                if(!empty($users_id_material_watchers))
                {
                    foreach($users_id_material_watchers as $users_val)
                    {
                        $ContactForm->SendMail($users_val,Notifications::MATERIAL_SENT_FOR_REVISION,$param);
                    }
                }
            }
            else if($model['status'] == Materials::ARCHIVE)
            {
                $ContactForm->SendMail(Yii::$app->user->identity->email,Notifications::MATERIAL_MOVED_TO_ARCHIVE,$param);

                if(!empty($users_id_material_watchers))
                {
                    foreach($users_id_material_watchers as $users_val)
                    {
                        $ContactForm->SendMail($users_val,Notifications::MATERIAL_MOVED_TO_ARCHIVE,$param);
                    }
                }
            }
        }
    }


}
