<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "widget_gallery".
 *
 * @property int $id
 * @property int|null $widget_id
 * @property string|null $src
 */
class WidgetGallery extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'widget_gallery';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['widget_id'], 'default', 'value' => null],
            [['widget_id','order_id','materials_id'], 'integer'],
            [['image'], 'string'],
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
            'widget_id' => 'Widget ID',
            'image' => 'image',
        ];
    }

//    public static function resize($image, $w_o = false, $h_o = false) {
//        if (($w_o < 0) || ($h_o < 0)) {
//            echo "Некорректные входные параметры";
//            return false;
//        }
//        list($w_i, $h_i, $type) = getimagesize($image); // Получаем размеры и тип изображения (число)
//        $types = array("", "jpeg", "png"); // Массив с типами изображений
//        $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа
//        if ($ext) {
//            $func = 'imagecreatefrom'.$ext; // Получаем название функции, соответствующую типу, для создания изображения
//            $img_i = $func($image); // Создаём дескриптор для работы с исходным изображением
//        } else {
//            echo 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый
//            return false;
//        }
//        /* Если указать только 1 параметр, то второй подстроится пропорционально */
//        if (!$h_o) $h_o = $w_o / ($w_i / $h_i);
//        if (!$w_o) $w_o = $h_o / ($h_i / $w_i);
//        $img_o = imagecreatetruecolor($w_o, $h_o); // Создаём дескриптор для выходного изображения
//        imagecopyresampled($img_o, $img_i, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i); // Переносим изображение из исходного в выходное, масштабируя его
//        $func = 'image'.$ext; // Получаем функция для сохранения результата
//        return $func($img_o, $image); // Сохраняем изображение в тот же файл, что и исходное, возвращая результат этой операции
//    }
}
