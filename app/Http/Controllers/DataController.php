<?php

namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class DataController extends Controller
    {
            public function open() 
            {
                $data = "Este dado é publico";
                return response()->json(compact('data'),200);

            }

            public function closed() 
            {
                $data = "Somente usuários autorizados podem acessar este dado";
                return response()->json(compact('data'),200);
            }
    }