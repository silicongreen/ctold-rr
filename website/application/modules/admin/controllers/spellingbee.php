<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class spellingbee extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->form_validation->CI = & $this;
    }

    /**
     * Index function
     * @param None
     * @defination use for showing table header and setting table id and filtering for admin category
     * @author Fahim
     */
    public function index()
    {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1"  class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('ID','Word', 'Bangla Meaning', 'Type', 'Level', 'Status','Year','Sound Problem', 'Action');

        
        $data['datatableSortBy'] = 0;
        $data['datatableSortDirection'] = 'DESC';
        
        $this->render('admin/spellingbee/index', $data);
    }

    /**
     * datatable function
     * @param none
     * @defination use for showing datatable of category with child tree callback function
     * @author Fahim
     */
    public function datatable()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        
        $this->datatables->set_buttons("change_status", "ajax");
        $this->datatables->set_controller_name("spellingbee");
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_custom_string(4, array(0 => "Easy", 1 => "Medium", 2 => "Hard", 3 => "Extreme Hard"));
        $this->datatables->set_custom_string(5, array(0 => "Disabled", 1 => "Enabled"));
        $this->datatables->set_custom_string(7, array(1 => "Yes", 0 => "No"));
        #$this->datatables->set_custom_string(8, array(1 => "Word Bank", 2 => "Others", 3 => "Daily Star"));
        
        $this->datatables->select('spellingbee.id as primary_id, spellingbee.word, spellingbee.bangla_meaning, spellingbee.wtype, spellingbee.level, spellingbee.enabled,spellingbee.year,spellingbee.has_problem')
                ->from('spellingbee');

        echo $this->datatables->generate();
    }

    /**
     * add function
     * @param none
     * @defination use for insert Category and child category as tree
     * @author Fahim
     */
    public function add()
    {
        $obj_spell = new Spelling_bee();
        if ( !empty($_POST) )
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_spell->$key = $value;
            }    
        }
        
        $data['b_edit'] = FALSE;
        $data['model'] = $obj_spell;
        
        if (!$obj_spell->save())
        {
            $this->render('admin/spellingbee/form', $data);
        }
        else
        {
            $status = $this->_downloadMP3($obj_spell->word);
            if(!$status)
            {
                $obj_spell = new Spelling_bee($obj_spell->id);
                $obj_spell->has_problem = 1;
                $obj_spell->save();
            }    
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * edit function
     * @param none
     * @defination use for edit category
     * @author Fahim
     */
    public function edit($id)
    {

        $obj_spell = new Spelling_bee($id);
        if ( !empty($_POST) )
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_spell->$key = $value;
            }
        }
        
        $data['b_edit'] = TRUE;
        $data['model'] = $obj_spell;
        
        if (!$obj_spell->save() || !$_POST)
        {
            $this->render('admin/spellingbee/form', $data);
        }
        else
        {
            $status = $this->_downloadMP3($obj_spell->word);
            if(!$status)
            {
                $obj_spell = new Spelling_bee($obj_spell->id);
                $obj_spell->has_problem = 1;
                $obj_spell->save();
            }
            else
            {
                $obj_spell = new Spelling_bee($obj_spell->id);
                $obj_spell->has_problem = 0;
                $obj_spell->save();
            }    
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    
    
    public function downloadExcle()
    {
        $this->db->where("has_problem",0);
        $this->db->where("enabled",1);
        $this->db->order_by("year", "asc");
        $spell_words = $this->db->get("spellingbee")->result();
        //load our new PHPExcel library
        $this->load->library('excel');
        //activate worksheet number 1
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Spelling Bee');
        //set cell A1 content with some text
        $this->excel->getActiveSheet()->setCellValue('A1', 'Word');
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        
        $this->excel->getActiveSheet()->setCellValue('B1', 'Bangla');
        $this->excel->getActiveSheet()->getStyle('B1')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
        
        $this->excel->getActiveSheet()->setCellValue('C1', 'Defination');
        $this->excel->getActiveSheet()->getStyle('C1')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
        
        $this->excel->getActiveSheet()->setCellValue('D1', 'Sentence');
        $this->excel->getActiveSheet()->getStyle('D1')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
        
        $this->excel->getActiveSheet()->setCellValue('E1', 'Type');
        $this->excel->getActiveSheet()->getStyle('E1')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
        
        $this->excel->getActiveSheet()->setCellValue('F1', 'Level');
        $this->excel->getActiveSheet()->getStyle('F1')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
        
        
        $this->excel->getActiveSheet()->setCellValue('G1', 'Year');
        $this->excel->getActiveSheet()->getStyle('G1')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
        
        
        $type = array("Easy","Normal","Hard","Vary Hard");
        $i = 1;
        if(count($spell_words)>0)
        {
            foreach($spell_words as $value)
            {
                $i++;
                $this->excel->getActiveSheet()->setCellValue('A'.$i, $value->word);
                $this->excel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(20);

                $this->excel->getActiveSheet()->setCellValue('B'.$i, $value->bangla_meaning);
                $this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setSize(20);

                $this->excel->getActiveSheet()->setCellValue('C'.$i,$value->definition);
                $this->excel->getActiveSheet()->getStyle('C'.$i)->getFont()->setSize(20);

                $this->excel->getActiveSheet()->setCellValue('D'.$i, $value->sentence);
                $this->excel->getActiveSheet()->getStyle('D'.$i)->getFont()->setSize(20);
               
                
                $level = "None";
                if(array_key_exists($value->level, $type))
                {
                    $level = $type[$value->level];
                }        

                $this->excel->getActiveSheet()->setCellValue('E'.$i, $value->wtype);
                $this->excel->getActiveSheet()->getStyle('E'.$i)->getFont()->setSize(20);

                $this->excel->getActiveSheet()->setCellValue('F'.$i, $level);
                $this->excel->getActiveSheet()->getStyle('F'.$i)->getFont()->setSize(20);


                $this->excel->getActiveSheet()->setCellValue('G'.$i, $value->year);
                $this->excel->getActiveSheet()->getStyle('G'.$i)->getFont()->setSize(20);
            }    
        }    
        
        //set aligment to center for that merged cell (A1 to D1)
        
        $filename = 'spellingbee.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //force user to download the Excel file without writing it to server's HD
        $objWriter->save('php://output');

    }

/**
     * delete function
     * @param None
     * @defination use for delete change status of the work enable/disable
     * @author Fahim
     */
    function change_status()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_spellingbee = new Spelling_bee($this->input->post('primary_id'));
       
        if($obj_spellingbee->enabled)
        {
            $status = 0;
        }    
        else
        {
            $status = 1;
        }    
        
        $data  = array('enabled' =>$status);
        $where = "id = ".$this->input->post('primary_id');

        $str   = $this->db->update_string('tds_spellingbee', $data, $where);
        $this->db->query($str);
        
        
        echo 1;
    }
    
    function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_spellingbee = new Spelling_bee($this->input->post('primary_id'));
        $obj_spellingbee->delete();
        echo 1;
    }
    
    function _downloadMP3( $strWord )
    {
       
        $strDestination = "upload/spellingbee";
        if (!is_dir($strDestination))
        {
            @mkdir($strDestination, 0777, true);
        }
        $strMusicFile = $strDestination."/".strtolower( trim( $strWord ) ) . ".mp3";
        $sound_status = 1;
        @unlink($strMusicFile);
        if(!is_file($strMusicFile))  
        {
            $objCURL = curl_init( "http://translate.google.com/translate_tts?q=" . str_replace( " ", "+", strtolower( trim( $strWord ) ) ) . "&tl=en" );
            $fp = fopen( $strMusicFile, "w+" );

            curl_setopt( $objCURL, CURLOPT_FILE, $fp );
            curl_setopt( $objCURL, CURLOPT_HEADER, 0 );

            curl_exec( $objCURL );
            $curl_status = curl_getinfo ($objCURL);
            if($curl_status['http_code']==200)
            {
                $sound_status = 1;
                curl_close( $objCURL );
                fclose( $fp );
            }
            else
            {
                $sound_status = 0;
                curl_close( $objCURL );
                fclose( $fp );
                @unlink($strMusicFile);
            } 
            
        }
        if($sound_status==0)
        {
            @unlink($strMusicFile);
            $file_response = $this->_download_bing_audio($strWord);
            if($file_response)
            {
                $sound_status = 1;
                file_put_contents( $strMusicFile, $file_response );
            }   
        } 
        
        return $sound_status;
      
    }
    public function all_xml()
    {
        $zipname = 'spelling_bee.zip';
        $zip = new ZipArchive;
        $zip->open($zipname, ZipArchive::CREATE);
        if ($handle = opendir('upload/spellingbee/xml'))
        {
            while (false !== ($entry = readdir($handle)))
            {
                if ($entry != "." && $entry != ".." && !strstr($entry,'.php')) {
              
                    $zip->addFile("upload/spellingbee/xml/".$entry);
                }
            }
            closedir($handle);
        }

        $zip->close();

        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename='spelling_bee.zip'");
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
    }
    
    public function xml_create()
    {
        @chmod("upload", 0777);
        @chmod("upload/spellingbee", 0777);
        @chmod("upload/spellingbee/xml", 0777);
        $this->db->select("DISTINCT year",false);
        $yesrs = $this->db->get("spellingbee")->result();
        $xmlstr = '<?xml version="1.0" encoding="UTF-8"?>'.
              '<set xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"></set>';
        if(count($yesrs)>0)
        {
            $sxe   = new SimpleXMLElement($xmlstr);
            $years = $sxe->addChild('years'); 
            foreach($yesrs as $item) {
                $sale = $years->addChild('year',$item->year);
                
                $this->db->where("has_problem",0);
                $this->db->where("enabled",1);
                $this->db->where("year",$item->year);
                $spell_word = $this->db->get("spellingbee")->result();
                
                #print_r($spell_word);
                
                $wordsxe = new SimpleXMLElement($xmlstr);
                
                if(count($spell_word)>0)
                {
                    foreach($spell_word as $value)
                    {
                        $data = $wordsxe->addChild('data');
                        $data->addAttribute('id', $value->id);
                        $data->addChild('word',$value->word);
                        $data->addChild('bangla_meaning',$value->bangla_meaning);
                        $data->addChild('definition',$value->definition);
                        $data->addChild('sentence',$value->sentence);
                        $data->addChild('wtype',$value->wtype);
                        $data->addChild('level',$value->level);
                        $data->addChild('year',$value->year);
                        $data->addChild('source',$value->source);
                    } 
                    $strDestination = "upload/spellingbee/xml";
                    if (!is_dir($strDestination))
                    {
                       mkdir($strDestination, 0777, true);
                    }
                    
                    
                    $xmlFileword = $strDestination."/word_".$value->year.".xml";
                    
                    if(is_file($xmlFileword))
                    {
                        @unlink($xmlFileword);
                    }    

                    $dom_word = dom_import_simplexml($wordsxe)->ownerDocument;
                    $dom_word->formatOutput = TRUE;
                    $formatted_word = $dom_word->saveXML();
                    

                    file_put_contents( $xmlFileword, $formatted_word );
                }    
                
                
                

            }
          
        
            $strDestination = "upload/spellingbee/xml";
            if (!is_dir($strDestination))
            {
                @mkdir($strDestination, 0777, true);
            }
            $xmlFile = $strDestination."/year.xml";
            
            if(is_file($xmlFile))
            {
                @unlink($xmlFile);
            }
           

            $dom = dom_import_simplexml($sxe)->ownerDocument;
            $dom->formatOutput = TRUE;
            $formatted = $dom->saveXML();
            
            file_put_contents( $xmlFile, $formatted );
            
            echo 1;
        }

    }

    
    function _download_bing_audio($strWord)
    {
        $sound_status = 0;
        $clientID = '00000000480E8A3E';
        $clientSecret = 'VvjLerJ7T8v5X4g+9r4WKWOG3Ih0yNEwz2tpveoYmsw=';
        $authUrl = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/';
        $scopeUrl = 'http://api.microsofttranslator.com';
        $grantType = 'client_credentials';
        $accessToken = getBingTokens( $grantType, $scopeUrl, $clientID, $clientSecret, $authUrl );
        if($accessToken)
        {
            try
            {
                $strLang = 'en';
                $strAuthHeader = "Authorization: Bearer " . $accessToken;
                $strParams = "text=" . urlencode( $strWord ) . "&language=" . $strLang . "&format=audio/mp3";
                $strURL = "http://api.microsofttranslator.com/V2/Http.svc/Speak?" . $strParams;
                $strResponse = curlRequest( $strURL, $strAuthHeader );
                return $strResponse;
            }
            catch ( Exception $e )
            {
                return false;
            }
        }
        else
        {
            return false;
        } 
        return false;
    }
    
    function playaudio()
    {
       
        $url = "http://translate.google.com/translate_tts?q=" . str_replace( " ", "+", strtolower( trim( $_GET["q"] ) ) ) . "&tl=en" ;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $soundfile = curl_exec($curl);
        $curl_status = curl_getinfo ($curl);
        curl_close($curl);
        
        if($curl_status['http_code']!=200)
        {
           $soundfile = $this->_download_bing_audio($_GET["q"]);
        }
        header("Content-type: audio/mpeg");
        header("Content-Transfer-Encoding: binary");
        header('Pragma: no-cache');
        header('Expires: 0');
        echo($soundfile);
        
    }
    
    

}

?>
