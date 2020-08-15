<?php
/**
 * @link http://www.konkeanweb.com
 * 11/27/2016 AD 12:21 PM
 * @copyright Copyright (c) 2016 served
 * @author Prawee Wongsa <konkeanweb@gmail.com>
 * @license BSD-3-Clause
 */
namespace prawee\behaviors;

use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\behaviors\AttributeBehavior;

class UploadBehavior extends AttributeBehavior
{
    public $path;
    public $attribute = 'cover';
    public $filename = null;

    public function init()
    {
        if (empty($this->path)) {
            throw new Exception('Please configuration path of cover.');
        } else {
            if (!is_dir($this->path)) {
                @mkdir($this->path, 0777, true);
            }
        }
        return parent::init();
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }

    public function beforeInsert()
    {
        $uploaded = UploadedFile::getInstance($this->owner, $this->attribute);
        if (is_object($uploaded)) {
            $filename = time() . '.' . $uploaded->extension;
            $uploaded->saveAs($this->path . $filename);
            $this->owner->{$this->attribute} = $filename;
        }
    }

    public function beforeUpdate()
    {
        $uploaded = UploadedFile::getInstance($this->owner, $this->attribute);
        if (is_object($uploaded)) {
            $model = new $this->owner();
            $old_data = $model->findOne($this->owner->id);
            $attribute = $old_data->{$this->attribute};
            (file_exists($this->path . $attribute)) ? @unlink($this->path . $attribute) : '';
            $filename = time() . '.' . $uploaded->extension;
            $uploaded->saveAs($this->path . $filename);
            $this->owner->{$this->attribute} = $filename;
        }
    }

    public function afterDelete()
    {
        @unlink($this->path . $this->owner->{$this->attribute});
    }

    public function afterInsert()
    {
        $path = $this->path . $this->owner->{$this->attribute};
        if (!file_exists($path)) {
            $this->owner->{$this->attribute} = null;
        }
    }
}