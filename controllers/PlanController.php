<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\DelegationRightsInUserGroups;
use app\models\Notifications;
use app\models\RoleUsersInUserGroups;
use app\models\User;
use app\models\UserGroups;
use app\models\WorkPlan;
use app\models\WorkPlanDate;
use app\models\WorkPlanNote;
use app\models\WorkPlanPeriod;

use Yii;
use app\models\Users;

use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\UploadedFile;
//use yii\db\Expression;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PlanController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            throw new HttpException(517 ,'Сессия закончилась. Выполните повторно вход.');
        }
        if(Yii::$app->user->identity->role != Users::ROLE_ADMIN &&
            Yii::$app->user->identity->role != Users::ROLE_MODERATOR &&
            Yii::$app->user->identity->role != Users::ROLE_SENIOR_METHODIST)
        {
            $this->redirect(['/']);
        }

        return true;
    }

    public function actionAddEvent()
    {
        $work_plans = new WorkPlan();
        $work_plan_date[0] = new WorkPlanDate();
        $work_plan_note[0] = new WorkPlanNote();

        $user_groups_array = [];


        if(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST)
        {
            $role_users_in_user_groups = RoleUsersInUserGroups::find()
                ->select('user_groups_id, user_groups.name AS user_groups_name')
                ->innerJoin(UserGroups::tableName(),'user_groups.id = role_users_in_user_groups.user_groups_id')
                ->where(['users_id'=>Yii::$app->user->identity->id,'user_groups.in_archive'=>false])->all();

            if(!empty($role_users_in_user_groups))
            {
                foreach ($role_users_in_user_groups as $role_users_in_user_groups_val)
                {
                    $user_groups_array[$role_users_in_user_groups_val['user_groups_id']] = $role_users_in_user_groups_val['user_groups_name'];
                }
            }

            $delegation_rights_in_user_groups = DelegationRightsInUserGroups::find()
                ->select('user_groups_id, user_groups.name AS user_groups_name')
                ->innerJoin(UserGroups::tableName(),'user_groups.id = delegation_rights_in_user_groups.user_groups_id')
                ->where(['users_id'=>Yii::$app->user->identity->id,'user_groups.in_archive'=>false])->all();

            if(!empty($delegation_rights_in_user_groups))
            {
                foreach ($delegation_rights_in_user_groups as $delegation_rights_in_user_groups_val)
                {
                    if(!isset($user_groups_array[$delegation_rights_in_user_groups_val['user_groups_id']]))
                    {
                        $user_groups_array[$delegation_rights_in_user_groups_val['user_groups_id']] = $delegation_rights_in_user_groups_val['user_groups_name'];
                    }
                }
            }
        }
        else if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN)
        {
            $user_groups = UserGroups::find()->where(['in_archive'=>false])->all();
            if(!empty($user_groups))
            {
                foreach ($user_groups as $user_groups_val)
                {
                    $user_groups_array[$user_groups_val['id']] = $user_groups_val['name'];
                }
            }
        }

        $post = Yii::$app->getRequest()->post();

        if(!empty($post['WorkPlan']))
        {
            $model = WorkPlanPeriod::find()->where(['month'=>$post['WorkPlan']['month'],'year'=>$post['WorkPlan']['year']])->one();
            if(is_null($model))
            {
                $model = new WorkPlanPeriod();
                $model['month'] = $post['WorkPlan']['month'];
                $model['year'] = $post['WorkPlan']['year'];
                $model->save();
            }

            $work_plans = new WorkPlan();
            $work_plans->setAttributes($post['WorkPlan']);
            $work_plans['work_plan_period_id'] = $model['id'];
            $work_plans['users_id'] = Yii::$app->user->identity->id;

            if(isset($post['in-archive-event']))
            {
                $work_plans['in_archive'] = true;
            }

            if($work_plans->save())
            {
                if(!empty($post['WorkPlanDate']))
                {
                    foreach($post['WorkPlanDate'] as $WorkPlanDateKey=>$WorkPlanDateValue)
                    {
                        $work_plan_date = new WorkPlanDate();
                        $work_plan_date['start_date'] = date("Y-m-d",strtotime($WorkPlanDateValue['start_date']));
                        $work_plan_date['end_date'] = date("Y-m-d",strtotime($WorkPlanDateValue['end_date']));
                        $work_plan_date['work_plan_id'] = $work_plans['id'];

                        if(!$work_plan_date->save())
                        {
                            throw new HttpException(517 ,var_export($work_plan_date->getErrors(),true));
                        }
                    }
                }

                if(!empty($post['WorkPlanNote']))
                {
                    foreach($post['WorkPlanNote'] as $WorkPlanNoteKey=>$WorkPlanNoteValue)
                    {
                        $work_plan_note = new WorkPlanNote();
                        $work_plan_note['note_name'] = $WorkPlanNoteValue['note_name'];
                        $work_plan_note['note_url'] = $WorkPlanNoteValue['note_url'];
                        $work_plan_note['work_plan_id'] = $work_plans['id'];

                        if(!$work_plan_note->save())
                        {
                            throw new HttpException(517 ,var_export($work_plan_note->getErrors(),true));
                        }
                    }
                }

                $text_email_add = '';
                $text_email_delete = '';

                if($work_plans['in_archive'] == false)
                {
                    $text_email_add = $work_plans['event_name'].'<br><br>';
                }
                else
                {
                    $text_email_delete = $work_plans['event_name'].'<br><br>';
                }


                $recipients = Users::find()->where(['in_archive'=>false,'not_send_email'=>false,'role'=>[Users::ROLE_MODERATOR,Users::ROLE_ADMIN]])->all();

                if(!empty($recipients))
                {
                    $ContactForm =  new ContactForm();

                    $user_groups = UserGroups::find()->where(['id'=>$work_plans['user_groups_id']])->one();

                    $param['<%user%>'] = Yii::$app->user->identity->second_name.' '.Yii::$app->user->identity->first_name.' '.Yii::$app->user->identity->third_name;
                    $param['<%month%>'] = WorkPlanPeriod::$month[$model['month']];
                    $param['<%year%>'] = $model['year'];
                    $param['<%direction%>'] = $user_groups['name'];
                    $param['<%added_events%>'] = $text_email_add;
                    $param['<%remote_events%>'] = $text_email_delete;
                    $param['<%edited_events%>'] = '';

                    foreach ($recipients as $recipients_val)
                    {
                        $ContactForm->SendMail($recipients_val['email'],Notifications::WORK_PLAN_HAS_CHANGED,$param);
                    }
                }
                return $this->redirect(['plan/update-work-plan','id'=>$model['id']]);
            }
            else
            {
                throw new HttpException(517 ,var_export($work_plans->getErrors(),true));
            }
        }

        return $this->render('addEvent', [
            'work_plans' => $work_plans,
            'work_plan_date' => $work_plan_date,
            'work_plan_note' => $work_plan_note,
            'user_groups_array' => $user_groups_array,
        ]);
    }

    public function actionUpdateEvent($id)
    {
        $work_plans = WorkPlan::find()->where(['id'=>$id,'in_archive'=>false])->one();
        if(is_null($work_plans))
        {
            throw new HttpException(517 ,'Событие не найдено');
        }

        $work_plan_date = WorkPlanDate::find()->where(['work_plan_id'=>$work_plans['id']])->all();

        if(!empty($work_plan_date))
        {
            foreach ($work_plan_date as &$work_plan_date_val)
            {
                $work_plan_date_val['start_date'] = date("d.m.Y", strtotime($work_plan_date_val['start_date']));
                $work_plan_date_val['end_date'] = date("d.m.Y", strtotime($work_plan_date_val['end_date']));
            }
        }
        else
        {
            $work_plan_date[0] = new WorkPlanDate();
        }

        $work_plan_note = WorkPlanNote::find()->where(['work_plan_id'=>$work_plans['id']])->all();

        if(empty($work_plan_note))
        {
            $work_plan_note[0] = new WorkPlanNote();
        }

        $user_groups_array = [];

        if(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST)
        {
            $role_users_in_user_groups = RoleUsersInUserGroups::find()
                ->select('user_groups_id, user_groups.name AS user_groups_name')
                ->innerJoin(UserGroups::tableName(),'user_groups.id = role_users_in_user_groups.user_groups_id')
                ->where(['users_id'=>Yii::$app->user->identity->id,'user_groups.in_archive'=>false])->all();

            if(!empty($role_users_in_user_groups))
            {
                foreach ($role_users_in_user_groups as $role_users_in_user_groups_val)
                {
                    $user_groups_array[$role_users_in_user_groups_val['user_groups_id']] = $role_users_in_user_groups_val['user_groups_name'];
                }
            }

            $delegation_rights_in_user_groups = DelegationRightsInUserGroups::find()
                ->select('user_groups_id, user_groups.name AS user_groups_name')
                ->innerJoin(UserGroups::tableName(),'user_groups.id = delegation_rights_in_user_groups.user_groups_id')
                ->where(['users_id'=>Yii::$app->user->identity->id,'user_groups.in_archive'=>false])->all();

            if(!empty($delegation_rights_in_user_groups))
            {
                foreach ($delegation_rights_in_user_groups as $delegation_rights_in_user_groups_val)
                {
                    if(!isset($user_groups_array[$delegation_rights_in_user_groups_val['user_groups_id']]))
                    {
                        $user_groups_array[$delegation_rights_in_user_groups_val['user_groups_id']] = $delegation_rights_in_user_groups_val['user_groups_name'];
                    }
                }
            }
        }
        else if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN)
        {
            $user_groups = UserGroups::find()->where(['in_archive'=>false])->all();
            if(!empty($user_groups))
            {
                foreach ($user_groups as $user_groups_val)
                {
                    $user_groups_array[$user_groups_val['id']] = $user_groups_val['name'];
                }
            }
        }


        $work_plan_period = WorkPlanPeriod::find()->where(['id'=>$work_plans['work_plan_period_id']])->one();

        $work_plans['month'] = $work_plan_period['month'];
        $work_plans['year'] = $work_plan_period['year'];

        $post = Yii::$app->getRequest()->post();

        if(!empty($post['WorkPlan']))
        {

            $model = WorkPlanPeriod::find()->where(['month'=>$post['WorkPlan']['month'],'year'=>$post['WorkPlan']['year']])->one();
            if(is_null($model))
            {
                $model = new WorkPlanPeriod();
                $model['month'] = $post['WorkPlan']['month'];
                $model['year'] = $post['WorkPlan']['year'];
                $model->save();
            }

//            $work_plans = new WorkPlan();
            $work_plans->setAttributes($post['WorkPlan']);
            $work_plans['work_plan_period_id'] = $model['id'];

            if(isset($post['in-archive-event']))
            {
                $work_plans['in_archive'] = true;
            }
//            var_dump();
//            $work_plans['users_id'] = Yii::$app->user->identity->id;

            if($work_plans->save())
            {
                WorkPlanDate::deleteAll(['work_plan_id'=>$work_plans['id']]);

                if(!empty($post['WorkPlanDate']))
                {
                    foreach($post['WorkPlanDate'] as $WorkPlanDateKey=>$WorkPlanDateValue)
                    {

                        $work_plan_date = new WorkPlanDate();
                        $work_plan_date['start_date'] = date("Y-m-d",strtotime($WorkPlanDateValue['start_date']));
                        $work_plan_date['end_date'] = date("Y-m-d",strtotime($WorkPlanDateValue['end_date']));
                        $work_plan_date['work_plan_id'] = $work_plans['id'];

                        if(!$work_plan_date->save())
                        {
                            throw new HttpException(517 ,var_export($work_plan_date->getErrors(),true));
                        }
                    }
                }

                WorkPlanNote::deleteAll(['work_plan_id'=>$work_plans['id']]);
                if(!empty($post['WorkPlanNote']))
                {
                    foreach($post['WorkPlanNote'] as $WorkPlanNoteKey=>$WorkPlanNoteValue)
                    {
                        $work_plan_note = new WorkPlanNote();
                        $work_plan_note['note_name'] = $WorkPlanNoteValue['note_name'];
                        $work_plan_note['note_url'] = $WorkPlanNoteValue['note_url'];
                        $work_plan_note['work_plan_id'] = $work_plans['id'];

                        if(!$work_plan_note->save())
                        {
                            throw new HttpException(517 ,var_export($work_plan_note->getErrors(),true));
                        }
                    }
                }

                $text_email_edited = '';
                $text_email_delete = '';

                if($work_plans['in_archive'] == false)
                {
                    $text_email_edited = $work_plans['event_name'].'<br><br>';
                }
                else
                {
                    $text_email_delete = $work_plans['event_name'].'<br><br>';
                }


                $recipients = Users::find()->where(['in_archive'=>false,'not_send_email'=>false,'role'=>[Users::ROLE_MODERATOR,Users::ROLE_ADMIN]])->all();

                if(!empty($recipients))
                {
                    $ContactForm =  new ContactForm();

                    $user_groups = UserGroups::find()->where(['id'=>$work_plans['user_groups_id']])->one();

                    $param['<%user%>'] = Yii::$app->user->identity->second_name.' '.Yii::$app->user->identity->first_name.' '.Yii::$app->user->identity->third_name;
                    $param['<%month%>'] = WorkPlanPeriod::$month[$model['month']];
                    $param['<%year%>'] = $model['year'];
                    $param['<%direction%>'] = $user_groups['name'];
                    $param['<%added_events%>'] = '';
                    $param['<%remote_events%>'] = $text_email_delete;
                    $param['<%edited_events%>'] = $text_email_edited;

                    foreach ($recipients as $recipients_val)
                    {
                        $ContactForm->SendMail($recipients_val['email'],Notifications::WORK_PLAN_HAS_CHANGED,$param);
                    }
                }


                return $this->redirect(['plan/update-work-plan','id'=>$model['id']]);
            }
            else
            {
                throw new HttpException(517 ,var_export($work_plans->getErrors(),true));
            }
        }

        return $this->render('addEvent', [
            'work_plans' => $work_plans,
            'work_plan_date' => $work_plan_date,
            'work_plan_note' => $work_plan_note,
            'user_groups_array' => $user_groups_array,
        ]);
    }

    public function actionUpdateWorkPlan($id)
    {
        $model = WorkPlanPeriod::find()->where(['id'=>$id])->one();
        if(is_null($model))
        {
            throw new HttpException(517 ,'Период не найден');
        }

        $user_groups_array[0] = 'Направление не выбрано...';


        $query = WorkPlan::find()
            ->select(['work_plan.*','user_groups.name as user_groups_name','CONCAT(users.second_name,\' \',users.first_name,\' \',users.third_name) AS fio'])
            ->innerJoin(UserGroups::tableName(),'user_groups.id = work_plan.user_groups_id')
            ->innerJoin(Users::tableName(),'work_plan.users_id = users.id')
            ->where(['work_plan_period_id'=>$model['id'],'work_plan.in_archive'=>false,'user_groups.in_archive'=>false]);

        $get = Yii::$app->getRequest()->get();

        if(isset($get['user_groups_id']) && $get['user_groups_id'] != 0)
        {
            $query->andWhere(['user_groups.id'=>$get['user_groups_id']]);
        }

        if(isset($get['not_included_main_report']) && $get['not_included_main_report'] != 0)
        {
            $query->andWhere(['work_plan.not_included_main_report'=>$get['not_included_main_report']]);
        }


        if(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST)
        {
            $role_users_in_user_groups = RoleUsersInUserGroups::find()
                ->select('user_groups_id, user_groups.name AS user_groups_name')
                ->innerJoin(UserGroups::tableName(),'user_groups.id = role_users_in_user_groups.user_groups_id')
                ->where(['users_id'=>Yii::$app->user->identity->id,'user_groups.in_archive'=>false])->all();

            if(!empty($role_users_in_user_groups))
            {
                foreach ($role_users_in_user_groups as $role_users_in_user_groups_val)
                {
                    $user_groups_array[$role_users_in_user_groups_val['user_groups_id']] = $role_users_in_user_groups_val['user_groups_name'];
                }
            }

            $delegation_rights_in_user_groups = DelegationRightsInUserGroups::find()
                ->select('user_groups_id, user_groups.name AS user_groups_name')
                ->innerJoin(UserGroups::tableName(),'user_groups.id = delegation_rights_in_user_groups.user_groups_id')
                ->where(['users_id'=>Yii::$app->user->identity->id,'user_groups.in_archive'=>false])->all();

            if(!empty($delegation_rights_in_user_groups))
            {
                foreach ($delegation_rights_in_user_groups as $delegation_rights_in_user_groups_val)
                {
                    if(!isset($user_groups_array[$delegation_rights_in_user_groups_val['user_groups_id']]))
                    {
                        $user_groups_array[$delegation_rights_in_user_groups_val['user_groups_id']] = $delegation_rights_in_user_groups_val['user_groups_name'];
                    }
                }
            }

            $query->andWhere(['users_id'=>Yii::$app->user->identity->id]);
        }
        else if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN)
        {
            $user_groups = UserGroups::find()->where(['in_archive'=>false])->all();
            if(!empty($user_groups))
            {
                foreach ($user_groups as $user_groups_val)
                {
                    $user_groups_array[$user_groups_val['id']] = $user_groups_val['name'];
                }
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort(['attributes' =>
            [
                'type_event',
                'for_whom',
                'user_groups_name',
                'event_name',
                'responsible',
                'fio',
                'id',
            ],
            'defaultOrder' => [ 'id' => SORT_DESC],
        ]);


        $list_winners_array = [];

        $text_email_add = '';
        $text_email_delete = '';


        $post = Yii::$app->getRequest()->post();
        if(isset($post['WorkPlanPeriod']['upload-report-file']))
        {

            $model->setAttributes($post['WorkPlanPeriod']);

            $UploadedFile = UploadedFile::getInstance($model, 'work_plan_input');

            if(!is_null($UploadedFile))
            {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($UploadedFile->tempName);
                foreach ($spreadsheet->getActiveSheet()->getRowIterator() as $row)
                {
                    if ($spreadsheet->getActiveSheet()
                            ->getRowDimension($row->getRowIndex())->getVisible() && $row->getRowIndex() != 1)
                    {
                        //Тип мероприятия
                        $type_event = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'A'.$row->getRowIndex()
                            )
                            ->getValue();

                        //Для кого
                        $for_whom = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'B'.$row->getRowIndex()
                            )
                            ->getValue();

//                            //Предмет/направление
//                            $subject = $spreadsheet->getActiveSheet()
//                                ->getCell(
//                                    'C'.$row->getRowIndex()
//                                )
//                                ->getValue();

                        //Дата
                        $date = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'C'.$row->getRowIndex()
                            )
                            ->getFormattedValue();

                        $array_date = [];

                        if(strripos($date,'–'))
                        {
                            $array_date[0] = ['start_date' => explode('–',$date)[0], 'end_date' => explode('–',$date)[1]] ;
                        }
                        else if(strripos($date,'-'))
                        {
                            $array_date[0] = ['start_date' => explode('-',$date)[0], 'end_date' => explode('-',$date)[1]];
                        }
                        else if(strripos($date,';'))
                        {
                            $array_date_explode = explode(';',$date);

                            foreach($array_date_explode as $value)
                            {
                                $array_date[] = ['start_date' => $value, 'end_date' => $value];
                            }
                        }
                        else
                        {
                            $array_date[0] = ['start_date' => date("d.m.Y",strtotime(trim($date))), 'end_date' => date("d.m.Y",strtotime(trim($date)))];
                        }


                        //Время
                        $event_time = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'D'.$row->getRowIndex()
                            )
                            ->getFormattedValue();

                        //Мероприятие
                        $event_name = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'E'.$row->getRowIndex()
                            )->getValue();

                        //Округ
                        $district = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'F'.$row->getRowIndex()
                            )->getValue();

                        //Место проведения
                        $location = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'G'.$row->getRowIndex()
                            )->getValue();

                        //Ответственный
                        $responsible = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'H'.$row->getRowIndex()
                            )->getValue();

                        //Описание
                        $description = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'I'.$row->getRowIndex()
                            )->getValue();

                        //Название примечание (1 ссылка)
                        $note_l[0] = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'J'.$row->getRowIndex()
                            )->getValue();

                        //Название примечание (1 ссылка)
                        $link[0] = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'J'.$row->getRowIndex()
                            )->getHyperlink();

                        //Название примечание (2 ссылка)
                        $note_l[1] = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'K'.$row->getRowIndex()
                            )->getValue();

                        //Название примечание (2 ссылка)
                        $link[1] = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'K'.$row->getRowIndex()
                            )->getHyperlink();

                        //Название примечание (3 ссылка)
                        $note_l[2] = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'L'.$row->getRowIndex()
                            )->getValue();

                        //Название примечание (3 ссылка)
                        $link[2] = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'L'.$row->getRowIndex()
                            )->getHyperlink();

                        //Название примечание (4 ссылка)
                        $note_l[3] = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'M'.$row->getRowIndex()
                            )->getValue();

                        //Название примечание (4 ссылка)
                        $link[3] = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'M'.$row->getRowIndex()
                            )->getHyperlink();

                        //Название примечание (5 ссылка)
                        $note_l[4] = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'N'.$row->getRowIndex()
                            )->getValue();

                        //Название примечание (5 ссылка)
                        $link[4] = $spreadsheet->getActiveSheet()
                            ->getCell(
                                'N'.$row->getRowIndex()
                            )->getHyperlink();

                        $note = [];
                        foreach($link as $key => $value)
                        {
                            if($value->getURL() != '')
                            {
                                $note[] = ['note_url'=>$value->getURL(),'note_name'=>$note_l[$key]];
                            }
                        }

                        if($type_event == '')
                        {
                            continue;
                        }

                        $list_winners_array[] = [
                            'type_event' => trim($type_event),
                            'for_whom' => trim($for_whom),
                            'user_groups_id' => $post['WorkPlanPeriod']['user_groups_id'],
                            'description' => trim($description),
                            'work_plan_period_id'=>$model['id'],
                            'event_date' => $array_date,
                            'event_time' => $event_time,
                            'event_name' => $event_name,
                            'district' => $district,
                            'location' => $location,
                            'responsible' => $responsible,
                            'event_note' => $note,
                            'users_id' => Yii::$app->user->identity->id,
                            'in_archive' => false,
                        ];

                    }
                }

