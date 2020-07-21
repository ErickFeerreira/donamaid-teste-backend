<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Professional;
use App\Order;
class ProfessionalController extends Controller
{
    public function create (Request $request){
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array();
        if (Professional::where('email', $request->email)->exists()) $messages['errors']['email.exists'] = 'Este e-mail já está cadastrado.';
        
        if (Professional::where('cpf', $request->cpf)->exists()) $messages['errors']['cpf.exists'] = 'Este cpf já está cadastrado.';
        
        if (!isset($request->email )) $messages['errors']['email.undefined'] = 'Você não informou um e-mail';
        if (!isset($request->cpf )) $messages['errors']['cpf.undefined'] = 'Você não informou um cpf';
        if (!isset($request->nome )) $messages['errors']['nome.undefined'] = 'Você não informou um nome';

       
        if (!empty($messages['errors'])){
            return response()->json($messages, 409);
        }
        $professional = Professional::create($request->all());
        $messages['success']['client.created'] = 'Sua conta de Profissional foi registrada com sucesso!';
        return response()->json([$messages, $professional], 201);
    }
    public function delete ($id) {
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array();

        if (Professional::where('id', $id)->exists()){
            $professional = Professional::where('id', $id)->first();
            $nome = $professional->nome;
            $professional->delete();
            $messages['success']['order.deleted'] = "Profissional ".$nome." de ID ".$id." foi deletado com sucesso";

            return response()->json($messages, 200);
         } else {
            $messages['errors']['order.unknown'] = "Não há Profissional com este ID";
            return response()->json($messages, 404);
         }
    }
    public function readAll (){
        $professional = Professional::all();
        return response()->json($professional, 200);
        
    }
    public function read ($id){
        $messages = array(); $messages['errors'] = array(); $messages['success'] = array();

        if (Professional::where('id', $id)->exists()) {
            $professional = Professional::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
            return response($professional, 200);

          } else {
            $messages['errors']['professional.unknown'] = "Não há Profissional com este ID.";
            return response()->json($messages, 404);
          }
        return response()->json($professional, 200);
    }
    public function update(Request $request, $id) {
        if (Professional::where('id', $id)->exists()) {
            $professional = Professional::find($id);
            $professional->nome = is_null($request->nome) ? $professional->nome : $request->nome;
            $professional->email = is_null($request->email) ? $professional->email : $request->email;

            $professional->save();
            $messages['success']['professional.updated'] = "Dados do Professional atualizados com sucesso";

            return response()->json([$messages, $professional], 200);
            } else {
                $messages['errors']['professional.unknown'] = "Não há Profissional com este ID.";
                return response()->json($messages, 404);
            
        }

    }
    public function getIdle(){
        $waitingProfessionals = Order::where('status', "!=", "0")->pluck('profissional')->toArray();
        return response()->json(Professional::whereNotIn('id', $waitingProfessionals )->get(), 200);
    }
}
