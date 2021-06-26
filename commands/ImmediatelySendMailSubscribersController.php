<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\SendEmailMaterialPublished;
use Yii;
use app\models\Materials;
use app\models\Subscribers;
use app\models\SubscribersCategory;
use app\models\MaterialCategories;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;


/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ImmediatelySendMailSubscribersController extends Controller
{
    public function actionIndex()
    {
        $start_date = strtotime(date("d.m.Y 00:00:00"));
        $end_date = strtotime(date("d.m.Y 23:59:59"));

        $materials = Materials::find()->where('(published_date BETWEEN :start_date AND :end_date) AND (status = :status)',
            [':start_date'=>$start_date, ':end_date'=>$end_date, ':status'=>Materials::PUBLISHED])
            ->orderBy('material_categories_id')->all();

        if(!empty($materials))
        {
            $subscribers = \app\models\Subscribers::find()->where(['status' => Subscribers::CONFIRMATION,'email'=>'i25585@yandex.ru','send_notification_immediately'=>true])->all();

            if(!empty($subscribers))
            {
                foreach ($subscribers as $value)
                {
                    $array_category_id = [];
                    $materials_array = [];
                    $category_id_tree = [];
                    $content = '';
                    $subscribers_category = SubscribersCategory::find()->select('category_id')->where(['user_id'=>$value['id']])->all();

                    if(!empty($subscribers_category))
                    {
                        foreach($subscribers_category as $subscribers_category_key=>$subscribers_category_val)
                        {
                            $category_id_tree[] = $subscribers_category_val['category_id'];
                        }
                    }

                    $category_id_tree = MaterialCategories::CreateTreeMaterialsUserParent($category_id_tree,$category_id_tree);

                    foreach($materials as $materials_val)
                    {
                        if(array_search($materials_val['material_categories_id'], $category_id_tree) != false)
                        {
                            $check_send_email = SendEmailMaterialPublished::find()->where(['materials_id'=>$materials_val['id']])->count();

                            if($check_send_email == 0)
                            {
                                $parent_categories = MaterialCategories::CreateTreeMaterialsUserChild($materials_val['material_categories_id']);

                                $title_0 = MaterialCategories::find()->where(['id'=>$parent_categories[0]])->one()['title'];
                                $title_1 = MaterialCategories::find()->where(['id'=>$materials_val['material_categories_id']])->one()['title'];

                                if(!in_array($parent_categories[0],$array_category_id))
                                {
                                    $content .= '<div style="margin: 10px 0px 10px 0px;width: 600px;padding: 0 20px;background-color:#EBF5FA;border-radius:5px;">
                                    <span style="color:#444444;font-family: \'Times New Roman\',Times,serif;font-style: italic;font-size: 16px;line-height: 22px;">"' . $title_0 . ' - '.$title_1.'"</span>
                                </div>';

                                    $array_category_id[] = $parent_categories[0];
                                }

                                $content .=  Html::a($materials_val['title'],   Url::to(['/teaching-space/169/'.$materials_val['id']],true));
                                $content .=  '<br><br>';

                                $materials_array[]=$materials_val['id'];
                            }
                        }
                    }

                    if($content != '')
                    {
                        $content .=  '<br><br>';
                        $content .=  '<br><br>';


                        $subject = 'Рассылка новостей сайта Городского методического центра';

                        $header = '<div style="font: 14px/20px Arial,Helvetica,sans-serif;">';
                        $header .= '<div>Уважаемый подписчик!</div><div style="padding: 10px 0px 10px 0px;">Предоставляем Вашему вниманию подборку новостей сайта <a style="color:#4488BB;" href="http://mosmetod.ru">mosmetod.ru</a> за ' . date('d.m.Y', strtotime('-1 day', time())) . '.</div>';


                        $code = md5($value['id'] . $value['email'] . $value['time_created']);
                        $Html_a =  Html::a('Управление подпиской на разделы сайта Городского методического центра',
                            Url::to(['/site/update-material-categories', 'email' => $value['email'], 'guid' => $code ],true),
                            ['style'=>'color:#4488BB;']);

//                        $footer = '<div style="margin-top: 10px;">Если Вы не хотите получать эту рассылку, то перейдите по '.$Html_a.'.</div>';
//                        $urlManagement = $host . 'subscriptionManagement.php' . $code;
                        $footer = '<div style="margin-top: 10px;">'.$Html_a.'</div>';
                        $footer .= '<p><a href="http://mosmetod.ru"><img src="http://mosmetod.ru/gmc.png" width="18px">Городской методический центр Департамента образования и науки г. Москвы</a></p>
<p>Подписывайтесь на наши страницы в социальных сетях:<br /><a href="https://www.facebook.com/mosmetod">Facebook</a><br />
<a href="http://vk.com/mosmetod">ВКонтакте</a><br />
<a href="https://www.youtube.com/user/mosmetod">Youtube</a><br />
<a href="https://www.instagram.com/mosmetod">Instagram</a></p></div>';

                        $content = $header.$content.$footer;

                        $client = new \SoapClient(Yii::$app->params['CFG_URL_SOAP'], ["cache_wsdl" => 0, "trace" => 1, "exceptions" => 0]);
                        $result =  $client->sendMail($value['email'],$subject,$content,Yii::$app->params['CFG_PROJECT_TOKEN']);

                        if($result == '1')
                        {
                            foreach ($materials_array as $materials_array_val)
                            {
                                $send_email_material_published = new SendEmailMaterialPublished();
                                $send_email_material_published['user_id'] = $value['id'];
                                $send_email_material_published['materials_id'] = $materials_array_val;
                                $send_email_material_published['time_created'] = new Expression('NOW()');
                                $send_email_material_published->save();
                            }
//                            $materials_array[]=$materials_val['id'];
                        }
                        var_dump($result);
                    }
                }
            }
        }
    }
}
