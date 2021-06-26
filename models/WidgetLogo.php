<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "widget_logo".
 *
 * @property int $id
 * @property int|null $materials_id
 * @property string|null $image
 * @property int|null $type
 */
class WidgetLogo extends \yii\db\ActiveRecord
{
    public $path_widget_logo;

    const LEFT = '1';
    const TOP = '2';
    const RIGHT = '3';

    public static $types = [
        self::LEFT =>'Слева',
        self::TOP =>'По центру',
        self::RIGHT =>'Справа'
    ];

    public static $types_en = [
        self::LEFT =>'left',
        self::TOP =>'center',
        self::RIGHT =>'right'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'widget_logo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['materials_id', 'type'], 'default', 'value' => null],
            [['materials_id', 'type'], 'integer'],
            [['image','url'], 'string'],
            [['path_widget_logo'], 'file'], //, 'extensions' => 'png, jpg, jpeg'
            [['created', 'modified'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'materials_id' => 'Materials ID',
            'image' => 'Image',
            'url' => 'URL-адрес',
            'type' => 'Расположение логотипа',
            'path_widget_logo' => 'Логотип',
        ];
    }
}
