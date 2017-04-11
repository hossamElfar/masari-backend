<?php

namespace App\Http\Controllers\API;

//use App\Message;
use App\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $threads = Thread::forUser(Auth::user()->id)->orderBy('created_at','desc')->get();
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        foreach ($threads as $thread) {
            $last_message = $thread->messages()->orderBy('created_at','desc')->get()[0]->body;
            $participants = $thread->participants()->get();

            foreach ($participants as $participant){
                if (User::find($participant->id)->id != $thread->messages()->orderBy('created_at','desc')->get()[0]->user_id){
                    $thread['receiver'] = User::find($participant->id);
                }
            }
            $thread['last_message'] = $last_message;
            $thread['last_sender']= User::find($thread->messages()->orderBy('created_at','desc')->get()[0]->user_id);
        }
        $data['data']['threads'] = $threads;
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $thread = Thread::create(
            [
                'subject' => $input['subject'],
            ]
        );
        // Message
        Message::create(
            [
                'thread_id' => $thread->id,
                'user_id' => Auth::user()->id,
                'body' => $input['message'],
            ]
        );
        // Sender
        Participant::create(
            [
                'thread_id' => $thread->id,
                'user_id' => Auth::user()->id,
                'last_read' => new Carbon(),
            ]
        );
        $thread->addParticipant($input['receiver']);
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data'] = null;
        return response()->json($data, 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $data['statues'] = "404 not found";
            $data['error'] = "Not found";
            $data['data'] = null;
            return response()->json($data, 404);
        }
        $thread['messages'] = $thread->messages()->get();
        foreach ($thread['messages'] as $message) {
            $message['sender'] = $message->recipients()->get();
            $message['receiver'] = $message->participants()->first()->get()[0];
        }
        $data['statues'] = "200 ok";
        $data['error'] = null;
        $data['data']['thread'] = $thread;
        return response()->json($data, 200);
    }

    public function sendMessage(Request $request, $id)
    {
        $input = $request->all();
        $thread = Thread::findOrFail($id);
        // Message
        Message::create(
            [
                'thread_id' => $thread->id,
                'user_id' => Auth::user()->id,
                'body' => $input['message'],
            ]
        );
        // Sender
        Participant::create(
            [
                'thread_id' => $thread->id,
                'user_id' => Auth::user()->id,
                'last_read' => new Carbon(),
            ]
        );
        $thread->addParticipant($input['receiver']);
        $data['statues'] = "200 Ok";
        $data['error'] = null;
        $data['data'] = null;
        return response()->json($data, 200);
    }

}
