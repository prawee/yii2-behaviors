<?php
/**
 * @link http://www.konkeanweb.com
 * 11/27/2016 AD 12:23 PM
 * @copyright Copyright (c) 2016 served
 * @author Prawee Wongsa <konkeanweb@gmail.com>
 * @license BSD-3-Clause
 */
namespace prawee\behaviors;

use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;

class YoutubeBehavior extends AttributeBehavior
{

    public $attribute = 'url';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }

    public function beforeInsert()
    {
        $url = $this->owner->{$this->attribute};
        $this->owner->{$this->attribute} = $this->parseUrl($url);
    }

    public function beforeUpdate()
    {
        $url = $this->owner->{$this->attribute};
        $this->owner->{$this->attribute} = $this->parseUrl($url);
    }

    public function parseUrl($url)
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $output);
        return $output['v'];
    }
}