<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscribe_category".
 *
 * @property int $id
 * @property int $user_id
 * @property string $school
 * @property int $category_id
 * @property int $time_created
 */
class SubscribeCategory extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscribe_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'school', 'category_id', 'time_created'], 'required'],
            [['user_id', 'category_id', 'time_created'], 'integer'],
            [['school'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'school' => 'School',
            'category_id' => 'Category ID',
            'time_created' => 'Time Created',
        ];
    }
}
