<?php

namespace App\Filament\Portal\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected string $view = 'filament.auth.login';

    public function getMaxWidth(): Width|string|null
    {
        return 'max-w-[420px]';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Roxas Memorial Provincial Hospital';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString(
            '<span class="block text-sm font-semibold text-teal-800">Ward Requisition Portal</span>'.
            '<span class="block text-xs text-slate-500 mt-1">For CSR, HDU, Dialysis, Pharmacy and Yakap staff</span>'
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
            ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email address')
            ->email()
            ->required()
            ->autocomplete('email')
            ->autofocus()
            ->extraInputAttributes(['class' => 'min-h-[52px] text-sm']);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable()
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['class' => 'min-h-[52px] text-sm']);
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Sign in')
            ->submit('authenticate')
            ->extraAttributes(['class' => 'w-full min-h-[52px] text-base font-bold justify-center']);
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => 'The email or password is not correct.',
        ]);
    }
}
