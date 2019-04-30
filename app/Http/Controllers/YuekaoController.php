<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class YuekaoController extends Controller
{
    //
    public function yuekao(){
        echo $_GET['echostr'];
    }
}
