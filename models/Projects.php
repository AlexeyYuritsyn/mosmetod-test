<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "projects".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $background
 * @property string|null $logo
 * @property string|null $description
 * @property string|null $time_create
 * @property string|null $color_projects_id
 */
class Projects extends \yii\db\ActiveRecord
{
//    public $background_input;
    public $logo_input;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title','url','time_create'], 'required'],
            [['background', 'logo',  'url'], 'string'],
//            [['time_create'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 150],
            [['color_projects_id'], 'integer'],
            [['in_archive','display_on_home_page','outdated'], 'boolean'],
//            [['title'], 'custom_function_validation_save_projects'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
//            'background_input' => 'Фон баннера',
            'logo_input' => 'Логотип',
            'description' => 'Описание',
            'time_create' => 'Дата создания',
            'color_projects_id' => 'Цвет плашки',
            'in_archive' => 'Удалить',
            'url' => 'URL кнопки подробнее',
            'display_on_home_page' => 'Отображать на главной странице',
            'outdated' => 'Архивый проект'
        ];
    }

//    function custom_function_validation_save_projects($attribute)
//    {
//        if(($attribute == 'title') && $this->background == '' && $this->background_input  == '')
//        {
//            $this->addError('background_input', 'Презентация должна быть загружена');
//        }
//    }

    // Транслитерация строк.
    public static function  transliterate($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );

        $str = strtr($string, $converter);
        // в нижний регистр
        $str = strtolower($str);
        // заменям все ненужное нам на "-"
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        // удаляем начальные и конечные '-'
        $str = trim($str, "-");
        return $str;

//        return $st;
    }
}
