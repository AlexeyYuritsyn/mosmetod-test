<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscribers_category".
 *
 * @property int $id
 * @property int $user_id
 * @property string $school
 * @property int $category_id
 * @property string $time_created
 */
class SubscribersCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscribers_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id',  'time_created'], 'required'],//'school',
            [['user_id', 'category_id'], 'default', 'value' => null],
            [['user_id', 'category_id'], 'integer'],
//            [['school'], 'string'],
            [['time_created'], 'safe'],
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
//            'school' => 'School',
            'category_id' => 'Category ID',
            'time_created' => 'Time Created',
        ];
    }
}
