<?php
class MyActiveRecord extends CActiveRecord {
    private static $dbadvert = null;

    protected static function getAdvertDbConnection()
    {
        if (self::$dbadvert !== null)
            return self::$dbadvert;
        else
        {
            self::$dbadvert = Yii::app()->dbadvert;
            if (self::$dbadvert instanceof CDbConnection)
            {
                self::$dbadvert->setActive(true);
                return self::$dbadvert;
            }
            else
                throw new CDbException(Yii::t('yii','Active Record requires a "db" CDbConnection application component.'));
        }
    }
    protected static function getAdvertDbConnection2()
    {
        if (self::$dbadvert2 !== null)
            return self::$dbadvert2;
        else
        {
            self::$dbadvert2 = Yii::app()->dbadvert2;
            if (self::$dbadvert2 instanceof CDbConnection)
            {
                self::$dbadvert2->setActive(true);
                return self::$dbadvert2;
            }
            else
                throw new CDbException(Yii::t('yii','Active Record requires a "db" CDbConnection application component.'));
        }
    }
    
}   