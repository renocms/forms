<?php
/** @var \Reno\Forms\Containers\FormContainer $formContainer */
/** @var \Reno\Forms\Containers\ConsentContainer[] $consents */
/** @var string $submitUrl */
/** @var string $formRequest */
?>
<form method="post" action="{{ $submitUrl }}" class="ajax">
    @csrf

    @yield('fields')

    @if (!empty($consents))
        @foreach ($consents as $consent)
            <label>
                <input type="checkbox" name="consent[{{ $consent->getId() }}]" value="1" checked>
                {!! $consent->getConsent()->getText() !!}
            </label>
        @endforeach
    @endif

    <button type="submit">{{ __('forms::forms.submit') }}</button>
</form>
