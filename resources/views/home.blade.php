@extends('layouts.app')

@section('content')
<div class="container">

    <div class="container">
        <h3 align="center">Chat Application using PHP Ajax Jquery</a></h3><br />
        <div class="row">
            <div class="col-md-8 col-sm-6">
                <h4>Online User</h4>
            </div>
            <div class="col-md-4 col-sm-3">
                <p align="right">Hi - {{Auth::user()->name}} -
                    <a href="javascript:;" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="nav-link">
                        <i data-feather="log-out"></i>
                        <span>Log Out</span>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </a>
                </p>
            </div>
        </div>
        <div class="table-responsive">
            <div id="user_details"></div>
            <div id="user_model_details"></div>
        </div>
    </div>

    <!-- <div id="group_chat_dialog" title="Group Chat Window">
        <div id="group_chat_history" style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;">

        </div>
        <div class="form-group">
            <textarea name="group_chat_message" id="group_chat_message" class="form-control"></textarea>!
            <div class="chat_message_area">
                <div id="group_chat_message" contenteditable class="form-control">

                </div>
                <div class="image_upload">
                    <form id="uploadImage" method="post" action="upload.php">
                        <label for="uploadFile"><img src="upload.png" /></label>
                        <input type="file" name="uploadFile" id="uploadFile" accept=".jpg, .png" />
                    </form>
                </div>
            </div>
        </div>
        <div class="form-group" align="right">
            <button type="button" name="send_group_chat" id="send_group_chat" class="btn btn-info">Send</button>
        </div>
    </div> -->
</div>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

<script>  
    $(document).ready(function(){
        fetch_user();
        setInterval(function(){
            update_last_activity();
            fetch_user();
            update_chat_history_data();
        }, 5000);

        function fetch_user(){
            $.ajax({
                url:"{{ route('fetch-user') }}",
                method:"POST",
                success:function(data){
                    $('#user_details').html(data);
                }
            })
        }

        function update_last_activity(){
            $.ajax({
                url:"{{ route('update-last-activity') }}",
                success:function()
                {

                }
            })
        }

        function make_chat_dialog_box(to_user_id, to_user_name){
            var modal_content = '<div id="user_dialog_'+to_user_id+'" class="user_dialog" title="You have chat with '+to_user_name+'">';
            modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">';
            modal_content += fetch_user_chat_history(to_user_id);
            modal_content += '</div>';
            modal_content += '<div class="form-group">';
            modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="form-control chat_message"></textarea>';
            modal_content += '</div><div class="form-group" align="right">';
            modal_content+= '<span class="btn btn-primary btn-file">Photo<input type="file" name="uploadFile" id="uploadFile" accept=".jpg, .png" /></span> <button type="button" name="send_chat" id="'+to_user_id+'" class="btn btn-info send_chat">Send</button></div></div>';
            $('#user_model_details').html(modal_content);
        }

        $(document).on('click', '.start_chat', function(){
            var to_user_id = $(this).data('touserid');
            var to_user_name = $(this).data('tousername');
            make_chat_dialog_box(to_user_id, to_user_name);
            $("#user_dialog_"+to_user_id).dialog({
                autoOpen:false,
                width:400
            });
            $('#user_dialog_'+to_user_id).dialog('open');
            $('#chat_message_'+to_user_id).emojioneArea({
                pickerPosition:"top",
                toneStyle: "bullet"
            });
        });

        $(document).on('click', '.send_chat', function(){
            var to_user_id = $(this).attr('id');
            var chat_message = $('#chat_message_'+to_user_id).val();
            $.ajax({
                url:"{{ route('insert-chat') }}",
                method:"POST",
                data:{to_user_id:to_user_id, chat_message:chat_message},
                success:function(data){
                    $('#chat_message_'+to_user_id).val('');
                    var element = $('#chat_message_'+to_user_id).emojioneArea();
                    element[0].emojioneArea.setText('');
                    $('#chat_history_'+to_user_id).html(data);
                }
            })
        });

        function fetch_user_chat_history(to_user_id){
            var to_user_id = $('.send_chat').attr('id')
            $.ajax({
                url:"{{ route('fetch-user-chat-history') }}",
                method:"POST",
                data:{to_user_id:to_user_id},
                success:function(data){
                    $('#chat_history_'+to_user_id).html(data);
                }
            })
        }

        function update_chat_history_data(){
            $('.chat_history').each(function(){
                var to_user_id = $(this).data('touserid');
                fetch_user_chat_history(to_user_id);
            });
        }

        $(document).on('click', '.ui-button-icon', function(){
            $('.user_dialog').dialog('destroy').remove();
            $('#is_active_group_chat_window').val('no');
        });

        $(document).on('focus', '.chat_message', function(){
            var is_type = 'yes';
            $.ajax({
                url:"{{route('update-is-type-status')}}",
                method:"POST",
                data:{is_type:is_type},
                success:function(){

                }
            })
        });

        $(document).on('blur', '.chat_message', function(){
            var is_type = 'no';
            $.ajax({
                url:"{{route('update-is-type-status')}}",
                method:"POST",
                data:{is_type:is_type},
                success:function(){

                }
            })
        });

        $(document ).on('change','#uploadFile' , function(e){
            e.preventDefault();
            let formData = new FormData();
            var imageFile = $('#uploadFile')[0].files[0];
            var to_user_id = $('.send_chat').attr('id');
            formData.append('imageFile', imageFile);
            formData.append('to_user_id', to_user_id);

            $.ajax({
                data: formData,
                url: "{{ route('upload') }}",
                type: "POST",
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#uploadFile').val(null);
                }
            });
        });
    });  
</script>

@endsection
