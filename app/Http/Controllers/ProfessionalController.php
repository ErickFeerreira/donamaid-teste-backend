<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Professional;
class ProfessionalController extends Controller
{
    public function create (Request $request){
        $errorMsg = array(); 
        $errorMsg['errors'] = array();
        if (Professional::where('email', $request->email)->exists()) $errorMsg['errors']['email.exists'] = 'Este e-mail já está cadastrado.';
        
        if (Professional::where('cpf', $request->cpf)->exists()) $errorMsg['errors']['cpf.exists'] = 'Este cpf já está cadastrado.';
        
        if (!isset($request->email )) $errorMsg['errors']['email.undefined'] = 'Você não informou um e-mail';
        if (!isset($request->cpf )) $errorMsg['errors']['cpf.undefined'] = 'Você não informou um cpf';
        if (!isset($request->nome )) $errorMsg['errors']['nome.undefined'] = 'Você não informou um nome';

       
        if (!empty($errorMsg['errors'])){
            return response()->json($errorMsg, 200);
        }
        $professional = Professional::create($request->all());
        return response()->json($professional, 200);
    }
    public function delete ($id) {
       
        if (Professional::where('id', $id)->exists()){
            $professional = Professional::where('id', $id)->first();
            $nome = $professional->nome;
            $professional->delete();
            return response()->json([
                "message" => "Profissional ".$nome." de ID ".$id." foi deletado com sucesso"
            ], 200);
         } else {
            return response()->json([
                "error" => "Não há Profissional com este ID"
            ], 404);
         }
    }
    public function readAll (){
        $professional = Professional::all();
        return response()->json($professional, 200);
        
    }
    public function read ($id){
        if (Professional::where('id', $id)->exists()) {
            $professional = Professional::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($professional, 200);

          } else {
            return response()->json([
              "error" => "Não há Profissional com este ID."
            ], 404);
          }
        return response()->json($professional, 200);
    }
    public function update(Request $request, $id) {
        if (Professional::where('id', $id)->exists()) {
            $professional = Professional::find($id);
            $professional->nome = is_null($request->nome) ? $professional->nome : $request->nome;
            $professional->email = is_null($request->email) ? $professional->email : $request->email;

            $professional->save();
    
            return response()->json([
                "message" => "Dados do Professional atualizados com sucesso", json_encode($professional)
            ], 200);
            } else {
            return response()->json([
                "message" => "Não há Profissional com este ID"
            ], 404);
            
        }
    }
}
