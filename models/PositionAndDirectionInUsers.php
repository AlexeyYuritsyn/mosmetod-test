<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "position_and_direction_in_users".
 *
 * @property int $id
 * @property int|null $users_id
 * @property int|null $position_and_direction_id
 */
class PositionAndDirectionInUsers extends \yii\db\ActiveRecord
{
    public $position_and_direction_position_api;
    public $position_and_direction_name_api;
    public $users_fio_api;
    public $users_id_api;
    public $users_image_api;
    public $users_description_api;
    public $users_basic_information_api;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'position_and_direction_in_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['users_id', 'position_and_direction_id'], 'default', 'value' => null],
            [['users_id', 'position_and_direction_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'users_id' => 'Users ID',
            'position_and_direction_id' => 'Position And Direction ID',
        ];
    }
}
