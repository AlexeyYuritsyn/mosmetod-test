<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "color_projects".
 *
 * @property int $id
 * @property string|null $start
 * @property string|null $end
 * @property string|null $name
 */
class ColorProjects extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'color_projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start', 'end'], 'string', 'max' => 7],
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
            'start' => 'Start',
            'end' => 'End',
            'name' => 'Name',
        ];
    }
}
