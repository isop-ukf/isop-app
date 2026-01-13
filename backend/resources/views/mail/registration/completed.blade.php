@include("parts.header")
<p>Vážená/ý {{ $name }},</p>
<p>vaša registrácia do systému ISOP UKF prebehla úspešne!</p>
<br />

<p>Aktivujte účet pomocou nasledujúceho linku:</p>
<br />
<p>
    <a
        href="{{ config('app.frontend_url') }}/login/activation/{{ $activation_token }}">{{ config('app.frontend_url') }}/login/activation/{{ $activation_token }}</a>
</p>

<br />

<p>Ďakujeme za vašu registráciu.</p>

<br />

<p>s pozdravom</p>
<p>Systém ISOP UKF</p>
@include("parts.footer")