//                    die;
                if(!empty($list_winners_array))
                {
                    $my_work_plan_array=[];
                    $my_work_plan = WorkPlan::find()
                        ->where(['users_id'=>Yii::$app->user->identity->id,'user_groups_id' => $post['WorkPlanPeriod']['user_groups_id'],
                            'work_plan_period_id'=>$model['id'],'in_archive'=>false])->all();

                    if(!empty($my_work_plan))
                    {
                        foreach ($my_work_plan as $my_work_plan_val)
                        {
                            $my_work_plan_array[] = $my_work_plan_val['id'];
                            $text_email_delete .= $my_work_plan_val['event_name'].'<br><br>';
                        }
                    }

                    if(!empty($my_work_plan_array))
                    {
                        WorkPlan::updateAll(['in_archive'=>true],['id'=>$my_work_plan_array]);
                    }


                    foreach ($list_winners_array as $key=>$val)
                    {
                        $work_plans = new WorkPlan();
                        $work_plans->setAttributes($val);

                        if($work_plans->save())
                        {
                            foreach($val['event_date'] as $key_event_date => $value)
                            {
                                if($value['start_date'] != '')
                                {
                                    $work_plan_date =  new WorkPlanDate();
                                    $work_plan_date['start_date'] = date("Y-m-d",strtotime($value['start_date']));
                                    $work_plan_date['end_date'] = date("Y-m-d",strtotime($value['end_date']));
                                    $work_plan_date['work_plan_id'] = $work_plans['id'];

                                    if(!$work_plan_date->save())
                                    {
                                        throw new HttpException(517 ,var_export($work_plan_date->getErrors(),true));
                                    }
                                }
                            }

                            if(!empty($val['event_note']))
                            {
                                foreach($val['event_note'] as $key_event_note => $event_note_value)
                                {
                                    $work_plan_note =  new WorkPlanNote();
                                    $work_plan_note->setAttributes($event_note_value);
                                    $work_plan_note['work_plan_id'] = $work_plans['id'];

                                    if(!$work_plan_note->save())
                                    {
                                        throw new HttpException(517 ,var_export($work_plan_note->getErrors(),true));
                                    }
                                }
                            }

                            $text_email_add .= $work_plans['event_name'].'<br><br>';
                        }
                        else
                        {
                            throw new HttpException(517 ,var_export($work_plans->getErrors(),true));
                        }
                    }

                    $recipients = Users::find()->where(['in_archive'=>false,'not_send_email'=>false,'role'=>[Users::ROLE_MODERATOR,Users::ROLE_ADMIN]])->all();

                    if(!empty($recipients))
                    {
                        $ContactForm =  new ContactForm();

                        $user_groups = UserGroups::find()->where(['id'=>$post['WorkPlanPeriod']['user_groups_id']])->one();

                        $param['<%user%>'] = Yii::$app->user->identity->second_name.' '.Yii::$app->user->identity->first_name.' '.Yii::$app->user->identity->third_name;
                        $param['<%month%>'] = WorkPlanPeriod::$month[$model['month']];
                        $param['<%year%>'] = $model['year'];
                        $param['<%direction%>'] = $user_groups['name'];
                        $param['<%added_events%>'] = $text_email_add;
                        $param['<%remote_events%>'] = $text_email_delete;
                        $param['<%edited_events%>'] = '';

                        foreach ($recipients as $recipients_val)
                        {
                            $ContactForm->SendMail($recipients_val['email'],Notifications::WORK_PLAN_HAS_CHANGED,$param);
                        }

                    }

                    return $this->redirect(['plan/update-work-plan','id'=>$model['id']]);
                }
            }
        }
        else if(isset($post['WorkPlanPeriod']['filter-work-plan']))
        {
            return $this->redirect(['plan/update-work-plan','id'=>$model['id'],'user_groups_id'=>$post['WorkPlanPeriod']['user_groups_id'],'not_included_main_report'=>$post['WorkPlanPeriod']['not_included_main_report']]);
        }
        else if(isset($post['WorkPlanPeriod']['export-to-excel']))
        {

            $spreadsheet = new Spreadsheet();

            $work_plan_report = WorkPlan::find()
                ->select(['work_plan.*','user_groups.name as user_groups_name','CONCAT(users.second_name,\' \',users.first_name,\' \',users.third_name) AS fio'])
                ->innerJoin(UserGroups::tableName(),'user_groups.id = work_plan.user_groups_id')
                ->innerJoin(Users::tableName(),'work_plan.users_id = users.id')
                ->where(['work_plan_period_id'=>$model['id'],'work_plan.user_groups_id'=>$post['WorkPlanPeriod']['user_groups_id'],'work_plan.in_archive'=>false,'user_groups.in_archive'=>false]);

            if(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST)
            {
                $work_plan_report->andWhere(['users_id'=>Yii::$app->user->identity->id]);
            }

            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                1,
                1,
                'Тип мероприятия');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                2,
                1,
                'Для кого');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                3,
                1,
                'Дата');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                4,
                1,
                'Время');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                5,
                1,
                'Мероприятие');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                6,
                1,
                'Округ');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                7,
                1,
                'Место проведения');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                8,
                1,
                'Ответственный');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                9,
                1,
                'Описание');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                10,
                1,
                'Название примечание(1 ссылка)');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                11,
                1,
                'Название примечание(2 ссылка)');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                12,
                1,
                'Название примечание(3 ссылка)');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                13,
                1,
                'Название примечание(4 ссылка)');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                14,
                1,
                'Название примечание(5 ссылка)');

            $i = 2;
