<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\ChatService;
use Auth;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\LoginDetail;

class ChatController extends Controller
{
    // Chat service start
    

    // Chat service end

    // Chat control start
    public function fetch_user(){
        $user = User::where('id', '!=', Auth::user()->id)->get();
        $output = '
        <table class="table table-bordered table-striped">
        <tr>
        <th width="70%">Username</td>
        <th width="20%">Status</td>
        <th width="10%">Action</td>
        </tr>
        ';

        foreach($user as $row){
            $status = '';
            $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
            $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);

            $user_last_activity = ChatService::fetch_user_last_activity($row['id']);
            if($user_last_activity > $current_timestamp) {
                $status = '<span class="badge badge-success">Online</span>';
            } else {
                $status = '<span class="badge badge-danger">Offline</span>';
            }

            $output .= '
            <tr>
            <td>'.$row['name'].' '.ChatService::count_unseen_message($row['id'], Auth::user()->id).' '.ChatService::fetch_is_type_status($row['id']).'</td>
            <td>'.$status.'</td>
            <td><span type="button" class="badge badge-success start_chat" data-touserid="'.$row['id'].'" data-tousername="'.$row['name'].'">Start Chat</span></td>
            </tr>
            ';
        }

        $output .= '</table>';

        return $output;
    }

    public function fetch_user_chat_histories(Request $request){
        return ChatService::fetch_user_chat_history(Auth::user()->id, $request->to_user_id);
    }

    public function insert_chat(Request $request){
        $insert = ChatMessage::insert([
            'to_user_id' => $request->to_user_id,
            'from_user_id' => Auth::user()->id,
            'chat_message' => $request->chat_message,
            'timestamp' => now(),
            'status' => 1
        ]);

        if($insert){
            return ChatService::fetch_user_chat_history(Auth::user()->id, $request->to_user_id);
        }
    }

    public function update_is_type_status(Request $request){
        return LoginDetail::where('user_id', Auth::user()->id)->update([
            'is_type' => $request->is_type
        ]);
    }

    public function update_last_activity(){
        return LoginDetail::where('user_id', Auth::user()->id)->update([
            'last_activity' => now()
        ]);
    }

    // Chat control end
}
