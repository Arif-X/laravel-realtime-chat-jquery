## Note

Tambahkan kode berikut pada file "AuthenticatesUsers" dan "RegistersUsers"
```
use App\Models\LoginDetail;
```

Tambahkan kode berikut pada function "authenticated" di file "AuthenticatesUsers"
```
$check = LoginDetail::where('user_id', $user->id)->first();
if(empty($check)){
    LoginDetail::create(
        [
            'user_id' => $user->id,
            'last_activity' => now(),
            'is_type' => 0
        ]
    );
} else {
    LoginDetail::where('user_id', $user->id)->update(
        [
            'user_id' => $user->id,
            'last_activity' => now(),
            'is_type' => 0
        ]
    );
}
```

Tambahkan kode berikut pada function "authenticated" di file "AuthenticatesUsers"
```
LoginDetail::create(
    [
        'user_id' => $user->id,
        'last_activity' => now(),
        'istype' => 0
    ]
);
```
