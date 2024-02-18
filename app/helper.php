<?php

use Illuminate\Support\Facades\Log;
use App\Models\paymentRequest;
use Carbon\Carbon;

/**
 * Tigo Function
 */
function postToTigo_C2B( $payload ){

    $base_url = config('services.TIGO_AGR_MON.BASE_URL');
    $api_client_id = config('services.TIGO_AGR_MON.API_SERVICE_ID');
    $api_client_secret = config('services.TIGO_AGR_MON.API_CLIENT_SECRET');
    $api_auth_endpoint = config('services.TIGO_AGR_MON.API_AUTH_END_POINT');
    $api_push_endpoint = config('services.TIGO_AGR_MON.API_COLLECTION_PUSH_API_ENDPOINT');
    $biller_msisdn = config('services.TIGO_AGR_MON.BILLER_MSISDN');

    $dt = Carbon::now()->format('d-m-Y');


    Log::debug("Function | ".__FUNCTION__." | Received payload....  : ". json_encode($payload));

    Log::debug("Function | ".__FUNCTION__." | Received payload Date....  : ". $dt);

     $payload['network'] = 'tigopesa';

    Log::debug("Function | ".__FUNCTION__." | Call to Tigo to get barrier token  : ".
        json_encode(
            [

                'username' => $api_client_id,
                'password' => $api_client_secret,
                'AuthEndpoint' => $api_auth_endpoint,
                'grant_type' => 'password',
                'Endpoint' => $base_url,
        
            ]
        ));



        $response = Http::withHeaders([

            'Content-Type' => 'application/x-www-form-urlencoded'
    
        ])->withOptions([
            'verifiy_host' => false,
            'verify_peer' => false,
            'verify' => false
        ])
        ->withBody(
            http_build_query([

                'username' => $api_client_id,
                'password' => $api_client_secret,
                'grant_type' => 'password',

            ])
        )->post($base_url . '/' . $api_auth_endpoint );

    
        Log::debug("Function | ".__FUNCTION__." | Received payload from Tigo  : ".$response);






    try{


        


    
        if($response->successful()){
    
            Log::debug("Function | ".__FUNCTION__." | We have been succesfuly authenticated  ");

            Log::debug("Function | ".__FUNCTION__." | Preping to send push request |  Basu URL : ".
            $base_url ." | Endpoint : ".$api_push_endpoint);

            $response = json_decode($response);

            Log::debug("Function | ".__FUNCTION__." | Data retuened json:  ". json_encode($response->access_token));


            Log::debug("Function | ".__FUNCTION__." | Call to tigo for push notification  : ".
            json_encode(
                [
    
                    "CustomerMSISDN" => $payload->client_mobile,
                    "BillerMSISDN" => $biller_msisdn,
                    "Amount" => $payload->amount,
                    "Remarks" => "Pesaport Deposit",
                    "ReferenceID" => $payload->request_reference
            
                ]
            ));


            //Sending push request to Tigo

            $response = Http::withHeaders([

                'Content-Type' => 'application/json',
                'Username' => $api_client_id,
                'Password' => $api_client_secret,
                'Authorization' => 'bearer '.$response->access_token,
                'Cache-Control' => 'no-cache'
        
            ])->post($base_url . '/' . $api_push_endpoint, [
        
                "CustomerMSISDN" => $payload->client_mobile,
                "BillerMSISDN" => $biller_msisdn,
                "Amount" => $payload->amount,
                "Remarks" => "Pesaport Deposit",
                "ReferenceID" => "PPC".$payload->request_reference
        
            ]);

            Log::debug("Function | ".__FUNCTION__." | Received payload from Tigo  : ".$response);

            if($response->successful()){


                
                //Updating request table with new status
                $EvmakTrxRequest = paymentRequest::find($payload->id);
                $EvmakTrxRequest->status = 'Posted';
                $EvmakTrxRequest->save();



                Log::debug("Function | ".__FUNCTION__." | Posting of push notification has been success ". $response);

               

                return response()->json([
                    "status" => "success", 
                    "message" => "All is well!", 
                    "data" => $response->body()
                ]);


            }

    
        }elseif( $response->failed() || $response->clientError() || $response->serverError() ){

            Log::debug("Function | ".__FUNCTION__." | We fail to post ");
            return response()->json(["status" => "error", "message" => "We fail to get reponse", "data" => $response]);
    
        }



    }catch (Exception $e){

        Log::debug("Function | ".__FUNCTION__." | Posting have not being done, somthing has hapen  | ". json_encode($e));
        return response()->json(["status" => "error", "message" => "We had exception on the request", "data" => '']);

    }



}


