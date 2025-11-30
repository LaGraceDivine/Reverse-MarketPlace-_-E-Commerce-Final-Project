<?php
require_once __DIR__ . "/db_connection.php";
require_once __DIR__ . "/../settings/paystack_config.php";

class Payment extends Database {
    private $secretKey;
    private $publicKey;

    public function __construct() {
        parent::__construct();
        $this->secretKey = PAYSTACK_SECRET_KEY;
        $this->publicKey = PAYSTACK_PUBLIC_KEY;
    }

    public function initializePayment($email, $amount, $orderId) {
        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
            'email' => $email,
            'amount' => $amount * 100, // Amount in kobo
            'callback_url' => "http://localhost:8888/register_sample/actions/payment_action.php?action=verify&order_id=$orderId",
            'metadata' => ["order_id" => $orderId]
        ];

        $fields_string = http_build_query($fields);

        // Open connection
        $ch = curl_init();
        
        // Set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . $this->secretKey,
            "Cache-Control: no-cache",
        ));
        
        // So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        // Execute post
        $result = curl_exec($ch);
        
        if ($result === false) {
            return ['status' => false, 'message' => 'Curl error: ' . curl_error($ch)];
        }
        
        curl_close($ch);
        
        return json_decode($result, true);
    }

    public function verifyTransaction($reference) {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $this->secretKey,
            "Cache-Control: no-cache",
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        if ($err) {
            return ['status' => false, 'message' => "cURL Error #:" . $err];
        } else {
            return json_decode($response, true);
        }
    }
}
