<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Message extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'echo:time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        file_put_contents('/wwwroot/1809_weixin_shop/public/logs/test.log', date("Y-m-d H:i:s"), FILE_APPEND);
    }
}
