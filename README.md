# 🏠 Real Estate API
 
Laravel REST API aplikacija za prodaju nekretnina. Projekat je razvijen kao deo predmetnog zadatka i pokriva autentifikaciju korisnika, upravljanje nekretninama i upitima, role-based pristup, pretragu i paginaciju.
 
---
 
## 📋 Sadržaj
 
- [Tehnologije](#tehnologije)
- [Instalacija](#instalacija)
- [Modeli](#modeli)
- [API Rute](#api-rute)
- [Autentifikacija](#autentifikacija)
- [Role i pristup](#role-i-pristup)
- [Pretraga i paginacija](#pretraga-i-paginacija)
- [Ugnježđene rute](#ugnježđene-rute)
- [Zaboravljena lozinka](#zaboravljena-lozinka)
---
 
## 🛠 Tehnologije
 
- PHP 8.2+
- Laravel 11
- Laravel Sanctum (autentifikacija)
- MySQL
- Postman (testiranje)
---
 
## ⚙️ Instalacija
 
```bash
# Kloniraj repozitorijum
git clone https://github.com/elab-development/serverske-veb-tehnologije-2025-26-nekretnineaplikacija_2023_0497.git
cd real-estate-api
 
# Instaliraj zavisnosti
composer install
 
# Kopiraj .env fajl
cp .env.example .env
 
# Generiraj ključ
php artisan key:generate
 
# Podesi bazu u .env fajlu
DB_CONNECTION=mysql
DB_DATABASE=nekretnine_app
DB_USERNAME=root
DB_PASSWORD=
 
# Pokreni migracije i seedere
php artisan migrate --seed
 
# Pokreni server
php artisan serve
```
 
---
 
## 🗄️ Modeli
 
### User
| Kolona | Tip | Opis |
|--------|-----|------|
| id | bigint | Primarni ključ |
| name | string | Ime korisnika |
| email | string | Email (unique) |
| password | string | Lozinka (hash) |
| role | enum | admin / agent / buyer |
| timestamps | - | created_at, updated_at |
 
### Property
| Kolona | Tip | Opis |
|--------|-----|------|
| id | bigint | Primarni ključ |
| user_id | foreignId | Vlasnik (agent) |
| title | string | Naziv nekretnine |
| description | text | Opis |
| price | decimal | Cena |
| location | string | Lokacija |
| type | enum | apartment / house / commercial |
| bedrooms | tinyint | Broj spavaćih soba |
| bathrooms | tinyint | Broj kupatila |
| area_sqm | float | Površina u m² |
| status | enum | available / sold / rented |
| image_url | string | URL slike (nullable) |
| timestamps | - | created_at, updated_at |
 
### Inquiry
| Kolona | Tip | Opis |
|--------|-----|------|
| id | bigint | Primarni ključ |
| user_id | foreignId | Kupac koji šalje upit |
| property_id | foreignId | Nekretnina |
| message | text | Poruka |
| status | enum | pending / answered / closed |
| timestamps | - | created_at, updated_at |
 
---
 
## 🔗 API Rute
 
### Javne rute (bez autentifikacije)
 
| Method | URL | Opis |
|--------|-----|------|
| POST | `/api/register` | Registracija korisnika |
| POST | `/api/login` | Prijava |
| POST | `/api/forgot-password` | Zahtev za reset lozinke |
| GET | `/api/properties` | Lista nekretnina (paginacija + filteri) |
| GET | `/api/properties/{id}` | Detalji nekretnine |
| GET | `/api/properties/{id}/inquiries` | Upiti za nekretninu *(ugnježđena ruta)* |
| GET | `/api/properties/export` | Export u CSV |
| GET | `/api/mortgage/calculate` | Kalkulator hipoteke |
 
### Zaštićene rute (potreban Bearer token)
 
| Method | URL | Opis |
|--------|-----|------|
| POST | `/api/logout` | Odjava |
| POST | `/api/reset-password` | Reset lozinke |
| POST | `/api/properties` | Dodaj nekretninu *(agent/admin)* |
| PUT | `/api/properties/{id}` | Izmijeni nekretninu *(agent/admin)* |
| DELETE | `/api/properties/{id}` | Obriši nekretninu *(agent/admin)* |
| GET | `/api/my-properties` | Moje nekretnine |
| GET | `/api/users/{id}/properties` | Nekretnine korisnika *(ugnježđena ruta)* |
| GET | `/api/inquiries` | Lista upita |
| POST | `/api/inquiries` | Pošalji upit |
| GET | `/api/inquiries/{id}` | Detalji upita |
| PATCH | `/api/inquiries/{id}/status` | Izmijeni status upita *(admin)* |
| DELETE | `/api/inquiries/{id}` | Obriši upit |
 
---
 
## 🔐 Autentifikacija
 
API koristi **Laravel Sanctum** token autentifikaciju.
 
**Registracija:**
```json
POST /api/register
{
    "name": "Marko Marković",
    "email": "marko@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "agent"
}
```
 
**Prijava:**
```json
POST /api/login
{
    "email": "marko@example.com",
    "password": "password123"
}
```
 
Odgovor sadrži `access_token` koji se šalje u headeru:
```
Authorization: Bearer {access_token}
```
 
---
 
## 👥 Role i pristup
 
| Akcija | Admin | Agent | Buyer |
|--------|:-----:|:-----:|:-----:|
| Pregled nekretnina | ✅ | ✅ | ✅ |
| Dodavanje nekretnine | ✅ | ✅ | ❌ |
| Izmena svoje nekretnine | ✅ | ✅ | ❌ |
| Izmena tuđe nekretnine | ✅ | ❌ | ❌ |
| Brisanje svoje nekretnine | ✅ | ✅ | ❌ |
| Brisanje tuđe nekretnine | ✅ | ❌ | ❌ |
| Slanje upita | ✅ | ✅ | ✅ |
| Izmena statusa upita | ✅ | ❌ | ❌ |
 
---
 
## 🔍 Pretraga i paginacija
 
```
GET /api/properties?location=Beograd&type=apartment&min_price=50000&max_price=200000&per_page=5
```
 
Dostupni parametri:
 
| Parametar | Opis | Primjer |
|-----------|------|---------|
| `location` | Filtriranje po lokaciji | `Beograd` |
| `type` | Tip nekretnine | `apartment`, `house`, `commercial` |
| `min_price` | Minimalna cena | `50000` |
| `max_price` | Maksimalna cena | `200000` |
| `per_page` | Broj rezultata po stranici | `5` (default: 10) |
 
---
 
## 🔗 Ugnježđene rute
 
### Upiti za određenu nekretninu
```
GET /api/properties/{id}/inquiries
```
Vraća sve upite vezane za nekretninu sa datim ID-em.
 
### Nekretnine određenog korisnika
```
GET /api/users/{id}/properties
Authorization: Bearer {token}
```
Admin može vidjeti nekretnine bilo kojeg korisnika. Agent i buyer mogu vidjeti samo svoje.
 
---
 
## 🔑 Zaboravljena lozinka
 
**Korak 1 — zatraži reset link:**
```json
POST /api/forgot-password
{
    "email": "marko@example.com"
}
```
 
**Korak 2 — resetuj lozinku:**
```json
POST /api/reset-password
{
    "token": "TOKEN_IZ_EMAILA",
    "email": "marko@example.com",
    "password": "novaLozinka123",
    "password_confirmation": "novaLozinka123"
}
```
 
> Za testiranje, u `.env` postavi `MAIL_MAILER=log` — token će biti vidljiv u `storage/logs/laravel.log`.
 
---
 
## 👨‍💻 Tim
 
| Ime | GitHub |
|-----|--------|
| Član 1 | [@shandulej](https://github.com/shandulej) |
| Član 2 | [@ivaggg](https://github.com/ivaggg) |
| Član 3 | [@KaLu4721](https://github.com/KaLu4721) |