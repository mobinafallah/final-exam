<?php 

namespace App\Controller;
use App\Model\User;
class FrontController{
    public static function home(){
        return view('home.php');
    }
    public static function register_successful(){
        return view('register_successful.php');
    }
    public static function dashboard(){
        return view('dashboard.php');
    }
}