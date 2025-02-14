<?php

namespace App\Class;

use \Mailjet\Resources;
use \Mailjet\Client;


// class créée manuellement pour envoyer les mails
class Mail{

    public function send($to_email,$to_name,$subject,$template,$vars = null){

        // récupération du template
        $content = file_get_contents(dirname(__DIR__).'/Mail/'.$template);

        // recupération des variables facultatives 
        if($vars){
            // on remplace chaque variable de la template par du contenu 
            foreach($vars as $key=>$var){
                // dd($key)
                // str_replace('RECHERCHE', 'REMPLACER', 'OU CA ?')
               $content = str_replace('{'.$key.'}', $var, $content);
            }
        }

        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'],$_ENV['MJ_APIKEY_PRIVATE'], true, ['version'=>'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "milan.forrat@gmail.com",
                        'Name' => "VOD Project"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID'=>6727615,
                    'TemplateLanguage'=>true,
                    'Subject' => $subject,
                    'Variables'=>[
                        'content'=>$content,
                        'name' => $to_name,
                    ]
                ]
            ]
        ];

        $mj->post(Resources::$Email, ['body' => $body]);
    }
}