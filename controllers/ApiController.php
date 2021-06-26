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
use app\models\ColorProjects;
use app\models\MaterialCategories;
use app\models\Materials;
use app\models\MaterialTags;
use app\models\MaterialTagsInMaterials;
use app\models\PositionAndDirection;
use app\models\PositionAndDirectionInUsers;
use app\models\Projects;
use app\models\Subscribers;
use app\models\SubscribersCategory;
use app\models\Users;
use app\models\WidgetGallery;
use app\models\WidgetLogo;
use app\models\WidgetYoutube;
use app\models\WidgetMap;
use app\models\WidgetAccordion;
use app\models\WidgetTabs;
use app\models\Widget;
use app\models\WorkPlan;
use app\models\WorkPlanDate;
use app\models\WorkPlanNote;
use app\models\WorkPlanPeriod;
use app\models\UserGroups;
use app\models\Webinars;
use Yii;
use yii\web\Controller;
use yii\web\Response;
//use yii\helpers\Url;
//use yii\sphinx\Query;
use kartik\mpdf\Pdf;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\HttpException;

class ApiController extends Controller
{
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
        ];
    }

    public function actionGetSimilarMaterials($id)
    {
        //http://mosmetod-new.local.gmc/api/get-similar-materials?id=94

        $model = Materials::find()->where(['id' => $id,'status'=>Materials::PUBLISHED])->one();

        if(is_null($model))
        {
            throw new HttpException(517 ,'Материал не найден');
        }

        $json = [];

        $not_in_array = [0];
        $material_tags_in_materials = MaterialTagsInMaterials::find()
            ->select('material_tags.name as material_tags_name')
            ->innerJoin(MaterialTags::tableName(),'material_tags_in_materials.material_tags_id = material_tags.id')
            ->where(['materials_id'=>$id,'material_tags.published'=>true])->all();


        if(!empty($material_tags_in_materials))
        {


            foreach($material_tags_in_materials as $material_tags_in_materials_val)
            {
                $not_in_str = implode(",", $not_in_array);
                $sSql = 'SELECT * FROM doc_index_tags '.
                     'WHERE MATCH('.Yii::$app->sphinx->quoteValue($material_tags_in_materials_val['material_tags_name']).') '.
                     'AND id NOT IN('.$id.', '.$not_in_str.') '.
                     'ORDER BY hits DESC, published_date DESC '.
                     'limit 3 option max_matches=3';
                $ids = Yii::$app->sphinx->createCommand($sSql)->queryAll();

                if(!empty($ids))
                {
                    foreach($ids as $ids_val)
                    {
                        $not_in_array[] = $ids_val['id'];
                    }
                }
            }
        }

        $in_array = $not_in_array;
        if(count($in_array)<10)
        {
            $not_in_array[] = $id;

            $materials = Materials::find()
                ->where(['material_categories_id'=>$model['material_categories_id']])
                ->andWhere(['status'=>Materials::PUBLISHED])
                ->andWhere(['not in','id', $not_in_array])
                ->orderBy('hits desc, published_date desc')
                ->limit(10-count($not_in_array))
                ->all();

            if(!empty($materials))
            {
                foreach ($materials as $materials_val)
                {
                    $in_array[]=$materials_val['id'];
                    $not_in_array[] = $materials_val['id'];
                }
            }
        }

        if(count($in_array)>1)
        {
            $materials = Materials::find()
                ->where(['id'=>$in_array,'status'=>Materials::PUBLISHED])
                ->orderBy('hits desc, published_date desc')
                ->all();

            if(!empty($materials))
            {
                foreach ($materials as $materials_key=>$materials_val)
                {
                    $json[$materials_key]['id'] = $materials_val['id'];
                    $json[$materials_key]['date'] = date('j', $materials_val['published_date']).' '.Materials::$month[date('m', $materials_val['published_date'])];
                    $json[$materials_key]['title'] = $materials_val['title'];
                    $json[$materials_key]['hits'] = $materials_val['hits'];
                    $json[$materials_key]['category'] = MaterialTags::find()->where(['id'=>$materials_val['material_categories_id']])->one()['name'] ;
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;

    }

    public function actionGetStaffOrganization()
    {
//        http://mosmetod-new.local.gmc/api/get-staff-organization
        $json = [];

        $position_and_direction_in_users = PositionAndDirectionInUsers::find()
            ->select(['position_and_direction_in_users.id',
                'position_and_direction.position as position_and_direction_position_api',
                'position_and_direction.name as position_and_direction_name_api',
                'CONCAT(users.second_name,\' \',users.first_name,\' \',users.third_name) AS users_fio_api',
                'users.id as users_id_api',
                'users.image as users_image_api',
                'users.description as users_description_api',
                'users.basic_information as users_basic_information_api',

                ])
            ->where(['users.in_archive' => false])
            ->innerJoin(Users::tableName(),'users.id = position_and_direction_in_users.users_id')
            ->innerJoin(PositionAndDirection::tableName(),'position_and_direction.id = position_and_direction_in_users.position_and_direction_id')
            ->orderBy('position_and_direction.position,users.role')
            ->all();


        if(!empty($position_and_direction_in_users))
        {
            foreach ($position_and_direction_in_users as $position_and_direction_in_users_val)
            {
                $json[$position_and_direction_in_users_val['position_and_direction_position_api']]['id'] = $position_and_direction_in_users_val['position_and_direction_position_api'];
                $json[$position_and_direction_in_users_val['position_and_direction_position_api']]['name'] = $position_and_direction_in_users_val['position_and_direction_name_api'];
                $json[$position_and_direction_in_users_val['position_and_direction_position_api']]['user'][] =
                    ['users_id' => $position_and_direction_in_users_val['users_id_api'],'users_fio' => $position_and_direction_in_users_val['users_fio_api'],
                        'users_image' => ($position_and_direction_in_users_val['users_image_api'] != ''?Url::to([$position_and_direction_in_users_val['users_image_api']],true):Url::to(['/images/mosmetod/labor-teacher.png'],true)) ,'users_description' =>  $position_and_direction_in_users_val['users_description_api'],
                        'link'=>trim(strip_tags($position_and_direction_in_users_val['users_basic_information_api'])) == ''?false:true
                    ];
//                $json[$position_and_direction_in_users_val['position_and_direction_position_api']]['link'] = trim(strip_tags($position_and_direction_in_users_val['users_basic_information_api'])) == ''?false:true;
            }
        }

        $json = array_values($json);

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;

    }



    public function actionShowPreliminaryMaterial($id,$type,$guid)
    {
        //http://mosmetod-new.local.gmc/api/show-preliminary-material?id=34530&type=1&guid=d41d8cd98f00b204e9800998ecf8427e

        $json['id'] = 0;
        $json['title'] = '';

        $json['author'] = [];
        $json['category'] = [];

        $json['anchors'] = [];

        $json['banner'] = [];

        if($guid != Yii::$app->session->get('access_token') && $guid != 'd41d8cd98f00b204e9800998ecf8427e')
        {
            $json['content'] = 'Токен подключение пользвателя не совпадает';
//            throw new HttpException(517 ,'Токен подключение пользвателя не совпадает');
        }
        else
        {
            if($type == 1)
            {
                $model = AutoSaveMaterials::find()->where(['id' => $id])->one();

                if(is_null($model))
                {
                    $json['content'] = 'Материал не найден';
//                    throw new HttpException(517 ,'Материал не найден');
                }
                else
                {
                    $json['id'] = $model['id'];
                    $json['title'] = $model['title'];
                    $json['content'] = $model['content'];

                    $json['author'] = [];
                    $json['category'] = [];

                    $json['anchors'] = [];


                    $widget_logo = AutoSaveWidgetLogo::find()->where(['auto_save_materials_id' => $model['id']])
                        ->andWhere('image IS NOT NULL OR image != \'\'')
                        ->one();

                    $json['banner'] = [];
                    if(!is_null($widget_logo))
                    {
                        $json['banner']['logo'] = Url::to($widget_logo['image'],true);
                        $json['banner']['url'] = '#';
                        $json['banner']['align'] = WidgetLogo::$types_en[$widget_logo['type']];
                    }

                    $users = Users::find()->where(['id'=>$model['created_by']])->one();
                    if(!is_null($users))
                    {
                        $json['author']['id'] = $users['id'];
                        $json['author']['name'] = $users['second_name'].' '.$users['first_name'].' '.$users['third_name'];
                        if($users['image'] != '')
                        {
                            $json['author']['photo'] = Url::to($users['image'],true);
                        }
                        else
                        {
                            $json['author']['photo'] = '';
                        }
                    }

                    $material_categories = MaterialCategories::find()->where(['id'=>$model['material_categories_id']])->one();
                    if(!is_null($material_categories))
                    {
                        $json['category']['id'] = $material_categories['id'];
                        $json['category']['name'] = $material_categories['title'];
                    }

                    $json['publication'] = date('j', $model['published_date']).' '.Materials::$month[date('m', $model['published_date'])].' '.date('Y', $model['published_date']).' '.date('H:i', $model['published_date']);


                    $widget_gallery = AutoSaveWidgetGallery::find()->where(['auto_save_materials_id'=>$model['id']])->orderBy('order_id')->all();


                    if(!empty($widget_gallery))
                    {
                        $json['anchors']['gallery'] = [];
                        $widget = AutoSaveWidget::find()->select('name')->where(['auto_save_materials_id'=>$model['id'],'type'=>'gallery'])->one();
                        $json['anchors']['gallery']['title'] = isset($widget['name'])?$widget['name']:'Галерея';

                        $widget_gallery_image = [];
                        foreach ($widget_gallery as $widget_gallery_key=>$widget_gallery_val)
                        {
//                            $widget_gallery_image[$widget_gallery_key]['id'] = $widget_gallery_val['id'];
//                            $widget_gallery_image[$widget_gallery_key]['url'] = Url::to($widget_gallery_val['image'],true);
                            $widget_gallery_image[$widget_gallery_key]['original'] = Url::to($widget_gallery_val['image'],true);
                            $widget_gallery_image[$widget_gallery_key]['thumbnail'] = Url::to($widget_gallery_val['image'],true);
                        }
                        $json['anchors']['gallery']['imagesList'] = $widget_gallery_image;


                    }

                    $widget_youtube = AutoSaveWidgetYoutube::find()->select('youtube_url')->where(['auto_save_materials_id' => $model['id']])->one();
                    if(!is_null($widget_youtube))
                    {
                        $json['anchors']['video'] = [];
                        $widget = AutoSaveWidget::find()->select('name')->where(['auto_save_materials_id'=>$model['id'],'type'=>'youtube'])->one();
                        $json['anchors']['video']['title'] = isset($widget['name'])?$widget['name']:'Видео';
                        $json['anchors']['video']['url'] = $widget_youtube['youtube_url'];
                    }

                    $widget_map = AutoSaveWidgetMap::find()->where(['auto_save_materials_id'=>$model['id']])->all();


                    if(!empty($widget_map))
                    {
                        $json['anchors']['eventmap'] = [];
                        $widget = AutoSaveWidget::find()->select('name')->where(['auto_save_materials_id'=>$model['id'],'type'=>'map'])->one();
                        $json['anchors']['eventmap']['title'] = isset($widget['name'])?$widget['name']:'Карта';


                        $widget_map_array = [];

                        foreach($widget_map as $widget_map_key=>$widget_map_val)
                        {
                            $widget_map_array[$widget_map_key]['id'] = $widget_map_val['id'];
                            $widget_map_array[$widget_map_key]['placeTitle'] = $widget_map_val['name'];
                            $widget_map_array[$widget_map_key]['address'] = $widget_map_val['title'];
                            $widget_map_array[$widget_map_key]['latitude'] = $widget_map_val['lat'];
                            $widget_map_array[$widget_map_key]['longitude'] = $widget_map_val['lng'];
                        }

                        $json['anchors']['eventmap']['coordinates'] = $widget_map_array;

                    }



                    $widget_tab = AutoSaveWidgetTabs::find()->where(['auto_save_materials_id'=>$model['id']])->all();

                    if(!empty($widget_tab))
                    {
                        $json['anchors']['tabs'] = [];
                        $widget = AutoSaveWidget::find()->select('name')->where(['auto_save_materials_id'=>$model['id'],'type'=>'tabs'])->one();
                        $json['anchors']['tabs']['title'] = isset($widget['name'])?$widget['name']:'Вкладки';

                        $widget_tab_array = [];

                        foreach($widget_tab as $widget_tab_key=>$widget_tab_val)
                        {
                            $widget_tab_array[$widget_tab_key]['id'] = $widget_tab_val['id'];
                            $widget_tab_array[$widget_tab_key]['tabsTitle'] = $widget_tab_val['title'];
                            $widget_tab_array[$widget_tab_key]['tabsContent'] = $widget_tab_val['content'];
                        }

                        $json['anchors']['tabs']['items'] = $widget_tab_array;
                    }


                    $widget_accordion = AutoSaveWidgetAccordion::find()->where(['auto_save_materials_id'=>$model['id']])->all();

                    if(!empty($widget_accordion))
                    {
                        $json['anchors']['accordion'] = [];
                        $widget = AutoSaveWidget::find()->select('name')->where(['auto_save_materials_id'=>$model['id'],'type'=>'accordion'])->one();
                        $json['anchors']['accordion']['title'] = isset($widget['name'])?$widget['name']:'Раскрывающиеся списки';

                        $widget_accordion_array = [];

                        foreach($widget_accordion as $widget_accordion_key=>$widget_accordion_val)
                        {
                            $widget_accordion_array[$widget_accordion_key]['id'] = $widget_accordion_val['id'];
                            $widget_accordion_array[$widget_accordion_key]['accordionTitle'] = $widget_accordion_val['title'];
                            $widget_accordion_array[$widget_accordion_key]['accordionContent'] = $widget_accordion_val['content'];
                        }

                        $json['anchors']['accordion']['items'] = $widget_accordion_array;
                    }
                }
            }
            else if($type == 2)
            {
                $model = Materials::find()->where(['id' => $id])->one();

                if(is_null($model))
                {
                    $json['content'] = 'Материал не найден';
//                    throw new HttpException(517 ,'Материал не найден');
                }
                else
                {
                    $json = $this->generateMaterialContent($model);
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }


    public function actionShowMaterial($id)
    {
        //http://mosmetod-new.local.gmc/api/show-material?id=94
        $model = Materials::find()->where(['id' => $id,'status'=>Materials::PUBLISHED])->one();

        if(is_null($model))
        {
            $json['content'] = 'Материал не найден';
//            throw new HttpException(517 ,'Материал не найден');
        }

        $parent_in_archive = MaterialCategories::CreateTreeMaterialsUserChildInArchive($model['material_categories_id']);

        if(empty($parent_in_archive))
        {
            $json = $this->generateMaterialContent($model);
        }
        else
        {
            $json['content'] = 'Категория убрана в архив';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }


    public function actionGetUserInfo($id)
    {
        //http://mosmetod-new.local.gmc/api/get-user-info?id=15515

        $model = Users::find()
            ->select([
                'id',
                'CONCAT(users.second_name,\' \',users.first_name,\' \',users.third_name) AS fio',
                'description',
                'basic_information',
                'image',
            ])
            ->where(['id' => $id, 'in_archive'=>false])->one();

        if(is_null($model))
        {
            $json['basic_information'] = 'Пользователь не найден';
        }
        else
        {
            $description = str_replace('<br />','<br/>',$model['description']);
            $description = explode('<br/>', $description);
            $position = strip_tags($description[0]);

            $json['fio'] = $model['fio'];
            $json['position'] = ($model['id'] != 15515)?$position:'Директор';
            $json['basic_information'] = $model['basic_information'];
            $json['image'] = Url::to([$model['image']],true);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    private function generateMaterialContent($model)
    {
        $json['id'] = $model['id'];
        $json['title'] = $model['title'];
        $json['content'] = $model['content'];

        $json['author'] = [];
        $json['category'] = [];

        $json['anchors'] = [];


        $widget_logo = WidgetLogo::find()->where(['materials_id' => $model['id']])
            ->andWhere('image IS NOT NULL OR image != \'\'')
            ->one();

        $json['banner'] = [];
        if(!is_null($widget_logo))
        {

            $json['banner']['logo'] = Url::to($widget_logo['image'],true);
            $json['banner']['url'] = '#';
            $json['banner']['align'] = WidgetLogo::$types_en[$widget_logo['type']];
        }

        $users = Users::find()->where(['id'=>$model['created_by']])->one();
        if(!is_null($users))
        {
            $json['author']['id'] = $users['id'];
            $json['author']['name'] = $users['second_name'].' '.$users['first_name'].' '.$users['third_name'];
            if($users['image'] != '')
            {
                $json['author']['photo'] = Url::to($users['image'],true);
            }
            else
            {
                $json['author']['photo'] = '';
            }
        }

        $material_categories = MaterialCategories::find()->where(['id'=>$model['material_categories_id']])->one();
        if(!is_null($material_categories))
        {
            $json['category']['id'] = $material_categories['id'];
            $json['category']['name'] = $material_categories['title'];
        }

        $json['publication'] = date('j', $model['published_date']).' '.Materials::$month[date('m', $model['published_date'])].' '.date('Y', $model['published_date']).' '.date('H:i', $model['published_date']);


        $widget_gallery = WidgetGallery::find()->where(['materials_id'=>$model['id']])->orderBy('order_id')->all();


        if(!empty($widget_gallery))
        {
            $json['anchors']['gallery'] = [];
            $widget = Widget::find()->select('name')->where(['materials_id'=>$model['id'],'type'=>'gallery'])->one();
            $json['anchors']['gallery']['title'] = isset($widget['name'])?$widget['name']:'Галерея';

            $widget_gallery_image = [];
            foreach ($widget_gallery as $widget_gallery_key=>$widget_gallery_val)
            {

//                $widget_gallery_image[$widget_gallery_key]['id'] = $widget_gallery_val['id'];
                $widget_gallery_image[$widget_gallery_key]['original'] = Url::to($widget_gallery_val['image'],true);
                $widget_gallery_image[$widget_gallery_key]['thumbnail'] = Url::to($widget_gallery_val['image'],true);
            }
            $json['anchors']['gallery']['imagesList'] = $widget_gallery_image;


        }

        $widget_youtube = WidgetYoutube::find()->select('youtube_url')->where(['materials_id' => $model['id']])->one();
        if(!is_null($widget_youtube))
        {
            $json['anchors']['video'] = [];
            $widget = Widget::find()->select('name')->where(['materials_id'=>$model['id'],'type'=>'youtube'])->one();
            $json['anchors']['video']['title'] = isset($widget['name'])?$widget['name']:'Видео';
            $json['anchors']['video']['url'] = $widget_youtube['youtube_url'];
        }

        $widget_map = WidgetMap::find()->where(['materials_id'=>$model['id']])->all();


        if(!empty($widget_map))
        {
            $json['anchors']['eventmap'] = [];
            $widget = Widget::find()->select('name')->where(['materials_id'=>$model['id'],'type'=>'map'])->one();
            $json['anchors']['eventmap']['title'] = isset($widget['name'])?$widget['name']:'Карта';


            $widget_map_array = [];

            foreach($widget_map as $widget_map_key=>$widget_map_val)
            {
                $widget_map_array[$widget_map_key]['id'] = $widget_map_val['id'];
                $widget_map_array[$widget_map_key]['placeTitle'] = $widget_map_val['name'];
                $widget_map_array[$widget_map_key]['address'] = $widget_map_val['title'];
                $widget_map_array[$widget_map_key]['latitude'] = $widget_map_val['lat'];
                $widget_map_array[$widget_map_key]['longitude'] = $widget_map_val['lng'];
            }

            $json['anchors']['eventmap']['coordinates'] = $widget_map_array;

        }

        $widget_tab = WidgetTabs::find()->where(['materials_id'=>$model['id']])->all();

        if(!empty($widget_tab))
        {
            $json['anchors']['tabs'] = [];
            $widget = Widget::find()->select('name')->where(['materials_id'=>$model['id'],'type'=>'tabs'])->one();
            $json['anchors']['tabs']['title'] = isset($widget['name'])?$widget['name']:'Вкладки';

            $widget_tab_array = [];

            foreach($widget_tab as $widget_tab_key=>$widget_tab_val)
            {
                $widget_tab_array[$widget_tab_key]['id'] = $widget_tab_val['id'];
                $widget_tab_array[$widget_tab_key]['tabsTitle'] = $widget_tab_val['title'];
                $widget_tab_array[$widget_tab_key]['tabsContent'] = $widget_tab_val['content'];
            }

            $json['anchors']['tabs']['items'] = $widget_tab_array;
        }




        $widget_accordion = WidgetAccordion::find()->where(['materials_id'=>$model['id']])->all();

        if(!empty($widget_accordion))
        {
            $json['anchors']['accordion'] = [];
            $widget = Widget::find()->select('name')->where(['materials_id'=>$model['id'],'type'=>'accordion'])->one();
            $json['anchors']['accordion']['title'] = isset($widget['name'])?$widget['name']:'Раскрывающиеся списки';

            $widget_accordion_array = [];

            foreach($widget_accordion as $widget_accordion_key=>$widget_accordion_val)
            {
                $widget_accordion_array[$widget_accordion_key]['id'] = $widget_accordion_val['id'];
                $widget_accordion_array[$widget_accordion_key]['accordionTitle'] = $widget_accordion_val['title'];
                $widget_accordion_array[$widget_accordion_key]['accordionContent'] = $widget_accordion_val['content'];
            }

            $json['anchors']['accordion']['items'] = $widget_accordion_array;
        }

        return $json;
    }

    public function actionSearchPage($search,$site='site',$time='all',$page=1,$count=20)
    {
        //http://mosmetod-new.local.gmc/api/search-page?search=тест&site=site&time=day&page=1&count=20
        $json = [];
        $j = 0;

        $sSql='';
        $params = [];
        $projects = [];

        $date_start = '';
        $date_end = '';

        $search = str_replace('/', '', $search);
        if($search != '')
        {
            if($time == 'day')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00'));
                $date_end = strtotime(date('Y-m-d 23:59:59'));
            }
            else if($time == 'week')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("last Monday")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("next Sunday")));
            }
            else if($time == 'month')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("first day of +0 month")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("last day of +0 month")));
            }

            if($site != 'title')
            {
                $projects = Projects::find()->where(['in_archive'=>false])
                    ->andFilterWhere(['like', 'LOWER(title)', mb_strtolower($search)])
                    ->limit($count)
                    ->offset((($page-1)*$count))
                    ->all();

                if(!empty($projects))
                {
                    foreach ($projects as $projects_key=>$projects_val)
                    {
                        $json[$j]['title'] = $projects_val['title'];
                        $json[$j]['description'] = $projects_val['description'];
                        $json[$j]['logo'] = Url::to($projects_val['logo'],true);
                        $json[$j]['url'] = $projects_val['url'];
                        ++$j;
                    }
                }
            }

            if($site == 'site')
            {
                $sSql = 'SELECT id,title,description,published_date FROM doc_index WHERE MATCH('.Yii::$app->sphinx->quoteValue($search).') ';
            }
            else if($site == 'title')
            {
                $sSql = 'SELECT id,title,description,published_date FROM doc_index_title WHERE MATCH('.Yii::$app->sphinx->quoteValue($search).') ';
            }

            if($sSql != '' && $date_start != '' && $date_end != '')
            {
                $params=[
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                ];

                $sSql .= ' AND published_date BETWEEN :date_start AND :date_end ';

            }

            if(count($projects) < $count && count($projects) != 0 && $sSql != '')
            {
                $sSql .= ' LIMIT '.(($page-1)*$count).', '.($count-count($projects)).' option max_matches=30000';
            }
            else if(count($projects) == 0 && $sSql != '')
            {
                $remaining_count = 0;

                if($site != 'title')
                {
                    $projects_count = Projects::find()->where(['in_archive'=>false])
                        ->andFilterWhere(['like', 'LOWER(title)', mb_strtolower($search)])
                        ->count();

                    for($i=$projects_count; $i > 0; $i -= 10)
                    {
                        $remaining_count = $i;
                    }
                }

                $sSql .= ' LIMIT '.((($page-1) * $count) - $remaining_count).', '.($count).' option max_matches=30000';
            }

            if(count($projects) < $count && $sSql != '')
            {
                $ids = Yii::$app->sphinx->createCommand($sSql,$params)->queryAll();

                if(!empty($ids))
                {
                    foreach ($ids as $ids_key=>$ids_val)
                    {
                        $json[$j]['title'] = $ids_val['title'];
                        $json[$j]['date'] = date('d.m.Y H:i:s', $ids_val['published_date']) ;
                        $json[$j]['text'] = $ids_val['description'];
                        $json[$j]['id'] = $ids_val['id'];
                        ++$j;
                    }
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionSearchPageCount($search,$site='site',$time='all')
    {
        //http://mosmetod-new.local.gmc/api/search-page-count?search=тест&site=site&time=day
        $json = [];
        $sSql = '';
        $params = [];

        $ids = [];

        $date_start = '';
        $date_end = '';

        $projects = 0;

        $search = str_replace('/', '', $search);
        if($search != '')
        {

            if($time == 'day')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00'));
                $date_end = strtotime(date('Y-m-d 23:59:59'));
            }
            else if($time == 'week')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("last Monday")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("next Sunday")));
            }
            else if($time == 'month')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("first day of +0 month")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("last day of +0 month")));
            }

            if($site != 'title')
            {
                $projects = Projects::find()->where(['in_archive'=>false])
                    ->andFilterWhere(['like', 'LOWER(title)', mb_strtolower($search)])->count();
            }


            if($site == 'site')
            {
                $sSql = 'SELECT id FROM doc_index WHERE MATCH('.Yii::$app->sphinx->quoteValue($search).') ';
            }
            else if($site == 'title')
            {
                $sSql = 'SELECT id FROM doc_index_title WHERE MATCH('.Yii::$app->sphinx->quoteValue($search).') ';
            }


            if($sSql != '' && $date_start != '' && $date_end != '')
            {
                $params=[
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                ];

                $sSql .= ' AND published_date BETWEEN :date_start AND :date_end ';

            }

            if($sSql != '')
            {
                $sSql .= ' limit 30000 option max_matches=30000 ';
                $ids = Yii::$app->sphinx->createCommand($sSql,$params)->queryAll();
            }


        }


        $json['searchPageCount'] = ($projects+count($ids));

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionSearch($search)
    {
        //http://mosmetod-new.local.gmc/api/search?search=обучение
        $json = [];
        $j = 0;

        $search = str_replace('/', '', $search);
        if($search != '')
        {
            $projects = Projects::find()->where(['in_archive'=>false])
                ->andFilterWhere(['like', 'LOWER(title)', mb_strtolower($search)])->all();

            if(!empty($projects))
            {
                foreach ($projects as $projects_key=>$projects_val)
                {
                    $json[$j]['title'] = $projects_val['title'];
                    $json[$j]['description'] = $projects_val['description'];
                    $json[$j]['logo'] =  Url::to($projects_val['logo'],true);
                    $json[$j]['url'] = $projects_val['url'];
                    ++$j;
                }
            }

            if(count($json) < 10)
            {
                $limit = 10 - count($json);
                $sSql = 'SELECT * FROM doc_index WHERE MATCH('.Yii::$app->sphinx->quoteValue($search).') LIMIT '.$limit; //

                $ids = Yii::$app->sphinx->createCommand($sSql)->queryAll();

                if(!empty($ids))
                {
                    foreach ($ids as $ids_key=>$ids_val)
                    {
                        $json[$j]['title'] = $ids_val['title'];
                        $json[$j]['id'] = $ids_val['id'];
                        ++$j;
                    }
                }
            }
        }


        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetMethodicalSpaceMaterialCount($id,$time='all',$date_start = '',$date_end = '',$category = 0)
    {
        //http://mosmetod-new.local.gmc/api/get-methodical-space-material-count?id=35&time=month
        $json = [];

        if($date_start == '' && $date_end == '')
        {
            if($time == 'day')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00'));
                $date_end = strtotime(date('Y-m-d 23:59:59'));
            }
            else if($time == 'week')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("last Monday")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("next Sunday")));
            }
            else if($time == 'month')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("first day of +0 month")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("last day of +0 month")));
            }
        }
        else
        {
            $date_start = strtotime(date('Y-m-d 00:00:00',strtotime($date_start)));
            $date_end = strtotime(date('Y-m-d 23:59:59',strtotime($date_end)));
        }

        $material_categories = MaterialCategories::find()->where(['id'=>$id])->one();

        if(is_null($material_categories))
        {
            throw new HttpException(517 ,'Страница не найдена');
        }

        $category_id_tree = [$id];

        $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree,$category_id_tree);

        if($category != 0)
        {
            $category_id_tree = [$category];
        }

        $materials = Materials::find()
            ->where(['material_categories_id'=>$category_id_tree,'status'=>Materials::PUBLISHED]);

        if($date_start != '' && $date_end != '')
        {
            $materials->andWhere(['between','published_date',$date_start,$date_end]);
        }

        $json['newsPageCount'] = $materials->count();



        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;

    }

    public function actionGetMethodicalSpaceMaterial($id,$page = 1,$quantity=40,$time='all',$date_start = '',$date_end = '',$category = 0)
    {
//        http://mosmetod-new.local.gmc/api/get-methodical-space-material?id=35&page=1&quantity=9&time=month&category=335
        $json = [];

        if($date_start == '' && $date_end == '')
        {
            if($time == 'day')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00'));
                $date_end = strtotime(date('Y-m-d 23:59:59'));
            }
            else if($time == 'week')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("last Monday")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("next Sunday")));
            }
            else if($time == 'month')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("first day of +0 month")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("last day of +0 month")));
            }
        }
        else
        {
            $date_start = strtotime(date('Y-m-d 00:00:00',strtotime($date_start)));
            $date_end = strtotime(date('Y-m-d 23:59:59',strtotime($date_end)));
        }

        $material_categories = MaterialCategories::find()->where(['id'=>$id])->one();

        if(is_null($material_categories))
        {
            throw new HttpException(517 ,'Страница не найдена');
        }

        $category_id_tree = [$id];

        $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParentOrderBy($category_id_tree,$category_id_tree);


        $color_projects = ColorProjects::find()->orderBy('id')->all();

        $color_projects_array = [];
        foreach ($category_id_tree as $category_id_tree_key=>$category_id_tree_val)
        {
            $color_projects_array[$category_id_tree_val] = $color_projects[$category_id_tree_key]['start'];
        }

        if($category != 0)
        {
            $category_id_tree = [$category];
        }

        $materials = Materials::find()
            ->select(['materials.*','mc.title AS categories_name'])
            ->where(['material_categories_id'=>$category_id_tree,'status'=>Materials::PUBLISHED])
            ->orderBy('published_date desc')
            ->limit($quantity)
            ->offset((($page-1)*$quantity));

        $materials->innerJoin(MaterialCategories::tableName().' mc','materials.material_categories_id = mc.id');

        if($date_start != '' && $date_end != '')
        {
            $materials->andWhere(['between','published_date',$date_start,$date_end]);
        }

        $materials = $materials->all();

        if(!empty($materials))
        {
            foreach($materials as $materials_key => $materials_val)
            {
                $json[$materials_key]['id'] =  $materials_val['id'];
                $json[$materials_key]['title'] =  $materials_val['title'];
                $json[$materials_key]['material_categories_id'] =  $materials_val['material_categories_id'];
                $json[$materials_key]['material_categories_name'] =  $materials_val['categories_name'];
                $json[$materials_key]['color'] =  $color_projects_array[$materials_val['material_categories_id']];
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetMethodicalSpaceCategories($id)
    {
//        http://mosmetod-new.local.gmc/api/get-methodical-space-categories?id=35
        $json = [];
        $material_categories = MaterialCategories::find()->where(['id'=>$id,'in_archive'=>false])->one();

        if(is_null($material_categories))
        {
            throw new HttpException(517 ,'Страница не найдена');
        }

        $category_id_tree = [$id];

        $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParentOrderBy($category_id_tree,$category_id_tree);


        $color_projects = ColorProjects::find()->orderBy('id')->all();

        $color_projects_array = [];
        foreach ($category_id_tree as $category_id_tree_key=>$category_id_tree_val)
        {
            $color_projects_array[$category_id_tree_val] = $color_projects[$category_id_tree_key]['start'];
        }

        unset($category_id_tree[0]);

        if(!empty($category_id_tree))
        {
            $category_id_tree_array = [];
            foreach ($category_id_tree as $category_id_tree_val)
            {
                $materials = Materials::find()->where(['material_categories_id'=>$category_id_tree_val,'status'=>Materials::PUBLISHED])->count();
                if($materials > 0)
                {
                    $category_id_tree_array[] = $category_id_tree_val;
                }
            }

            $material_categories_tree = MaterialCategories::find()->where(['id'=>$category_id_tree_array,'in_archive'=>false])->all();
            if(!empty($material_categories_tree))
            {
                foreach($material_categories_tree as $material_categories_tree_key=>$material_categories_tree_val)
                {
                    $json[$material_categories_tree_key]['id'] =  $material_categories_tree_val['id'];
                    $json[$material_categories_tree_key]['title'] =  $material_categories_tree_val['title'];
                    $json[$material_categories_tree_key]['color'] =  $color_projects_array[$material_categories_tree_val['id']];

                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetMethodicalSpaceNameMainCategories($id)
    {
//        http://mosmetod-new.local.gmc/api/get-methodical-space-name-main-categories?id=35
        $json = [];

        $material_categories = MaterialCategories::find()->where(['id'=>$id,'in_archive'=>false])->one();

        if(is_null($material_categories))
        {
            throw new HttpException(517 ,'Страница не найдена');
        }

        $json['name_category'] = $material_categories['title'];

        $parent_categories = MaterialCategories::CreateTreeMaterialsUserChild($material_categories['id']);

        $material_categories_parent = MaterialCategories::find()->where(['id'=>$parent_categories[0],'in_archive'=>false])->one();

        $json['name_category_parent'] = $material_categories_parent['title'];
        $json['id_category_parent'] = $material_categories_parent['id'];

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetMethodicalSpaceSubcategories($id)
    {
        //http://mosmetod-new.local.gmc/api/get-methodical-space-subcategories?id=11
        $json = [];

        $material_categories = MaterialCategories::find()->where(['parent'=>$id,'in_archive'=>false])->orderBy('order_categories')->all();

        if(!empty($material_categories))
        {
            $material_categories_array = [];
            foreach ($material_categories as $material_categories_key=>$material_categories_val)
            {
                $material_categories_array[$material_categories_key]['title'] = $material_categories_val['title'];
                $material_categories_array[$material_categories_key]['id'] = $material_categories_val['id'];
            }

            $json = $material_categories_array;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetMethodicalSpace()
    {
        //http://mosmetod-new.local.gmc/api/get-methodical-space
        $json = [];

        $methodical_space = MaterialCategories::find()->where(['LOWER(title)'=>mb_strtolower('Методическое пространство'),'parent'=>0,'in_archive'=>false])->one();

        if(!is_null($methodical_space))
        {
            $material_categories = MaterialCategories::find()
                ->where(['parent'=>$methodical_space['id'],'in_archive'=>false])
                ->orderBy('order_categories')
                ->all();

            if(!empty($material_categories))
            {
                $material_categories_array = [];
                foreach ($material_categories as $material_categories_key=>$material_categories_val)
                {
                    $material_categories_array[$material_categories_key]['title'] = $material_categories_val['title'];
                    $material_categories_array[$material_categories_key]['id'] = $material_categories_val['id'];
                }

                $json = $material_categories_array;
            }
        }


        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionSpellingMistake($emphasized='',$url='',$comment='',$context='',$browser='')
    {
        //http://mosmetod-new.local.gmc/api/spelling-mistake?emphasized=Выделеный текст&url=https://yandex.ru/&comment=Текст комментария&context=Предложение&browser=Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; MASM; .NET4.0C; .NET4.0E; rv:11.0) like Gecko
        $json = [];

        $subject = 'Орфографическая ошибка "'.$emphasized.'"';

        $message = '<p>URL страницы: '.$url.'</p>';
        $message .= '<p>Комментарий пользователя: "'.$comment.'"</p>';
        $message .= '<p>Контекст: <br/>"'.$context.'"</p>';
        $message .= '<p>Браузер: '.$browser.'</p>';

        $client = new \SoapClient(Yii::$app->params['CFG_URL_SOAP'], ["cache_wsdl" => 0, "trace" => 1, "exceptions" => 0]);
        $result =  $client->sendMail('i25585@yandex.ru',$subject,$message,Yii::$app->params['CFG_PROJECT_TOKEN']);//'keel87@rambler.ru'

        if($result == true)
        {
            $json['result'] = true;
        }
        else
        {
            $json['result'] = false;
        }


        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }


    public function actionGetRegulationsCategory()
    {
        //http://mosmetod-new.local.gmc/api/get-regulations-category
        $json = [];
        $material_categories = MaterialCategories::find()->where(['LOWER(title)'=>mb_strtolower('Документы'),'parent'=>0,'in_archive'=>false])->one();


        $category_id_tree = [$material_categories['id']];

        $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParentOrderBy($category_id_tree,$category_id_tree);

        $color_projects = ColorProjects::find()->orderBy('id')->all();

        $color_projects_array = [];
        foreach ($category_id_tree as $category_id_tree_key=>$category_id_tree_val)
        {
            $color_projects_array[$category_id_tree_val] = $color_projects[$category_id_tree_key]['start'];
        }

        unset($category_id_tree[0]);

        if(!empty($category_id_tree))
        {
            $category_id_tree_array = [];
            foreach ($category_id_tree as $category_id_tree_val)
            {
                $materials = Materials::find()->where(['material_categories_id'=>$category_id_tree_val,'status'=>Materials::PUBLISHED])->count();
                if($materials > 0)
                {
                    $category_id_tree_array[] = $category_id_tree_val;
                }
            }

            $material_categories_tree = MaterialCategories::find()->where(['id'=>$category_id_tree_array,'in_archive'=>false])->all();
            if(!empty($material_categories_tree))
            {
                foreach($material_categories_tree as $material_categories_tree_key=>$material_categories_tree_val)
                {
                    $json[$material_categories_tree_key]['id'] =  $material_categories_tree_val['id'];
                    $json[$material_categories_tree_key]['title'] =  $material_categories_tree_val['title'];
                    $json[$material_categories_tree_key]['color'] =  $color_projects_array[$material_categories_tree_val['id']];

                }
            }
        }


        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetRegulations($page = 1,$quantity=40,$time='all',$date_start = '',$date_end = '',$category = 0)
    {
        //http://mosmetod-new.local.gmc/api/get-regulations
        $json = [];

        if($date_start == '' && $date_end == '')
        {
            if($time == 'day')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00'));
                $date_end = strtotime(date('Y-m-d 23:59:59'));
            }
            else if($time == 'week')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("last Monday")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("next Sunday")));
            }
            else if($time == 'month')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("first day of +0 month")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("last day of +0 month")));
            }
        }
        else
        {
            $date_start = strtotime(date('Y-m-d 00:00:00',strtotime($date_start)));
            $date_end = strtotime(date('Y-m-d 23:59:59',strtotime($date_end)));
        }

        $material_categories = MaterialCategories::find()->where(['LOWER(title)'=>mb_strtolower('Документы'),'parent'=>0,'in_archive'=>false])->one();

        if(is_null($material_categories))
        {
            throw new HttpException(517 ,'Страница не найдена');
        }


        $category_id_tree = [$material_categories['id']];

        $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParentOrderBy($category_id_tree,$category_id_tree);
        $color_projects = ColorProjects::find()->orderBy('id')->all();

        $color_projects_array = [];
        foreach ($category_id_tree as $category_id_tree_key=>$category_id_tree_val)
        {
            $color_projects_array[$category_id_tree_val] = $color_projects[$category_id_tree_key]['start'];
        }

        if($category > 0)
        {
            $category_id_tree = [$category];
        }

        $materials = Materials::find()
            ->select(['materials.*','mc.title AS categories_name'])
            ->where(['material_categories_id'=>$category_id_tree,'status'=>Materials::PUBLISHED])
            ->orderBy('published_date desc')
            ->limit($quantity)
            ->offset((($page-1)*$quantity));

        $materials->innerJoin(MaterialCategories::tableName().' mc','materials.material_categories_id = mc.id');

        if($date_start != '' && $date_end != '')
        {
            $materials->andWhere(['between','published_date',$date_start,$date_end]);
        }

        $materials = $materials->all();

        if(!empty($materials))
        {
            foreach($materials as $materials_key => $materials_val)
            {
                $json[$materials_key]['id'] =  $materials_val['id'];
                $json[$materials_key]['title'] =  $materials_val['title'];
                $json[$materials_key]['material_categories_id'] =  $materials_val['material_categories_id'];
                $json[$materials_key]['material_categories_name'] =  $materials_val['categories_name'];
                $json[$materials_key]['color'] =  $color_projects_array[$materials_val['material_categories_id']];
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetRegulationsCount($time='all',$date_start = '',$date_end = '',$category = 0)
    {
        //http://mosmetod-new.local.gmc/api/get-regulations-count
        $json = [];

        if($date_start == '' && $date_end == '')
        {
            if($time == 'day')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00'));
                $date_end = strtotime(date('Y-m-d 23:59:59'));
            }
            else if($time == 'week')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("last Monday")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("next Sunday")));
            }
            else if($time == 'month')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("first day of +0 month")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("last day of +0 month")));
            }
        }
        else
        {
            $date_start = strtotime(date('Y-m-d 00:00:00',strtotime($date_start)));
            $date_end = strtotime(date('Y-m-d 23:59:59',strtotime($date_end)));
        }

        $material_categories = MaterialCategories::find()->where(['LOWER(title)'=>mb_strtolower('Документы'),'parent'=>0,'in_archive'=>false])->one();

        if(is_null($material_categories))
        {
            throw new HttpException(517 ,'Страница не найдена');
        }

        if($category > 0)
        {
            $category_id_tree = [$category];
        }
        else
        {
            $category_id_tree = [$material_categories['id']];

            $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree,$category_id_tree);
        }


        $materials = Materials::find()
            ->where(['material_categories_id'=>$category_id_tree,'status'=>Materials::PUBLISHED]);

        if($date_start != '' && $date_end != '')
        {
            $materials->andWhere(['between','published_date',$date_start,$date_end]);
        }

        $json['newsPageCount'] = $materials->count();



        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;

    }

    public function actionGetCategoriesWebinars()
    {
        //http://mosmetod-new.local.gmc/api/get-categories-webinars
        $json = [];

       $user_groups = UserGroups::find()
           ->select('user_groups.id, user_groups.name')
           ->innerJoin(Webinars::tableName(),'webinars.user_groups_id = user_groups.id')
           ->where(['webinars.in_archive'=>false])
           ->orderBy('name asc')->all();

        if(!empty($user_groups))
        {

            $color_projects = ColorProjects::find()->orderBy('id')->all();

            $user_groups_array = [];


            foreach ($user_groups as $user_groups_key=>$user_groups_val)
            {
                $user_groups_array[$user_groups_key]['id'] = $user_groups_val['id'];
                $user_groups_array[$user_groups_key]['title'] = $user_groups_val['name'];
                $user_groups_array[$user_groups_key]['color'] = $color_projects[$user_groups_key]['start'];
            }

            $json = $user_groups_array;
        }


        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetWebinars($category=0,$time='all',$date_start = '',$date_end = '',$page=1,$quantity=8)
    {
        //http://mosmetod-new.local.gmc/api/get-webinars?category=0
        $json = [];

       $webinars = Webinars::find()
           ->select('webinars.*')
           ->innerJoin(UserGroups::tableName(),'webinars.user_groups_id = user_groups.id')
           ->where(['webinars.in_archive'=>false])
           ->orderBy('name asc')
           ->limit($quantity)
           ->offset((($page-1)*$quantity));

        if($date_start == '' && $date_end == '')
        {
            if($time == 'day')
            {
                $date_start = date('Y-m-d 00:00:00');
                $date_end = date('Y-m-d 23:59:59');
            }
            else if($time == 'week')
            {
                $date_start = date('Y-m-d 00:00:00', strtotime("last Monday"));
                $date_end = date('Y-m-d 23:59:59', strtotime("next Sunday"));
            }
            else if($time == 'month')
            {
                $date_start = date('Y-m-d 00:00:00', strtotime("first day of +0 month"));
                $date_end = date('Y-m-d 23:59:59', strtotime("last day of +0 month"));
            }
        }
        else
        {
            $date_start = date('Y-m-d 00:00:00',strtotime($date_start));
            $date_end = date('Y-m-d 23:59:59',strtotime($date_end));
        }

        if($date_start != '' && $date_end != '')
        {
            $webinars->andWhere(['between','webinars.time_created',$date_start,$date_end]);
        }

        if($category > 0)
        {
            $webinars->andWhere(['user_groups_id'=> $category]);
        }

        $webinars = $webinars->all();

        if(!empty($webinars))
        {

            $color_projects_array = [];

            $user_groups = UserGroups::find()
                ->select('user_groups.id, user_groups.name')
                ->innerJoin(Webinars::tableName(),'webinars.user_groups_id = user_groups.id')
                ->where(['webinars.in_archive'=>false])
                ->orderBy('name asc')->all();

            $category_array = [];
            if(!empty($user_groups))
            {
                $color_projects = ColorProjects::find()->orderBy('id')->all();

                foreach ($user_groups as $user_groups_key=>$user_groups_val)
                {
                    $color_projects_array[$user_groups_val['id']] = $color_projects[$user_groups_key]['start'];

                    $category_array[$user_groups_val['id']] = $user_groups_val['name'];
                }
            }

            $webinars_array = [];

            foreach ($webinars as $webinars_key=>$webinars_val)
            {

                $webinars_array[$webinars_key]['id'] = $webinars_val['id'];
                $webinars_array[$webinars_key]['title'] = $webinars_val['title'];
                $webinars_array[$webinars_key]['description'] = $webinars_val['description'];
                $webinars_array[$webinars_key]['youtube_url'] = $webinars_val['youtube_url'];
                $webinars_array[$webinars_key]['date'] = date('j', strtotime($webinars_val['time_created'])) . ' ' . Materials::$month[date('m', strtotime($webinars_val['time_created']))]. ' ' .date('H:s', strtotime($webinars_val['time_created']));
                $webinars_array[$webinars_key]['color'] =  isset($color_projects_array[$webinars_val['user_groups_id']])?$color_projects_array[$webinars_val['user_groups_id']]:'#fff' ;
                $webinars_array[$webinars_key]['category'] =  isset($category_array[$webinars_val['user_groups_id']])?$category_array[$webinars_val['user_groups_id']]:'';
            }

            $json = $webinars_array;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetWebinarsCount($category=0,$time='all',$date_start = '',$date_end = '')
    {
        //http://mosmetod-new.local.gmc/api/get-webinars-count?category=0
        $json = [];

       $webinars = Webinars::find()
           ->select('webinars.*')
           ->innerJoin(UserGroups::tableName(),'webinars.user_groups_id = user_groups.id')
           ->where(['webinars.in_archive'=>false])
           ->orderBy('name asc');


        if($date_start == '' && $date_end == '')
        {
            if($time == 'day')
            {
                $date_start = date('Y-m-d 00:00:00');
                $date_end = date('Y-m-d 23:59:59');
            }
            else if($time == 'week')
            {
                $date_start = date('Y-m-d 00:00:00', strtotime("last Monday"));
                $date_end = date('Y-m-d 23:59:59', strtotime("next Sunday"));
            }
            else if($time == 'month')
            {
                $date_start = date('Y-m-d 00:00:00', strtotime("first day of +0 month"));
                $date_end = date('Y-m-d 23:59:59', strtotime("last day of +0 month"));
            }
        }
        else
        {
            $date_start = date('Y-m-d 00:00:00',strtotime($date_start));
            $date_end = date('Y-m-d 23:59:59',strtotime($date_end));
        }

        if($date_start != '' && $date_end != '')
        {
            $webinars->andWhere(['between','webinars.time_created',$date_start,$date_end]);
        }

       if($category > 0)
       {
           $webinars ->andWhere(['user_groups_id'=> $category]);
       }

        $json['webinarsPageCount'] = $webinars->count();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetWorkPlanTypeEvent($date_start,$date_end)
    {
        //http://mosmetod-new.local.gmc/api/get-work-plan-type-event?date_start=1592773200&date_end=1593291600
        $json = [];
        $in_array_type_event = [];
        $j = 0;

        $date_start = strtotime('2019-04-21');
        $date_end = strtotime($date_end);

        for($i = $date_start; $i<=$date_end; $i+= 86400)
        {
            $work_plan_period = WorkPlanPeriod::find()
                ->where(['month'=>date('m', $i),'year'=>date('Y', $i)])->one();

            if(!is_null($work_plan_period))
            {
                $work_plan_type_events = WorkPlan::find()
                    ->select(['work_plan.type_event'])
                    ->innerJoin(WorkPlanDate::tableName(),'work_plan.id = work_plan_date.work_plan_id')
                    ->where(['work_plan.not_included_main_report'=>false,'work_plan.in_archive'=>false,'work_plan_period_id'=>$work_plan_period['id']])
                    ->andWhere( '\''.date('Y-m-d H:i:s', $i).'\' >= work_plan_date.start_date AND work_plan_date.end_date >= \''.date('Y-m-d H:i:s', $i).'\'')
                    ->groupBy('work_plan.type_event')
                    ->all();

                if(!empty($work_plan_type_events))
                {
                    foreach($work_plan_type_events as $work_plan_type_events_val)
                    {
                        if(!in_array($work_plan_type_events_val['type_event'],$in_array_type_event))
                        {
                            $array = [];
                            $in_array_type_event[] = $work_plan_type_events_val['type_event'];
                            $array['id'] = ++$j;
                            $array['value'] = $work_plan_type_events_val['type_event'];
                            $array['label'] = $work_plan_type_events_val['type_event'];
                            $json[] = $array;
                        }
                    }
                }
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetWorkPlanForWhom($date_start,$date_end)
    {
        //http://mosmetod-new.local.gmc/api/get-work-plan-for-whom?date_start=1592773200&date_end=1593291600
        $json = [];
        $in_array_for_whom = [];
        $j = 0;

        $date_start = strtotime('2019-04-21');
        $date_end = strtotime($date_end);


        for($i = $date_start; $i<=$date_end; $i+= 86400)
        {
            $work_plan_period = WorkPlanPeriod::find()
                ->where(['month'=>date('m', $i),'year'=>date('Y', $i)])->one();

            if(!is_null($work_plan_period))
            {
                $work_plan_for_whoms = WorkPlan::find()
                    ->select(['work_plan.for_whom'])
                    ->innerJoin(WorkPlanDate::tableName(),'work_plan.id = work_plan_date.work_plan_id')
                    ->where(['work_plan.not_included_main_report'=>false,'work_plan.in_archive'=>false,'work_plan_period_id'=>$work_plan_period['id']])
                    ->andWhere( '\''.date('Y-m-d H:i:s', $i).'\' >= work_plan_date.start_date AND work_plan_date.end_date >= \''.date('Y-m-d H:i:s', $i).'\'')
                    ->groupBy('work_plan.for_whom')
                    ->all();

                if(!empty($work_plan_for_whoms))
                {
                    foreach($work_plan_for_whoms as $work_plan_for_whoms_val)
                    {
                        if(!in_array($work_plan_for_whoms_val['for_whom'],$in_array_for_whom))
                        {
                            $array = [];
                            $in_array_for_whom[] = $work_plan_for_whoms_val['for_whom'];
                            $array['id'] = ++$j;
                            $array['value'] = $work_plan_for_whoms_val['for_whom'];
                            $array['label'] = $work_plan_for_whoms_val['for_whom'];
                            $json[] = $array;
                        }
                    }
                }
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetWorkPlanSubject($date_start,$date_end)
    {
        //http://mosmetod-new.local.gmc/api/get-work-plan-subject?date_start=2020-07-01&date_end=2020-07-30 !!!!!! Нужно сказать о методе
        $json = [];
        $in_array_subject = [];
//        $j = 0;

        $date_start = strtotime('2019-04-21');
        $date_end = strtotime($date_end);


        for($i = $date_start; $i<=$date_end; $i+= 86400)
        {
            $work_plan_period = WorkPlanPeriod::find()
                ->where(['month'=>date('m', $i),'year'=>date('Y', $i)])->one();

            if(!is_null($work_plan_period))
            {
                $work_plan_subjects = WorkPlan::find()
                    ->select(['work_plan.user_groups_id','user_groups.name AS user_groups_name'])
                    ->innerJoin(WorkPlanDate::tableName(),'work_plan.id = work_plan_date.work_plan_id')
                    ->innerJoin(UserGroups::tableName(),'user_groups.id = work_plan.user_groups_id')
                    ->where(['work_plan_period_id'=>$work_plan_period['id'],'work_plan.not_included_main_report'=>false,'work_plan.in_archive'=>false])
                    ->andWhere( '\''.date('Y-m-d H:i:s', $i).'\' >= work_plan_date.start_date AND work_plan_date.end_date >= \''.date('Y-m-d H:i:s', $i).'\'')
                    ->groupBy('user_groups.name, work_plan.user_groups_id')
                    ->all();

                if(!empty($work_plan_subjects))
                {
                    foreach($work_plan_subjects as $work_plan_subjects_val)
                    {
                        if(!in_array($work_plan_subjects_val['user_groups_name'],$in_array_subject))
                        {
                            $array = [];
                            $in_array_subject[] = $work_plan_subjects_val['user_groups_name'];
                            $array['id'] = $work_plan_subjects_val['user_groups_id'];
                            $array['value'] = $work_plan_subjects_val['user_groups_name'];
                            $array['label'] = $work_plan_subjects_val['user_groups_name'];
                            $json[] = $array;
                        }
                    }
                }
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetWorkPlanDistrict($date_start,$date_end)
    {
        //http://mosmetod-new.local.gmc/api/get-work-plan-district?date_start=1592773200&date_end=1593291600
        $j = 0;
//        $array_json['id'] = ++$j;
//        $array_json['value'] = 'Все округа';
//        $array_json['label'] = 'Все округа';
//        $json[] = $array_json;
        $json = [];
        $in_array_district = [];

        $date_start = strtotime($date_start);
        $date_end = strtotime($date_end);

        for($i = $date_start; $i<=$date_end; $i+= 86400)
        {
            $work_plan_period = WorkPlanPeriod::find()
                ->where(['month'=>date('m', $i),'year'=>date('Y', $i)])->one();

            if(!is_null($work_plan_period))
            {
                $work_plan_districts = WorkPlan::find()
                    ->select(['work_plan.district'])
                    ->innerJoin(WorkPlanDate::tableName(),'work_plan.id = work_plan_date.work_plan_id')
                    ->where(['work_plan.not_included_main_report'=>false,'work_plan.in_archive'=>false,'work_plan_period_id'=>$work_plan_period['id']])
                    ->andWhere( '\''.date('Y-m-d H:i:s', $i).'\' >= work_plan_date.start_date AND work_plan_date.end_date >= \''.date('Y-m-d H:i:s', $i).'\'')
                    ->groupBy('work_plan.district')
                    ->all();

                if(!empty($work_plan_districts))
                {
                    foreach($work_plan_districts as $work_plan_districts_val)
                    {
                        $district = explode(',',$work_plan_districts_val['district']);
                        foreach ($district as $district_val)
                        {
                            $district_val = trim($district_val);

                            if(!in_array($district_val,$in_array_district) && $district_val != 'Все округа')
                            {
                                $array = [];
                                $in_array_district[] = $district_val;
                                $array['id'] = ++$j;
                                $array['value'] = $district_val;
                                $array['label'] = $district_val;
                                $json[] = $array;
                            }
                        }
                    }
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetEventsWorkPlan($date_start,$date_end,$search='',$page=1,$count=20)
    {
        //http://mosmetod-new.local.gmc/api/get-events-work-plan?date_start=2020-09-01&date_end=2020-09-30&type_event[]=Викторина&type_event[]=Встреча&for_whom[]=Методист&for_whom[]=Учитель&user_groups_id[]=1&district[]=ЗелАО&district[]=ВАО
        $json = [];
        $work_plan_key = 0;
        $work_plan_key_array = [];

        $date_start = strtotime('2019-04-21');
        $date_end = strtotime($date_end);

        $work_plan = WorkPlan::find()
            ->select(['work_plan.*'])
            ->innerJoin(WorkPlanDate::tableName(),'work_plan.id = work_plan_date.work_plan_id')
            ->innerJoin(WorkPlanPeriod::tableName(),'work_plan_period.id = work_plan.work_plan_period_id')
            ->where(['work_plan.not_included_main_report'=>false,'work_plan.in_archive'=>false,])
            ->andWhere('(end_date >= \''.date('Y-m-d H:i:s', $date_end).'\' AND start_date <=\''.date('Y-m-d H:i:s', $date_start).'\') OR (end_date <= \''.date('Y-m-d H:i:s', $date_end).'\' AND start_date >=\''.date('Y-m-d H:i:s', $date_start).'\') OR (start_date <= \''.date('Y-m-d H:i:s', $date_end).'\' AND start_date >=\''.date('Y-m-d H:i:s', $date_start).'\' ) OR (end_date <= \''.date('Y-m-d H:i:s', $date_start).'\' AND end_date >=\''.date('Y-m-d H:i:s', $date_start).'\' )')
            ->limit($count)
            ->offset((($page-1)*$count))
            ->orderBy('work_plan.id')
            ->groupBy('work_plan.id');


        if(isset($_GET['type_event']) && $_GET['type_event'][0] !='' )
        {
            $type_event_str = '';
            foreach ($_GET['type_event'] as $type_event_val)
            {
                $type_event_str .= 'type_event=\''.$type_event_val.'\' OR ';
            }

            $type_event_str = substr($type_event_str, 0, -4);
            $work_plan->andWhere($type_event_str);
        }

        if(isset($_GET['for_whom']) && $_GET['for_whom'][0] != '')
        {
            $for_whom_str = '';
            foreach ($_GET['for_whom'] as $for_whom_val)
            {
                $for_whom_str .= 'for_whom=\''.$for_whom_val.'\' OR ';
            }

            $for_whom_str = substr($for_whom_str, 0, -4);
            $work_plan->andWhere($for_whom_str);
        }

        if(isset($_GET['user_groups_id']) && $_GET['user_groups_id'][0] != '')
        {
            $user_groups_id_str = '';
            foreach ($_GET['user_groups_id'] as $user_groups_id_val)
            {
                $user_groups_id_str .= 'user_groups_id=\''.$user_groups_id_val.'\' OR ';
            }

            $user_groups_id_str = substr($user_groups_id_str, 0, -4);
            $work_plan->andWhere($user_groups_id_str);
        }

        if(isset($_GET['district']) && $_GET['district'][0] != '')
        {
            $description_str = 'district=\'Все округа\' OR ';
            foreach ($_GET['district'] as $description_val)
            {
                $description_str .= 'district LIKE \'%'.$description_val.'%\' OR ';
            }

            $description_str = substr($description_str, 0, -4);
            $work_plan->andWhere($description_str);
        }


        if(isset($search) && $search != '')
        {
            $work_plan->andFilterWhere(['like', 'LOWER(event_name)', mb_strtolower($search)]);
        }

        $work_plan_result = $work_plan->all();


        if(!empty($work_plan_result))
        {
            foreach($work_plan_result as $work_plan_val)
            {
                if(!in_array($work_plan_val['id'],$work_plan_key_array))
                {
                    $json[$work_plan_key]['id'] = $work_plan_val['id'];
                    $json[$work_plan_key]['period'] = '';

                    $work_plan_date = WorkPlanDate::find()->where(['work_plan_id'=>$work_plan_val['id']])->orderBy('start_date')->all();

                    foreach($work_plan_date as $work_plan_date_val)
                    {
                        $json[$work_plan_key]['period'] .= date('d.m.Y', strtotime($work_plan_date_val['start_date'])).' - '.date('d.m.Y', strtotime($work_plan_date_val['end_date'])).' ';
                    }

                    $user_groups = UserGroups::find()->where(['id'=>$work_plan_val['user_groups_id']])->one();

                    $json[$work_plan_key]['event_time'] = $work_plan_val['event_time'];
                    $json[$work_plan_key]['subject'] =  is_null($user_groups)?'Не определено':$user_groups['name'];
                    $json[$work_plan_key]['desc'] = $work_plan_val['event_name'];
                    $json[$work_plan_key]['district'] = $work_plan_val['district'];

                    $json[$work_plan_key]['place'] = $work_plan_val['location'];
                    $json[$work_plan_key]['admin'] = $work_plan_val['responsible'];
                    $json[$work_plan_key]['links'] = [];

                    $work_plan_note = WorkPlanNote::find()->where(['work_plan_id'=>$work_plan_val['id']])->all();
                    if(!empty($work_plan_note))
                    {
                        foreach ($work_plan_note as $work_plan_note_key=>$work_plan_note_val)
                        {
                            if($work_plan_note_val['note_name'] != '')
                            {
                                $json[$work_plan_key]['links'][$work_plan_note_key] = [
                                    'title'=>$work_plan_note_val['note_name'],
                                    'url'=>$work_plan_note_val['note_url'],
                                ];
                            }
                        }
                    }

                    $work_plan_key++;

                    $work_plan_key_array[]=$work_plan_val['id'];
                }
            }
        }



//        for($i = $date_start; $i<=$date_end; $i+= 86400)
//        {
//            $work_plan_period = WorkPlanPeriod::find()
//                ->where(['in_archive'=>false,'month'=>date('m', $i),'year'=>date('Y', $i)])->one();
//
//            if(!is_null($work_plan_period))
//            {
//
//            }
//        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionGetEventsWorkPlanPdf($month,$year)
    {
        //http://mosmetod-new.local.gmc/api/get-events-work-plan-pdf?month=7&year=2020
        $content = [];
        $work_plan_key = 0;
        $work_plan_key_array = [];

        $date_start = strtotime($year.'-'.$month.'-01');
        $date_end = strtotime(date('Y-m-t',$date_start));

        $work_plan = WorkPlan::find()
            ->select(['work_plan.*'])
            ->innerJoin(WorkPlanDate::tableName(),'work_plan.id = work_plan_date.work_plan_id')
            ->innerJoin(WorkPlanPeriod::tableName(),'work_plan_period.id = work_plan.work_plan_period_id')
            ->where(['work_plan.not_included_main_report'=>false,'work_plan.in_archive'=>false])
            ->andWhere('(end_date >= \''.date('Y-m-d H:i:s', $date_end).'\' AND start_date <=\''.date('Y-m-d H:i:s', $date_start).'\') OR (end_date <= \''.date('Y-m-d H:i:s', $date_end).'\' AND start_date >=\''.date('Y-m-d H:i:s', $date_start).'\') OR (start_date <= \''.date('Y-m-d H:i:s', $date_end).'\' AND start_date >=\''.date('Y-m-d H:i:s', $date_start).'\' ) OR (end_date <= \''.date('Y-m-d H:i:s', $date_start).'\' AND end_date >=\''.date('Y-m-d H:i:s', $date_start).'\' )')
            ->orderBy('work_plan_date.start_date');


        if(isset($_GET['type_event']) && $_GET['type_event'][0] !='' )
        {
            $type_event_str = '';
            foreach ($_GET['type_event'] as $type_event_val)
            {
                $type_event_str .= 'type_event=\''.$type_event_val.'\' OR ';
            }

            $type_event_str = substr($type_event_str, 0, -4);
            $work_plan->andWhere($type_event_str);
        }

        if(isset($_GET['for_whom']) && $_GET['for_whom'][0] != '')
        {
            $for_whom_str = '';
            foreach ($_GET['for_whom'] as $for_whom_val)
            {
                $for_whom_str .= 'for_whom=\''.$for_whom_val.'\' OR ';
            }

            $for_whom_str = substr($for_whom_str, 0, -4);
            $work_plan->andWhere($for_whom_str);
        }

        if(isset($_GET['user_groups_id']) && $_GET['user_groups_id'][0] != '')
        {
            $user_groups_id_str = '';
            foreach ($_GET['user_groups_id'] as $user_groups_id_val)
            {
                $user_groups_id_str .= 'user_groups_id=\''.$user_groups_id_val.'\' OR ';
            }

            $user_groups_id_str = substr($user_groups_id_str, 0, -4);
            $work_plan->andWhere($user_groups_id_str);
        }

        if(isset($_GET['district']) && $_GET['district'][0] != '')
        {
            $description_str = 'district=\'Все округа\' OR ';
            foreach ($_GET['district'] as $description_val)
            {
                $description_str .= 'district LIKE \'%'.$description_val.'%\' OR ';
            }

            $description_str = substr($description_str, 0, -4);
            $work_plan->andWhere($description_str);
        }


        if(isset($search) && $search != '')
        {
            $work_plan->andFilterWhere(['like', 'LOWER(event_name)', mb_strtolower($search)]);
        }

        $work_plan_result = $work_plan->all();


        if(!empty($work_plan_result))
        {
            foreach($work_plan_result as $work_plan_val)
            {
                if(!in_array($work_plan_val['id'],$work_plan_key_array))
                {
                    $content[$work_plan_key]['id'] = $work_plan_val['id'];
                    $content[$work_plan_key]['period'] = '';

                    $work_plan_date = WorkPlanDate::find()->where(['work_plan_id'=>$work_plan_val['id']])->orderBy('start_date')->all();

                    foreach($work_plan_date as $work_plan_date_val)
                    {
                        $content[$work_plan_key]['period'] .= date('d.m.Y', strtotime($work_plan_date_val['start_date'])).' - '.date('d.m.Y', strtotime($work_plan_date_val['end_date'])).' ';
                    }

                    $user_groups = UserGroups::find()->where(['id'=>$work_plan_val['user_groups_id']])->one();

                    $content[$work_plan_key]['event_time'] = $work_plan_val['event_time'];
                    $content[$work_plan_key]['for_whom'] = $work_plan_val['for_whom'];
                    $content[$work_plan_key]['subject'] = is_null($user_groups)?'Не определено':$user_groups['name'];
                    $content[$work_plan_key]['desc'] = $work_plan_val['event_name'];
                    $content[$work_plan_key]['district'] = $work_plan_val['district'];

                    $content[$work_plan_key]['place'] = $work_plan_val['location'];
                    $content[$work_plan_key]['admin'] = $work_plan_val['responsible'];
                    $content[$work_plan_key]['description'] = $work_plan_val['description'];
                    $content[$work_plan_key]['links'] = [];

                    $work_plan_note = WorkPlanNote::find()->where(['work_plan_id'=>$work_plan_val['id']])->all();
                    if(!empty($work_plan_note))
                    {
                        foreach ($work_plan_note as $work_plan_note_key=>$work_plan_note_val)
                        {
                            if($work_plan_note_val['note_name'] != '')
                            {
                                $content[$work_plan_key]['links'][$work_plan_note_key] = [
                                    'title'=>$work_plan_note_val['note_name'],
                                    'url'=>$work_plan_note_val['note_url'],
                                ];
                            }
                        }
                    }

                    $work_plan_key++;

                    $work_plan_key_array[]=$work_plan_val['id'];
                }
            }
        }

//        return $this->renderPartial('work_plan',['content'=>$content,'title'=>'План работ за '.date('d.m.Y',$date_start).' - '.date('d.m.Y',$date_end)]);
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'destination' => Pdf::DEST_BROWSER,
            'format' => Pdf::FORMAT_A3,
            'content' => $this->renderPartial('work_plan',['content'=>$content,'title'=>'План работ за '.date('d.m.Y',$date_start).' - '.date('d.m.Y',$date_end)]),
            'options' => [
                // any mpdf options you wish to set
            ],
            'methods' => [
                'SetTitle' => 'План работ за '.date('d.m.Y',$date_start).' - '.date('d.m.Y',$date_end),
//                'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => ['План работ за '.date('d.m.Y',$date_start).' - '.date('d.m.Y',$date_end).' ||'],
                'SetFooter' => ['|Страница {PAGENO}|'],
//                'SetAuthor' => 'Kartik Visweswaran',
//                'SetCreator' => 'Kartik Visweswaran',
//                'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        return $pdf->render();
    }


    public function actionSubscribeNewsletter($email,$category)
    {
        //http://mosmetod-new.local.gmc/api/subscribe-newsletter?email=i25585@yandex.ru&category=11
        Yii::$app->response->format = Response::FORMAT_JSON;
        $json = [];

        if(preg_match('/(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,6})$/i',$email))
        {
            $subscriber = Subscribers::find()->where(['email'=>$email])->one();
            if(!is_null($subscriber))
            {
                if($subscriber['status'] == Subscribers::CONFIRMATION)
                {
                    $subscribers_category = SubscribersCategory::find()->where(['user_id'=>$subscriber['id'],'category_id'=>$category])->one();

                    if(is_null($subscribers_category))
                    {
                        $subscribers_category =  new SubscribersCategory();
                        $subscribers_category['user_id'] = $subscriber['id'];
                        $subscribers_category['category_id'] = $category;
                        $subscribers_category['time_created'] = date('Y-m-d H:i:s',time());
                        if(!$subscribers_category->save())
                        {
                            $json['result'] = 'Вы не подписались на категорию '.MaterialCategories::find()->where(['id'=>$category])->one()['title'].'. Обратитесь в техническую поддержку.';
                            return $json;
                        }
                    }

                    $json['result'] = 'Вы подписаны на категорию '.MaterialCategories::find()->where(['id'=>$category])->one()['title'];
                }
            }
            else
            {
                $subscriber = new Subscribers();
            }

            if($subscriber['status'] != Subscribers::CONFIRMATION)
            {
                $subscriber['email'] = $email;
                $subscriber['time_created'] = date('Y-m-d H:i:s',time());
                $subscriber['status'] = Subscribers::CONFIRMATION_NOT_SENT;
                $subscriber['time_send'] = null;

                if(!$subscriber->save())
                {
                    $json['result'] = 'Учетная карточка подписчика не сохранилась. Обратитесь в техническую поддержку.';
                    return $json;
                }
                else
                {
                    $subscribers_category = SubscribersCategory::find()->where(['user_id'=>$subscriber['id'],'category_id'=>$category])->one();

                    if(is_null($subscribers_category))
                    {
                        $subscribers_category = new SubscribersCategory();
                        $subscribers_category['user_id'] = $subscriber['id'];
                        $subscribers_category['category_id'] = $category;
                        $subscribers_category['time_created'] = date('Y-m-d H:i:s',time());

                        if(!$subscribers_category->save())
                        {
                            $json['result'] = 'Вы не подписались на категорию '.MaterialCategories::find()->where(['id'=>$category])->one()['title'].'. Обратитесь в техническую поддержку.';
                            return $json;
                        }
                    }

                    $subject = 'Подтверждение подписки на рассылку новостей сайта Городского методического центра';
                    $header = '<div style=" style="font: 14px/20px Arial,Helvetica,sans-serif;">';
                    $header .= '<div>Уважаемый подписчик!</div>';
                    $footer = '</div>';

                    $code = md5($subscriber['id'] . $subscriber['email'] . $subscriber['time_created']);
//    $url = $host . 'check-email.php?email=' . $row['email'] . '&code=' . $code;
//                $urlManagement = $host . 'subscriptionManagement.php?email=' . $row['email'] . '&code=' . $code.'&first=1';

                    $urlManagement =  Html::a('Нажмите на эту ссылку для подтверждения подписки и перехода к управлению подпиской',
                        Url::to(['/site/update-material-categories', 'email' => $subscriber['email'], 'guid' => $code ],true),
                        ['style'=>'color:#4488BB;']);
                    $message = $header;
                    $message .= '<div style="margin: 10px 0px 10px 0px;">Пожалуйста, подтвердите email для получения рассылок новостей с сайта <a style="color:#4488BB;" href="http://mosmetod.ru">mosmetod.ru</a>.</div>';
                    $message .= '<div style="margin: 10px 0px 10px 0px;">'.$urlManagement.'.</div>';
                    $message .= '<div style="margin: 10px 0px 10px 0px;"><H3>Письмо отправлено автоматически. Отвечать на него не нужно.</H3></div>';

                    $message .= $footer;


                    $client = new \SoapClient(Yii::$app->params['CFG_URL_SOAP'], ["cache_wsdl" => 0, "trace" => 1, "exceptions" => 0]);
                    $result =  $client->sendMail($subscriber['email'],$subject,$message,Yii::$app->params['CFG_PROJECT_TOKEN']);

                    if($result == true)
                    {
                        $subscriber['status'] = Subscribers::CONFIRMATION_SENT;
                        $subscriber->save();
                        $json['result'] = 'На указанный Email отправлено письмо для подтверждения подписки.';
                    }
                    else
                    {
                        $json['result'] = 'Сервер не смог отправить письмо для подтверждения вашего Email. Обратитесь в техническую поддержку.';
                    }
                }
            }
        }
        else
        {
            $json['result'] = 'Email не прошел валидацию';
        }


        return $json;

    }

    public function actionGetEventsWorkPlanCount($date_start,$date_end,$search='')
    {
        //http://mosmetod-new.local.gmc/api/get-events-work-plan-count?date_start=2020-09-01&date_end=2020-09-30&type_event[]=Викторина&type_event[]=Встреча&for_whom[]=Методист&for_whom[]=Учитель&user_groups_id[]=1&district[]=ЗелАО&district[]=ВАО
        $json = [];
//        $work_plan_key = 0;
//        $work_plan_key_array = [];

        $date_start = strtotime('2019-04-21');
        $date_end = strtotime($date_end);



        $work_plan = WorkPlan::find()
            ->select(['work_plan.*'])
            ->innerJoin(WorkPlanDate::tableName(),'work_plan.id = work_plan_date.work_plan_id')
            ->innerJoin(WorkPlanPeriod::tableName(),'work_plan_period.id = work_plan.work_plan_period_id')
            ->where(['work_plan.not_included_main_report'=>false,'work_plan.in_archive'=>false])
            ->andWhere('(end_date >= \''.date('Y-m-d H:i:s', $date_end).'\' AND start_date <=\''.date('Y-m-d H:i:s', $date_start).'\') OR (end_date <= \''.date('Y-m-d H:i:s', $date_end).'\' AND start_date >=\''.date('Y-m-d H:i:s', $date_start).'\') OR (start_date <= \''.date('Y-m-d H:i:s', $date_end).'\' AND start_date >=\''.date('Y-m-d H:i:s', $date_start).'\' ) OR (end_date <= \''.date('Y-m-d H:i:s', $date_start).'\' AND end_date >=\''.date('Y-m-d H:i:s', $date_start).'\' )')
            ->groupBy('work_plan.id');

        if(isset($_GET['type_event']) && $_GET['type_event'][0] !='' )
        {
            $type_event_str = '';
            foreach ($_GET['type_event'] as $type_event_val)
            {
                $type_event_str .= 'type_event=\''.$type_event_val.'\' OR ';
            }

            $type_event_str = substr($type_event_str, 0, -4);
            $work_plan->andWhere($type_event_str);
        }

        if(isset($_GET['for_whom']) && $_GET['for_whom'][0] != '')
        {
            $for_whom_str = '';
            foreach ($_GET['for_whom'] as $for_whom_val)
            {
                $for_whom_str .= 'for_whom=\''.$for_whom_val.'\' OR ';
            }

            $for_whom_str = substr($for_whom_str, 0, -4);
            $work_plan->andWhere($for_whom_str);
        }

        if(isset($_GET['user_groups_id']) && $_GET['user_groups_id'][0] != '')
        {
            $user_groups_id_str = '';
            foreach ($_GET['user_groups_id'] as $user_groups_id_val)
            {
                $user_groups_id_str .= 'user_groups_id=\''.$user_groups_id_val.'\' OR ';
            }

            $user_groups_id_str = substr($user_groups_id_str, 0, -4);
            $work_plan->andWhere($user_groups_id_str);
        }

        if(isset($_GET['district']) && $_GET['district'][0] != '')
        {
            $description_str = 'district=\'Все округа\' OR ';
            foreach ($_GET['district'] as $description_val)
            {
                $description_str .= 'district LIKE \'%'.$description_val.'%\' OR ';
            }

            $description_str = substr($description_str, 0, -4);
            $work_plan->andWhere($description_str);
        }

        if(isset($search) && $search != '')
        {
            $work_plan->andFilterWhere(['like', 'LOWER(event_name)', mb_strtolower($search)]);
        }

        $work_plan_result = $work_plan->all();

        $json['eventsWorkPlanCount'] = count($work_plan_result) ;

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionNewsItemsHomePage()
    {
        //http://mosmetod-new.local.gmc/api/news-items-home-page
        $json = [];

        $material_categories = MaterialCategories::find()->where(['LOWER(title)' => [mb_strtolower('Анонсы'),mb_strtolower('Оперативная информация'),mb_strtolower('Сми о нас')],'in_archive'=>false])->all();

        if(!empty($material_categories)) {
            $array_material_categories = [];
            foreach ($material_categories as $material_categories_val) {
                $array_material_categories[] = $material_categories_val['id'];
            }

            $materials = Materials::find()->where(['status' => Materials::PUBLISHED,'material_categories_id'=>$array_material_categories])
//            ->where(['materials.status'=>Materials::$status_query])
                ->orderBy('hits desc, published_date desc')
                ->limit(8)
                ->all();

            if (!empty($materials)) {
                foreach ($materials as $materials_key => $materials_val) {
                    $json[$materials_key]['id'] = $materials_val['id'];
                    $json[$materials_key]['date'] = date('j', $materials_val['published_date']) . ' ' . Materials::$month[date('m', $materials_val['published_date'])];

                    $material_categories = MaterialCategories::find()->where(['id' => $materials_val['material_categories_id']])->one();
                    $json[$materials_key]['category'] = $material_categories['title'];
                    $json[$materials_key]['title'] = $materials_val['title'];
                }
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionListNewsByCategoryCount($category,$time='all',$date_start = '',$date_end = '')
    {
        //http://mosmetod-new.local.gmc/api/list-news-by-category-count?category=Анонсы&time=month
        $json['newsPageCount'] = 0;


        if($date_start == '' && $date_end == '')
        {
            if($time == 'day')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00'));
                $date_end = strtotime(date('Y-m-d 23:59:59'));
            }
            else if($time == 'week')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("last Monday")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("next Sunday")));
            }
            else if($time == 'month')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("first day of +0 month")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("last day of +0 month")));
            }
        }
        else
        {
            $date_start = strtotime(date('Y-m-d 00:00:00',strtotime($date_start)));
            $date_end = strtotime(date('Y-m-d 23:59:59',strtotime($date_end)));
        }


        if(mb_strtolower($category) == 'оперативная информация')
        {
            $query = [mb_strtolower('оперативная информация'),mb_strtolower('новости спо')];
        }
        else
        {
            $query = [mb_strtolower($category)];
        }

        $material_categories = MaterialCategories::find()->where(['LOWER(title)' => $query,'in_archive'=>false])->all();

        if(!empty($material_categories))
        {
            $array_material_categories = [];
            foreach($material_categories as $material_categories_val)
            {
                $array_material_categories[] = $material_categories_val['id'];
            }

            $materials = Materials::find()->where(['status'=>Materials::PUBLISHED,'material_categories_id'=>$array_material_categories]);

            if($date_start != '' && $date_end != '')
            {
                $materials->andWhere(['between','published_date',$date_start,$date_end]);
            }

            $json['newsPageCount'] = $materials->count();

        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionListNewsByCategory($category,$page = 1,$quantity=40,$time='all',$date_start = '',$date_end = '')
    {
        //http://mosmetod-new.local.gmc/api/list-news-by-category?category=Анонсы&page=1&quantity=9&time=month
        $json = [];


        if($date_start == '' && $date_end == '')
        {
            if($time == 'day')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00'));
                $date_end = strtotime(date('Y-m-d 23:59:59'));
            }
            else if($time == 'week')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("last Monday")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("next Sunday")));
            }
            else if($time == 'month')
            {
                $date_start = strtotime(date('Y-m-d 00:00:00', strtotime("first day of +0 month")));
                $date_end = strtotime(date('Y-m-d 23:59:59', strtotime("last day of +0 month")));
            }
        }
        else
        {
            $date_start = strtotime(date('Y-m-d 00:00:00',strtotime($date_start)));
            $date_end = strtotime(date('Y-m-d 23:59:59',strtotime($date_end)));
        }

        if(mb_strtolower($category) == 'оперативная информация')
        {
            $query = [mb_strtolower('оперативная информация'),mb_strtolower('новости спо')];
        }
        else
        {
            $query = [mb_strtolower($category)];
        }

        $material_categories = MaterialCategories::find()->where(['LOWER(title)' => $query,'in_archive'=>false])->all();

        if(!empty($material_categories))
        {
            $array_material_categories = [];
            foreach($material_categories as $material_categories_val)
            {
                $array_material_categories[] = $material_categories_val['id'];
            }

            $materials = Materials::find()->where(['status'=>Materials::PUBLISHED,'material_categories_id'=>$array_material_categories])
                ->orderBy('hits desc, published_date desc')
                ->limit($quantity)
                ->offset((($page-1)*$quantity));


            if($date_start != '' && $date_end != '')
            {
                $materials->andWhere(['between','published_date',$date_start,$date_end]);
            }


            $materials = $materials->all();

            if(!empty($materials))
            {
                foreach ($materials as $materials_key=>$materials_val)
                {
//                    var_dump(date('Y-m-d H:i:s', $materials_val['published_date']));
//                    var_dump(date('m', $materials_val['published_date']));
                    $json[$materials_key]['id'] = $materials_val['id'];
                    $json[$materials_key]['date'] = date('j', $materials_val['published_date']).' '.Materials::$month[date('m', $materials_val['published_date'])].' '.date('H:i', $materials_val['published_date']);
                    $json[$materials_key]['title'] = $materials_val['title'];
                    $json[$materials_key]['desc'] = $materials_val['description'];
                    $json[$materials_key]['hits'] = $materials_val['hits'];
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionOurProjects()
    {
        //http://mosmetod-new.local.gmc/api/our-projects
        $json = [];

        $projects = Projects::find()->where(['in_archive'=>false,'display_on_home_page'=>true,'outdated'=>false])->orderBy('time_create desc')->all();

        if(!empty($projects))
        {
            foreach ($projects as $projects_key=>$projects_val)
            {
                $json[$projects_key]['id'] = $projects_val['id'];
                $json[$projects_key]['title'] = $projects_val['title'];
                $json[$projects_key]['logo'] = Url::to($projects_val['logo'],true);
                $json[$projects_key]['url'] = $projects_val['url'];
                $json[$projects_key]['id'] = $projects_val['id'];
                $json[$projects_key]['description'] = $projects_val['description'];

                $color_projects = ColorProjects::find()->where(['id'=>$projects_val['color_projects_id']])->one();

                if(!is_null($color_projects))
                {
                    $json[$projects_key]['gradient']['start'] = $color_projects['start'];
                    $json[$projects_key]['gradient']['finish'] = $color_projects['end'];
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionOurProjectsAll()
    {
        //http://mosmetod-new.local.gmc/api/our-projects-all
        $json = [];

        $projects = Projects::find()->where(['in_archive'=>false])->orderBy('outdated, time_create desc')->all();

        if(!empty($projects))
        {
            foreach ($projects as $projects_key=>$projects_val)
            {
                $json[$projects_key]['id'] = $projects_val['id'];
                $json[$projects_key]['title'] = $projects_val['title'];
                $json[$projects_key]['logo'] = Url::to($projects_val['logo'],true);
                $json[$projects_key]['url'] = $projects_val['url'];
                $json[$projects_key]['id'] = $projects_val['id'];
                $json[$projects_key]['description'] = $projects_val['description'];
                $json[$projects_key]['in_archive'] = $projects_val['outdated'];

                $color_projects = ColorProjects::find()->where(['id'=>$projects_val['color_projects_id']])->one();

                if(!is_null($color_projects))
                {
                    $json[$projects_key]['gradient']['start'] = $color_projects['start'];
                    $json[$projects_key]['gradient']['finish'] = $color_projects['end'];
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionTakeWorkPlanHomePageCount($date_start,$date_end)
    {
        //http://mosmetod-new.local.gmc/api/take-work-plan-home-page-count?date_start=2020-06-01&date_end=2020-06-30
        $json = [];

        $date_start = strtotime($date_start);
        $date_end = strtotime($date_end);

        for($i = ($date_start); $i<=$date_end; $i+= 86400)
        {
            $work_plan_period = WorkPlanPeriod::find()
                ->where(['month'=>date('m', $i),'year'=>date('Y', $i)])->one();

            if(!is_null($work_plan_period))
            {

                $work_plan_count = WorkPlan::find()
                    ->select(['work_plan.*', 'work_plan_date.start_date as start_date', 'work_plan_date.end_date as end_date'])
                    ->innerJoin(WorkPlanDate::tableName(),'work_plan.id = work_plan_date.work_plan_id')
                    ->where(['work_plan.not_included_main_report'=>false,'work_plan.in_archive'=>false,'work_plan_period_id'=>$work_plan_period['id']])
                    ->andWhere( '\''.date('Y-m-d', $i).'\' >= work_plan_date.start_date AND work_plan_date.end_date >= \''.date('Y-m-d', $i).'\'')
                    ->count();

                $json[]['calendarEventsQuantity'] = $work_plan_count;
            }
            else
            {
                $json[]['calendarEventsQuantity'] = 3;
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    public function actionTakeWorkPlanHomePage($date,$page = 1)
    {
//http://mosmetod-new.local.gmc/api/take-work-plan-home-page?date=1593205200&page=1
        $json = [];

        $date = strtotime($date);

        $work_plan_period = WorkPlanPeriod::find()
            //->where(['month'=>date('m', $date),'year'=>date('Y', $date)])
            ->one();
            if(!is_null($work_plan_period))
            {
                $work_plan = WorkPlan::find()
                    ->select(['work_plan.*', 'work_plan_date.start_date as start_date', 'work_plan_date.end_date as end_date'])
                    ->innerJoin(WorkPlanDate::tableName(),'work_plan.id = work_plan_date.work_plan_id')
                   // ->where(['work_plan.not_included_main_report'=>false,'work_plan.in_archive'=>false,'work_plan_period_id'=>$work_plan_period['id']])
                   // ->andWhere( '\''.date('Y-m-d H:i:s', $date).'\' >= work_plan_date.start_date AND work_plan_date.end_date >= \''.date('Y-m-d H:i:s', $date).'\'')
                    ->limit(4)
                    ->offset((($page-1)*4))
                    ->orderBy('work_plan_date.start_date')
                    ->all();



                if(!empty($work_plan))
                {

                    foreach ($work_plan as $work_plan_key=>$work_plan_val)
                    {

                        $user_groups = UserGroups::find()->where(['id'=>$work_plan_val['user_groups_id']])->one();
                        $json[$work_plan_key]['id'] = $work_plan_val['id'];
                        $json[$work_plan_key]['period']['start'] = date('d', strtotime($work_plan_val['start_date'])).' '.Materials::$month[date('m', strtotime($work_plan_val['start_date']))];
                        $json[$work_plan_key]['period']['finish'] = date('d', strtotime($work_plan_val['end_date'])).' '.Materials::$month[date('m', strtotime($work_plan_val['end_date']))];

                        $json[$work_plan_key]['event_time'] = $work_plan_val['event_time'];
                        $json[$work_plan_key]['subject'] = is_null($user_groups)?'Не определено':$user_groups['name'];
                        $json[$work_plan_key]['desc'] = $work_plan_val['event_name'];
                        $json[$work_plan_key]['district'] = $work_plan_val['district'];



                        $json[$work_plan_key]['place'] = $work_plan_val['location'];
                        $json[$work_plan_key]['admin'] = $work_plan_val['responsible'];
                        $json[$work_plan_key]['links'] = [];

                        $work_plan_note = WorkPlanNote::find()->where(['work_plan_id'=>$work_plan_val['id']])->all();
                        if(!empty($work_plan_note))
                        {
                            foreach ($work_plan_note as $work_plan_note_key=>$work_plan_note_val)
                            {
                                if($work_plan_note_val['note_name'] != '')
                                {
                                    $json[$work_plan_key]['links'][$work_plan_note_key] = [
                                        'title'=>$work_plan_note_val['note_name'],
                                        'url'=>$work_plan_note_val['note_url'],
                                    ];
                                }
                            }
                        }
                    }
                }
            }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }






}
