<?php

/**
 * This is the model class for table "events".
 *
 * The followings are the available columns in table 'events':
 * @property integer $id
 * @property integer $event_category_id
 * @property string $title
 * @property string $description
 * @property string $start_date
 * @property string $end_date
 * @property integer $is_common
 * @property integer $is_holiday
 * @property integer $is_exam
 * @property integer $is_due
 * @property string $created_at
 * @property string $updated_at
 * @property integer $origin_id
 * @property string $origin_type
 * @property integer $school_id
 */
class Spellingbee extends CActiveRecord
{

    public $rank = 0;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tds_spellingbee';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
        
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    public function getWordsByLevel( $iLevel, $iMaxWord = 20, $iYear = 2013  )
    {
        $strDestination = CHAMPS21_VAR_DIR . DS . 'upload';
        $strPath = implode( DS, array("userspelling", "front") );
        Champs21_Utility_File::createDirs( $strDestination, $strPath );
        $strDestinationDir = Champs21_Utility_File::makePath( $strDestination . DS . $strPath );
        $iUserID = Champs21_Utility_Session::getValue( 'userInfo', 'userid' );
        if ( empty( $iUserID ) )
            $iUserID = session_id();
        $strFileName = $strDestinationDir . DS . $iUserID . "_spellids.txt";
        $strFileName = str_replace( "./", "", $strFileName );
        //echo $strFileName;
        if ( file_exists( $strFileName ) )
        {
            try
            {
                $hFile = fopen( $strFileName, 'r' );
                $strUsedIds = fread( $hFile, filesize( $strFileName ) );
            }
            catch ( Exception $e )
            {
                $strUsedIds = '0';
            }
        }
        else
        {
            $strUsedIds = '0';
        }

        $strExtraSQL = "";
        if ( $iLevel == 0 )
        {
            $strExtraSQL = " AND spell_date = '" . date( "Y-m-d" ) . "'";
        }
        $strSQL = sprintf( "SELECT * FROM spellingbee WHERE level = %d %s AND year = %d AND enabled = 1 ", $iLevel, $strExtraSQL, $iYear );
        $hRes = mysql_query( $strSQL );
        if ( mysql_num_rows( $hRes ) == 0 )
        {
            return false;
        }
        while ( $arRow = mysql_fetch_assoc( $hRes ) )
        {
            $arData[] = $arRow;
        }
        if ( isset( $arData ) )
        {
            shuffle( $arData );
            shuffle( $arData );
            $arTmpData = array_slice( $arData, 0, $iMaxWord );
            $arData = $arTmpData;

            $strUsedIds = ( $strUsedIds == "0" ) ? '' : $strUsedIds . ',';
            $strSpellIds = $strUsedIds;
            foreach ( $arData as $arSpellData )
            {
                $strSpellIds .= $arSpellData['id'] . ',';
            }
            $strSpellIds = substr( $strSpellIds, 0, -1 );
            $hFile = fopen( $strFileName, 'w+' );
            fwrite( $hFile, $strSpellIds );
            fclose( $hFile );
        }

        return (isset( $arData )) ? $arData : "no_data";
    }

}
