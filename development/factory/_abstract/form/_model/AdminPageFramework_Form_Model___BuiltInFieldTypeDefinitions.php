<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to retrieve built-in filed type definitions array.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form_Model___BuiltInFieldTypeDefinitions {

    /**
     * Holds the built-in filed type slugs.
     * 
     * @since       2.1.5
     * @since       DEVVER      Changed the name from `$aDefaultFieldTypeSlugs`. Moved from `AdminPageFramework_FieldTypeRegistration`.
     */
    static protected $_aDefaultFieldTypeSlugs = array(
        'default', // undefined ones will be applied 
        'text',
        'number',
        'textarea',
        'radio',
        'checkbox',
        'select',
        'hidden',
        'file',
        'submit',
        'import',
        'export',
        'image',
        'media',
        'color',
        'taxonomy',
        'posttype',
        'size',
        'section_title', // 3.0.0+
        'system',        // 3.3.0+
    );    
    
    public $sCallerID = '';
    
    public $oMsg;
    
    /**
     * Sets up properties.
     * 
     * @param       string      $sCallerID        The call ID, usually the caller class name.
     * @param       object      $oMsg       A message object that field types refer to. 
     * Field types will show system messages to the user using the message defined in this object.
     * @since       DEVVER
     */    
    public function __construct( $sCallerID, $oMsg ) {
        $this->sCallerID    = $sCallerID;
        $this->oMsg         = $oMsg;
    }
        
    /**
     * Returns a field type definitions array.
     * 
     * @since       3.1.3       Moved from the constructor.
     * @since       DEVVER      Moved from `AdminPageFramework_FieldTypeRegistration`. Change the name from `register()`.
     * @return      array       The field type definitions array.
     */
    public function get() {
        
        $_aFieldTypeDefinitions = array();
        foreach( self::$_aDefaultFieldTypeSlugs as $_sFieldTypeSlug ) {
            
            $_sFieldTypeClassName = "AdminPageFramework_FieldType_{$_sFieldTypeSlug}";
            if ( ! class_exists( $_sFieldTypeClassName ) ) { 
                continue; 
            }

            $_oFieldType = new $_sFieldTypeClassName( 
                $this->sCallerID, 
                null, 
                $this->oMsg, 
                false           // `false` to disable auto-registering.     
            );    
            foreach( $_oFieldType->aFieldTypeSlugs as $_sSlug ) {     
                $_aFieldTypeDefinitions[ $_sSlug ] = $_oFieldType->getDefinitionArray();
            }
        }
        return $_aFieldTypeDefinitions;

    }    

}