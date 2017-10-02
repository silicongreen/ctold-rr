<?php

final class NF_Conversion_Calculations implements NF_Conversion
{
    private $operations = array(
        'add' => '+',
        'subtract' => '-',
        'multiply' => '*',
        'divide' => '/'
    );
    private $form = array();

    private $tax_rate;

    public function __construct( $form_data )
    {
        $this->form = $form_data;
    }

    public function run()
    {
        // Extract Calculations from Fields
        foreach( $this->form[ 'fields' ] as $key => $field ){

            if( 'tax' == $field[ 'type' ] ){
                $this->set_tax( $field[ 'default_value' ] );
                continue;
            }

            if( 'calc' != $field[ 'type' ] ) continue;

            $calculation = array(
                'order' => $key,
                'name'  => $field[ 'key' ],
                'eq'    => '',
                'dec' => $field[ 'calc_places' ]
            );

            switch( $field[ 'calc_method' ] ){
                case 'eq':
                    $calculation[ 'eq' ] = $field[ 'calc_eq' ];
                    break;
                case 'fields':
                    $calculation[ 'eq' ] = trim( array_reduce( $field[ 'calc' ], array( $this, 'reduce_operations' ), '' ) );
                    break;
                case 'auto':
                    $calculation[ 'eq' ] = trim( array_reduce( $this->form[ 'fields' ], array( $this, 'reduce_auto_total' ), '' ) );
                    break;
            }

            // Remove any leading `+`.
            $calculation[ 'eq' ] = ltrim( $calculation[ 'eq' ], '+' );

            // Handle opinionated "Total" calc and tax field.
            if( 'total' == $field[ 'calc_name' ] && isset( $this->tax_rate ) ){
                $calculation[ 'eq' ] = "( {$calculation[ 'eq' ]} ) * {$this->tax_rate}";
            }

            $this->form[ 'settings' ][ 'calculations' ][] = $calculation;
        }

        // Replace Field IDs with Merge Tags
        if( isset( $this->form[ 'settings' ][ 'calculations' ] ) ) {
            foreach ($this->form['fields'] as $field) {

                if( ! isset( $field[ 'id' ] ) ) continue;

                $search = 'field_' . $field['id'];
                $replace = $this->merge_tag( $field );

                foreach ($this->form['settings']['calculations'] as $key => $calculation) {
                    $this->form['settings']['calculations'][ $key ]['eq'] = str_replace($search, $replace, $calculation['eq']);
                }
            }
        }

        // Convert Calc Fields to HTML Fields for displaying Calculations
        foreach( $this->form[ 'fields' ] as $key => $field ){

            if( 'tax' == $field[ 'type' ] ){
                $this->form[ 'fields' ][ $key ][ 'type' ] = 'html';
                $this->form[ 'fields' ][ $key ][ 'default' ] = "<strong>{$field[ 'label' ]}</strong><br /> {$field[ 'default_value' ]}";
                continue;
            }

            if( 'calc' != $field[ 'type' ] ) continue;

            $this->form[ 'fields' ][ $key ][ 'type' ] = 'html';

            if( 'html' == $field[ 'calc_display_type' ] ){
                // TODO: HTML Output fields seem to loose the label.
                $search = '[ninja_forms_calc]';
                $replace = $this->merge_tag( $field );
                $subject = $field[ 'calc_display_html' ];
                $this->form[ 'fields' ][ $key ][ 'default' ] = str_replace( $search, $replace, $subject );
            } else {
                $this->form[ 'fields' ][ $key ][ 'default' ] = '<strong>' . $field[ 'label' ] . '</strong><br />' . $this->merge_tag( $field );
            }
        }

        return $this->form;
    }

    private function reduce_operations( $eq, $calc )
    {
        $operation = $calc[ 'op' ];
        return ' ' . $eq . $this->operations[ $operation ] . ' field_' . $calc[ 'field' ] . ' ';
    }

    private function reduce_auto_total( $eq, $field )
    {
        if( ! isset( $field[ 'calc_auto_include' ] ) || 1 != $field[ 'calc_auto_include' ] ) return $eq;
        return $eq . '+ {field:' . $field[ 'key' ] . ':calc} ';
    }

    private function merge_tag( $field )
    {
        $tag = $field[ 'key' ];
        if( 'calc' == $field[ 'type' ] ){
            return '{calc:' . $tag . '}';
        } else {
            return '{field:' . $tag . ':calc}';
        }
    }

    /**
     * Sets a float formatted tax rate.
     * @param string|int|float $tax
     *
     * @return float|int
     */
    private function set_tax( $tax )
    {
        // ex 15% -> 1.15
        return $this->tax_rate = ( floatval( $tax ) + 100 ) / 100;
    }
}

add_filter( 'ninja_forms_after_upgrade_settings', 'ninja_forms_conversion_calculations' );
function ninja_forms_conversion_calculations( $form_data ){
    $conversion = new NF_Conversion_Calculations( $form_data );
    return $conversion->run();
}
