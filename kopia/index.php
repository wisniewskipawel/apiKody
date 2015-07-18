<?php

error_reporting(E_ALL & ~E_NOTICE);

/*
 * Całość jest jeszcze przed testami.
 * Kominikaty błędów należy obsłużyć
 */
class login {
    private function autolad(){
       
                        
        spl_autoload_register(function ($class) {
            $file = 'system/'.$class.'.php';
            if(file_exists($file)){
                require_once $file;
            } else {
                require_once $class.'.php';
        }
        });  
   
    }

    public function setLogin() {
           //config nie jest klasą
         require_once 'system/config.php';
            $this->autolad();           
            $username = htmlspecialchars($_POST['username']);
            $key = htmlspecialchars($_POST['apiaccesskey']);
            if(htmlspecialchars($_POST['action'])=='accountinfo') {
                $action = 'account';
            }  else {
                $action = htmlspecialchars($_POST['action']);
            }
            $requestformat = strtolower($_POST['requestformat']);
            if($requestformat == 'xml') {
                $xml = new xmlapi();
            }elseif ($requestformat == 'json') {
                  $valid = new valid();
                  if($valid->email($username) && ($valid->key($key))) {
                       if($action){
                            switch ($action) {  
                                case 'account':
                                      $this->account($config,$key,$action);
                                    break;
                                case 'imeiservicelist':
                                     $this->imeiservicelist($config,$key);
                                    break;
                                case 'product':
                                   $this->product($config,$key,$action,$id=1);
                                    break;
                                case 'modellist':
                                    $this->modellist($config,$key);
                                    break;
                                default :
                                    echo json_encode(array('error'=>'Action not found'));
                                }
                        }
                  }  else {
                      echo json_encode(array('error'=>'Incorrect username or key.'));
                  }  
              }

        }         
        
        private function account($config,$key,$action) {
          
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$config[0]['adress'].'/api/'.$action.'.json?key='.$key.'&pretty=true');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            $result = curl_exec($ch);
            $array=json_decode($result);
            if($array->status == 'ok') {
                $resultaccount = array(
                    'SUCCESS' => array (array(
                        'message'=>'Your Accout Info',
                        'AccoutInfo'=>array(
                            'credit'=>$array->account->credits,
                            'mail'=>$array->account->email,
                            'currency'=>'PLN'
                            )
                          )
                       )
                     );
            }  else {
                $resultaccount = array(
                    'ERROR' =>'Action not found'
                  );
        }
        echo json_encode($resultaccount);
         }
        
        private function imeiservicelist($config,$key) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$config[0]['adress'].'/api/products.json?type=imei&key='.$key.'&pretty=true');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);      
            $result = curl_exec($ch);
            $resultimei = json_decode($result);
            if($resultimei->status == 'ok') {
               $size = count($resultimei->products);
            for($i=0;$i<$size;++$i){    
                $imieresult = array(
                    'ID'=>$resultimei->products[0]->id,   
                     'SUCCESS'=> array(array(
                               'MESSAGE'=>'MESSAGE',
                                'LIST'=>array(
                                    'Nazwa grupy'=>array(
                                       'GROUPNAME'=>$resultimei->products[0]->name,
                                       'SERVICES'=>array(
                                            $resultimei->products[0]->id=>array(
                                                    'discount'=>$resultimei->products[0]->description,
                                                    'standard_price'=>$resultimei->products[0]->standard_price,
                                                    'discount'=>$resultimei->products[0]->discount,
                                                    'discount_price'=>$resultimei->products[0]->discount_price,
                                                    'guarantee_return'=>$resultimei->products[0]->guarantee_return, // Cena (z uwzględnieniem rabatu użytkownika)
                                                    'guarantee_return_price'=>$resultimei->products[0]->guarantee_return_price, // Czas realizacji
                                                    'delivery_time'=>$resultimei->products[0]->delivery_time, // Opis
                                                    'status'=>$resultimei->products[0]->status, // None|Optional|Required
                                                    'required'=>array(
                                                        $resultimei->products[0]->required[0],
                                                        $resultimei->products[0]->required[1]
                                                            ),
                                                    'models'=>$resultimei->products[0]->name,

                                                    )
                                                )
                                             )
                                           )
                                         )
                                       )
                                    );
        }}
             else {
                 $imieresult = array(
                    'ERROR' => array(
                        'MESSAGE'=>'Action not found',
                    ),
 	
                    'ERROR'=>'Action not found',
                  );                      
         }

            echo json_encode($imieresult);
        }
                
        private function product($config,$key,$action,$id) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$config[0]['adress'].'/api/'.$action.'?id='.$id.'&key='.$key.'&pretty=true');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            $result = curl_exec($ch);
            $arrayproduct = json_decode($result);
            if($arrayproduct->status == 'ok') {
                $resultarray = array(
                    'SUCCESS'=>'Your Accout Info',
                    'product'=> array(
                     'ID'=>$arrayproduct->product->id,
                     'name'=>$arrayproduct->product->name,
                     'description'=>$arrayproduct->product->description,
                     'standard_price'=>$arrayproduct->product->standard_price,
                     'discount'=>$arrayproduct->product->discount,
                     'discount_price'=>$arrayproduct->product->discount_price,
                     'guarantee_return'=>$arrayproduct->product->guarantee_return,
                     'guarantee_return_price'=>$arrayproduct->product->guarantee_return_price,
                     'delivery_time'=>$arrayproduct->product->delivery_time,
                     'status'=>$arrayproduct->product->status,
                     'required'=> array(
                         $arrayproduct->product->required[0],
                         $arrayproduct->product->required[1],
                     ),
                    )
                );
            }         else {
                        $resultarray = array(
                            'ERROR' => array(
                                'MESSAGE'=>'Action not found'
                            )
                        );
         }
            echo json_encode($resultarray);
        }
        
        public function modellist($config,$key) {
            echo ($config[0]['adress'].'/api/?key='.$key.'&pretty=true');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$config[0]['adress'].'/api/?key='.$key.'&pretty=true');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            $result = curl_exec($ch);
            $arrayproduct = json_decode($result);
        }
    }



try {
$login = new login();
$login->setLogin();
} catch (Exception $ex) {
     echo(json_encode(array('error'=>$ex->getMessage())));
}
?>