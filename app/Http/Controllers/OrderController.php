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
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array();

        $newformatDate;
        if (!isset($request->horario_inicial)) $messages['errors']['horario.undefined'] = 'Você não informou o Horario marcado para realização do Contrato';
        if (!isset($request->dia )) {
            $messages['errors']['dia.undefined'] = 'Você não informou o Dia do Contrato';
        } else {
            $dataTyped = explode("/", $request->dia);
            $newformatDate = $dataTyped[2]."-".$dataTyped[1]."-".$dataTyped[0];

        }
        if (!isset($request->duracao ))  $messages['errors']['duracao.undefined'] = 'Você não informou a Duração do Contrato';
        if (!isset($request->endereco )) $messages['errors']['endereco.undefined'] = 'Você não não informou o ID de um Endereço';
        if (!isset($request->cliente )) $messages['errors']['cliente.undefined'] = 'Você não informou o ID de um Cliente';
        if (!isset($request->profissional )) $messages['errors']['profissional.undefined'] = 'Você não informou o ID de um Profissional';

       
        if (!empty($messages['errors'])){
            return response()->json($messages, 409);
        }
        $order = new Order;
        $order->fill($request->all());
        $order->dia = $newformatDate;
        $order->horario_inicial = $request->horario_inicial;
        $order->save();
        $messages['success']['order.created'] = 'Seu Pedido de Limpeza foi agendado!';

        return response()->json([$messages, $order], 201);
    }
    public function delete ($id) {
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array();

        if (Order::where('id', $id)->exists()){
            $order = Order::where('id', $id)->first();
            $order->delete();
            $messages['success']['order.deleted'] = "Contrato de ID ".$id." foi deletado com sucesso";
            return response()->json($messages, 200);
         } else {
            $messages['errors']['order.unknown'] = "Não há Contrato com este ID";
            return response()->json($messages, 404);
         }
    }

    public function read ($id){
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array();

        if (Order::where('id', $id)->exists()) {
            $order = Order::where('id', $id)->get();
            return response($order, 200);

          } else {
            $messages['errors']['order.unknown'] = "Não há Contrato com este ID.";
            return response()->json($messages, 404);
          }
        return response()->json($order,  200);
    }

    public function readAndFilter (Request $request){
        $status; $cliente; $profissional; $dia;
        $orders = Order::all();
        if (isset($request->cliente) && !is_null($request->cliente)) {
            $cliente = $request->cliente;
            $orders = $orders->where('cliente', $cliente);
        }
        if (isset($request->status) && !is_null($request->status)) {
            $status = $request->status;
            $orders = $orders->where('status', $status);
        }
        if (isset($request->profissional) && !is_null($request->profissional)) {
            $profissional = $request->profissional;
            $orders = $orders->where('profissional', $profissional);
        }
        if (isset($request->endereco) && !is_null($request->endereco)) {
            $endereco = $request->endereco;
            $orders = $orders->where('endereco', $endereco);
        }
        if (isset($request->dia) && !is_null($request->dia)){
            $dataTyped = explode("/", $request->dia);
            $newformatDate = $dataTyped[2]."-".$dataTyped[1]."-".$dataTyped[0];
            $orders = $orders->where('dia', '=', date('Y-m-d', strtotime($newformatDate)));
        } 
        return response()->json($orders,  200);
    }

    public function readAll (){
        $order = Order::all();
        return response()->json($order, 200);
        
    }
    public function update(Request $request, $id) {
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array();
        
        if (!is_null($request->endereco) && !Adress::where('id', $request->endereco)->exists()){
            $messages['errors']['adress.unknown'] = 'Este Endereço não está cadastrado';

        }
        if (!is_null($request->profissional) && !Professional::where('id', $request->profissional)->exists()){
            $messages['errors']['professional.unknown'] = 'Este Profissional não está cadastrado';

        }
        if (!is_null($request->cliente) && !Client::where('id', $request->cliente)->exists()){
            $messages['errors']['client.unknown'] = 'Este Cliente não está cadastrado';

        }
        if (!empty($messages['errors'])){
            return response()->json($messages, 409);
        }
        if (Order::where('id', $id)->exists()) {
            $order = Order::find($id);
            $order->endereco = is_null($request->endereco) ? $order->endereco : $request->endereco;
            $order->duracao = is_null($request->duracao) ?  $order->duracao : $request->duracao;
            $order->horario_inicial = is_null($request->horario) ?  $order->horario_inicial : $request->horario;
            $order->cliente = is_null($request->cliente) ?  $order->cliente : $request->cliente;
            $order->profissional = is_null($request->profissional) ?  $order->profissional : $request->profissional;
            $order->status = is_null($request->status) ?  $order->status : $request->status;
            if (!is_null($request->dia)){
                $dataTyped = explode("/", $request->dia);
                $newformatDate = $dataTyped[2]."-".$dataTyped[1]."-".$dataTyped[0];
                $order->dia =  $newformatDate;
            }

            $order->save();
            $messages['success']['order.updated'] = "Dados do Contrato atualizados com sucesso";
            return response()->json([$messages, $order], 200);
            } else {
                $messages['errors']['order.unknown'] = "Não há Contrato com este ID.";
                return response()->json([
                    "message" => "Não há Contrato com este ID"
                ], 404);
            
        }
    }
}
