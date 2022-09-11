<?php

namespace App\Service;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\LoginDetail;

class ChatService {
	function fetch_user_last_activity($user_id) {
        $data = LoginDetail::where('user_id', $user_id)->orderBy('last_activity', 'DESC')->limit(1)->get();

        foreach ($data as $row) {
            return $row['last_activity'];
        }
    }

    function get_name($user_id) {
        $data = User::where('id', $user_id)->select('name')->get();
        foreach($data as $row){
            return $row['name'];
        }
    }

    function fetch_user_chat_history($from_user_id, $to_user_id){
        $history = ChatMessage::where([
            ['from_user_id', $from_user_id],
            ['to_user_id', $to_user_id],
        ])->orWhere([
            ['to_user_id', $from_user_id],
            ['from_user_id', $to_user_id],
        ])->orderBy('timestamp', 'DESC')->get();

        $output = '<ul class="list-unstyled">';

        foreach($history as $row){
            $user_name = '';
            if($row["from_user_id"] == $from_user_id){
                $user_name = '<b class="text-success">You</b>';
            } else {
                $user_name = '<b class="text-danger">'.ChatService::get_name($row['from_user_id']).'</b>';
            } 
            $output .= '
            <li style="border-bottom:1px dotted #ccc">
            <p>'.$user_name.' - '.$row["chat_message"].'
            <div align="right">
            - <small><em>'.$row['timestamp'].'</em></small>
            </div>
            </p>
            </li>
            ';
        }

        $output .= '</ul>';
        $read = ChatMessage::where([
            ['from_user_id', $to_user_id],
            ['to_user_id', $from_user_id],
        ])->update([
            'status' => 0
        ]);

        return $output;
    }

    function count_unseen_message($from_user_id, $to_user_id){
        $count = ChatMessage::where([
            ['from_user_id', $from_user_id],
            ['to_user_id', $to_user_id],
            ['status', 1]
        ])
        ->count();
        
        $output = '';
        if($count > 0){
            $output = '<span class="badge badge-success">'.$count.'</span>';
        }
        return $output;
    }

    function fetch_is_type_status($user_id) {
        $data = LoginDetail::where('user_id', $user_id)->orderBy('last_activity', 'DESC')->limit(1)->get();

        $output = '';
        foreach($data as $row){
            if($row["is_type"] == 'yes'){
                $output = ' - <small><em><span class="text-muted">Typing...</span></em></small>';
            } else {
            	// $output = ' - <small><em><span class="text-muted">cok...</span></em></small>';
            }
        }
        return $output;
    }
}