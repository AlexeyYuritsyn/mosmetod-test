<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "materials".
 *
 * @property int $id
 * @property string $title
 * @property string $alias
 * @property int $material_categories_id
 * @property bool $published
 * @property string $content
 * @property string $created
 * @property int $created_by
 * @property int $checked_out
 * @property string $checked_out_time
 * @property string $modified
 * @property int $modified_by
 * @property string $publish_up
 * @property string $publish_down
 * @property int $hits
 */
class Materials extends \yii\db\ActiveRecord
{

//    const DRAFT = '1';
//    const CHECK_BY_SENIOR_METHODIST = '2';
//    const CHECK_BY_CORRECTOR = '3';
//    const CANCELED = '4';
//    const PUBLISHED = '5';
//    const REMOVE_PUBLICATION = '6';
//    const CHECK_BY_MODERATOR = '7';

    //статусы материала
    const DRAFT = '1';
    const SENT_FOR_CONFIRMATION = '2';
    const CONFIRMED = '3';
    const PUBLISHED = '4';
    const SENT_FOR_DEVELOPMENT = '5';
    const ARCHIVE = '6';
    const NOT_PUBLISHED = '7';

    //скорость публикации
    const LOW_SPEED = '0';
    const AVERAGE_SPEED = '1';
    const HIGH_SPEED = '2';

    public $categories_name;
    public $fio_created;
    public $fio_modified;
    public $tag = [];
    public $path_widget_gallery;
    public $material_watchers = [];
    public $comment;
    public $comment_log;
    public $youtube_url;

    public $new_status;

    public $name_widget_accordion;
    public $name_widget_gallery;
//    public $name_widget_logo;
    public $name_widget_map;
    public $name_widget_tabs;
    public $name_widget_youtube;


    public static $status = [
        self::DRAFT =>'Черновик',
        self::SENT_FOR_CONFIRMATION =>'Отправлено на подтверждение',
        self::CONFIRMED =>'Подтверждено',
        self::PUBLISHED =>'Опубликовано',
        self::SENT_FOR_DEVELOPMENT =>'Отправлено на доработку',
        self::ARCHIVE =>'Архив',
        self::NOT_PUBLISHED =>'Не опубликовано',

    ];

    public static $status_query = [
        self::DRAFT,
        self::SENT_FOR_CONFIRMATION,
        self::CONFIRMED,
        self::PUBLISHED,
        self::SENT_FOR_DEVELOPMENT
    ];

//    public static $status_methodist = [
//        self::DRAFT =>'Черновик',
//        self::CHECK_BY_SENIOR_METHODIST =>'На проверке у старшего методиста'
//    ];

//    public static $status_senior_methodist = [
//        self::DRAFT =>'Черновик',
//        self::CHECK_BY_SENIOR_METHODIST =>'На проверке у старшего методиста',
//        self::CHECK_BY_CORRECTOR =>'На проверке у корректора',
//        self::CANCELED =>'Отменен'
//    ];

//    public static $status_moderator = [
//        self::DRAFT =>'Черновик',
//        self::CHECK_BY_MODERATOR =>'На проверке у старшего корректора',
//        self::CANCELED =>'Отменен',
//        self::PUBLISHED =>'Опубликован',
//    ];

    public static $status_materials_log = [
        self::DRAFT =>'сохранил(а) черновик',
        self::SENT_FOR_CONFIRMATION =>'отправил(а) материал на подтверждение к старшему методисту',
        self::CONFIRMED =>'материал подтвердил(а)',
        self::PUBLISHED =>'материал опубликовал(а)',
        self::SENT_FOR_DEVELOPMENT =>'снял(а) с публикации материал',
        self::ARCHIVE =>'отправил(а) материал в архив'
    ];

    public static $urgency_withdrawal = [
        self::LOW_SPEED =>'Низкая',
        self::AVERAGE_SPEED =>'Средняя',
        self::HIGH_SPEED =>'Высокая'
    ];

    public static $month = array(
        "01" => "янв",
        "02" => "фев",
        "03" => "мар",
        "04" => "апр",
        "05" => "май",
        "06" => "июн",
        "07" => "июл",
        "08" => "авг",
        "09" => "сен",
        "10" => "окт",
        "11" => "ноя",
        "12" => "дек");
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title','tag','material_categories_id', 'content', 'created', 'created_by'], 'required'],
            [['title', 'alias', 'content'], 'string'],
            [['guid','description'], 'string', 'max' => 255],
            [['material_watchers'],'custom_function_material_watchers'],
            [['material_categories_id', 'created_by', 'checked_out', 'modified_by'], 'default', 'value' => null],
            [['material_categories_id', 'created_by', 'checked_out', 'modified_by', 'status', 'urgency_withdrawal'], 'integer'],
            [['hits'], 'boolean'],
//            [['hits'], 'integer', 'max'=> 10],
            [['path_widget_gallery'], 'file'],
            [['created', 'checked_out_time', 'modified', 'publish_up', 'publish_down', 'published_date', 'date_unpinning'], 'safe'],
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
            'alias' => 'Alias',
            'material_categories_id' => 'Категория',
            'content' => false,
            'created' => 'Создано',
            'created_by' => 'Автор',
            'status' => 'Статус',
            'checked_out' => 'Checked Out',
            'checked_out_time' => 'Checked Out Time',
            'modified' => 'Изменен',
            'modified_by' => 'Последним редактировал',
            'publish_up' => 'Publish Up',
            'publish_down' => 'Publish Down',
//            'in_archive' => 'Удалить',
            'hits' => 'Закрепить материал',
            'urgency_withdrawal' => 'Приоритет публикации материала',
            'fio_created' => 'Автор',
            'fio_modified' => 'Изменен',
            'categories_name' => 'Категория',
            'guid' => 'Идентификатор материала',
            'path_widget_gallery' => false,
            'published_date' => 'Дата публикации',
            'material_watchers' => 'Наблюдатели',
            'comment' => 'Комментарии к материалам',
            'comment_log' => 'Комментарии к материалам',
            'youtube_url' => 'Ссылка на youtube.com',
            'tag' => 'Теги',
            'date_unpinning' => 'Дата открепления материала'

        ];
    }

    function custom_function_material_watchers($attribute)
    {
        if(($attribute == 'material_watchers'))
        {
            foreach ($this->material_watchers as $val)
            {
                if(!preg_match("/(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,6})$/i",$val))
                {
                    $this->addError($attribute, 'Email наблюдателя "'.$val.'" не прошел валидацию');
                }
            }
        }
    }
}
