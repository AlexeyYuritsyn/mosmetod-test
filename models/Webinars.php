<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "webinars".
 *
 * @property int $id
 * @property int|null $user_groups_id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $youtube_url
 * @property string|null $time_created
 * @property bool|null $in_archive
 * @property int|null $users_id
 */
class Webinars extends \yii\db\ActiveRecord
{
    public $user_groups_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'webinars';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_groups_id', 'title', 'description', 'youtube_url', 'time_created'], 'required'],
            [['user_groups_id', 'users_id'], 'default', 'value' => null],
            [['user_groups_id', 'users_id'], 'integer'],
            [['title', 'youtube_url'], 'string'],
            [['time_created'], 'safe'],
            [['in_archive'], 'boolean'],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_groups_id' => 'Категория',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'youtube_url' => 'Ссылка на вебинар',
            'time_created' => 'Дата загрузки на youtube',
            'in_archive' => 'Удалить',
            'users_id' => 'Кто последний вносил изменения',
            'user_groups_name' => 'Название категории',
        ];
    }
}
