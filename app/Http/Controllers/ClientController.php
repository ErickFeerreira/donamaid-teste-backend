<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Adress;
use App\Http\Controllers\AdressController as AdressController;
class ClientController extends Controller
{
    public function create (Request $request){
        $messages = array();
        $messages['errors'] = array();
        $messages['success'] = array();
        $cepData;
        if (Client::where('email', $request->email)->exists()) $messages['errors']['email.exists'] = 'Este e-mail já está cadastrado.';
        
        if (Client::where('cpf', $request->cpf)->exists()) $messages['errors']['cpf.exists'] = 'Este cpf já está cadastrado.';
        
        if (!isset($request->email )) $messages['errors']['email.undefined'] = 'Você não informou um e-mail';
        
        if (!isset($request->cpf )) $messages['errors']['cpf.undefined'] = 'Você não informou um cpf';
        if (!isset($request->nome )) $messages['errors']['nome.undefined'] = 'Você não informou um nome';
        if (!empty($messages['errors'])){
            return response()->json($messages, 200);
        }

        $messages['success']['client.created'] = 'Você foi registrado com sucesso!';
        $client = new Client;
        $client->fill($request->all());
        $client->save();

        //Flexiona se é necessário ou não criar um novo endereço vinculado a um cliente na hora do Create
        if ((!is_null($request->novo_endereco_cep) && !is_null($request->novo_endereco_numero))){   
            $cepResponse = \Canducci\Cep\Facades\Cep::find($request->novo_endereco_cep );
            if ($cepResponse->isOk()) 
            {
                AdressController::createNewAdressToClient($client, $request, $cepResponse);
                $successMsg['success']['adress.created'] = 'Seu endereço foi registrado com sucesso!';
            } else {
                $errorMsg['errors']['cep.unknown'] = 'CEP não encontrado';
            }
            if (!empty($errorMsg['errors'])){
                return response()->json($errorMsg, 200);
            }
        } else if ((!is_null($request->novo_endereco_cep) && is_null($request->novo_endereco_numero))){
            $errorMsg['errors']['cep.unknown'] = 'Você informou um CEP mas não o número de sua residência. Ambos são necessários para o registro devido do seu endereço';
            return response()->json($errorMsg, 200);

        }
        $successMsg['success']['client.created'] = 'Você foi registrado com sucesso!';
        
        return response()->json([$client, $successMsg], 200);
    }
    public function delete ($id) {
       
        if (Client::where('id', $id)->exists()){
            $client = Client::where('id', $id)->first();
            $nome = $client->nome;
            $client->delete();
            return response()->json([
                "success" => "Cliente ".$nome." de ID ".$id." foi deletado com sucesso"
            ], 200);
         } else {
            return response()->json([
                "error" => "Não há Cliente com este ID"
            ], 404);
         }
    }
    public function read ($id){
   
        if (Client::where('id', $id)->exists()) {
            $client = Client::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($client, 200);

          } else {
            return response()->json([
              "error" => "Não há Cliente com este ID."
            ], 404);
          }
        return response()->json($client, 200);
        
    }
    public function readAll (){
        $client = Client::all();
        return response()->json($client, 200);
        
    }
    public function update(Request $request, $id) {
        if (Client::where('id', $id)->exists()) {
            $client = Client::find($id);
            $client->nome = is_null($request->nome) ? $client->nome : $request->nome;
            $client->email = is_null($request->email) ? $client->email : $request->email;
            $client->enderecos = is_null($request->enderecos) ? $client->enderecos : $request->enderecos;

            if ((!is_null($request->novo_endereco_cep) && !is_null($request->novo_endereco_numero))){
                
                $cepResponse = \Canducci\Cep\Facades\Cep::find($request->novo_endereco_cep );
                if ($cepResponse->isOk()) 
                {
                   AdressController::createNewAdressToClient($client, $request, $cepResponse);

                } else {
                    return response()->json([
                        "message" => "CEP não encontrado", $client
                    ], 200);               
                 }
            }
            $client->save();

            return response()->json([
                "message" => "Os dados foram atualizados com sucesso", $client
            ], 200);
        } else {
            return response()->json([
                "error" => "Não há Cliente com este ID."
            ], 404);  
        }
    }

}