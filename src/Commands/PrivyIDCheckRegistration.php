<?php

namespace Yamisok\Unipin\Commands;

use Illuminate\Console\Command;
use BlackIT\PrivyID\PrivyIDFacade as PrivyID;

class PrivyIDCheckRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'privyid:registration_check {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking order status';

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
        $token = $this->arguments()['token'];
        var_dump(PrivyID::checkRegistrationStatus($token));
    }
}
