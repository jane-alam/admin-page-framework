<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to handle a contact form.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model_FormSubmission_Validator_ContactFormConfirm extends AdminPageFramework_Model_FormSubmission_Validator_ContactForm {
    
    public $sActionHookPrefix = 'try_validation_after_';
    public $iHookPriority = 40;
    public $iCallbackParameters = 5;
    
    /**
     * Confirms contact form submission.
     * 
     * @internal
     * @since       3.5.3
     * @since       3.6.3       Moved from `AdminPageFramework_Validation`. Changed the name from `_confirmContactForm()`.
     * @return      void    
     * @internal
     * @callback    action      try_validation_after_{class name}
     */
    public function _replyToCallback( $aInputs, $aRawInputs, array $aSubmits, $aSubmitInformation, $oFactory ) {
                                
        if ( $this->oFactory->hasFieldError() ) {
            return;
        }
        $_bConfirmingToSendEmail    = ( bool ) $this->_getPressedSubmitButtonData( 
            $aSubmits, 
            'confirming_sending_email' 
        );
        if ( ! $_bConfirmingToSendEmail ) {
            return;
        }

        $this->oFactory->_setLastInputs( $aInputs );
        $this->oFactory->oProp->_bDisableSavingOptions = true;

        add_filter(
            "options_update_status_{$this->oFactory->oProp->sClassName}", 
            array( $this, '_replyToSetStatus' )
        );            
        
        // Go to the catch clause.
        $_oException = new Exception( 'aReturn' );  // the property name to return from the catch clasue.
        $_oException->aReturn = $this->_confirmSubmitButtonAction( 
            $this->getElement( $aSubmitInformation, 'input_name' ), 
            $this->getElement( $aSubmitInformation, 'section_id' ), 
            'email'  // type
        );
        throw $_oException;
        
    }   
        /**
         * @return      array
         * @since       3.6.3
         * @callback    filter      options_update_status_{class name}
         */
        public function _replyToSetStatus( $aStatus ) {
            return array( 
                'confirmation' => 'email' 
            ) + $aStatus;
        }
}