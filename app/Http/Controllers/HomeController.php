<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::where('id', '<>', auth()->id())->get();
        return view('home', compact('users'));
    }

    public function sendToken(Request $request)
    {
        $fcmToken = $request->token;
        $user = User::findOrFail($request->user_id);

        if($user->fcm_token != $fcmToken){
            $user->fcm_token = $fcmToken;
            $user->save();
        }

        response()->json(['success' => true, 'message' => 'Token salvo com sucesso!']);
    }

    public function sendPush(Request $request)
    {
        $userId = $request->id;
        $user = User::find($userId);
        $data = Carbon::createFromFormat('Y-m-d H:i:s', now())->format('d/m/Y H:i:s');
        $this->broadcastMessage($user->name, $data, $userId);
        return redirect()->back();
    }

    private function broadcastMessage($nome, $dataHora, $userId)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('Notificação do usuário: '.$nome);
        $notificationBuilder->setBody('Doutor(a), o paciente '.$nome.' está aguardando o início da consulta marcada para '.$dataHora.'.')
                            ->setSound('default')
                            ->setClickAction("{{ url('/') }}");
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'nome' => $nome,
            'data_hora'=> $dataHora
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = User::find($userId)->fcm_token;

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        return $downstreamResponse->numberSuccess();
    }
}
