
<?php
return call_user_func(function() {
    $data = <<<EOS
a:2:{s:7:"profile";a:4:{i:0;s:2:"id";i:1;s:10:"fk_user_id";i:2;s:10:"first_name";i:3;s:9:"last_name";}s:4:"user";a:4:{i:0;s:2:"id";i:1;s:5:"login";i:2;s:8:"password";i:3;s:11:"description";}}
EOS;
    return unserialize($data);
});