//            $j = 1;

            $work_plan_report = $work_plan_report->all();

            if(!empty($work_plan_report))
            {
                foreach ($work_plan_report as $work_plan_report_val)
                {
                    $j = 9;

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                        1,
                        $i,
                        $work_plan_report_val['type_event']);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                        2,
                        $i,
                        $work_plan_report_val['for_whom']);

                    $work_plan_date_report = WorkPlanDate::find()->where(['work_plan_id'=>$work_plan_report_val['id']])->all();
                    $work_plan_date_report_string = '';
                    if(!empty($work_plan_date_report))
                    {
                        foreach ($work_plan_date_report as $work_plan_date_report_val)
                        {
                            if($work_plan_date_report_val['start_date'] == $work_plan_date_report_val['end_date'])
                            {
                                $work_plan_date_report_string .= date("d.m.Y",strtotime(trim($work_plan_date_report_val['start_date']))).'; ';
                            }
                            else
                            {
                                $work_plan_date_report_string .= date("d.m.Y",strtotime(trim($work_plan_date_report_val['start_date']))).' - '.date("d.m.Y",strtotime(trim($work_plan_date_report_val['end_date']))).'; ';
                            }
                        }

                        $work_plan_date_report_string = substr($work_plan_date_report_string, 0, -2);
                    }

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                        3,
                        $i,
                        $work_plan_date_report_string);


                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                        4,
                        $i,
                        $work_plan_report_val['event_time']);
                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                        5,
                        $i,
                        $work_plan_report_val['event_name']);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                        6,
                        $i,
                        $work_plan_report_val['district']);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                        7,
                        $i,
                        $work_plan_report_val['location']);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                        8,
                        $i,
                        $work_plan_report_val['responsible']);

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                        9,
                        $i,
                        $work_plan_report_val['description']);



                    $work_plan_note_report = WorkPlanNote::find()->where(['work_plan_id'=>$work_plan_report_val['id']])->all();

                    if(!empty($work_plan_note_report))
                    {

                        foreach ($work_plan_note_report as $work_plan_note_report_key=>$work_plan_note_report_val)
                        {
                            $j++;

                            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(
                                $j,
                                $i,
                                $work_plan_note_report_val['note_name']);

                            $spreadsheet->getActiveSheet()->getCellByColumnAndRow($j,$i)->getHyperlink()->setUrl($work_plan_note_report_val['note_url']);
                        }
                    }
                    $i++;
                }
                            $spreadsheet->getActiveSheet()->getStyle("J2:N$i")
                                ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
            }


            $spreadsheet->getActiveSheet()->setTitle('План работ');

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="work_plan.xls"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            die;

