<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
<div class="ym-grid right-topic">
    <h1 style="color:#4D4D4D;"><?php echo $s_category_name; ?></h1>
    <div style="padding:8px;">
    <?php
        $category_html = "";
        if(isset($category)&& !empty($category))
        {   
            foreach ($category as $row)
            {
                if(!empty($row->lead_material))
                {
                    $category_html .= "<img src='".base_url().$row->lead_material."' width='102' class='floatLeft'/>";
                }
                else if(isset($row->image)&&!empty($row->image))
                {
                    $category_html .= "<img src='".$row->image."' width='102' class='floatLeft'/>";
                }
                else
                {
                    $category_html .= "<img src='".base_url()."styles/layouts/tdsfront/images/no_image/noimage_49x49.jpg' width='102' class='floatLeft' alt='".$row->headline."'/>";
                }
                $category_html .= "<div class='title'><h2 style='color:#4D4D4D;margin-top:5px;'>".create_link($s_ci_key, $row)."</h2></div>";
                $category_html .= "<p>".substr($row->content, 0, 150)."...</p>";
            }
        }
        else
        {
            $category_html .="<p>No Data Found.</p>";
        }
         echo $category_html;
    ?>   
    </div>
<!--<div class="ym-grid right-topic">
                <h1>category </h1>

                <img src="<?php echo  base_url()?>styles/layouts/tdsfront/images/category.png" alt="category" class="floatLeft" />

                <h2>
                    What went wrong for them?
                </h2>
                <p>Chowdhury and defeated mayor Badar Uddin Kamran too are seen each embracing ...</p>

            </div>

</div>   -->

