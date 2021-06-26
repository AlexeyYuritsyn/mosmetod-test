<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "position_and_direction".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $position
 */
class PositionAndDirection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'position_and_direction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position'], 'default', 'value' => null],
            [['position'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'position' => 'Position',
        ];
    }
}
