<?php

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

/**
 * A class to create a dropdown for all categories in your wordpress site
 */
 class Category_Dropdown extends WP_Customize_Control
 {
    private $aglee_lite_cats = false;

    public function __construct($manager, $id, $args = array(), $options = array())
    {
        $this->aglee_lite_cats = get_categories($options);

        parent::__construct( $manager, $id, $args );
    }

    /**
     * Render the content of the category dropdown
     *
     * @return HTML
     */
    public function render_content()
       {
            if(!empty($this->aglee_lite_cats))
            {
                ?>
                    <label>
                      <span class="customize-category-select-control"><?php echo esc_html( $this->label ); ?></span>
                      <select <?php $this->link(); ?>>
    
                            <option value=""><?php __('Choose Category','aglee-lite'); ?></option>
                           <?php
                                foreach ( $this->aglee_lite_cats as $aglee_lite_cat )
                                {
                                    printf('<option value="%s" %s>%s</option>', $aglee_lite_cat->term_id, selected($this->value(), $aglee_lite_cat->term_id, false), $aglee_lite_cat->name);
                                }
                           ?>
                      </select>
                    </label>
                <?php
            }
       }
 }
?>