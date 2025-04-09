<?php
namespace App;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FacebookApi {

    private $access_token,
    $phone_id,
    $whatsapp_ac_id,
    $graph_url = 'https://graph.facebook.com',
    $graph_version = 'v21.0',
    $app_id;

    public function __construct()
    {
        $this->access_token = env('APP_ACCESS_TOKEN');
        $this->phone_id = env('PHONE_NUMBER_ID');
        $this->whatsapp_ac_id = env('BUSINESS_ACCOUNT_ID');
        $this->app_id = env('APP_ID');
    }

    /**
     * Send WhatsApp message via API
     *
     * @param array $msgData
     * @return array
     */
    public function sendMessage($msgData){
        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token
        ])->post($this->graph_url .'/'. $this->graph_version .'/'. $this->phone_id .'/messages', $msgData);

        if($req->successful()){
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }

        return [
            'status' => false,
            'result' => $req->json()
        ];
    }


    /**
     * Send WhatsApp Text Message
     *
     * @param array $msgData
     * @return array
     */
    public function sendTextMessage($phone, $message){
        $msgData = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $phone,
            "type" => "text",
            "text" => [
                "preview_url" => true,
                "body" => $message
            ]
        ];

        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token
        ])->post($this->graph_url .'/'. $this->graph_version .'/'. $this->phone_id .'/messages', $msgData);

        if($req->successful()){
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }

        return [
            'status' => false,
            'result' => $req->json()
        ];
    }


    /**
     * Send Media Message
     *
     * @param array $msgData
     * @return array
     */
    public function sendMediaMessage($phone, $ext, $fileurl){
        $typeMapping = [
            'mp4' => 'video',
            'jpeg' => 'image',
            'png' => 'image',
            'jpg' => 'image',
            'pdf' => 'document',
            'doc' => 'document',
            'docx' => 'document',
        ];

        if (isset($typeMapping[$ext])) {
            $msgData = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $phone,
                "type" => $typeMapping[$ext],
                $typeMapping[$ext] => [
                    "link" => $fileurl
                ]
            ];
        }

        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token
        ])->post($this->graph_url .'/'. $this->graph_version .'/'. $this->phone_id .'/messages', $msgData);

        if($req->successful()){
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }

        return [
            'status' => false,
            'result' => $req->json()
        ];
    }

    /**
     * Send Media Template Message
     *
     * @param array $msgData
     * @return array
     */
    public function sendMediaTemplateMessageWithoutBody($phone, $template, $ext, $fileUrl, $sentuserData, $fileName){
        $typeMapping = [
            'mp4' => 'video',
            'jpeg' => 'image',
            'png' => 'image',
            'jpg' => 'image',
            'pdf' => 'document',
            'doc' => 'document',
            'docx' => 'document',
        ];

        if (isset($typeMapping[$ext])) {
            $msgData = [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => '91' . $phone,
                "type" => "template",
                "template" => [
                    "name" => $template->name,
                    "language" => ["code" => $template->language],
                    "components" => [
                        [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type" => "$typeMapping[$ext]",
                                    "$typeMapping[$ext]" => [
                                        "link" => $fileUrl
                                    ]
                                ]
                            ]
                        ],
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $sentuserData[0]['text'] ?? $fileName
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }


        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token
        ])->post($this->graph_url .'/'. $this->graph_version .'/'. $this->phone_id .'/messages', $msgData);

        if($req->successful()){
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }

        return [
            'status' => false,
            'result' => $req->json()
        ];
    }


    public function newTemplate($data){
        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token
        ])->post($this->graph_url .'/'. $this->graph_version .'/'. $this->whatsapp_ac_id .'/message_templates', $data);

        if($req->successful()){
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }

        return [
            'status' => false,
            'result' => $req->json()
        ];
    }


    /**
     * Update Template
     * @param integer $template_id
     * @param JSON $data
    */
    public function updateTemplate($templateId, $data){
        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token
        ])->post($this->graph_url .'/'. $this->graph_version .'/'. $templateId, $data);

        if($req->successful()){
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }

        return [
            'status' => false,
            'result' => $req->json()
        ];
    }


    /*
        Fetch Message Templates
    */
    public function fetchTemplates(){
        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token
        ])->get($this->graph_url .'/'. $this->graph_version .'/'. $this->whatsapp_ac_id .'/message_templates?limit=6000');


        if($req->successful()){
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }
        return [
            'status' => false,
            'result' => $req->json()
        ];
    }


    /**
     * Fetch Media Message
    */
    public function getMediaMsg($mediaId)
    {
        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token
        ])->get($this->graph_url .'/'. $this->graph_version .'/'. $mediaId);
        if($req->successful()){
            //Storage::disk('public')->put('log/resp.txt', $req);
            return $req;
        }
        return [
            'status' => false,
            'result' => $req->json()
        ];
    }


    /**
     * Upload Session For Media Template - Create Media Template STEP-1
     * @param string $docType Docuent MIME Type
     * @param double $docSize Docuent Size
     * @param string $docName Docuent Name
    */
    public function getMediaSession($docType, $docSize, $docName)
    {
        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->graph_url .'/'. $this->graph_version .'/'.$this->app_id.'/uploads?file_name='.$docName.'&file_length='.$docSize.'&file_type='.$docType.'&access_token='.$this->access_token);

        if($req->successful()){
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }

        return [
            'status' => false,
            'result' => $req->json()
        ];
    }


    /**
     * GET header Handle Id For Media Template - Create Media Template STEP-2
     * @param string $sessionid Step-1 response id
     * @param file $filePath local path of server uploaded file
    */
    public function getMediaHeaderHandle($sessionId, $filePath)
    {
        $fileContents = file_get_contents($filePath);

        $req = Http::withHeaders([
            'Authorization' => 'OAuth ' . $this->access_token,
        ])->attach('file', $fileContents, basename($filePath))
        ->post($this->graph_url . '/' . $this->graph_version . '/' . $sessionId);

        if ($req->successful()) {
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }

        return [
            'status' => false,
            'result' => $req->json()
        ];
    }




    /**
     * Download Media Message
     * @param MediaID
    */
    public function downloadMediaMsg($mediaUrl)
    {
        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token
        ])->get($mediaUrl);

        if($req->successful()){
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }
        return [
            'status' => false,
            'result' => $req->json()
        ];
    }



    /*
        Delete Message Template using API
    */

    public function deleteTemplate($name){
        $req = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token
        ])->DELETE($this->graph_url .'/'. $this->graph_version .'/'. $this->whatsapp_ac_id .'/message_templates?name='.$name);

        if($req->successful()){
            return [
                'status' => true,
                'result' => $req->json()
            ];
        }
        return [
            'status' => false,
            'result' => $req->json()
        ];
    }

}
