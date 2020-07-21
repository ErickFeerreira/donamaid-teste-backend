<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Adress;
use App\Client;
class AdressController extends Controller
{
    //---- CRUD Api ----
    public function create (Request $request){
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array(); $cepData;

        //Verifica  no Input
        if (!isset($request->cep)) $messages['errors']['cep.undefined'] = 'Você não informou um CEP';
        if (!isset($request->numero)) $messages['errors']['numero.undefined'] = 'Você não informou o Número da residência';
        if (!isset($request->user_id)) $messages['errors']['cliente.undefined'] = 'Você não informou a que Cliente pertence este endereço.';
        if (!empty($messages['errors'])) return response()->json($messages, 409);
        if (! Client::where('id', $request->user_id)->exists()) {
            $messages['errors']['client.unknown'] = "Não há Cliente com este ID";
            return response()->json($messages, 404);
        }

        $client = Client::find($request->user_id);

        //Verifica se o CEP é válido e cria o endereço

        $cepResponse = \Canducci\Cep\Facades\Cep::find($request->cep );
        if ($cepResponse->isOk()) {
            $adress = AdressController::createNewAdressToClient($client, $request, $cepResponse);
            $messages['success']['adress.created'] = 'Seu endereço foi registrado com sucesso!';
            return response()->json([$messages, $adress], 201);
        } else {
            $messages['errors']['cep.unknown'] = 'CEP não encontrado';
        }

    }

    public function delete ($id) {
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array();
        
        if (Adress::where('id', $id)->exists()){
            $adress = Adress::where('id', $id)->first();
            $adress->delete();
            $messages['success']['adress.deleted'] = "Endereço de ID ".$id." foi deletado com sucesso"; 
            return response()->json($messages, 200);
         } else {
            $messages['errors']['adress.unknown'] = "Não há Endereço com este ID";
            return response()->json($messages, 404);
         }
    }

    public function read ($id){
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array();

        if (Adress::where('id', $id)->exists()) {
            $adress = Client::where('id', $id)->get();
            return response($adress, 200);
        } else {
        $messages['errors']['adress.unknown'] = "Não há Endereço com este ID";
        return response()->json($messages, 404);
        }

    }

    public function readAll (){
        $client = Client::all();
        return response()->json($client, 200);
    }

    public function update(Request $request, $id) {
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array();

        if (Client::where('id', $id)->exists()) {
            $client = Client::find($id);
            $client->nome = is_null($request->nome) ? $client->nome : $request->nome;
            $client->email = is_null($request->email) ? $client->email : $request->email;
            $client->enderecos = is_null($request->enderecos) ? $client->enderecos : $request->enderecos;

            if ((!is_null($request->novo_endereco_cep) && !is_null($request->novo_endereco_numero))){  
     
                $cepResponse = \Canducci\Cep\Facades\Cep::find($request->novo_endereco_cep );

                if ($cepResponse->isOk()) {
                   AdressController::createNewAdressToClient($client, $request, $cepResponse);
                } else {
                    $messages['errors']['cep.unknown'] = "CEP não encontrado";
                    return response()->json([
                        $messages, $client
                    ], 409);               
                 }

            }

            $messages['success']['adress.updated'] = "Os dados deste Endereço foram atualizados com sucesso";
           
            return response()->json([$messages, $client], 200);
        } else {    
            $messages['errors']['adress.unknown'] = "Não há Endereço com este ID.";
 
            return response()->json($messages, 404);  
        }
    }
    
    //---- //--// ---- //

    public static function createNewAdressToClient ($client, $request, $cepResponse){
        //Flexibiliza entrada de dados
        $inputRua = isset($request->novo_endereco_rua) ?  $request->novo_endereco_rua : $request->rua ;
        $inputEstado = isset($request->novo_endereco_estado) ?  $request->novo_endereco_estado : $request->estado ;
        $inputCidade = isset($request->novo_endereco_cidade) ? $request->novo_endereco_cidade : $request->cidade ;
        $inputPais = isset($request->novo_endereco_pais) ?  $request->novo_endereco_pais : $request->pais;
        $inputNumero = isset($request->novo_endereco_numero) ?  $request->novo_endereco_numero : $request->numero;
        $inputComplemento = isset($request->novo_endereco_complemento) ? $request->novo_endereco_complemento: $request->complemento ;
        $inputCep = isset($request->novo_endereco_cep) ? $request->novo_endereco_cep : $request->cep ;

        //Cria um novo Endereço baseado no CEP ou nos inputs
        $cepData = $cepResponse->getCepModel();
        $adress = new Adress;
        $adress->rua = is_null($inputRua) ? $cepData->logradouro : $inputRua;
        $adress->cep = $inputCep;
        $adress->estado =  is_null($inputEstado) ? $cepData->uf : $inputEstado;
        $adress->cidade =  is_null($inputCidade) ? $cepData->localidade : $inputCidade;
        $adress->user_id = $client->id;
        $adress->pais = is_null($inputPais) ? "Brasil" : $inputPais;
        $adress->numero = $inputNumero;
        $adress->complemento = is_null($inputComplemento) ? " " : $inputComplemento;
        $adress->save();

        //Altera o valor de 'enderecos' do Cliente especificado
        if (is_null($client->enderecos)){
            $client->enderecos = $adress->id;
        } else {
            $client->enderecos = $client->enderecos.", ".$adress->id;
        }
        $client->save();

        return  $adress;
    }
}