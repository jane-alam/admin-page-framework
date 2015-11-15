<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for creating meta boxes for post types.
 * 
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework
 * @subpackage      UserMeta
 */
abstract class AdminPageFramework_UserMeta extends AdminPageFramework_UserMeta_Controller {
    
    /**
     * Defines the class object structure type.
     * 
     * This is used to create a property object as well as to define the form element structure.
     * 
     * @since       3.5.0
     * @since       DEVVER      Changed the name from `$_sStructureType`.
     * @internal
     */
    static protected $_sStructureType = 'user_meta';
    
    /**
     * Sets up the property objects.
     * 
     * <h4>Example<h4>
     * <code>
     * new APF_MyUserMeta( 'manage_options' );
     * </code>
     * @since       3.5.0
     * @todo        Examine the appropriate default capability level.
     */
    public function __construct( $sCapability='edit_user', $sTextDomain='admin-page-framework' ) {
        
        $this->oProp = new AdminPageFramework_Property_UserMeta( 
            $this,                  // the caller object
            get_class( $this ),     // the caller class name    
            $sCapability,           // the capability level
            $sTextDomain,           // the text domain
            self::$_sStructureType     // the fields type
        );     
        
        parent::__construct( $this->oProp );
        $this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" );
        
    }
    
}