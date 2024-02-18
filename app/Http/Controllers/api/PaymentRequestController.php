<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\paymentRequest;
use App\Http\Requests\mnoRequest;
use Illuminate\Http\Request;

class PaymentRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(paymentRequest $paymentRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(paymentRequest $paymentRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, paymentRequest $paymentRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(paymentRequest $paymentRequest)
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        Log::debug("Function | ".__FUNCTION__.
        " | post request with details : ".$request);


        $body = file_get_contents('php://input');

        Log::debug("Function | ".__FUNCTION__.
        " | Invest request with details : ".Json_encode($body));

        // Parse the string as an XML object
        $xml = simplexml_load_string($body);

        Log::debug("Function | ".__FUNCTION__.
        " | shared reference for validation : ". $xml->CUSTOMERREFERENCEID . 
        " | amount posted | " . $xml->AMOUNT);

        
        $trxID = $xml->TXNID;
        $ref = $xml->CUSTOMERREFERENCEID;
        $msisdn =  $xml->MSISDN;
        $flag = null;
        $result = null;
        $errorCode = null;
        $messg = "";
        $queryResponse = null;

        Log::debug("Function | ".__FUNCTION__.
                    " | shared reference of : ". $xml->CUSTOMERREFERENCEID . 
                    " |is valid as client subscription code");

        $queryResponse = paymentRequest::where('request_reference', $xml->CUSTOMERREFERENCEID )->first();

        Log::debug("Function | ".__FUNCTION__.
                    " | shared reference of : ". json_encode($queryResponse));

        //Reference account number
        //Serch for the subscrition code
        if( $queryResponse != null  && $xml->AMOUNT > 999) {
    
                    Log::debug("Function | ".__FUNCTION__.
                    " | shared reference of : ". $xml->CUSTOMERREFERENCEID . 
                    " |is valid as client subscription code");
    

                    $mnoRequest = new paymentRequest;
                    $mnoRequest->initiator_email = "tigopayment@pesaport.co.tz";
                    $mnoRequest->request_reference = $xml->CUSTOMERREFERENCEID;
                    $mnoRequest->trx_type = "deposite";
                    $mnoRequest->client_mobile = $xml->MSISDN;
                    $mnoRequest->network = "tigo";
                    $mnoRequest->amount = $xml->AMOUNT;
                    $mnoRequest->status = "Success";
            
                               
                    if($mnoRequest->save()) {

                        $result = 'TS';
                        $errorCode = 'error000';
                        $messg = "Payment has copleted successfuly";
                        $flag = 'Y';
            
                    }else {

                        $result = 'TF';
                        $errorCode = 'error111';
                        $messg = "Payment fail service not available";
                        $flag = 'N';
                    }

    
            }else{
                Log::debug("Function | ".__FUNCTION__.
                " | shared reference of : ". $xml->CUSTOMERREFERENCEID . 
                " |is not valid as client subscription code");

                if ( $xml->AMOUNT > 999){

                    $result = 'TF';
                    $errorCode = 'error010';
                    $messg = "Transaction fail, reference used is unknown";
                    $flag = 'N';

                }else {

                    $result = 'TF';
                    $errorCode = 'error013';
                    $messg = "Amount insufficient";
                    $flag = 'N';

                }


    
            }

        //Minimum amount

        $xmlstr = 
            "<COMMAND>".
                "<TYPE>SYNC_BILLPAY_RESPONSE</TYPE>".
                "<TXNID>$trxID</TXNID>".
                "<REFID>$ref</REFID>".
                "<RESULT>$result</RESULT>".
                "<ERRORCODE>$errorCode</ERRORCODE>".
                "<ERRORDESC/>".
                "<MSISDN>$msisdn</MSISDN>".
                "<FLAG>$flag</FLAG>".
                "<CONTENT>$messg</CONTENT>".
            "</COMMAND>";

        Log::debug("Function | ".__FUNCTION__.
        " | returned string : ". $xmlstr );

        $xml = new \SimpleXMLElement($xmlstr);

        return response($xml->asXML(), 200, [
            'Content-Type' => 'application/xml'
        ]);

    }

    public function pesaportTigoC2B (mnoRequest $paymentRequest) {

        Log::debug("Function | ".__FUNCTION__.
        " | post request with details : ".$paymentRequest);
       
        $mnoRequest = $paymentRequest->validated();

        $createdRequest = paymentRequest::create($mnoRequest);

        Log::info("Function | ".__FUNCTION__.
        " | Sending request to helper function, data : ". json_encode($createdRequest) );

        //Call the posting function
        $response = postToTigo_C2B($createdRequest);


        Log::debug("Function | ".__FUNCTION__.
        " | response from the MNO posting function : ".$response); 

        Log::debug("Function | ".__FUNCTION__.
        " | Well we are looking good!"); 


        return response()->json([
            "Status"=> true,
            "Description"=> "Purchase completed successfully",
            "MFSTransactionID"=> "MFSD201707241709w59",
            "ReferenceID"=> "billercode20170724170909",
            "paymentRequest" => json_encode($createdRequest)
            
        ]);

    }

    
    public function pesaportTigoB2C (mnoRequest $paymentRequest) {


        Log::debug("Function | ".__FUNCTION__.
        " | post request with details : ".$paymentRequest);
       
        $mnoRequest = $paymentRequest->validated();

        $createdRequest = paymentRequest::create($mnoRequest);

        Log::info("Function | ".__FUNCTION__.
        " | Sending request to helper function, data : ". json_encode($createdRequest) );


        //Call the posting function
        $response = postToTigo_B2C($createdRequest);
  
  
        Log::debug("Function | ".__FUNCTION__.
            " | response received : ". json_encode($response));

        
        $jsonData = $response->getdata();

        Log::info("Function | ".__FUNCTION__." | All is response  ############". $jsonData->status);
        

        if($jsonData->status== 'success'){


            Log::info("Function | ".__FUNCTION__." | getting back response ". $jsonData->status);

            Log::debug( "Function | ".__FUNCTION__." |   response :  ". 
            
                response()->json([
                    "Status"=> true,
                    "Description"=> "Purchase completed successfully",
                    "MFSTransactionID"=> "MFSD201707241709w59",
                    "ReferenceID"=> "billercode20170724170909",
                    "paymentRequest" => json_encode($createdRequest)
                    
                ])
            );

            return response()->json([
                "Status"=> true,
                "Description"=> "Purchase completed successfully",
                "MFSTransactionID"=> "MFSD201707241709w59",
                "ReferenceID"=> "billercode20170724170909",
                "paymentRequest" => json_encode($createdRequest)
                
            ]);


        }elseif( $jsonData->status == 'fail' ){


            Log::info("Function | ".__FUNCTION__." | getting back responsee ". $jsonData->status);

            Log::debug( "Function | ".__FUNCTION__." |   response :  ". 
            
                response()->json([
                    "Status"=> false,
                    "Description"=> "Purchase Fail to complete, we fail to get response or we get error",
                    "MFSTransactionID"=> "",
                    "ReferenceID"=> "",
                    "paymentRequest" => json_encode($createdRequest)
                    
                ])
            );

            return response()->json([
                "Status"=> false,
                "Description"=> "Purchase Fail to complete, we fail to get response or we get error",
                "MFSTransactionID"=> "",
                "ReferenceID"=> "",
                "paymentRequest" => json_encode($createdRequest)
                
            ]);

        }else {


            Log::info("Function | ".__FUNCTION__." | getting back response : ". $jsonData->status);

            Log::debug( "Function | ".__FUNCTION__." |   response :  ". 
                
                response()->json([
                    "Status"=> false,
                    "Description"=> "Purchase fail to complete, we couldnot post the request for some reason",
                    "MFSTransactionID"=> "",
                    "ReferenceID"=> "",
                    "paymentRequest" => json_encode($createdRequest)
                    
                ])
            );
             
            return response()->json([
                "Status"=> false,
                "Description"=> "Purchase fail to complete, we couldnot post the request for some reason",
                "MFSTransactionID"=> "",
                "ReferenceID"=> "",
                "paymentRequest" => json_encode($createdRequest)
                
            ]);

        }
    

    }



    public function pesaportTigoCallBack(Request $request){

        Log::debug("Function | ".__FUNCTION__.
        " | post request with details : ".$request);

        Log::debug("Function | ".__FUNCTION__.
        " | post request reference ID : ".$request->ReferenceID);

        Log::debug("Function | ".__FUNCTION__.
        " | post request reference ID : ".ltrim($request->ReferenceID, 'PPC'));

        //Updating request table with new status
        $EvmakTrxRequest = paymentRequest::where('request_reference', ltrim($request->ReferenceID, 'PPC'))
                ->update(['status'=>'Completed']);


        // Log::debug("Function | ".__FUNCTION__.
        // " | post request reference ID : ".$request->ReferenceID);

        if($request->Status == 'true'){

            //Updating request table with new status
            $EvmakTrxRequest = paymentRequest::where('request_reference', ltrim($request->ReferenceID, 'PPC'))
            ->update(['status'=>'Success']);

            return response()->json([
                "ResponseCode"=>  "BILLER-18-0000-S",
                "ResponseStatus"=> true,
                "ResponseDescription"=> "Callback successful",
                "ReferenceID"=> $request->ReferenceID,
                
            ]);


        }else{

            //Updating request table with new status
            $EvmakTrxRequest = paymentRequest::where('request_reference', ltrim($request->ReferenceID, 'PPC'))
            ->update(['status'=>'Fail']);

            return response()->json([
                "ResponseCode"=>  "BILLER-18-0000-E",
                "ResponseStatus"=> False,
                "ResponseDescription"=> "Callback failed",
                "ReferenceID"=> $request->ReferenceID,
                
            ]);
        }


        
       


    }
}
