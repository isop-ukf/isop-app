<!DOCTYPE html>
<html lang="sk">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dohoda o odbornej praxi študenta</title>
        <style>
            @page {
                margin: 2cm;
            }

            body {
                font-family: "Calibri", Times, serif;
                font-size: 11pt;
                line-height: 1.1;
                color: #000;
                max-width: 210mm;
                margin: 0 auto;
                padding: 20mm;
                background: #fff;
                text-align: justify;
            }

            .header-right {
                text-align: right;
                font-style: italic;
                margin-bottom: 20px;
            }

            .title {
                text-align: center;
                font-weight: bold;
            }

            .subtitle {
                text-align: center;
                font-size: 11pt;
                margin-bottom: 20px;
            }

            .section-header {
                font-weight: bold;
                margin-top: 20px;
                margin-bottom: 10px;
            }

            .university-info,
            .provider-info,
            .student-info {
                margin-bottom: 20px;
                font-size: 11pt;
            }

            .indented {
                margin-left: 20px;
            }

            .field-line {
                border-bottom: 1px dotted #000;
                display: inline-block;
                min-width: 300px;
            }

            .roman-numeral {
                text-align: center;
                font-weight: bold;
                margin: 25px 0 15px;
            }

            .list-item {
                margin-bottom: 4px;
            }

            .sub-list {
                margin-left: 30px;
            }

            #signature-section {
                margin-top: 50px;
                display: flex;
                justify-content: space-between;
            }

            .signature-block {
                text-align: center;
            }

            .signature-line {
                border-top: 1px dotted #000;
                margin-top: 40px;
                padding-top: 5px;
                min-width: 200px;
            }

            .date-line {
                display: inline-block;
                border-bottom: 1px dotted #000;
                min-width: 100px;
            }

            ul {
                list-style: none;
                padding-left: 0;
            }
        </style>
    </head>

    <body>
        <div class="header-right">
            Platnosť tlačiva od 1.10.2024 (aplikovaná informatika)
        </div>

        <div class="title">
            DOHODA O ODBORNEJ PRAXI ŠTUDENTA
        </div>
        <div class="subtitle">
            uzatvorená v zmysle § 51 Občianskeho zákonníka a Zákona č. 131/2002 Z.z. o vysokých školách
        </div>

        <div class="university-info">
            <strong>Univerzita Konštantína Filozofa v Nitre</strong><br>
            <div class="indented">
                Fakulta prírodných vied a informatiky<br>
                Trieda A. Hlinku 1, 949 01 Nitra<br>
                v zastúpení prof. RNDr. František Petrovič, PhD., MBA - dekan fakulty<br>
                e-mail: dfpvai@ukf.sk &nbsp;&nbsp;&nbsp; tel. 037/6408 555
            </div>
        </div>

        <div class="provider-info">
            <strong>Poskytovateľ odbornej praxe (organizácia, resp. inštitúcia)</strong><br>
            <div class="indented">
                <em>{{ $company->name }}</em> v zastúpení <em>{{ $companyContact->name }}</em>
            </div>
        </div>

        <div class="student-info">
            <strong>Študent:</strong><br>
            <div class="indented">
                <table style="border: none; width: 100%; line-height: 1;">
                    <tr>
                        <td style="width: 260px;">Meno a priezvisko:</td>
                        <td><em>{{ $student->name }}</em></td>
                    </tr>
                    <tr>
                        <td>Adresa trvalého bydliska:</td>
                        <td><em>{{ $student_address }}</em></td>
                    </tr>
                    <tr>
                        <td>Kontakt študenta FPVaI UKF v Nitre:</td>
                        <td><em>{{ $student->email }}</em></td>
                    </tr>
                    <tr>
                        <td>Študijný program:</td>
                        <td>aplikovaná informatika</td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="margin: 20px 0;">
            uzatvárajú túto dohodu o odbornej praxi študenta.
        </div>

        <div class="roman-numeral">I. Predmet dohody</div>

        <div>
            Predmetom tejto dohody je vykonanie odbornej praxe študenta v rozsahu 150 hodín, v termíne od
            <em>{{ $internship->start->format('d.m.Y') }}</em> do
            <em>{{ $internship->end->format('d.m.Y') }}</em> bezodplatne.
        </div>

        <div class="roman-numeral">II. Práva a povinnosti účastníkov dohody</div>

        <div class="section-header">1. Fakulta prírodných vied a informatiky Univerzity Konštantína Filozofa v Nitre:
        </div>
        <div class="list-item">
            1.1 Poverí svojho zamestnanca: Mgr. Dominik Halvoník, PhD. (ďalej garant odbornej praxe) garanciou odbornej
            praxe.
        </div>
        <div class="list-item">
            1.2 Prostredníctvom garanta odbornej praxe:
            <div class="sub-list">
                a) poskytne študentovi:
                <div style="margin-left: 20px;">
                    - informácie o organizácii praxe, o podmienkach dojednania dohody o odbornej praxi, o obsahovom
                    zameraní odbornej praxe a o požiadavkách na obsahovú náplň správy z odbornej praxe,<br>
                    - návrh dohody o odbornej praxi študenta,
                </div>
                b) rozhodne o udelení hodnotenia „ABS" (absolvoval) študentovi na základe dokladu „Výkaz o vykonanej
                odbornej praxi", vydaného poskytovateľom odbornej praxe a na základe študentom vypracovanej správy o
                odbornej praxi, ktorej súčasťou je verejná obhajoba výsledkov odbornej praxe,<br><br>
                c) spravuje vyplnenú a účastníkmi podpísanú dohodu o odbornej praxi.
            </div>
        </div>

        <div class="section-header">2. Poskytovateľ odbornej praxe:</div>
        <div class="list-item">
            2.1 poverí svojho zamestnanca (tútor - zodpovedný za odbornú prax v organizácii)
            <em>{{ $companyContact->name }}</em>, ktorý bude dohliadať na dodržiavanie dohody o
            odbornej praxi, plnenie obsahovej náplne odbornej praxe a bude nápomocný pri získavaní potrebných údajov pre
            vypracovanie správy z odbornej praxe,
        </div>
        <div class="list-item">
            2.2 na začiatku praxe vykoná poučenie o bezpečnosti a ochrane zdravia pri práci v zmysle platných predpisov,
        </div>

        <div style="page-break-before: always;"></div>

        <div class="list-item">
            2.3 vzniknuté organizačné problémy súvisiace s plnením dohody rieši spolu s garantom odbornej praxe,
        </div>
        <div class="list-item">
            2.4 po ukončení odbornej praxe vydá študentovi „Výkaz o vykonanej odbornej praxi", ktorý obsahuje popis
            vykonávaných činností a stručné hodnotenie študenta a je jedným z predpokladov úspešného ukončenia predmetu
            Odborná prax,
        </div>
        <div class="list-item">
            2.5 umožní garantovi odbornej praxe a garantovi študijného predmetu kontrolu študentom plnených úloh.
        </div>

        <div class="section-header">3. Študent FPVaI UKF v Nitre:</div>
        <div class="list-item">
            3.1 osobne zabezpečí podpísanie tejto dohody o odbornej praxi študenta,
        </div>
        <div class="list-item">
            3.2 zodpovedne vykonáva činnosti pridelené tútorom odbornej praxe,
        </div>
        <div class="list-item">
            3.3 zabezpečí doručenie dokladu „Výkaz o vykonanej odbornej praxi" najneskôr v termínoch predpísaných
            garantom pre daný semester,
        </div>
        <div class="list-item">
            3.4 okamžite, bez zbytočného odkladu informuje garanta odbornej praxe o problémoch, ktoré bránia plneniu
            odbornej praxe.
        </div>

        <div class="roman-numeral">III. Všeobecné a záverečné ustanovenia</div>

        <div class="list-item">
            1. Dohoda sa uzatvára na dobu určitú. Dohoda nadobúda platnosť a účinnosť dňom podpísania obidvomi zmluvnými
            stranami. Obsah dohody sa môže meniť písomne len po súhlase jej zmluvných strán.
        </div>
        <div class="list-item">
            2. Diela vytvorené študentom sa spravujú režimom zamestnaneckého diela podľa § 90 zákona č. 185/2015 Z. z.
            (Autorský zákon). V prípade, že sa dielo stane školským dielom podľa § 93 citovaného zákona, Fakulta
            prírodných vied a informatiky Univerzity Konštantína Filozofa v Nitre týmto udeľuje Poskytovateľovi odbornej
            praxe výhradnú, časovo a teritoriálne neobmedzenú, bezodplatnú licenciu na akékoľvek použitie alebo
            sublicenciu diel vytvorených študentom počas trvania odbornej praxe.
        </div>
        <div class="list-item">
            3. Dohoda sa uzatvára v 3 vyhotoveniach, každá zmluvná strana obdrží jedno vyhotovenie dohody.
        </div>

        <div id="signature-section" style="margin-top: 40px;">
            <div>V Nitre, dňa <em>{{ now()->format('d.m.Y') }}</em>.</div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 60px;">
            <div class="signature-block">
                <div class="signature-line">
                    prof. RNDr. František Petrovič, PhD., MBA<br>
                    dekan FPVaI UKF v Nitre
                </div>
            </div>

            <br>
            <br>
            <br>

            <div class="signature-block">
                <div class="signature-line">
                    {{ $companyContact->name }}<br>
                    štatutárny zástupca pracoviska odb. praxe
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 60px;">
            <div class="signature-line" style="display: inline-block;">
                {{ $student->name }}
            </div>
        </div>
    </body>

</html>