<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokenHashCheck extends Controller
{
    public function verifyToken(Request $request)
    {
        // Token dari Postman
        $plainToken = '4|YAUNfmudW369EZe0AxLaDmOJ0z81U2AdvFYTGNkD9f4ef82b';

        // Token yang di-hash di database
        $hashedToken = '21292ad18bb6036aabd7ceae222d358a41fe4bf42c97f6c274e4bde0aac077c8';

        // Verifikasi token
        $isValid = Hash::check($plainToken, $hashedToken);

        if ($isValid) {
            return response()->json(['status' => 'Token valid.']);
        } else {
            return response()->json(['status' => 'Token tidak valid.']);
        }
    }
}
