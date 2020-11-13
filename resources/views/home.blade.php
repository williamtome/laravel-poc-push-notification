@extends('layouts.app')

@section('content')
<div id="app" class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @if(Session::has('message'))
                <div class="alert alert-success">
                    {{session('message')}}
                </div>
            @endif
            <div class="card">

                    <div class="card-header">Send push to Users</div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <form action="{{ route('home.send-push') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$user->id}}" />

                                            <input class="btn btn-primary" type="submit" value="Send Push">
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

            </div>
        </div>
    </div>
</div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
    const messaging = firebase.messaging();
    messaging.usePublicVapidKey("BOZ2NkeE4W_2bUZqw7fKTmnXUOJhXQYEeWeRJ-VtjesivYFjBoThgZKZNs6WBUUccr0jDU6Kc_JW-Po2TBfuCdg");
    messaging.requestPermission()
        .then(function () {
            return messaging.getToken()
        })
        .catch(function (err) {
            console.log("Unable to get permission to notify.", err);
        });

    const sendTokenToServer = (token) => {
        const user_id = {{ Auth::user()->id }}
        axios.post("{{ route('home.send-token') }}", {
            token, user_id
        }).then(res => {
            console.log(res)
        });
    }

    function retrieveToken(){
        messaging.getToken().then((currentToken) => {
            if (currentToken) {
                // console.log(currentToken);
                sendTokenToServer(currentToken);
                // updateUIForPushEnabled(currentToken);
            }
        })
        .catch(function(err) {
            console.log('An error occurred while retrieving token. ', err);
            //showToken('Error retrieving Instance ID token. ', err);
            // setTokenSentToServer(false);
        });
    }

    retrieveToken();
    messaging.onTokenRefresh(() => {
        retrieveToken();
    });

    messaging.onMessage((payload) => {
        console.log('Mensagem recebida.');
        console.log(payload);
        const title = payload.notification.title;
        const message = payload.notification.body;
        swal(title, message);
    });


    </script>
@endsection
