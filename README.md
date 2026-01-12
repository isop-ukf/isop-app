# Informačný systém odbornej praxe - ISOP

## CI Status

| Badge | Poznámka |
|-------|-------|
| [![Cypress E2E Tests](https://github.com/isop-ukf/isop-app/actions/workflows/cypress.yml/badge.svg)](https://github.com/isop-ukf/isop-app/actions/workflows/cypress.yml) | Kompletný test aplikácie |

## Integrácia s externými systémami (API)
Systém poskytuje API pre integráciu s exterými systémami. Každý endpoint sa začína s cestou `/api/external`. Výnimkou je cesta `/api/external/keys`, ktorá slúži na správu API kľúčov a je využitá frontendom.

### API kľúče
Pre využitie tento API je potrebné vytvoriť API kľúč. Kľúč môže vytvoriť iba garant (administrátor). Postup na vygenerovanie kľúča je nasledovný:
1. Prihláste sa do systému ako garant.
2. V dashboarde zvoľte "API Manažment."
3. Kliknite na tlačítko "Pridať."
4. Do poľa "Názov kľúča" napíšte nejaký názov pre tento API kľúč. Toto slúži na jednoduchšiu identifikáciu kľúča.
5. Kliknite na tlačítko "Vytvoriť."
6. Novo vygenerovaný kľúč sa vám zobrazí. Skopírujte ho na bezpečné miesto.

> **⚠️ POZOR:**
>   
> API kľúč sa zobrazí iba __RAZ__! Z bezpečnostných dôvodov __nie__ je ho možné znovu zobraziť po zatvorení dialógového okna!

### Autentikácia
Pri volaní akejkoľvek API endpointu pre externé systémy, API kľúč je potrebné uviesť ako Bearer Token v headeroch.

| **Header**      | **Hodnota**       |
|-----------------|-------------------|
| `Authorization` | Bearer [API kľúč] |

> **⚠️ POZOR:**
>   
> API endpointy použité frontendom __NIE__ sú funkčné pomocou API kľúčov! *Tie sú určené striktne na frontend s autorizáciou pomocou session cookies.*

### Formáty požiadaviek a odpovedí
Oficiálne testovaný a podporovaný formát je **JSON**. Vďaka backendovému frameworku iné formáty _môžu_ byť funknčké, avšak podpora nie je garantovaná.

Vo vašom kliente nastavte nasledoné headery na použitie JSONu:
| **Header**     | **Hodnota**        |
|----------------|--------------------|
| `Content-Type` | `application/json` |
| `Accept`       | `application/json` |

### Endpointy
#### GET `/api/external/internships`
Vráti zoznam praxí s možnosťou stránkovania a filtrovania. Pri úspešnom vykonaní vráti kód _200_.

##### Parametre
|     **Názov**     |            **Popis**            | **Pravidlá** | **Dátový typ** |  **Povinné?** |
|-------------------|---------------------------------|:------------:|:--------------:|:-------------:|
| `year`            | Rok praxe                       | -            |       int      |       ❌      |
| `company`         | Názov firmy                     | 3-32 znakov  |     string     |       ❌      |
| `study_programme` | Študijný odbor                  | 3-32 znakov  |     string     |       ❌      |
| `student`         | Meno študenta                   | 3-32 znakov  |     string     |       ❌      |
| `page`            | Stránka                         | >=1          |       int      |       ❌      |
| `per_page`        | Max. počet záznamov na 1 stranu | \[-1; 100\]  |       int      |       ❌      |

##### Príklad požiadavky
```json
{
    "year": 2025,
    "company": "AZ Tech s.r.o.",
    "study_programme": "2AI22m",
    "student": "Andrej",
    "page": 2,
    "per_page": 2
}
```

<details>

  <summary>cURL príklad</summary>

  ```sh
  curl -X GET "https://isop.ukf.sk/api/external/internships" \
    -H "Authorization: Bearer TOKEN" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{
          "year": 2025,
          "company": "AZ Tech s.r.o.",
          "study_programme": "2AI22m",
          "student": "Andrej",
          "page": 2,
          "per_page": 2
        }'
  ```
</details>

<details>
  <summary>Popis</summary>
  Vyhľadá praxe študentov, pre ktoré platia nasledovné podmienky:
  
  - boli evidované v roku _2025_
  - boli vykonané vo firme s takým názvom, ktorý obsahuje reťazec _AZ Tech s.r.o._
  - boli vykonané študentmi 2. ročníka magisterského štúdia aplikovanej informatiky (2AI22m)
  - plné meno študenta obsahuje reťazec _Andrej_

  Výsledok je _2._ strana vyhľadávania a vrátia sa maximálne _2_ výsledky.
</details>

##### Príklad odpovede
```json
{
  "total": 2,
  "data": [
    {
      "id": 1,
      "student": {
        "id": 22,
        "name": "Andrej Novák",
        "email": "andrej.novak@student.ukf.sk",
        "first_name": "Andrej",
        "last_name": "Novák",
        "phone": "+421049728827",
        "role": "STUDENT",
        "student_data": {
          "id": 1,
          "user_id": 22,
          "address": "Szabó Mall 201, Malatíny, 668 88",
          "personal_email": "gregor68@example.net",
          "study_field": "AI22m"
        }
      },
      "company": {
        "id": 11,
        "name": "AZ Tech s.r.o.",
        "address": "Andrea River 98160, Kuchyňa, 386 06",
        "ico": 461951,
        "contact": 12,
        "hiring": 1
      },
      "start": "07.12.1999",
      "end": "13.12.1999",
      "year_of_study": 2,
      "semester": "WINTER",
      "position_description": "aut",
      "proof": false,
      "report": false,
      "report_confirmed": false,
      "status": {
        "id": 1,
        "internship_id": 1,
        "status": "SUBMITTED",
        "changed": "2010-09-12 16:07:53",
        "note": "made by seeder", // ?
        "modified_by": {
          "id": 1,
          "name": "Test User",
          "email": "test@example.com",
          "first_name": "Test",
          "last_name": "User",
          "phone": "+421907444555",
          "role": "ADMIN"
        }
      }
    },
    {
      "id": 2,
      "student": {
        "id": 23,
        "name": "Andrej Malý",
        "email": "andrej.maly@student.ukf.sk",
        "first_name": "Andrej",
        "last_name": "Malý",
        "phone": "25319710",
        "role": "STUDENT",
        "student_data": {
          "id": 2,
          "user_id": 23,
          "address": "Stanislav Landing 80127, Čierna nad Tisou, 732 01",
          "personal_email": "edmund.cierny@example.net",
          "study_field": "AI22b"
        }
      },
      "company": {
        "id": 10,
        "name": "AZ Tech s.r.o.",
        "address": "Mečíř Estate 43861, Hodejovec, 321 68",
        "ico": 699698,
        "contact": 11,
        "hiring": 0
      },
      "start": "13.08.2001",
      "end": "19.08.2001",
      "year_of_study": 2,
      "semester": "SUMMER",
      "position_description": "ipsum",
      "proof": false,
      "report": false,
      "report_confirmed": false,
      "status": {
        "id": 2,
        "internship_id": 2,
        "status": "SUBMITTED",
        "changed": "1990-07-05 06:50:35",
        "note": "made by seeder", // ?
        "modified_by": {
          "id": 1,
          "name": "Test User",
          "email": "test@example.com",
          "first_name": "Test",
          "last_name": "User",
          "phone": "+421907444555",
          "role": "ADMIN"
        }
      }
    }
  ],
}
```

> **ℹ️ Poznámka:**
>
> V odpovedi sa vráti viac údajov kvôli backendu, ktoré nie sú uvedené vyššie, avšak vo väščine prípadov nie sú relevantné.
> Pre detaily si naštudujte serializáciu stránkovaných dát vo frameworku Laravel.

> **ℹ️ Poznámka:**
>
> Nulovateľné hodnoty sú označené komentármi so znakom `?`.

#### PUT `/api/external/internships/{internship_id}/status`
Aktualizuje stav praxe podľa jeho ID. Pri úspešnom vykonaní vráti kód _201_.

> **⚠️ POZOR:**
>   
> Tento endpoint je možné volať iba pre praxe, ktoré sú v stave _CONFIRMED_BY_ADMIN_ (potvrdené garantom).

Ak operácia nespĺňa požiadavky, vráti sa kód _422_ s popisom chyby v tele odpovede.

##### Parametre
| **Názov** |     **Popis**    |           **Pravidlá**          | **Dátový typ** |  **Povinné?** |
|-----------|------------------|:-------------------------------:|:--------------:|:-------------:|
| `status`  | Nový stav praxe  | `DEFENDED` alebo `NOT_DEFENDED` |     string     |       ✅      |
| `note`    | Poznámka k zmene | min. 1 znak                     |     string     |       ✅      |

##### Príklad požiadavky
```json
{
    "status": "DEFENDED",
    "note": "všetko ok"
}
```

<details>

  <summary>cURL príklad</summary>

  ```sh
  curl -X PUT "https://isop.ukf.sk/api/external/internships/123/status" \
    -H "Authorization: Bearer TOKEN" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{
          "status": "DEFENDED",
          "note": "všetko ok"
        }'
  ```
</details>

<details>
  
  <summary>Popis</summary>
  
  Aktualizuje stav praxe na stav _DEFENDED_ (obhájená) s poznámkou _všetko ok_.
</details>

##### Príklad odpovede
Pri úspešnej aktualizácii sa nevracia žiadne telo odpovede.