<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\Request;

/**
 * Class Click
 * @property string id
 * @property string ua
 * @property string ip
 * @property string ref
 * @property string param1
 * @property string param2
 * @property integer error
 * @property integer bad_domain
 *
 * @package app\models
 */
class Click extends ActiveRecord
{
    /**
     * Stores class name for handling the click if it exists
     */
    const CLICK_EXISTS_HANDLER = 'app\models\handlers\ClickExistsHandler';
    /**
     * Stores class name for handling the click if it does not exist
     */
    const CLICK_NOT_EXISTS_HANDLER = 'app\models\handlers\ClickNotExistsHandler';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%click}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ua', 'ip', 'ref', 'param1', 'param2'], 'required'],
            [['id', 'ua', 'ip', 'ref', 'param1', 'param2'], 'string'],
            [['error', 'bad_domain'], 'integer'],
            ['id', 'unique'],
        ];
    }

    /**
     * Returns an array with attribute labels of click model
     *
     * @return array
     */
    public static function getAttributeLabels()
    {
        return [
            'id' => 'Click ID',
            'ua' => 'User Agent',
            'ip' => 'User IP',
            'ref' => 'Referrer',
            'param1' => 'Param1',
            'param2' => 'Param2',
            'error' => 'Error',
            'bad_domain' => 'Bad domain',
        ];
    }

    /**
     * Checking of unique click exists in database
     *
     * @param $request Request
     * @param $param1 string
     * @return bool
     */
    public static function clickExists($request, $param1)
    {
        $model = self::find()
            ->where([
                'ua' => $request->userAgent,
                'ip' => $request->userIP,
                'ref' => $request->referrer,
                'param1' => $param1,
            ])->one();

        return empty($model) ? false : true;
    }

    /**
     * Generates unique ID for click model
     *
     * @return string
     */
    public function generateId()
    {
        return uniqid();
    }

    /**
     * Save click info into database
     *
     * @param $request Request
     * @param $param1 string
     * @param $param2 string
     * @return bool|string
     */
    public static function saveClick($request, $param1, $param2)
    {
        $click = new Click();
        $click->id = $click->generateId();
        $click->ua = $request->userAgent;
        $click->ip = $request->userIP;
        $click->ref = $request->referrer;
        $click->param1 = $param1;
        $click->param2 = $param2;

        return ($click->save()) ? $click->id : false;
    }

    /**
     * Returns constant with handler class name by condition
     *
     * @param $condition boolean
     * @return string
     */
    public function getHandler($condition)
    {
        switch ($condition) {
            case true:
                return self::CLICK_EXISTS_HANDLER;
                break;
            case false:
                return self::CLICK_NOT_EXISTS_HANDLER;
                break;
        }
    }

    /**
     * Checks if bad_domain field does not equals to zero
     *
     * @return bool
     */
    public function checkBadDomain()
    {
        return $this->bad_domain == 0 ? false : true;
    }
}