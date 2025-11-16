<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar um novo usuário administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎬 Criar Novo Administrador');
        $this->newLine();

        $name = $this->ask('Nome do administrador');
        $email = $this->ask('Email do administrador');
        $password = $this->secret('Senha (mínimo 8 caracteres)');
        $passwordConfirmation = $this->secret('Confirme a senha');

        // Validação
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->error('❌ Erros de validação:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("  - $error");
            }
            return 1;
        }

        if ($password !== $passwordConfirmation) {
            $this->error('❌ As senhas não coincidem!');
            return 1;
        }

        // Criar administrador
        $admin = Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->newLine();
        $this->info('✅ Administrador criado com sucesso!');
        $this->newLine();
        $this->line("👤 Nome: {$admin->name}");
        $this->line("📧 Email: {$admin->email}");
        $this->line("🔑 ID: {$admin->id}");
        $this->newLine();
        $this->info('Acesse o painel em: ' . url('/admin'));

        return 0;
    }
}
