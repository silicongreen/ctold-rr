<?php
class MyActiveRecord extends CActiveRecord {
    private static $dbadvert = null;

    protected static function getAdvertDbConnection()
    {
        if (self::$dbadvert !== null)
            return self::$dbadvert;
        else
        {
            self::$dbadvert = Yii::app()->db;
            if (self::$dbadvert instanceof CDbConnection)
            {
                self::$dbadvert->setActive(true);
                return self::$dbadvert;
            }
            else
                throw new CDbException(Yii::t('yii','Active Record requires a "db" CDbConnection application component.'));
        }
    }
    
}   