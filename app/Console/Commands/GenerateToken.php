<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateToken extends Command
{
    protected $signature = 'larabbs:generate-token';

    protected $description = '快速未用户生成token';
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $userId = $this->ask('输入用户 id');

        $user = User::find($userId);

        if (!$user) {
            return $this->error('用户不存在');
        }

        $ttl = 365*24*60;
        $this->info(\Auth::guard('api')->setTTL($ttl)->fromUser($user));
    }
}
