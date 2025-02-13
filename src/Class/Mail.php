<?php

namespace App\Class;

use \Mailjet\Resources;
use \Mailjet\Client;


// class créée manuellement pour envoyer les mails
class Mail{

    public function send($to_email,$to_name,$subject,$content){

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
                    'Subject' => $subject,
                    'TextPart' => "Greetings from Mailjet!",
                    'HTMLPart' => $content
                ]
            ]
        ];

        $mj->post(Resources::$Email, ['body' => $body]);
    }
}