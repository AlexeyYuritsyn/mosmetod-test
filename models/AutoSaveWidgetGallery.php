<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auto_save_widget_gallery".
 *
 * @property int $id
 * @property string|null $image
 * @property int|null $order_id
 * @property int|null $auto_save_materials_id
 */
class AutoSaveWidgetGallery extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auto_save_widget_gallery';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image'], 'string'],
            [['order_id', 'auto_save_materials_id'], 'default', 'value' => null],
            [['order_id', 'auto_save_materials_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image' => 'Image',
            'order_id' => 'Order ID',
            'auto_save_materials_id' => 'Auto Save Materials ID',
        ];
    }
}