/**
 * Tigo Function
 */
function postToTigo_B2C( $payload ){

    $base_url = config('services.TIGO_AGR_MON.BASE_URL');
    $api_client_id = config('services.TIGO_AGR_MON.API_SERVICE_ID');
    $api_client_secret = config('services.TIGO_AGR_MON.API_CLIENT_SECRET');
    $api_auth_endpoint = config('services.TIGO_AGR_MON.API_AUTH_END_POINT');
    $api_push_endpoint = config('services.TIGO_AGR_MON.API_COLLECTION_PUSH_API_ENDPOINT');
    $biller_msisdn = config('services.TIGO_AGR_MON.BILLER_MSISDN');
    $api_payout_b2c = config('services.TIGO_AGR_MON.API_CALL_BACK_B2C');

    $dt = Carbon::now()->format('d-m-Y');


    Log::debug("Function | ".__FUNCTION__." | Received payload....  : ". json_encode($payload));

    Log::debug("Function | ".__FUNCTION__." | Received payload....  : ". $dt);

    $payload['network'] = 'tigopesa';


    $pin = '0831';

    try{


        //Updating request table with new status
        $EvmakTrxRequest = paymentRequest::find($payload->id);
        $EvmakTrxRequest->status = 'Posted';
        $EvmakTrxRequest->save();

        $xmlstr = '<?xml version="1.0" encoding="UTF-8"?>'.
                '<COMMAND>'.
                '<TYPE>RESMFICI</TYPE>'.
                '<REFERENCEID>'.$payload->request_reference.'</REFERENCEID>'.
                '<PIN>'.$pin.'</PIN>'.
                '<MSISDN>25566000831</MSISDN>'.
                '<AMOUNT>'.$payload->amount.'</AMOUNT>'.
                '<MSISDN1>'.$payload->client_mobile.'</MSISDN1>'.
                '<SENDERNAME>PESAPORT</SENDERNAME>'.
                '<BRAND_ID>4613</BRAND_ID>'.
                '<LANGUAGE1>en</LANGUAGE1>'.
                '</COMMAND>';

        Log::debug("Function | ".__FUNCTION__.
                " | xml string to be posted  : ". $xmlstr );
        
        $xml = new \SimpleXMLElement($xmlstr);

        $response = Http::withHeaders([

            'Content-Type' => 'text/xml;charset=utf-8'
    
        ])->withBody(
            $xml->asXML(),'text/xml'
        )->post($base_url . '/' . $api_payout_b2c,[
            $xmlstr
        ]);

    
        Log::debug("Function | ".__FUNCTION__." | Received payload from Tigo  : ".$response);
    
        if($response->successful()){
    


            Log::debug("Function | ".__FUNCTION__." | Posting of push notification has been success ". $response);

            return response()->json([
                "status" => "success", 
                "message" => "All is well!", 
                "data" => $response->body()
            ]);

    
        }elseif( $response->failed() || $response->clientError() || $response->serverError() ){

            Log::debug("Function | ".__FUNCTION__." | Posting have being done but we havent receive response or response has an error ");
            return response()->json(["status" => "fail", "message" => "We fail to get reponse or we get error response", "data" => $response]);
    
        }



    }catch (Exception $e){

        Log::debug("Function | ".__FUNCTION__." | Posting have not being done, somthing has hapen  | ". json_encode($e));
        return response()->json(["status" => "error", "message" => "We had exception on the request", "data" => '']);

    }



}