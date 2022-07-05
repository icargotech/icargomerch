<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Models\User;
use App\Models\Verify_merchant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;



//IMPORT DB IF YOU USE DB FOR QUERYING
use Illuminate\Support\Facades\DB;

class IdentificationController extends Controller
{
    //

    public function create(Request $request)
    {

        //This is the code for validation of every request
        $validated = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'contact_number' => 'required',
            'name_of_business' => 'required',
            'password' => 'required|confirmed|min:8|',

        ]);

        //if validation fails this will return the error response
        if ($validated->fails()) {
            $responseArr['message'] = $validated->errors();
            return response()->json($responseArr);
        }


        $emailCheck = DB::table('merchant')->where('email', $request->email)->limit(1)->count();

        if ($emailCheck === 0) {

            //if correct, it will insert in the table of merchant
            $merchantTable = DB::table('merchant')->insert(
                [

                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'contact_number' => $request->contact_number,
                    'name_of_business' => $request->name_of_business,
                    'password' => Hash::make($request->password),
                    'about' => $request->about,
                ]
            );

            //it will return status 200 if it was inserted in the DB and 500 if inserting is not successful
            if ($merchantTable) {
                return response()->json(
                    [
                        'success' => $merchantTable,
                        'message' => 'successfully inserted',
                    ],
                    200
                );
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to insert',
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Email already existing',
            ], 404);
        }
    }

    function logIn(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        //if validation fails this will return the error response
        if ($validated->fails()) {
            $responseArr['message'] = $validated->errors();
            return response()->json($responseArr);
        }

        $user = Merchant::where('email', $request->email)->first();
        //requesting first the email                  

      
       if (!$user || !Hash::check($request->password, $user->password)) {
        return response('Login invalid', 503);
     }

        $token = $user->createToken($request->device_name)->plainTextToken;

            return response([
                'token' => [$token],
                'message' => ['Log In Successs.']                                    //success log in message
            ], 201);
      
    } 
    
    //Verify Merchant : Proof of Identification
    function verifymerchant()
     {
        $verify_merchant = DB::table('verify_merchant')->get();
        
        return response()->json
            ([
                'proof_of_identification'=>$verify_merchant,
                
                ]
                ,200);

     }

     function logout(Request $request){                        // logut / delete token
        $request ->user()->currentAccessToken()->delete();
        return response()->json([                              // postman->headers-> key: Authorizaion || value: Bearer[space][token]
            'status_code' => 200,
            'message' => 'Token deleted successfuly.'
        ]);
     }

     
}