//            return $this->redirect(['plan/update-work-plan','id'=>$model['id'],'user_groups_id'=>$post['WorkPlanPeriod']['user_groups_id']]);
        }


        return $this->render('AllActivitiesWorkPlan', [
            'dataProvider' => $dataProvider,
            'user_groups_array' => $user_groups_array,
            'model' => $model
        ]);
    }

    public function actionAddWorkPlan()
    {
        $model = new WorkPlanPeriod();

        $user_groups_array = [];


        if(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST)
        {
            $role_users_in_user_groups = RoleUsersInUserGroups::find()
                ->select('user_groups_id, user_groups.name AS user_groups_name')
                ->innerJoin(UserGroups::tableName(),'user_groups.id = role_users_in_user_groups.user_groups_id')
                ->where(['users_id'=>Yii::$app->user->identity->id,'user_groups.in_archive'=>false])->all();

            if(!empty($role_users_in_user_groups))
            {
                foreach ($role_users_in_user_groups as $role_users_in_user_groups_val)
                {
                    $user_groups_array[$role_users_in_user_groups_val['user_groups_id']] = $role_users_in_user_groups_val['user_groups_name'];
                }
            }

            $delegation_rights_in_user_groups = DelegationRightsInUserGroups::find()
                ->select('user_groups_id, user_groups.name AS user_groups_name')
                ->innerJoin(UserGroups::tableName(),'user_groups.id = delegation_rights_in_user_groups.user_groups_id')
                ->where(['users_id'=>Yii::$app->user->identity->id,'user_groups.in_archive'=>false])->all();

            if(!empty($delegation_rights_in_user_groups))
            {
                foreach ($delegation_rights_in_user_groups as $delegation_rights_in_user_groups_val)
                {
                    if(!isset($user_groups_array[$delegation_rights_in_user_groups_val['user_groups_id']]))
                    {
                        $user_groups_array[$delegation_rights_in_user_groups_val['user_groups_id']] = $delegation_rights_in_user_groups_val['user_groups_name'];
                    }
                }
            }
        }
        else if(Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN)
        {
            $user_groups = UserGroups::find()->where(['in_archive'=>false])->all();
            if(!empty($user_groups))
            {
                foreach ($user_groups as $user_groups_val)
                {
                    $user_groups_array[$user_groups_val['id']] = $user_groups_val['name'];
                }
            }
        }


        $list_winners_array = [];

        $text_email_add = '';
        $text_email_delete = '';

        $post = Yii::$app->getRequest()->post();

        if(isset($post['WorkPlanPeriod']['upload-report-file']))
        {
            $model_old = WorkPlanPeriod::find()->where(['month'=>$post['WorkPlanPeriod']['month'],'year'=>$post['WorkPlanPeriod']['year']])->one();
            if(!is_null($model_old))
            {
                $model = $model_old;
            }

            $model->setAttributes($post['WorkPlanPeriod']);

            if($model->save())
            {
                $UploadedFile = UploadedFile::getInstance($model, 'work_plan_input');

                if(!is_null($UploadedFile))
                {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($UploadedFile->tempName);
                    foreach ($spreadsheet->getActiveSheet()->getRowIterator() as $row)
                    {
                        if ($spreadsheet->getActiveSheet()
                                ->getRowDimension($row->getRowIndex())->getVisible() && $row->getRowIndex() != 1)
                        {
                            //Тип мероприятия
                            $type_event = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'A'.$row->getRowIndex()
                                )
                                ->getValue();

                            //Для кого
                            $for_whom = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'B'.$row->getRowIndex()
                                )
                                ->getValue();

//                            //Предмет/направление
//                            $subject = $spreadsheet->getActiveSheet()
//                                ->getCell(
//                                    'C'.$row->getRowIndex()
//                                )
//                                ->getValue();

                            //Дата
                            $date = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'C'.$row->getRowIndex()
                                )
                                ->getFormattedValue();

                            $array_date = [];

                            if(strripos($date,'–'))
                            {
                                $array_date[0] = ['start_date' => explode('–',$date)[0], 'end_date' => explode('–',$date)[1]] ;
                            }
                            else if(strripos($date,'-'))
                            {
                                $array_date[0] = ['start_date' => explode('-',$date)[0], 'end_date' => explode('-',$date)[1]];
                            }
                            else if(strripos($date,';'))
                            {
                                $array_date_explode = explode(';',$date);

                                foreach($array_date_explode as $value)
                                {
                                    $array_date[] = ['start_date' => $value, 'end_date' => $value];
                                }
                            }
                            else
                            {
                                $array_date[0] = ['start_date' => date("d.m.Y",strtotime(trim($date))), 'end_date' => date("d.m.Y",strtotime(trim($date)))];
                            }


                            //Время
                            $event_time = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'D'.$row->getRowIndex()
                                )
                                ->getFormattedValue();

                            //Мероприятие
                            $event_name = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'E'.$row->getRowIndex()
                                )->getValue();

                            //Округ
                            $district = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'F'.$row->getRowIndex()
                                )->getValue();

                            //Место проведения
                            $location = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'G'.$row->getRowIndex()
                                )->getValue();

                            //Ответственный
                            $responsible = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'H'.$row->getRowIndex()
                                )->getValue();

                            //Описание
                            $description = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'I'.$row->getRowIndex()
                                )->getValue();

                            //Название примечание (1 ссылка)
                            $note_l[0] = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'J'.$row->getRowIndex()
                                )->getValue();

                            //Название примечание (1 ссылка)
                            $link[0] = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'J'.$row->getRowIndex()
                                )->getHyperlink();

                            //Название примечание (2 ссылка)
                            $note_l[1] = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'K'.$row->getRowIndex()
                                )->getValue();

                            //Название примечание (2 ссылка)
                            $link[1] = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'K'.$row->getRowIndex()
                                )->getHyperlink();

                            //Название примечание (3 ссылка)
                            $note_l[2] = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'L'.$row->getRowIndex()
                                )->getValue();

                            //Название примечание (3 ссылка)
                            $link[2] = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'L'.$row->getRowIndex()
                                )->getHyperlink();

                            //Название примечание (4 ссылка)
                            $note_l[3] = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'M'.$row->getRowIndex()
                                )->getValue();

                            //Название примечание (4 ссылка)
                            $link[3] = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'M'.$row->getRowIndex()
                                )->getHyperlink();

                            //Название примечание (5 ссылка)
                            $note_l[4] = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'N'.$row->getRowIndex()
                                )->getValue();

                            //Название примечание (5 ссылка)
                            $link[4] = $spreadsheet->getActiveSheet()
                                ->getCell(
                                    'N'.$row->getRowIndex()
                                )->getHyperlink();

                            $note = [];
                            foreach($link as $key => $value)
                            {
                                if($value->getURL() != '')
                                {
                                    $note[] = ['note_url'=>$value->getURL(),'note_name'=>$note_l[$key]];
                                }
                            }

                            if($type_event == '')
                            {
                                continue;
                            }

                            $list_winners_array[] = [
                                'type_event' => trim($type_event),
                                'for_whom' => trim($for_whom),
                                'user_groups_id' => $post['WorkPlanPeriod']['user_groups_id'],
                                'description' => trim($description),
                                'work_plan_period_id'=>$model['id'],
                                'event_date' => $array_date,
                                'event_time' => $event_time,
                                'event_name' => $event_name,
                                'district' => $district,
                                'location' => $location,
                                'responsible' => $responsible,
                                'event_note' => $note,
                                'users_id' => Yii::$app->user->identity->id,
                                'in_archive' => false,
                            ];

                        }
                    }

