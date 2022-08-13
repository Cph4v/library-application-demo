<?php

class Flash
{
    public static function set($type, $message)
    {
        // TODO: Implement set method here
        $_SESSION['flash'] = compact('type','message');
    }

    public static function get()
    {
        // TODO: Implement get method here
        $get = null;
        if(isset($_SESSION['flash'])){
            $get = $_SESSION['flash'];
            $_SESSION['flash'] = null;
        }
        return $get;
    }
    
}
