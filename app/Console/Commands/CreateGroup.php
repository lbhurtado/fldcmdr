<?php

namespace App\Console\Commands;

use App\Group;
use Illuminate\Console\Command;

class CreateGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:make:group
                            {name : The name of the group}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a campaign group';

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
        $name = $this->argument('name');

        Group::build($name);

        return 0;
    }
}
