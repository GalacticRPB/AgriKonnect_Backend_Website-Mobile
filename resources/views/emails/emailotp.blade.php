@component('mail::message')
# Welcome to AgriKonnect

Dear {{ $email }}

Kindly verify your account using the OTP code below.

# Code: {{ $otp }}
Thanks,<br>
{{ config('app.name') }}
@endcomponent
