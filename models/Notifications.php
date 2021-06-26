<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notifications".
 *
 * @property int $id
 * @property string|null $subject
 * @property string|null $body
 * @property int|null $type
 */
class Notifications extends \yii\db\ActiveRecord
{
    const MATERIAL_SENT_FOR_CONFIRMATION = 1; //
    const NEW_MATERIAL_AWAITING_CONFIRMATION = 2; //
    const MATERIAL_CONFIRMED_SENIOR_METHODIST = 3; //
    const MATERIAL_CONFIRMED = 4; //
    const NEW_MATERIAL_AWAITING_PUBLICATION = 5; //
    const MATERIAL_PUBLISHED = 6; //
    const MATERIAL_SENT_FOR_REVISION = 7; //
    const MATERIAL_MOVED_TO_ARCHIVE = 8; //
    const RECORD_ACCOUNT_RECOVERY = 9; //
    const WORK_PLAN_HAS_CHANGED = 10; //
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notifications';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['body'], 'string'],
            [['type'], 'default', 'value' => null],
            [['type'], 'integer'],
            [['subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Subject',
            'body' => 'Body',
            'type' => 'Type',
        ];
    }
}
