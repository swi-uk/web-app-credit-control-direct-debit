<x-layout.auth title="Portal Login">
    <div class="text-h2">Customer Portal</div>
    <p class="text-small">Sign in with a one-time link.</p>

    @if (!empty($sent))
        <x-ui.alert type="success">
            If your email exists in our system, a login link has been sent.
        </x-ui.alert>
    @endif

    @if ($errors->any())
        <x-ui.alert type="danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    <form method="POST" action="{{ route('portal.login.send') }}">
        @csrf
        <x-ui.input name="email" label="Email" type="email" />
        <x-ui.button variant="primary">Send login link</x-ui.button>
    </form>
</x-layout.auth>
