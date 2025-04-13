<?php

function base64url_encode($data) {  //Codify the data string into base64
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); //rtrim delete spaces to the right
}

class AuthHelper {
    function getAuthHeaders() { //Returns the authentication header
        $header = "";
        if(isset($_SERVER['HTTP_AUTHORIZATION']))
            $header = $_SERVER['HTTP_AUTHORIZATION'];
        if(isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
            $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        return $header;
    } //If none of the ifs are true, returns an empty header

    function createToken($payload) {    //Payload (carga util) is given as a parameter
        $header = array(
            'alg' => 'HS256',
            'typ' => 'JWT'  //Type of token
        );
        
        $payload->exp = time() + JWT_EXP; //Payload expiration date is current day and hour plus a certain amount of time

        $header = base64url_encode(json_encode($header));   //Convert header and payload into JSON
        $payload = base64url_encode(json_encode($payload)); //Codify header and payload into base64
        
        $signature = hash_hmac('SHA256', "$header.$payload", JWT_KEY, true); //Creates the signature
        $signature = base64url_encode($signature);  //Converts signature into base64

        $token = "$header.$payload.$signature"; //Creates el token linking header, payload and signatur together
        
        return $token; //Returns token to be sent to user
    }

    function verify($token) {
        //Token format is $header.$payload.$signature

        $token = explode(".", $token); //Separates tkoen components in an array [$header, $payload, $signature]
        $header = $token[0];
        $payload = $token[1];
        $signature = $token[2];

        $new_signature = hash_hmac('SHA256', "$header.$payload", JWT_KEY, true); //Creates a new signature
        $new_signature = base64url_encode($new_signature);  //Codify new signature into base64

        if($signature!=$new_signature) {    //Compares token signature with the new signature
            return false;
        }

        $payload = json_decode(base64_decode($payload));    //Decodes payload base64 and disassembles the JSON

        if($payload->exp<time()) { //If exp is minor than the actual date, it means that the token is expired
            return false;
        }

        return $payload;    //Returns payload to the controller
    }

    function currentUser() {
        $auth = $this->getAuthHeaders(); //It should return something of the type "Bearer $token"
        $auth = explode(" ", $auth); //Separates %auth components into an array["Bearer", "$token"]

        if($auth[0] != "Bearer") { //Verify that authentication format is bearer type
            return false;
        }

        return $this->verify($auth[1]); //Verify that $token is correct
    }                                   //If the $token is correct, returns payload
    

}