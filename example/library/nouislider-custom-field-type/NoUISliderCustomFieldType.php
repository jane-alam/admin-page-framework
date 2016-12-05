<?php
/**
 * Admin Page Framework
 *
 * Facilitates WordPress plugin and theme development.
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2016 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @package     AdminPageFramework
 */

if ( ! class_exists( 'NoUISliderCustomFieldType' ) ) :
/**
 * A field type that lets the user toggle a switch.
 * 
 * @since       3.8.6
 * @version     0.0.4
 * @remark      Requires Admin Page Framework 3.8.8 or above.
 */
class NoUISliderCustomFieldType extends AdminPageFramework_FieldType_text {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'no_ui_slider', );

    /**
     * Defines the default key-values of this field type settings.
     *
     * @remark\ $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        
        'attributes'    => array(
            'size'  => 12,
        ),
    
        /**
         * @see     http://refreshless.com/nouislider/slider-options/
         */
        'options'   => array(
            'range'         => array( // Slider can select '0' to '100'
                'min'   => 0,
                'max'   => 100,
            ),
            'step'          => 1, // Slider moves in increments of '1'         
        	'start'         => array( 0 ), // Handle start position
            
            // Custom options
            
            'round' => 0,  // for the number of digits to multiply to the actual result. e.g. 10.00 -> 10 for the value `2`.

            'interactive'   => array( false ),
        ),
        
        
    );
    
    protected function construct() {
    }
    
    /**
     * Loads the field type necessary components.
     */
    public function setUp() {}
          
            
    /**
     * Returns an array holding the urls of enqueuing scripts.
     * @return      array
     */
    protected function getEnqueuingScripts() {
        return array(
            array( 
                'src'           => $this->isDebugMode()
                    ? dirname( __FILE__ ) . '/no-ui-slider/js/nouislider.js'
                    : dirname( __FILE__ ) . '/no-ui-slider/js/nouislider.min.js',
                'in_footer'     => true,
                'dependencies'  => array( 'jquery' ) 
            ),
            dirname( __FILE__ ) . '/js/no-ui-slider-initializer.js',
        );
    }

    /**
     * @return      array
     */
    protected function getEnqueuingStyles() {
        return array(
            $this->isDebugMode()
                ? dirname( __FILE__ ) . '/no-ui-slider/css/nouislider.css'
                : dirname( __FILE__ ) . '/no-ui-slider/css/nouislider.min.css',
            dirname( __FILE__ ) . '/no-ui-slider/css/nouislider.pips.css',
            dirname( __FILE__ ) . '/no-ui-slider/css/nouislider.tooltips.css',
            dirname( __FILE__ ) . '/css/no-ui-slider-field-type.css',
        );
    }

    /**
     * Returns the output of the field type.
     * 
     * @return      string
     */
    public function getField( $aField ) {
        
        // Set required attributes.
        $aField[ 'attributes' ][ 'type' ]           = 'text';   // not `number` because noUISlider supports a format option that appends a unit like `10 pounds`.
        $aField[ 'attributes' ][ 'data-type' ]      = 'no_ui_slider';
        $aField[ 'attributes' ][ 'readonly' ]       = 'readonly';
        
        // Format the `options` argument.
        $aField[ 'options' ]    = $this->_getNoUISliderOptionsFormatted(
            $this->getElementAsArray( $aField, 'options' ),
            $aField
        );

        // Format the `label` argument.
        $aField[ 'label' ]      = $this->_getLabelsFormatted( $aField[ 'label' ], $aField );
        
        // Format the input attributes. This must be AFTER formatting the `label` argument.
        $aField[ 'attributes' ] = $this->_getAttributesFormatted( $aField );       
   
        $_aAttributes = array(
                'class' => $this->_getSliderElementClassSelectors( $aField ),
            ) + $this->getDataAttributeArray( $aField[ 'options' ] )
              + $this->getElementAsArray( $aField, array( 'attributes', 'slider' ) );

        return '<div ' . $this->getAttributes( $_aAttributes ) . ' ></div>'
            . parent::getField( $aField );            

    }

        /**
         * @return      string
         */
        private function _getSliderElementClassSelectors( $aField ) {
            $_sSelectors = 'no-ui-sliders';
            $_aPips      = $this->getElement( $aField[ 'options' ], 'pips' );
            if ( empty( $_aPips ) ) {
                return $_sSelectors;
            }
            if ( 'vertical' === $this->getElement( $aField[ 'options' ], 'orientation' ) ) {
                return $_sSelectors;
            }
            return $_sSelectors . ' has-pips'; 
        }    
        
        /**
         * Formats the `attributes` argument.
         * @return      array
         */
        private function _getAttributesFormatted( $aField ) {
            
            $_aLabels          = $this->getAsArray( 
                $aField[ 'label' ], 
                true    // preserve empty
            );  
            $_iNumberOfHandles = count( $_aLabels );
                        
            $_aAttributes      = array();
            foreach( $_aLabels as $_isIndex => $_sLabel ) {

                $_bInteractive     = ( boolean ) $this->getElement( $aField, array( 'options', 'interactive', $_isIndex ) );
                $_aInputAttributes = array(
                    'data-key'         => $_isIndex,
                    'data-handles'     => $_iNumberOfHandles,
                    'data-interactive' => $_bInteractive,
                    'readonly'         => $_bInteractive ? null : 'readonly',
                ) + $this->getElementAsArray( $aField, array( 'attributes', $_isIndex ) );
                
                // If the label is a single item, there is no nested attribute element.
                if ( 1 === $_iNumberOfHandles ) {
                    $_aAttributes = $_aInputAttributes;                        
                    break;
                }                
                
                $_aAttributes[ $_isIndex ] = $_aInputAttributes;
                
            }
            return $_aAttributes + $this->getAsArray( $aField[ 'attributes' ] );
            
        }
        
        /**
         * Formats the `label` argument.
         * 
         * This determines the number of input fields that store the selected numbers.
         * 
         * If only one label is set, the option structure of the field will be one dimension.
         * So just return the label itself. Otherwise, return an array holding labels.
         * 
         * @return      array|string
         */
        private function _getLabelsFormatted( $aLabels, $aField ) {
            $_aStart   = $this->getElementAsArray( $aField, array( 'options', 'start' ) );
            $_aLabels  = $this->getAsArray( 
                $aLabels, 
                true    // preserve empty
            );
            $_iHandles = count( $_aStart );
            $_aLabels = $_aLabels + array_fill( 
                0,          // start index
                $_iHandles ? $_iHandles : 1, // end index (must be a positive number)
                ''          // the value to fill
            );
            
            return 1 >= count( $_aLabels )
                ? $_aLabels[ 0 ]
                : $_aLabels;
        }    

    
        /**
         * 
         * @return      array
         */
        private function _getNoUISliderOptionsFormatted( $aOptions, $aField ) {
                        
            // Determine the position of the slider handles. Set the stored values to the `start` argument.
            $aOptions[ 'start' ]   = $this->_getHandlePositions( $aOptions, $aField );

            // Format the `connect` argument.
            $aOptions[ 'connect' ] = $this->_getConnectArgumentFormatted( $aOptions, $aField );
            
            return $aOptions;            
            
        }
        
            /**
             * Formats the `connect` argument to avoid errors on the JS script side.
             * 
             * @remark      This must be called AFTER the `start` argument is formatted as it counts the number of element of the `start` argument.
             * @return      array|null
             */
            private function _getConnectArgumentFormatted( $aOptions, $aField ) {

                $_iHandles = count( $aOptions[ 'start' ] );
                $_aConnect = $this->getElementAsArray( $aOptions, 'connect' );
                if ( empty( $_aConnect ) ) {
                    return null;
                }
                
                $_aFalses = array_fill( 0, $_iHandles + 1, false );
                return $_aConnect + $_aFalses;
                
            }
            
            /**
             * Retrieves the value of the `start` argument which determines the position of the slider handle.
             * @return      array
             */
            private function _getHandlePositions( $aOptions, $aField ) {

                // For the first time of loading the form, a value is not set.
                // If the value is not set, use the value set to the `start` argument.
                if ( ! isset( $aField[ 'value' ] ) ) {
                    return $this->getElementAsArray( $aOptions, 'start', array( 0 ) );
                }
                 
                return $this->getAsArray( 
                    $aField[ 'value' ], 
                    true    // preserve empty
                );  

            }
                        
}
endif;
