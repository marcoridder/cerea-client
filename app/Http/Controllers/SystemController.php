<?php

namespace App\Http\Controllers;


class SystemController extends Controller
{
    public function __construct(

    ){}

    public function reboot()
    {
        exec("sudo reboot");
    }

    public function off()
    {
        exec("sudo init 0");
    }

}
