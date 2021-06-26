<?php
use yii\bootstrap\Tabs;
use app\models\Users;
?>

<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Профиль</h3>
    </div>
    <div class="box-body">
        <?php

        echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Основная информация',
                    'linkOptions' => ['class'=>'gkTabs-1'],
                    'content' => $this->render('_basic_information_user',['model'=>$model,'position_and_direction_array'  => isset($position_and_direction_array)?$position_and_direction_array:[]]),
                    'visible' => Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN?true:false,
                    'active' => !isset($no_validate) && !isset($no_validate_translation_user)
                ],
                [
                    'label' => 'Пароль для входа',
                    'linkOptions' => ['class'=>'gkTabs-2'],
                    'content' => $this->render('_change_password_user',['model'=>$model]),
                    'active' => isset($no_validate) && !isset($no_validate_translation_user) || (Yii::$app->user->identity->role == Users::ROLE_METHODIST || Yii::$app->user->identity->role == Users::ROLE_SENIOR_METHODIST)
                ],
                [
                    'label' => 'Перевод материалов пользователя',
                    'linkOptions' => ['class'=>'gkTabs-3'],
                    'content' => $this->render('_translation_user_materials',['model'=>$model,'users_array'=>isset($users_array)?$users_array:[]]),
                    'visible' => Yii::$app->user->identity->role == Users::ROLE_MODERATOR || Yii::$app->user->identity->role == Users::ROLE_ADMIN?true:false,
                    'active' => isset($no_validate_translation_user)
                ],
            ],
        ]);
        ?>
    </div>
</div>
<?php

$RESULT = isset($result)?$result:'';
$script = <<< JS

    $(document).ready(function() {
        
        if('$RESULT' != '')
        {
            alert('$RESULT');
        }
    
   }); 
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>