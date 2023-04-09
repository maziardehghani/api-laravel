<?php

namespace App\Traits;


trait ApiResponser
{
    public function SuccessResponse($message , $status_code , $data=null)
    {
        return response()->json(
            [
                'status' =>'Success',
                'message' => $message,
                'data' => $data
            ],$status_code);
    }
    public function ErrorResponse($message , $status_code , $data=null)
    {
        return response()->json(
            [
                'status' => 'Error',
                'message' => $message,
                'data' => $data
            ],$status_code) ;
    }
}
