# Docker

Hostovanie s Dockerom je možné použitím `docker-compose.yml`. Definuje 3 služby:
- Frontend
    - `node:22-alpine`
    - Build aplikácie prebieha cez [pnpm](https://pnpm.io/).
- Backend
    - `dunglas/frankenphp:1.10-php8.4-bookworm`
    - PHP verzia `8.4`
    - Extensions:
        - `pdo_mysql`
        - `gd`
        - `intl`
        - `zip`
        - `opcache`
    - Aplikácia je servovaná pomocou [Caddy](https://caddyserver.com/)
- Databáza
    - `mariadb:11.8.2-noble`

Každá služba má `healthcheck` a `depends_on` na zabezpečenie vhodného spustenia a kontroly funkčnosti.

> ⚠️ Tento setup je určený pre produkčné nasadenie a vyžaduje manuálne nastavenie reverzných proxy na zabezpečenie pomocou HTTPS. Pre lokálne testovanie (neodporúčané) je nutné ručne nastaviť DNS záznamy (napr. cez `/etc/hosts`).

## Prerekvizity

### Softvérové požiadavky

Je vyžadovaný iba Docker s Docker Compose. V novších verzách Dockeru je už Compose zabudovaný.

Testované s:
| **Verzia**        | **OS**             |
|-------------------|--------------------|
| 28.5.2/ecc694264d | linux/arch-cachyos |
| 29.1.1/0aedba5    | linux/debian-lxc   |

Ako reverzné proxy môžete použiť napríklad [Caddy](https://caddyserver.com/), [Traefik](https://traefik.io/), [Nginx](https://nginx.org/) alebo [Cloudflare Tunely](https://www.cloudflare.com/products/tunnel/). Odporúčame však Caddy kvôli jednoduchej konfigurácii a automatickému získavaniu SSL certifikátov cez Let's Encrypt, prípadne Cloudflare Tunely pre jednoduché nastavenie bez potreby spravovať DNS záznamy a certifikáty.

### Hardvérové požiadavky (pre build)
|     | Minimálne | Odporúčané |
|-----|-----------|------------|
| RAM | 2GB       | 4GB        |
| CPU | 2         | 4          |

Ak máte iba 2GB RAM, odporúčame nastaviť min 2GB swap pamäte, aby build prebehol úspešne. Prípadne môžete obmedziť počet paralelných buildov.

## Základná inštalácia a nastavenie

### Stiahnutie projektu a buildovanie

Projekt si najprv stiahnite do vami zvoleného priečinka:
```sh
git clone https://github.com/isop-ukf/isop-app.git
```

Podľa potreby prejdite na požadovanú branchu:
```sh
cd isop-app
git checkout [branch]
```

Prejdite do adresára `docker`:
```sh
cd docker
```

Spustite build nasledovným príkazom:
```sh
docker compose build
```
> Počet paralelných buildov môžete obmedziť pomocou `--parallel N` (pred `build`), kde N je počet súčasne bežiacich buildov.

> ⏱️ Build trvá približne 2 min.


> ⚠️ Kvôli kompilácii PHP modulov počas buildu dôjde k zvýšenému využitiu CPU a RAM.

### Nastavenie prostredia
Pred prvým spustením je potrebné vytvoriť súbor `.env` na základe šablóny `.env.example`:
```sh
cp .env.example .env
```

- `BACKEND_URL`: URL adresa backendu **vrátane** protokolu (a prípade portu)
- `FRONTEND_URL`: URL adresa frontendu **vrátane** protokolu (a prípade portu)
- `BACKEND_DOMAIN`: Doména pre backend **bez** protokolu a portu
- `FRONTEND_DOMAIN`: Doména pre frontend **bez** protokolu a portu
- `SESSION_DOMAIN`: Doména pre session cookies, prípadne aj s bodkou na začiatku pre zdieľanie medzi subdoménami
- `APP_KEY`: Aplikačný kľúč pre šifrovanie (postup nižšie)

Príklad `.env` súboru:
```env
BACKEND_URL=https://backend.myapp.com
FRONTEND_URL=https://myapp.com
BACKEND_DOMAIN=backend.myapp.com
FRONTEND_DOMAIN=myapp.com
SESSION_DOMAIN=.myapp.com
APP_KEY=base64:Xxx00XX+X/ABC+AABBCCDDEEFFGGHHXYZ0+00000000=
```

### Vygenerovanie aplikačného kľúča
Aplikačný kľúč môžete vygenerovať pomocou nasledujúceho príkazu:
```sh
docker compose run --rm --no-deps isop-backend php artisan key:generate --show
```

> ⚠️ Odporúčame zmazať vytvorený kontajner po vygenerovaní kľúča.
> ```sh
> docker compose down
> ```

### Spustenie migrácií
Pred prvým spustením aplikácie je potrebné spustiť databázové migrácie príkazmi:
```sh
docker compose up -d isop-backend isop-database
docker compose exec isop-backend php artisan migrate:fresh
docker compose down
```

## Spustenie aplikácie
Aplikáciu spustíte príkazom:
```sh
docker compose up -d
```

Logy môžete sledovať pomocou:
```sh
docker compose logs -f
```

## Zastavenie aplikácie
Aplikáciu zastavíte príkazom:
```sh
docker compose down
```

## Aktualizácia aplikácie
Pre aktualizáciu aplikácie postupujte nasledovne:
1. Stiahnite najnovšie zmeny z repozitára:
    ```sh
    git pull
    ```
2. Zastavte bežiacu aplikáciu:
    ```sh
    docker compose down
    ```
3. Znovu postavte kontajnery:
    ```sh
    docker compose build
    ```
4. Spustite aplikáciu:
    ```sh
    docker compose up -d
    ```

## Záloha a obnova dát
Dáta aplikácie sú uložené v databáze, ktorú je možné zálohovať a obnoviť pomocou štandardných nástrojov pre MariaDB/MySQL, ako je `mysqldump` a `mysql`, alebo pomcou archivácie dátového adresára databázy.

Príklad na zálohovanie databázy:
```sh
tar czvf db-backup.tar.gz mariadb-data/
```

> ⚠️ Odporúčame použiť `tar` na archiváciu dátového adresára, aby ste zachovali správne oprávnenia.

## Zabezpečenie

## Cloudflare Tunnel
Inštaláciu a základné nastavenie nájdete v [dokumentácii Cloudflare](https://developers.cloudflare.com/cloudflare-one/networks/connectors/cloudflare-tunnel/). Je potrebné vytvoriť 2 tunely - jeden pre frontend a druhý pre backend.

Na spustenie tunela môžete vytvoriť samostatný Docker Compose súbor, napríklad:
```yaml
services:
  tunnel:
    image: cloudflare/cloudflared:2025.11.1
    container_name: cloudflared-tunnel
    restart: unless-stopped
    network_mode: "host"
    command: tunnel run
    environment:
      - TUNNEL_TOKEN=TOKEN-XYZ
```

Následne nastavne DNS záznamy vo vašom Cloudflare účte, aby smerovali na tunely, napríklad:
- `myapp.com` -> `localhost:80`
- `backend.myapp.com` -> `localhost:8111`

## Caddy
Inštaláciu a základné nastavenie nájdete v [dokumentácii Caddy](https://caddyserver.com/docs/).

Príklad konfigurácie:
```
backend.myapp.com {
    reverse_proxy localhost:8111
}

myapp.com {
    reverse_proxy localhost:80
}
```