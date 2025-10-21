@include("parts.header")
<p>Vážená/ý {{ $name }},</p>
<p>vaše heslo bolo úspešne resetované</p>
<br />

<p>Vaše nové heslo je: <em>{{ $password }}</em></p>

<br />

<p>s pozdravom</p>
<p>Systém ISOP UKF</p>
@include("parts.footer")