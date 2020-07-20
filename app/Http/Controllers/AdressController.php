<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Adress;
use App\Client;
class AdressController extends Controller
{
    //---- CRUD Api ----
    public function create ($id){
    }
    public function delete (Request $request) {
    }
    public function update(Request $request, $id) {
    }
    public function read(Request $request, $id) {
    }
    //---- //--// ---- //

    public static function createNewAdressToClient ($client, $request, $cepResponse){
        
        $cepData = $cepResponse->getCepModel();
        $endereço = new Adress;
        $endereço->rua = is_null($request->novo_endereco_rua) ? $cepData->logradouro : $request->novo_endereco_rua;
        $endereço->cep = $request->novo_endereco_cep;
        $endereço->estado =  is_null($request->novo_endereco_estado) ? $cepData->uf : $request->novo_endereco_estado;
        $endereço->cidade =  is_null($request->novo_endereco_cidade) ? $cepData->localidade : $request->novo_endereco_cidade;
        $endereço->user_id = $client->id;
        $endereço->pais = is_null($request->novo_endereco_pais) ? "Brasil" : $request->novo_endereco_pais;
        $endereço->numero = $request->novo_endereco_numero;
        $endereço->complemento = is_null($request->novo_endereco_complemento) ? $cepData->complemento : $request->novo_endereco_complemento;
        $endereço->save();
        if (is_null($client->enderecos)){
            $client->enderecos = $endereço->id;
        } else {
            $client->enderecos = $client->enderecos.", ".$endereço->id;
        }
        $client->save();

    }
}
