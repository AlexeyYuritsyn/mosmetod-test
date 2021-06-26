<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auto_save_materials".
 *
 * @property int $id
 * @property int|null $materials_id
 * @property string|null $title
 * @property string|null $alias
 * @property int|null $material_categories_id
 * @property int|null $status
 * @property int|null $published_date
 * @property string|null $content
 * @property string|null $description
 * @property string|null $created
 * @property int|null $created_by
 * @property int|null $checked_out
 * @property string|null $checked_out_time
 * @property string|null $modified
 * @property int|null $modified_by
 * @property string|null $publish_up
 * @property string|null $publish_down
 * @property bool|null $hits
 * @property int|null $urgency_withdrawal
 * @property string|null $guid
 * @property string|null $save_date
 */
class AutoSaveMaterials extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auto_save_materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['materials_id', 'material_categories_id', 'status', 'published_date', 'created_by', 'checked_out', 'modified_by', 'urgency_withdrawal'], 'default', 'value' => null],
            [['materials_id', 'material_categories_id', 'status', 'published_date', 'created_by', 'checked_out', 'modified_by', 'urgency_withdrawal', 'users_id'], 'integer'],
            [['title', 'alias', 'content', 'comment'], 'string'],
            [['created', 'checked_out_time', 'modified', 'publish_up', 'publish_down', 'save_date', 'date_unpinning'], 'safe'],
            [['hits'], 'boolean'],
            [['description', 'guid'], 'string', 'max' => 255],
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
            'title' => 'Title',
            'alias' => 'Alias',
            'material_categories_id' => 'Material Categories ID',
            'status' => 'Status',
            'published_date' => 'Published Date',
            'content' => 'Content',
            'description' => 'Description',
            'created' => 'Created',
            'created_by' => 'Created By',
            'checked_out' => 'Checked Out',
            'checked_out_time' => 'Checked Out Time',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
            'publish_up' => 'Publish Up',
            'publish_down' => 'Publish Down',
            'hits' => 'Hits',
            'urgency_withdrawal' => 'Urgency Withdrawal',
            'guid' => 'Guid',
            'users_id' => 'users_id',
            'save_date' => 'Save Date',
            'comment' => 'Save Date'
        ];
    }
}
