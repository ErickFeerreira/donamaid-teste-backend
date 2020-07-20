<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Adress;
use App\Client;
use App\Professional;
class OrderController extends Controller
{
    public function create (Request $request){
        $errorMsg = array(); 
        $errorMsg['errors'] = array();
        $newformatDate;
        if (!isset($request->horario_inicial)) $errorMsg['errors']['horario.undefined'] = 'Você não informou o Horario marcado para realização do Contrato';
        if (!isset($request->dia )) {
            $errorMsg['errors']['dia.undefined'] = 'Você não informou o Dia do Contrato';
        } else {
            $dataTyped = explode("/", $request->dia);
            $newformatDate = $dataTyped[2]."-".$dataTyped[1]."-".$dataTyped[0];

        }
        if (!isset($request->duracao )) $errorMsg['errors']['duracao.undefined'] = 'Você não informou a Duração do Contrato';
        if (!isset($request->endereco )) $errorMsg['errors']['endereco.undefined'] = 'Você não não informou o ID de um Endereço';
        if (!isset($request->cliente )) $errorMsg['errors']['cliente.undefined'] = 'Você não informou o ID de um Cliente';
        if (!isset($request->profissional )) $errorMsg['errors']['profissional.undefined'] = 'Você não informou o ID de um Profissional';

       
        if (!empty($errorMsg['errors'])){
            return response()->json($errorMsg, 200);
        }
        $order = new Order;
        $order->fill($request->all());
        $order->dia = $newformatDate;
        $order->horario_inicial = $request->horario_inicial;
        $order->save();
        return response()->json($order, 200);
    }
    public function delete ($id) {
        if (Order::where('id', $id)->exists()){
            $order = Order::where('id', $id)->first();
            $order->delete();
            return response()->json([
                "message" => "Contrato de ID ".$id." foi deletado com sucesso"
            ], 200);
         } else {
            return response()->json([
                "error" => "Não há Contrato com este ID"
            ], 404);
         }
    }
    public function read ($id){
        if (Order::where('id', $id)->exists()) {
            $order = Order::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($order, 200);

          } else {
            return response()->json([
              "error" => "Não há Contrato com este ID."
            ], 404);
          }
        return response()->json($order,  200);
    }
    public function readAll (){
        $order = Order::all();
        return response()->json($order, 200);
        
    }
    public function update(Request $request, $id) {
        $errorMsg = array(); 
        $errorMsg['errors'] = array();
        
        if (!is_null($request->endereco) && !Adress::where('id', $request->endereco)->exists()){
            $errorMsg['errors']['adress.unknown'] = 'Este Endereço não está cadastrado';

        }
        if (!is_null($request->profissional) && !Professional::where('id', $request->profissional)->exists()){
            $errorMsg['errors']['professional.unknown'] = 'Este Profissional não está cadastrado';

        }
        if (!is_null($request->cliente) && !Client::where('id', $request->cliente)->exists()){
            $errorMsg['errors']['client.unknown'] = 'Este Cliente não está cadastrado';

        }
        if (!empty($errorMsg['errors'])){
            return response()->json($errorMsg, 200);
        }
        if (Order::where('id', $id)->exists()) {
            $order = Order::find($id);
            $order->endereco = is_null($request->endereco) ? $order->endereco : $request->endereco;
            $order->duracao = is_null($request->duracao) ?  $order->duracao : $request->duracao;
            $order->dia = is_null($request->dia) ?  $order->dia : $request->dia;
            $order->horario_inicial = is_null($request->horario) ?  $order->horario_inicial : $request->horario;
            $order->cliente = is_null($request->cliente) ?  $order->cliente : $request->cliente;
            $order->profissional = is_null($request->profissional) ?  $order->profissional : $request->profissional;

            $order->save();
    
            return response()->json([
                "message" => "Dados do Contrato atualizados com sucesso", $order
            ], 200);
            } else {
            return response()->json([
                "message" => "Não há Contrato com este ID"
            ], 404);
            
        }
    }
}