//                    die;
                    if(!empty($list_winners_array))
                    {
                        $my_work_plan_array=[];
                        $my_work_plan = WorkPlan::find()
                            ->where(['users_id'=>Yii::$app->user->identity->id,'user_groups_id' => $post['WorkPlanPeriod']['user_groups_id'],
                                'work_plan_period_id'=>$model['id'],'in_archive'=>false])->all();

                        if(!empty($my_work_plan))
                        {
                            foreach ($my_work_plan as $my_work_plan_val)
                            {
                                $my_work_plan_array[] = $my_work_plan_val['id'];
                                $text_email_delete .= $my_work_plan_val['event_name'].'<br><br>';
                            }
                        }

                        if(!empty($my_work_plan_array))
                        {
                            WorkPlan::updateAll(['in_archive'=>true],['id'=>$my_work_plan_array]);
                        }


                        foreach ($list_winners_array as $key=>$val)
                        {
                            $work_plans = new WorkPlan();
                            $work_plans->setAttributes($val);

                            if($work_plans->save())
                            {
                                foreach($val['event_date'] as $key_event_date => $value)
                                {
                                    if($value['start_date'] != '')
                                    {
                                        $work_plan_date =  new WorkPlanDate();
                                        $work_plan_date['start_date'] = date("Y-m-d",strtotime($value['start_date']));
                                        $work_plan_date['end_date'] = date("Y-m-d",strtotime($value['end_date']));
                                        $work_plan_date['work_plan_id'] = $work_plans['id'];

                                        if(!$work_plan_date->save())
                                        {
                                            throw new HttpException(517 ,var_export($work_plan_date->getErrors(),true));
                                        }
                                    }
                                }

                                if(!empty($val['event_note']))
                                {
                                    foreach($val['event_note'] as $key_event_note => $event_note_value)
                                    {
                                        $work_plan_note =  new WorkPlanNote();
                                        $work_plan_note->setAttributes($event_note_value);
                                        $work_plan_note['work_plan_id'] = $work_plans['id'];

                                        if(!$work_plan_note->save())
                                        {
                                            throw new HttpException(517 ,var_export($work_plan_note->getErrors(),true));
                                        }
                                    }
                                }
                                $text_email_add .= $work_plans['event_name'].'<br><br>';
                            }
                            else
                            {
                                throw new HttpException(517 ,var_export($work_plans->getErrors(),true));
                            }
                        }

                        $recipients = Users::find()->where(['in_archive'=>false,'not_send_email'=>false,'role'=>[Users::ROLE_MODERATOR,Users::ROLE_ADMIN]])->all();

                        if(!empty($recipients))
                        {
                            $ContactForm =  new ContactForm();

                            $user_groups = UserGroups::find()->where(['id'=>$post['WorkPlanPeriod']['user_groups_id']])->one();

                            $param['<%user%>'] = Yii::$app->user->identity->second_name.' '.Yii::$app->user->identity->first_name.' '.Yii::$app->user->identity->third_name;
                            $param['<%month%>'] = WorkPlanPeriod::$month[$model['month']];
                            $param['<%year%>'] = $model['year'];
                            $param['<%direction%>'] = $user_groups['name'];
                            $param['<%added_events%>'] = $text_email_add;
                            $param['<%remote_events%>'] = $text_email_delete;
                            $param['<%edited_events%>'] = '';

                            foreach ($recipients as $recipients_val)
                            {
                                $ContactForm->SendMail($recipients_val['email'],Notifications::WORK_PLAN_HAS_CHANGED,$param);
                            }

                        }

                        return $this->redirect(['plan/update-work-plan','id'=>$model['id']]);
                    }
                }
            }
        }

//        Url::remember();
        return $this->render('AllActivitiesWorkPlan', [
            'model' => $model,
            'user_groups_array' => $user_groups_array,
        ]);
    }

    public function actionAllWorkPlan()
    {
        $model = new WorkPlanPeriod();

        $query = WorkPlanPeriod::find()->distinct('work_plan_period.*');

        $query->innerJoin(WorkPlan::tableName(),'work_plan.work_plan_period_id = work_plan_period.id');
        if(Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST)
        {
            $query->where(['work_plan.users_id'=>Yii::$app->user->identity->id]);
        }

        $query->andWhere(['work_plan.in_archive'=>false]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

//        $dataProvider->setSort(['attributes' =>
//            [
//                'title',
//                'published'
//            ]
//        ]);

//        Url::remember();
        return $this->render('allWorkPlan', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

}
