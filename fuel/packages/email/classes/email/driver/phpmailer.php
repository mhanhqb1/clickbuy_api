<?php

/**
 * Send mail with PHPMailer
 *
 * @package		FastNail
 * @version		1.0
 * @author		thailvn@gmail.com
 * 
 */
class Email_Driver_Phpmailer extends \Email_Driver {

    public $config = null;
    public $phpmailer = null;
    
    public function __construct($config = array()) {
        Package::load('phpmailer');  
        $this->config = $config;                
        $this->phpmailer = new PHPMailer();   
        $this->phpmailer->IsSMTP(true);
        foreach ($config['phpmailer'] as $key => $value) {
            $this->phpmailer->{$key} = $value;
        } 
    }

    /**
     * Sends the email using the phpmailer
     * 
     * @return boolean	True if successful, false if not.
     */
    protected function _send() {
        $this->phpmailer->SetFrom($this->get_from()['email'], $this->get_from()['name']);        
        foreach ($this->get_to() as $t) {
            $this->phpmailer->AddAddress($t['email'], $t['name']); 
        }
        $this->phpmailer->Subject = $this->subject;   
        $this->phpmailer->MsgHTML($this->body);

        //add to
        $this->addTo();
        //add cc
        $this->addCC();
        //add bcc
        $this->addBCC();
        //add attachment
        $this->addAttachment();
        
        if (!$this->phpmailer->Send()) {    
            $errorInfo = $this->phpmailer->ErrorInfo;
            $this->phpmailer->smtpClose();
            throw new \SendmailFailedException($errorInfo);            
        }
        $this->phpmailer->smtpClose();
        return true;
    }
    
    /**
     * Add "To" address.
     * @return boolean true on success, false if address already used
     */
    protected function addTo() {
        $toList = $this->get_to();
        if(!empty($toList)){
            foreach($toList as $to){
                $this->phpmailer->addAddress($to['email'], is_string($to['name'])? $to['name'] : '');
            }
            return true;
        }
        return false;
    }
    
    /**
     * Add "CC" address.
     * @return boolean true on success, false if address already used
     */
    protected function addCC() {
        $ccList = $this->get_cc();
        if(!empty($ccList)){
            foreach($ccList as $cc){
                $this->phpmailer->addCC($cc['email'], is_string($cc['name'])? $cc['name'] : '');
            }
            return true;
        }
        return false;
    }
    
    /**
     * Add "BCC" address.
     * @return boolean true on success, false if address already used
     */
    protected function addBCC() {
        $bccList = $this->get_bcc();
        if(!empty($bccList)){
            foreach($bccList as $bcc){
                $this->phpmailer->addBCC($bcc['email'], is_string($bcc['name'])? $bcc['name'] : '');
            }
            return true;
        }
        return false;
    }
    
    /**
     * Add attachment 
     * @return boolean
     */
    protected function addAttachment(){
        $attachList = $this->get_attachment();
        if(!empty($attachList['attachment'])){
            foreach ($attachList['attachment'] as $attach){
                $path = $attach['file'][0];
                $name = $attach['file'][1];
                $encoding = 'base64';
                $type = '';
                $disposition = !empty($attach['disp']) ?$attach['disp'] : 'attachment';
                $this->phpmailer->addAttachment($path, $name, $encoding, $type, $disposition);
            }
            return true;
        }
        return false;
    }

}
