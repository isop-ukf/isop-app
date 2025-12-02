@include("parts.header")

@if($recipiantIsStudent)
    <p>Vážená/ý {{ $internship->student->name }},</p>
@else
    <p>Vážená/ý {{ $internship->company->contactPerson->name }},</p>
@endif

<p>oznamujeme Vás o zmene praxe:</p>
@if(!$recipiantIsStudent)
    <p>Študent: <em>{{ $internship->student->name }} ({{ $internship->student->email }},
            {{ $internship->student->phone }})</em></p>
@endif
<p>Stav: <em>{{ $oldStatus }}</em> <strong>-></strong> <em>{{ $newStatus }}</em></p>
<p>Poznámka: <em>{{ $note }}</em>.</p>
<p>Zmenu vykonal <em>{{ $changedBy->name }}</em>.</p>
<br />

<p>S pozdravom.</p>
<p>Systém ISOP UKF</p>
@include("parts.footer")