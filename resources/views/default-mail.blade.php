<?php
/** @var string $formTitle */
/** @var array<int, array{key: string, label: string, value: string}> $fieldRows */
?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $formTitle }}</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #222;">
<h2 style="margin: 0 0 16px 0;">{{ $formTitle }}</h2>

@if (empty($fieldRows))
    <p>{{ __('forms::forms.no_data') }}</p>
@else
    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; border-color: #d9d9d9;">
        <thead>
            <tr style="background: #f6f6f6;">
                <th align="left">{{ __('forms::forms.field') }}</th>
                <th align="left">{{ __('forms::forms.value') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fieldRows as $row)
                <tr>
                    <td style="vertical-align: top;">
                        {{ $row['label'] }}
                        @if ($row['label'] !== $row['key'])
                            <div style="font-size: 12px; color: #777;">{{ $row['key'] }}</div>
                        @endif
                    </td>
                    <td style="vertical-align: top; white-space: pre-wrap;">{{ $row['value'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
