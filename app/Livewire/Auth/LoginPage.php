<?php

namespace App\Livewire\Auth;

use App\Http\Api\Auth\Requests\LoginWithCredentialsRequest;
use App\Modules\Auth\Actions\LoginWithCredentialsAction;
use App\Modules\Auth\DataTransferObjects\LoginWithCredentialsData;
use App\Modules\Auth\Exceptions\UnableToLoginException;
use App\Modules\Auth\Responses\SessionLoginResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginPage extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function mount(): void
    {
        if (auth()->guard('web')->check()) {
            $this->redirectRoute('users.index', navigate: true);
        }
    }

    protected function rules(): array
    {
        return (new LoginWithCredentialsRequest)->rules();
    }

    public function login(): void
    {
        $this->validate();

        try {
            $response = resolve(LoginWithCredentialsAction::class)->execute(
                LoginWithCredentialsData::fromEmail(
                    email: $this->email,
                    password: $this->password,
                    remember: $this->remember,
                ),
            );
        } catch (UnableToLoginException) {
            throw ValidationException::withMessages([
                'email' => ['Unable to log in with the provided credentials.'],
            ]);
        }

        if ($response instanceof SessionLoginResponse && $response->twoFactor === false) {
            session()->regenerate();

            $this->redirectRoute('users.index', navigate: true);

            return;
        }

        throw ValidationException::withMessages([
            'email' => ['Two-factor authentication is not supported on this page.'],
        ]);
    }

    public function render(): View
    {
        return view('livewire.auth.login-page');
    }
}
