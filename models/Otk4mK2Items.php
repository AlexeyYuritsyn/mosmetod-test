<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "otk4m_k2_items".
 *
 * @property int $id
 * @property string $title
 * @property string $alias
 * @property int $catid
 * @property int $published
 * @property string $introtext
 * @property string $fulltext
 * @property string $video
 * @property string $gallery
 * @property string $extra_fields
 * @property string $extra_fields_search
 * @property string $created
 * @property int $created_by
 * @property string $created_by_alias
 * @property int $checked_out
 * @property string $checked_out_time
 * @property string $modified
 * @property int $modified_by
 * @property string $publish_up
 * @property string $publish_down
 * @property int $trash
 * @property int $access
 * @property int $ordering
 * @property int $featured
 * @property int $featured_ordering
 * @property string $image_caption
 * @property string $image_credits
 * @property string $video_caption
 * @property string $video_credits
 * @property int $hits
 * @property string $params
 * @property string $metadesc
 * @property string $metadata
 * @property string $metakey
 * @property string $plugins
 * @property string $language
 */
class Otk4mK2Items extends \yii\db\ActiveRecord
{
    public static function getDb() {
        return \Yii::$app->db2;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'otk4m_k2_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'catid', 'introtext', 'fulltext', 'extra_fields_search', 'created', 'created_by_alias', 'checked_out', 'checked_out_time', 'modified', 'publish_up', 'publish_down', 'image_caption', 'image_credits', 'video_caption', 'video_credits', 'hits', 'params', 'metadesc', 'metadata', 'metakey', 'plugins', 'language'], 'required'],
            [['catid', 'published', 'created_by', 'checked_out', 'modified_by', 'trash', 'access', 'ordering', 'featured', 'featured_ordering', 'hits'], 'integer'],
            [['introtext', 'fulltext', 'video', 'extra_fields', 'extra_fields_search', 'image_caption', 'video_caption', 'params', 'metadesc', 'metadata', 'metakey', 'plugins'], 'string'],
            [['created', 'checked_out_time', 'modified', 'publish_up', 'publish_down'], 'safe'],
            [['title', 'alias', 'gallery', 'created_by_alias', 'image_credits', 'video_credits'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 7],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'alias' => 'Alias',
            'catid' => 'Catid',
            'published' => 'Published',
            'introtext' => 'Introtext',
            'fulltext' => 'Fulltext',
            'video' => 'Video',
            'gallery' => 'Gallery',
            'extra_fields' => 'Extra Fields',
            'extra_fields_search' => 'Extra Fields Search',
            'created' => 'Created',
            'created_by' => 'Created By',
            'created_by_alias' => 'Created By Alias',
            'checked_out' => 'Checked Out',
            'checked_out_time' => 'Checked Out Time',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
            'publish_up' => 'Publish Up',
            'publish_down' => 'Publish Down',
            'trash' => 'Trash',
            'access' => 'Access',
            'ordering' => 'Ordering',
            'featured' => 'Featured',
            'featured_ordering' => 'Featured Ordering',
            'image_caption' => 'Image Caption',
            'image_credits' => 'Image Credits',
            'video_caption' => 'Video Caption',
            'video_credits' => 'Video Credits',
            'hits' => 'Hits',
            'params' => 'Params',
            'metadesc' => 'Metadesc',
            'metadata' => 'Metadata',
            'metakey' => 'Metakey',
            'plugins' => 'Plugins',
            'language' => 'Language',
        ];
    }
}
