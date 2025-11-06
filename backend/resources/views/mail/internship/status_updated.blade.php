@include("parts.header")
<p>Vážená/ý {{ $studentName }},</p>
<p>stav vašej praxe vo firme {{ $companyName }} bola aktualizovaná zo stavu "{{ $oldStatus }}" na
    "{{ $newStatus }}".</p>
<p>Zmenu vykonal <em>{{ $changedByName }}</em>.</p>
<br />

<p>Poznámka: <em>{{ $note }}</em>.</p>

<br />

<p>s pozdravom</p>
<p>Systém ISOP UKF</p>
@include("parts.footer")