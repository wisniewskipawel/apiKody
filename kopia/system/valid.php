<?php
class valid {
    public function text($text){
        
            if(preg_match('/[a-z]$/',$text)) {     
            return TRUE;
            }  else {
                return FALSE;
            }
    }
    
    public function key($key) {

            if(preg_match('/[";",":","%","?",".",","]$/',$key)) {  
                return FALSE;
              }  else {
                return TRUE;
            }    
    }
    
    public function email($email) {
            if(preg_match('/^[a-zA-Z0-9.\-_]+@[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,4}$/', $email)){
                return TRUE;
            }  else {
                return FALSE;
            }
    }
}
?>