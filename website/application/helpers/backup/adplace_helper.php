<?php

    if ( !defined( 'BASEPATH' ) )
        exit( 'No direct script access allowed' );

    /**
     * Widget Helper

     */
    class Adplace
    {

        private static $name;

        function printAds( $planId ,$menu_id = null, $b_lazy_load = true, $s_ci_key = '0' )
        {
            $b_lazy_load = false;
            $b_found_cache = false;
            $cache_name = "AD_" . $planId;
            
            $CI = & get_instance();
            //$CI->cache->delete($cache_name);
            if ( $ad_data = $CI->cache->get($cache_name) )
            {
                print $ad_data;
                return $ad_data;
            }
            else
            {
                $CI->load->model( 'post', 'model' );
                $obj_post = new Post_model();

                if ( !isset( $arAds ) && empty( $name ) )
                {
                    $arAds = $obj_post->getAllAds( $planId, $s_ci_key );
                    self::$name = $arAds;
                }
                else
                {
                    #Under Contruction.Do not Park Here.
                    echo "POOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO";
                    exit;
                }
            }
            $arMenu = array();
            if($menu_id != null)
            {
               $cache_menu_name = "AD_MENU_" . $planId . "_" . $menu_id;
               //$CI->cache->delete($cache_menu_name);
               if ( $ad_menu_data = $CI->cache->get($cache_menu_name) )
               {
                   return $ad_menu_data;
               }
               else
               {
                   $arMenu = $obj_post->getMenuHasAds( $menu_id );
               }
//               echo count($arMenu);
            }
            
//            echo "<pre>";
//            print_r( $arMenu );
            //exit;
            
            $i = 1;
            $ads_html = "";
            //echo "COC".$planId."POPO";
            $count_ad_number = count($arAds->result());
            if($count_ad_number>0)
            {
                foreach ( $arAds->result() as $row )
                {
                    if ( isset($arAdsSection->num_rows) && $arAdsSection->num_rows > 0 )
                    {
                        foreach ($arAdsSection->result() as $rowsection)
                        {
                            if($rowsection->ad_id == $row->id && $row->plan_id == 17 && ($i <= $row->qty))
                            {
                                $ads_html .= " <a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>";
                                $i++;
                            }
                            else if($rowsection->ad_id == $row->id && ($row->plan_id == 34 || $row->plan_id == 35 || $row->plan_id == 27 || $row->plan_id == 28) && ($i <= $row->qty))
                            {
                                $ads_html .= " <a style='margin-bottom:10px;' href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>";
                             $i++;
                            }
                            else if(($rowsection->ad_id == $row->id)&&(($row->plan_id == 18) || ($row->plan_id == 19) || ($row->plan_id == 21) && ($i <= $row->qty)) )
                            {
                                $ads_html .= ($i == 1)?"<div class='ym-gbox adds-".$planId."'><center><ul>":"";
                                $ads_html .= '<li style="'.(($row->plan_id == 16)?'':'float: left;').'list-style: none;'.(($i != 1)?'margin-left:10px;':'').'">'.(!empty($row->url_link)?"<a href='".$row->url_link."' target='_blank'>":"") . $this->getHtml( $row, $b_lazy_load ) . (!empty($row->url_link)?"</a>":"").'</li>';
                                $ads_html .= ($i == $count_ad_number)?'</ul></center></div>':'';                        
                                $i++;
                            }
                            else if(($rowsection->ad_id == $row->id) && ($row->plan_id == 22 || $row->plan_id == 25 || $row->plan_id == 30 || $row->plan_id == 33) && ($i <= $row->qty))
                            {
                                $ads_html .= "<dd>"."<a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>". "</dd>";
                            }
                                
                        }
                    }
                    else if ( count($arMenu) > 0 && $row->plan_id == 9)
                    {
                        #Under Contruction.Do not Park Here. 
                        if($arMenu->ad_id == $row->id)
                        {
                          $ads_menu_html = "<span class='sub-menu-news-part2 sub-menu-news-part2-last'>"."<a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."'>".$this->getHtml( $row, $b_lazy_load ). "</a><h1>Sponsored by lux</h1></span>";
                          $CI->cache->save($cache_menu_name, $ads_menu_html, 86400 * 30 * 12);
                          return $ads_menu_html;
                        }
                    }
                    else
                    {
                        if(( $row->plan_id == 1 ) && ($i <= $row->qty))
                        {
                            $ads_html .= " <a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>";
                             $i++;
                        }
                        else if ( ( ( $row->plan_id == 1 )|| ($row->plan_id == 3)|| ($row->plan_id == 4)|| ($row->plan_id == 5)|| ($row->plan_id == 6) || ($row->plan_id == 16) || ($row->plan_id == 20) || ($row->plan_id == 29) ) && ($i <= $row->qty) )
                        {
                            $ads_html .= ($i == 1)?"<div class='ym-gbox adds-".$planId."'><center><ul>":"";
                            $ads_html .= '<li style="'.(($row->plan_id == 16)?'':'float: left;').'list-style: none;'.(($i != 1)?'margin-left:10px;':'').'">'.(!empty($row->url_link)?"<a href='".$row->url_link."' target='_blank'>":"") . $this->getHtml( $row, $b_lazy_load ) . (!empty($row->url_link)?"</a>":"").'</li>';
                            $ads_html .= ($i == $count_ad_number)?'</ul></center></div>':'';                        
                            $i++;
                        }
                        else if( (($row->plan_id == 7)|| ($row->plan_id == 13)|| ($row->plan_id == 8)) && ($i <= $row->qty)  )
                        {
                            $ads_html .= (!empty($row->url_link)?"<a href='".$row->url_link."' target='_blank'>":"") . $this->getHtml( $row, $b_lazy_load ) . (!empty($row->url_link)?"</a>":"");
                            $i++;
                        }
                        else if ( ( $row->plan_id == 10 || $row->plan_id == 11 || $row->plan_id == 12 || $row->plan_id == 14) && ($i <= $row->qty) )
                        {
                            $ads_html .= "<dd>"."<a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>". "</dd>";
                        }
                        else if($row->plan_id == 15 && ($i <= $row->qty))
                        {
                            $ads_html .= ($i == 1)?'<div class="header-bottom-adds" style="height:63px;margin-top:5px;"><ul>':'';
                            $ads_html .= ($i == 1)?'<li style="float: left;list-style: none;">'."<a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>".'</li>':'';
                            $ads_html .= ($i == 2)?'<li style="float: right;list-style: none;">'."<a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>".'</li>':'';
                            $ads_html .= ($i == $count_ad_number)?'</ul></div>':'';
                            $i++;
                        }
                        else if($row->plan_id == 2 && ($i <= $row->qty))
                        {                        
                            $ads_html .= ($i == 1)?'<div class="search-adds"><ul class="search-adds-list">':'';
                            $ads_html .= ($i == 1)?'<li>'."<a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>".'</li>':'';
                            $ads_html .= ($i == 2)?'<li style="margin-right:8px;">'."<a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>".'</li>':'';
                            $ads_html .= ($i == $count_ad_number)?'</ul></div>':'';
                            $i++;
                        }
                        else if(($row->plan_id == 22 || $row->plan_id == 25  || $row->plan_id == 30 || $row->plan_id == 33 ) && ($row->for_all==1) && ($i <= $row->qty))
                        {                            
                            $ads_html .= "<dd>"."<a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>". "</dd>";
                        }
                        else if(($row->plan_id == 27 || $row->plan_id == 28 || $row->plan_id == 35) && ($row->for_all==1) && ($i <= $row->qty))
                        {
                            $ads_html .= " <a href='".(!empty($row->url_link)?$row->url_link:"javascript:(void);")."' ".(!empty($row->url_link)?"target='_blank'":"").">" . $this->getHtml( $row, $b_lazy_load ) . "</a>";
                            $i++;
                        }
                    }
                }
            }
            $CI->cache->save($cache_name, $ads_html, 86400 * 30 * 12);
            echo $ads_html;
        }

        function getHtml( $row, $b_lazy_load )
        {
            if ( $row->type == 1 )
            {
                $s_image_path = "upload/ads/" . $row->image_path;
                $ar_path = pathinfo($s_image_path);
                $s_desctination_path = str_replace( "." . $ar_path['extension'],  "-3c." . $ar_path['extension'], $s_image_path);
                if ( !file_exists($s_desctination_path) )
                {
                    $image_url = base_url() . $s_image_path; 
                }
                else
                {
                    $image_url = base_url() . $s_desctination_path; 
                }
//                if ( !file_exists($s_desctination_path) )
//                {
//                    super_compress(realpath($s_image_path), "/" . $s_desctination_path);
//                    $image_url = base_url() . $s_desctination_path; 
//                }
//                else
//                {
//                    $image_url = base_url() . $s_desctination_path; 
//                }
                if ( $b_lazy_load )
                {
                    return "<img data-original='" . $image_url . "' class='lazy-load-ad' alt='".$row->image_path."'/>";
                }
                else
                {
                    return "<img src='" . $image_url . "' alt='".$row->image_path."'/>";
                }
            }
            else if ( $row->type == 2 )
            {
                return $row->html_code;
            }
            else
            {
                return false;
            }
        }

    }

    /* End of file widget_helper.php */
/* Location: ./application/helpers/widget_helper.php */  
