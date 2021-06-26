<?php

namespace app\controllers;

use app\models\Materials;
use rmrevin\yii\fontawesome\FA;
use Yii;
use app\models\Users;
use app\models\UserGroups;
use app\models\MaterialCategories;
use app\models\UserGroupsRightsMaterialCategories;
use app\models\RoleUsersInUserGroups;
use app\models\DelegationRightsInUserGroups;

use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\UploadedFile;

//use yii\db\Expression;

use app\models\PositionAndDirection;
use app\models\PositionAndDirectionInUsers;


class UserController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
//        if (Yii::$app->user->isGuest) {
//            throw new HttpException(517 ,'Сессия закончилась. Выполните повторно вход.');
//        }
//        if(Yii::$app->user->identity->role != Users::ROLE_ADMIN)
//        {
//            $this->redirect(['/']);
//        }

        return true;
    }

    public function actions()
    {
        return [
            'uploadPhoto' => [
                'class' => 'budyaga\cropper\actions\UploadAction',
                'url' =>  '/uploads/users',
                'path' => 'uploads/users',
            ]
        ];
    }

    public function actionAllUserGroups($name=null)
    {
        $query = UserGroups::find()
            ->select('user_groups.*, COUNT(role_users_in_user_groups.users_id) AS role_users_in_user_groups_users_id')
            ->innerJoin(RoleUsersInUserGroups::tableName(),'role_users_in_user_groups.user_groups_id = user_groups.id')
            ->groupBy('user_groups.id')
            ->orderBy('id');

        if(!is_null($name) && $name != '')
        {
            $query->andFilterWhere(['like', 'LOWER(name)', mb_strtolower($name)]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort(['attributes' =>
            [
                'name',
                'in_archive',
                'role_users_in_user_groups_users_id'
            ]
        ]);

        return $this->render('allUserGroups', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdateUserGroups($id)
    {
        $model = UserGroups::find()->where(['id'=>$id])->one();
        if(is_null($model))
        {
            throw new HttpException(517 ,'Группа пользователей не найдена');
        }
        $user_groups_rights = UserGroupsRightsMaterialCategories::find()->where(['user_groups_id'=>$model['id']])->all();

        $category_id = [];
        foreach ($user_groups_rights as $value)
        {
            $category_id[] = $value['category_id'];
        }


        $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
        $json = json_encode(MaterialCategories::CreateTree($material_categories,0, $category_id));

        $role_users_in_user_groups_array = [];
        $role_senior_methodist = [];
        $role_methodist = [];
        $users_array_senior_methodist = [];
        $users_array_methodist = [];
        $delegation_rights = [];
        $users_array = [];
        $users_array_system = [];
        $role_users_in_user_groups_result = RoleUsersInUserGroups::find()->where(['user_groups_id'=>$model['id']])->all();
        if(!empty($role_users_in_user_groups_result))
        {
            foreach ($role_users_in_user_groups_result as $role_users_in_user_groups_val)
            {
                $role_users_in_user_groups_array[$role_users_in_user_groups_val['users_id']] = $role_users_in_user_groups_val['user_groups_id'];
            }
        }

        $users = Users::find()->where(['role'=>[Users::ROLE_SENIOR_METHODIST,Users::ROLE_METHODIST],'in_archive'=>false])->all();//,'in_archive'=>false

        if(!empty($users))
        {
            foreach ($users as $users_val)
            {
                $users_array_system[$users_val['id']]['fio'] = $users_val['second_name'].' '.$users_val['first_name'].' '.$users_val['third_name'];
                $users_array_system[$users_val['id']]['role'] = $users_val['role'];

                if($users_val['role'] == Users::ROLE_SENIOR_METHODIST)
                {
                    $users_array_senior_methodist[$users_val['id']] = $users_val['second_name'].' '.$users_val['first_name'].' '.$users_val['third_name'];
                }
                else
                {
                    $users_array_methodist[$users_val['id']] = $users_val['second_name'].' '.$users_val['first_name'].' '.$users_val['third_name'];
                }
                $users_array[$users_val['id']] = $users_val['second_name'].' '.$users_val['first_name'].' '.$users_val['third_name'];
            }
        }


        if(!empty($role_users_in_user_groups_array) && !empty($users_array_system))
        {
            foreach($users_array_system as $users_array_system_key=>$users_array_system_val)
            {
                if(isset($role_users_in_user_groups_array[$users_array_system_key]) && $users_array_system_val['role'] == Users::ROLE_SENIOR_METHODIST && !empty($users_array_senior_methodist))
                {
                    $role_senior_methodist[] = $users_array_system_key;
                }

                if(isset($role_users_in_user_groups_array[$users_array_system_key]) && $users_array_system_val['role'] == Users::ROLE_METHODIST && !empty($users_array_methodist))
                {
                    $role_methodist[] = $users_array_system_key;
                }

            }

            $model['role_senior_methodist'] =   $role_senior_methodist;
            $model['role_methodist'] =   $role_methodist;
        }

        $delegation_rights_in_user_groups = DelegationRightsInUserGroups::find()->where(['user_groups_id'=>$model['id']])->all();

        if(!empty($delegation_rights_in_user_groups))
        {
            foreach($delegation_rights_in_user_groups as $delegation_rights_in_user_groups_val)
            {
                $delegation_rights[] = $delegation_rights_in_user_groups_val['users_id'];
            }

            $model['delegation_rights'] = $delegation_rights;
        }

        $post_user_groups = Yii::$app->getRequest()->post('UserGroups');


        if($post_user_groups)
        {
            $model->setAttributes($post_user_groups);

            if($model->save())
            {
                UserGroupsRightsMaterialCategories::deleteAll(['user_groups_id'=>$model['id']]);

                if(!empty($post_user_groups['category_id']))
                {
                    foreach ($post_user_groups['category_id'] as $post_value)
                    {
                        $user_groups_rights_material_categories = new UserGroupsRightsMaterialCategories();
                        $user_groups_rights_material_categories['user_groups_id'] = $model['id'];
                        $user_groups_rights_material_categories['category_id'] = $post_value;

                        $user_groups_rights_material_categories->save();
                    }
                }

                RoleUsersInUserGroups::deleteAll(['user_groups_id'=>$model['id']]);

                if(!empty($post_user_groups['role_senior_methodist']))
                {
                    foreach ($post_user_groups['role_senior_methodist'] as $role_senior_methodist_value)
                    {
                        $role_users_in_user_groups = new RoleUsersInUserGroups();
                        $role_users_in_user_groups['user_groups_id'] = $model['id'];
                        $role_users_in_user_groups['users_id'] = $role_senior_methodist_value;

                        $role_users_in_user_groups->save();
                    }
                }

                if(!empty($post_user_groups['role_methodist']))
                {
                    foreach ($post_user_groups['role_methodist'] as $role_methodist_value)
                    {
                        $role_users_in_user_groups = new RoleUsersInUserGroups();
                        $role_users_in_user_groups['user_groups_id'] = $model['id'];
                        $role_users_in_user_groups['users_id'] = $role_methodist_value;

                        $role_users_in_user_groups->save();
                    }
                }

                DelegationRightsInUserGroups::deleteAll(['user_groups_id'=>$model['id']]);

                if(!empty($post_user_groups['delegation_rights']))
                {
                    foreach ($post_user_groups['delegation_rights'] as $delegation_rights_value)
                    {
                        $delegation_rights_in_user_groups = new DelegationRightsInUserGroups();
                        $delegation_rights_in_user_groups['user_groups_id'] = $model['id'];
                        $delegation_rights_in_user_groups['users_id'] = $delegation_rights_value;

                        $delegation_rights_in_user_groups->save();
                    }
                }

                return $this->redirect(['/user/all-user-groups']);
            }
        }

        return $this->render('addUserGroups',[
            'model'  => $model,
            'json' => $json,
            'users_array_senior_methodist' => $users_array_senior_methodist,
            'users_array_methodist' => $users_array_methodist,
            'users_array' => $users_array,
        ]);
    }

    public function actionAddUserGroups()
    {
        $model = new UserGroups();

        $material_categories = MaterialCategories::find()->select('id , title, parent')->where(['in_archive' => false])->all();
        $json = json_encode(MaterialCategories::CreateTree($material_categories));

        $users_array_senior_methodist = [];
        $users_array_methodist = [];
        $users_array = [];

        $users = Users::find()->where(['role'=>[Users::ROLE_SENIOR_METHODIST,Users::ROLE_METHODIST],'in_archive'=>false])->all();//,'in_archive'=>false

        if(!empty($users))
        {
            foreach ($users as $users_val)
            {
                if($users_val['role'] == Users::ROLE_SENIOR_METHODIST)
                {
                    $users_array_senior_methodist[$users_val['id']] = $users_val['second_name'].' '.$users_val['first_name'].' '.$users_val['third_name'];
                }
                else
                {
                    $users_array_methodist[$users_val['id']] = $users_val['second_name'].' '.$users_val['first_name'].' '.$users_val['third_name'];
                }
                $users_array[$users_val['id']] = $users_val['second_name'].' '.$users_val['first_name'].' '.$users_val['third_name'];
            }
        }

        $post_user_groups = Yii::$app->getRequest()->post('UserGroups');

        if($post_user_groups)
        {
            $model->setAttributes($post_user_groups);

            if($model->save())
            {
                if(!empty($post_user_groups['category_id']))
                {
                    foreach ($post_user_groups['category_id'] as $post_value)
                    {
                        $user_groups_rights_material_categories = new UserGroupsRightsMaterialCategories();
                        $user_groups_rights_material_categories['user_groups_id'] = $model['id'];
                        $user_groups_rights_material_categories['category_id'] = $post_value;

                        $user_groups_rights_material_categories->save();
                    }
                }

                if(!empty($post_user_groups['role_senior_methodist']))
                {
                    foreach ($post_user_groups['role_senior_methodist'] as $role_senior_methodist_value)
                    {
                        $role_users_in_user_groups = new RoleUsersInUserGroups();
                        $role_users_in_user_groups['user_groups_id'] = $model['id'];
                        $role_users_in_user_groups['users_id'] = $role_senior_methodist_value;

                        $role_users_in_user_groups->save();
                    }
                }

                if(!empty($post_user_groups['role_methodist']))
                {
                    foreach ($post_user_groups['role_methodist'] as $role_methodist_value)
                    {
                        $role_users_in_user_groups = new RoleUsersInUserGroups();
                        $role_users_in_user_groups['user_groups_id'] = $model['id'];
                        $role_users_in_user_groups['users_id'] = $role_methodist_value;

                        $role_users_in_user_groups->save();
                    }
                }

                if(!empty($post_user_groups['delegation_rights']))
                {
                    foreach ($post_user_groups['delegation_rights'] as $delegation_rights_value)
                    {
                        $delegation_rights_in_user_groups = new DelegationRightsInUserGroups();
                        $delegation_rights_in_user_groups['user_groups_id'] = $model['id'];
                        $delegation_rights_in_user_groups['users_id'] = $delegation_rights_value;

                        $delegation_rights_in_user_groups->save();
                    }
                }

                return $this->redirect(['/user/all-user-groups']);
            }
        }

        return $this->render('addUserGroups',[
            'model'  => $model,
            'json' => $json,
            'users_array_senior_methodist' => $users_array_senior_methodist,
            'users_array_methodist' => $users_array_methodist,
            'users_array' => $users_array,
        ]);
    }

    public function actionProfile()
    {
        $model = Users::find()->where(['id'=>Yii::$app->user->identity->getId()])->one();

//        $position_and_direction_array = [];

        $post_user = Yii::$app->getRequest()->post('Users');

        if($post_user)
        {
            if(isset($post_user['scenario']) && $post_user['scenario'] == 'change_password_user')
            {
                $model->setScenario('change_password_user');

                $model['repeat_password'] = $post_user['repeat_password'];
                $model['new_password'] = $post_user['new_password'];

                if(!$model->validate())
                {
                    return $this->render('updateUser',[
                        'model'  => $model,
                        'no_validate' => true
                    ]);
                }
                else
                {
                    $model->setScenario('default');
                    $model['password'] = password_hash($post_user['new_password'],PASSWORD_DEFAULT);
                }


            }
            else
            {
                $model->setScenario('default');
                $model->setAttributes($post_user);

//                $model->path_image_input = UploadedFile::getInstance($model, 'path_image_input');
//
//                if(!is_null($model->path_image_input))
//                {
//                    $image_path = 'uploads/users/'.$model['id'];
//                    if(!file_exists($image_path))
//                    {
//                        mkdir($image_path, 0777);
//                    }
//
//                    $image_path .= '/' .md5(time()).'_'. $model->path_image_input->baseName . '.' . $model->path_image_input->extension;
//                    if($model->path_image_input->saveAs($image_path))
//                    {
//                        $model['image'] = $image_path;
//                    }
//                    else
//                    {
//                        throw new HttpException(517 ,var_export($model->path_image_input->getErrors(),true));
//                    }
//                }
            }

            if($model->save())
            {
//                return $this->redirect([Url::previous()]);
                return $this->redirect(['/user/profile']);
            }
        }

        return $this->render('updateUser',[
            'model'  => $model,
//            'position_and_direction_array'  => $position_and_direction_array,
        ]);
    }

    public function actionAllUsers($user_id=null,$role=null,$not_user=null)
    {
        $query = Users::find()->select(['id','CONCAT(second_name,\' \',first_name,\' \',third_name) AS fio','email','register_date','last_visit_date','role','in_archive']); //->where(['in_archive'=>$archive])

        if(!is_null($user_id) && $user_id != '' && $user_id != 0)
        {
            $query->andwhere(['id'=>$user_id]);
        }

        if(!is_null($not_user) && $not_user == '1')
        {
            $query->andwhere('role != :role',[':role'=>Users::ROLE_USER]);
        }

        if(!is_null($role) && $role != '' && $role != 0)
        {
            $query->andwhere(['role'=>$role]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort(['attributes' =>
            [
                'fio',
                'email',
                'register_date',
                'last_visit_date',
                'role',
                'in_archive'
            ],
            'defaultOrder' => ['in_archive' =>SORT_DESC,'fio' => SORT_ASC],
        ]);

        $users_array[0] = 'Пользователь не выбран ...';
        $users = Users::find()->select(['id','CONCAT(second_name,\' \',first_name,\' \',third_name) AS fio','email'])->all();

        if(!empty($users))
        {
            foreach($users as $users_val)
            {
                $users_array[$users_val['id']] = $users_val['fio'].' ['.$users_val['email'].']';
            }
        }

        $role_array[0] = 'Роль не найдена ...';

        foreach(Users::$roles as $roles_key=>$roles_val)
        {
            $role_array[$roles_key] = $roles_val;
        }


//        Url::remember();
        return $this->render('allUsers', [
            'dataProvider' => $dataProvider,
            'users_array' => $users_array,
            'role_array' => $role_array,
        ]);
    }

    public function actionAddUser()
    {
        $model = new Users();
        $model['guid'] = md5(rand(1,2147483647).' '.rand(1,2147483647).' '.time());

        $model['position_and_direction_in_users'] = [];

        $position_and_direction_array = [];
        $position_and_direction = PositionAndDirection::find()->all();

        if(!empty($position_and_direction))
        {
            foreach($position_and_direction as $position_and_direction_val)
            {
                $position_and_direction_array[$position_and_direction_val['id']] = $position_and_direction_val['name'];
            }
        }

        if(Yii::$app->getRequest()->post())
        {
            $post_user = Yii::$app->getRequest()->post('Users');
            $model->setAttributes($post_user);
            $model['password'] = password_hash($post_user['password'],PASSWORD_DEFAULT);
            $model['register_date'] = date("Y-m-d H:i:s",time());

            if($model->save())
            {
                if(!empty($post_user['position_and_direction_in_users']))
                {
                    foreach ($post_user['position_and_direction_in_users'] as $post_position_and_direction_in_users_val)
                    {
                        $New_position_and_direction = PositionAndDirection::find()->where(['id'=>(int)$post_position_and_direction_in_users_val])->one();

                        if(is_null($New_position_and_direction))
                        {
                            $max = PositionAndDirection::find()->max('position');

                            $New_position_and_direction = new PositionAndDirection();
                            $New_position_and_direction['name'] = $post_position_and_direction_in_users_val;
                            $New_position_and_direction['position'] = $max+1;

                            $New_position_and_direction->save();
                        }

                        $new_position_and_direction_in_users = new PositionAndDirectionInUsers();
                        $new_position_and_direction_in_users['users_id'] = $model['id'];
                        $new_position_and_direction_in_users['position_and_direction_id'] = $New_position_and_direction['id'];
                        $new_position_and_direction_in_users->save();

                    }
                }

                return $this->redirect(['/user/all-users','not_user' => true]);
            }
        }

        return $this->render('addUser',[
            'model'  => $model,
            'position_and_direction_array' => $position_and_direction_array
        ]);
    }

    public function actionUpdateUser($id)
    {
        $model = Users::find()->where(['id'=>$id])->one();

        if(is_null($model))
        {
            throw new HttpException(517 ,'Пользователь не найден');
        }

        $position_and_direction_in_users = PositionAndDirectionInUsers::find()->where(['users_id'=>$model['id']])->all();

        if(!empty($position_and_direction_in_users))
        {
            $position_and_direction_in_users_array = [];
            foreach($position_and_direction_in_users as $position_and_direction_in_users_val)
            {
                $position_and_direction_in_users_array[] = $position_and_direction_in_users_val['position_and_direction_id'];
            }

            $model['position_and_direction_in_users'] = $position_and_direction_in_users_array;

        }

        $position_and_direction_array = [];
        $position_and_direction = PositionAndDirection::find()->all();

        if(!empty($position_and_direction))
        {
            foreach($position_and_direction as $position_and_direction_val)
            {
                $position_and_direction_array[$position_and_direction_val['id']] = $position_and_direction_val['name'];
            }
        }

        $users_array = [];

        $users = Users::find()->select(['id','CONCAT(second_name,\' \',first_name,\' \',third_name) AS fio','email'])
            ->where(['in_archive'=>false])
            ->all();

        if(!empty($users))
        {
            foreach($users as $users_val)
            {
                $users_array[$users_val['id']] = $users_val['fio'].' ['.$users_val['email'].']';
            }
        }

        $post_user = Yii::$app->getRequest()->post('Users');

        if($post_user)
        {

            if(isset($post_user['scenario']) && $post_user['scenario'] == 'change_password_user')
            {
                $model->setScenario('change_password_user');

                $model['repeat_password'] = $post_user['repeat_password'];
                $model['new_password'] = $post_user['new_password'];

                if(!$model->validate())
                {
                    return $this->render('updateUser',[
                        'model'  => $model,
                        'no_validate' => true,
                        'users_array' => $users_array
                    ]);
                }
                else
                {

                    $model->setScenario('default');
                    $model['password'] = password_hash($post_user['new_password'],PASSWORD_DEFAULT);
                }


            }
            else if(isset($post_user['scenario']) && $post_user['scenario'] == 'translation_user_materials')
            {
                Materials::updateAll(['created_by'=>$post_user['created_by']],['created_by'=>$model['id']]);

                return $this->render('updateUser',[
                    'model'  => $model,
                    'no_validate_translation_user' => true,
                    'users_array' => $users_array,
                    'result' => 'Материалы успешно переданы другому пользователю'
                ]);
//                $users_array
            }
            else
            {
                $model->setScenario('default');
                $model->setAttributes($post_user);

                if(!$model->validate())
                {
                    return $this->render('updateUser',[
                        'model'  => $model,
                        'no_validate' => false,
                        'users_array' => $users_array
                    ]);
                }
                else
                {
                    if($model['in_archive'] == true)
                    {
                        RoleUsersInUserGroups::deleteAll(['users_id'=>$model['id']]);
                    }

                    PositionAndDirectionInUsers::deleteAll(['users_id'=>$model['id']]);

                    if(!empty($post_user['position_and_direction_in_users']))
                    {
                        foreach ($post_user['position_and_direction_in_users'] as $post_position_and_direction_in_users_val)
                        {
                            $New_position_and_direction = PositionAndDirection::find()->where(['id'=>(int)$post_position_and_direction_in_users_val])->one();

                            if(is_null($New_position_and_direction))
                            {
                                $max = PositionAndDirection::find()->max('position');

                                $New_position_and_direction = new PositionAndDirection();
                                $New_position_and_direction['name'] = $post_position_and_direction_in_users_val;
                                $New_position_and_direction['position'] = $max+1;

                                $New_position_and_direction->save();
                            }

                            $new_position_and_direction_in_users = new PositionAndDirectionInUsers();
                            $new_position_and_direction_in_users['users_id'] = $model['id'];
                            $new_position_and_direction_in_users['position_and_direction_id'] = $New_position_and_direction['id'];
                            $new_position_and_direction_in_users->save();

                        }
                    }
                }
            }

            if($model->save())
            {
//                return $this->redirect([Url::previous()]);
                return $this->redirect(['/user/all-users','not_user' => true]);
            }
        }

        return $this->render('updateUser',[
            'model'  => $model,
            'position_and_direction_array'  => $position_and_direction_array,
            'users_array'  => $users_array,
        ]);
    }

}
