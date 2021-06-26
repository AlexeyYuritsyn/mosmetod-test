<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "material_categories".
 *
 * @property int $id
 * @property string $title
 * @property string $alias
 * @property string $description
 * @property int $parent
 * @property bool $published
 * @property int $access
 * @property bool $in_archive
 */
class MaterialCategories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'material_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'parent'], 'required'],
            [['title', 'alias', 'description'], 'string'],
            [['parent', 'access'], 'default', 'value' => null],
            [['parent', 'access', 'order_categories'], 'integer'],
            [['published', 'in_archive', 'exclude_from_search'], 'boolean'],
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
            'alias' => 'Псевдоним заголовка',
            'description' => 'Описание',
            'parent' => 'Родительская категории',
            'published' => 'Опубликовано',
            'access' => 'Уровень доступа',
            'in_archive' => 'В архиве',
            'order_categories' => 'Порядок',
            'exclude_from_search' => 'Исключить категорию из поиска сайта',
        ];
    }

    /**
     * Note overriding isDisabled method is slightly different when
     * using the trait. It uses the alias.
     */
//    public function isDisabled()
//    {
////        if (Yii::$app->user->username !== 'admin') {
////            return true;
////        }
//        return $this->parentIsDisabled();
//    }

    static public function CreateTree($array,$sub=0,$category_id=[])
    {
        $a = array();
        foreach($array as $ki=> $v)
        {
            if($sub == $v['parent'])
            {
                $checked = in_array($v['id'],$category_id);
                $a[] = ['id'=>$v['id'],'text'=>$v['title'],'state'=>['selected'=>$checked,],'children'=>self::CreateTree($array,$v['id'],$category_id)];
            }
        }
        return $a;
    }

    static public function CreateTreeMaterials($array,$sub=0,$category_id=[])
    {
        $a = array();
        foreach($array as $ki=> $v)
        {
            if($sub == $v['parent'])
            {
                $checked = in_array($v['id'],$category_id);
                $children = self::CreateTreeMaterials($array,$v['id'],$category_id);

                $a[] = ['id'=>$v['id'],'text'=>$v['title'],'state'=>['selected'=>$checked,'disabled'=>empty($children) == true?false:true],'children'=>$children];
            }
        }
        return $a;
    }

    static public function CreateTreeCategory($array,$sub=0,$category_id=[])
    {
        $a = array();
        foreach($array as $ki=> $v)
        {
            if($sub == $v['parent'])
            {
                $checked = in_array($v['id'],$category_id);
                $a[] = ['id'=>$v['id'],'text'=>$v['title'],'published'=>$v['published'],'state'=>['selected'=>$checked,'disabled'=>empty($children) == true?false:true],'children'=>self::CreateTree($array,$v['id'],$category_id)];
            }
        }
        return $a;
    }

    static public function CreateTreeMaterialsUser($array,$sub=0,$category_id=[],$category_id_tree=null)
    {
        $a = array();

        foreach($array as $ki=> $v)
        {
            if($sub == $v['parent'])
            {
                $checked = in_array($v['id'],$category_id);
                $children = self::CreateTreeMaterialsUser($array,$v['id'],$category_id,$category_id_tree);
                if(in_array($v['id'],$category_id_tree) || ($v['parent'] == 0  && !is_null(self::find()->where(['id'=>$category_id_tree,'parent'=>$v['id']])->one())))
                {
                    $a[] = ['id'=>$v['id'],'text'=>$v['title'],'state'=>['selected'=>$checked,'disabled'=>empty($children) == true?false:true],'children'=>$children];
                }
            }
        }

        return $a;
    }

    static public function CreateTreeMaterialsUserWithoutDisabled($array,$sub=0,$category_id=[],$category_id_tree=null)
    {
        $a = array();

        foreach($array as $ki=> $v)
        {
            if($sub == $v['parent'])
            {
                $checked = in_array($v['id'],$category_id);
                $children = self::CreateTreeMaterialsUserWithoutDisabled($array,$v['id'],$category_id,$category_id_tree);
                if(in_array($v['id'],$category_id_tree) || ($v['parent'] == 0  && !is_null(self::find()->where(['id'=>$category_id_tree,'parent'=>$v['id']])->one())))
                {
                    $a[] = ['id'=>$v['id'],'text'=>$v['title'],'state'=>['selected'=>$checked],'children'=>$children];
                }
            }
        }

        return $a;
    }

    static public function CreateTreeMaterialsUserParent($array_start,&$array_end)
    {
        $a = array();
        $foreach = self::find()->where(['parent'=>$array_start,'in_archive'=>false])->all();
        if(!empty($foreach))
        {
            foreach($foreach as $ki=> $v)
            {
                if(!in_array($v['id'],$array_end))
                {
                    $array_end[] = $v['id'];
                }


                array_push($a,$v['id']);
            }
        }

        if(!empty($a))
        {
            $array_start = $a;
            self::CreateTreeMaterialsUserParent($array_start,$array_end);
        }

        return $array_end;
    }

    static public function CreateTreeMaterialsUserParentOrderBy($array_start,&$array_end)
    {
        $a = array();
        $foreach = self::find()->where(['parent'=>$array_start,'in_archive'=>false])->orderBy('id')->all();
        if(!empty($foreach))
        {
            foreach($foreach as $ki=> $v)
            {
                if(!in_array($v['id'],$array_end))
                {
                    $array_end[] = $v['id'];
                }


                array_push($a,$v['id']);
            }
        }

        if(!empty($a))
        {
            $array_start = $a;
            self::CreateTreeMaterialsUserParent($array_start,$array_end);
        }

        return $array_end;
    }

    static public function CreateTreeMaterialsUserChild($material_categories_id,&$array_end=[])
    {
        $material_categories = self::find()->where(['id'=>$material_categories_id,'in_archive'=>false])->one();
        if(!is_null($material_categories) && $material_categories['parent'] != 0)
        {
            $array_end[] = $material_categories['parent'];
            self::CreateTreeMaterialsUserChild($material_categories['parent'],$array_end);
        }

        return $array_end;
    }

    static public function CreateTreeMaterialsUserChildInArchive($material_categories_id,&$array_end=[])
    {
        $material_categories = self::find()->where(['id'=>$material_categories_id,'in_archive'=>true])->one();
        if(!is_null($material_categories))
        {
            $array_end[] = $material_categories['id'];
            self::CreateTreeMaterialsUserChildInArchive($material_categories['parent'],$array_end);
        }

        return $array_end;
    }
}
