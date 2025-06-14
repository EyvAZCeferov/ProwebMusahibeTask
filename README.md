---

# ATM İdarəetmə Sistemi API Sənədləri

Bu sənəd, ATM idarəetmə sistemi üçün hazırlanmış REST API-nin texniki detallarını və istifadə qaydalarını təsvir edir. API, istifadəçi autentifikasiyası, hesabların idarə edilməsi, nağdlaşdırma, pul köçürmələri və inzibati funksiyaları əhatə edir.

## Mündəricat
1. [Texniki Stek](#1-texniki-stek)
2. [Layihənin Qurulması](#2-layihənin-qurulmasi)
3. [Ümumi Qaydalar](#3-ümumi-qaydalar)
    - [API Prefiksi və Lokalizasiya](#api-prefiksi-və-lokalizasiya)
    - [Header-lər](#header-lər)
    - [Cavab Formatı](#cavab-formati)
4. [API Endpoint-ləri](#4-api-endpoint-ləri)
    - [Autentifikasiya (`Auth`)](#autentifikasiya-auth)
    - [Ümumi Məlumatlar](#ümumi-məlumatlar)
    - [Hesablar (`Accounts`)](#hesablar-accounts)
    - [Əməliyyatlar (`Operations`)](#əməliyyatlar-operations)
    - [Tranzaksiya Tarixçəsi](#tranzaksiya-tarixçəsi)
    - [İnzibati Panel (`Admin`)](#inzibati-panel-admin)

---

## 1. Texniki Stek
- **PHP**: ^8.2
- **Laravel Framework**: ^11.0
- **Verilənlər Bazası**: MySQL 8+
- **Autentifikasiya**: Laravel Sanctum (Token-əsaslı)
- **Rollar və İcazələr**: Spatie/laravel-permission
- **Çoxdillilik Dəstəyi**: Mcamara/laravel-localization

---

## 2. Layihənin Qurulması

1.  **Layihəni klonlayın:**
    ```bash
    git clone <repository_url>
    cd <project_folder>
    ```
2.  **Asılılıqları yükləyin:**
    ```bash
    composer install
    ```
3.  **`.env` faylını yaradın və konfiqurasiya edin:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` kimi dəyərləri öz sisteminizə uyğun dəyişin.

4.  **Verilənlər bazasını yaradın və ilkin məlumatları əlavə edin:**
    ```bash
    php artisan migrate:fresh --seed
    ```
5.  **Development serverini işə salın:**
    ```bash
    php artisan serve
    ```
    API `http://127.0.0.1:8000` ünvanında aktiv olacaq.

---

## 3. Ümumi Qaydalar

### API Prefiksi və Lokalizasiya
Bütün API sorğuları `/api/` prefiksi ilə göndərilməlidir. Dəstəklənən dillər: `az`, `ru`, `en`.
- **Nümunə:** `http://127.0.0.1:8000/api/az/login`

### Header-lər
Bütün sorğularda aşağıdakı header-lər göndərilməlidir:
- `Accept: application/json`
- `Accept-Language: az` (və ya `ru`, `en`. Göndərilməzsə, default `az` dili istifadə olunur.)

Autentifikasiya tələb edən endpoint-lər üçün əlavə olaraq:
- `Authorization: Bearer {token}`

### Cavab Formatı
- **Uğurlu Cavablar:** Adətən `200 OK` və ya `201 Created` statusu ilə birlikdə `data`, `message` və ya digər müvafiq məlumatları qaytarır.
- **Xəta Cavabları:** Müvafiq HTTP status kodu (4xx, 5xx) və xəta haqqında məlumat verən `message` və bəzən `errors` obyektini qaytarır.
- **Fallback Cavabı (Yanlış URL):**
    ```json
    {
        "success": false,
        "message": "Axtarılan mənbə tapılmadı."
    }
    ```

---

## 4. API Endpoint-ləri

### Autentifikasiya (`Auth`)

#### **Qeydiyyat**
- **Endpoint:** `POST /api/register`
- **Təsvir:** Yeni istifadəçi yaradır (`person` rolu ilə).
- **Body:**
  - `name` (string, **required**, min:3)
  - `email` (string, **required**, email, unique)
  - `password` (string, **required**, min:8, 1 böyük hərf, 1 kiçik hərf, 1 rəqəm, 1 simvol)
  - `password_confirmation` (string, **required**)
- **Cavab (201):**
    ```json
    {
        "message": "İstifadəçi uğurla qeydiyyatdan keçdi.",
        "user": { "id": 1, "name": "...", "email": "..." },
        "token": "1|abcdef..."
    }
    ```

#### **Giriş**
- **Endpoint:** `POST /api/login`
- **Body:**
  - `email` (string, **required**, exists)
  - `password` (string, **required**)
- **Cavab (200):**
    ```json
    {
        "message": "İstifadəçi uğurla giriş etdi.",
        "user": { "id": 1, "name": "...", "email": "..." },
        "token": "2|ghijkl..."
    }
    ```

#### **Çıxış**
- **Endpoint:** `POST /api/logout`
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Cavab (200):** `{"message": "İstifadəçi hesabdan çıxış etdi."}`

#### **İstifadəçi Məlumatları (`me`)**
- **Endpoint:** `GET /api/me`
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Cavab (200):** Autentifikasiya olunmuş istifadəçinin `UserResource` formatında məlumatları.

---
### Ümumi Məlumatlar

#### **Valyutalar**
- **Endpoint:** `GET /api/currencies`
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Cavab (200):** Sistemdəki aktiv valyutaların `CurrencyResource` formatında siyahısı.

---
### Hesablar (`Accounts`)

#### **Hesabların Siyahısı**
- **Endpoint:** `GET /api/account`
- **İcazə:** Bütün autentifikasiya olunmuş istifadəçilər. `person` rolu yalnız öz hesablarını, digər rollar (`superadmin`, `manager`) bütün hesabları görür və filtrləyə bilir.
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Query Parametrləri (Filter, optional):**
  - `currency_id` (integer)
  - `status` (boolean: `1` və ya `0`)
  - `start_date` / `end_date` (string, format: `d.m.Y`)
  - `code` (string)
  - `balance_min` / `balance_max` (numeric)
- **Cavab (200):** Hesab resurslarının siyahısı (paginated).

#### **Yeni Hesab Yaratmaq**
- **Endpoint:** `POST /api/account`
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Body:**
  - `currency_id` (integer, **required**)
  - `balance` (numeric, **required**)
  - `status` (boolean, optional)
  - `daily_transaction_limit` (integer, optional, default:1000)
  - `user_id` (integer, optional): Yalnız adminlər tərəfindən, `person` roluna malik istifadəçi üçün hesab yaradarkən istifadə olunur. `person` rolundakı istifadəçi bu parametri göndərə bilməz.
- **Cavab (201):** Yaradılmış hesab resursu.

#### **Hesab Məlumatı**
- **Endpoint:** `GET /api/account/{id}`
- **İcazə:** İstifadəçi yalnız öz hesabına baxa bilər (adminlər istisna olmaqla).
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Cavab (200):** Tək hesab resursu.

#### **Hesabı Yeniləmək**
- **Endpoint:** `POST /api/account/{id}`
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Body:**
  - `_method` (string, **required**): `PUT` və ya `PATCH`
  - `balance` (numeric, optional)
  - `status` (boolean, optional)
  - `daily_transaction_limit` (integer, optional, default:1000)
- **Cavab (200):** Yenilənmiş hesab resursu.

#### **Hesabı Silmək**
- **Endpoint:** `DELETE /api/account/{id}`
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Cavab (200):** `{"status": true, "message": "..."}`

---
### Əməliyyatlar (`Operations`)

#### **Nağdlaşdırma**
- **Endpoint:** `POST /api/withdraw`
- **İcazə:** `role:person`
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Body:**
  - `account_id` (integer, **required**): İstifadəçinin özünə aid olmalıdır.
  - `amount` (numeric, **required**, min:1)
- **Cavab (200):** `{"message": "Əməliyyat uğurla tamamlandı.", "transaction": {...}}`

#### **Öz Hesabları Arasında Köçürmə**
- **Endpoint:** `POST /api/transfers/self`
- **İcazə:** `role:person`
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Body:**
  - `from_account_id` (integer, **required**)
  - `to_account_id` (integer, **required**)
  - `amount` (numeric, **required**, min:0.01)
- **Cavab (200):** `{"message": "...", "transactions": {"from_transaction": {...}, "to_transaction": {...}}}`

#### **Başqa Hesaba Köçürmə**
- **Endpoint:** `POST /api/transfers/external`
- **İcazə:** `role:person`
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Body:**
  - `from_account_id` (integer, **required**)
  - `to_account_code` (string, **required**, mövcud hesab kodu)
  - `amount` (numeric, **required**, min:0.01)
- **Cavab (200):** `{"message": "...", "transactions": {...}}}`

---
### Tranzaksiya Tarixçəsi

#### **Tranzaksiyaların Siyahısı**
- **Endpoint:** `GET /api/transactions`
- **İcazə:** `person` rolu yalnız öz, digər rollar bütün tranzaksiyaları görür.
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Query Parametrləri (Filter, optional):**
  - `user_id`, `account_id`, `currency_id`, `status_id`, `banknote_id`, `counterparty_account_id` (integer)
  - `start_date` / `end_date` (string, format: `d.m.Y H:i`)
  - `notes` (string)
  - `amount_min` / `amount_max` (numeric)
- **Cavab (200):** Tranzaksiya resurslarının siyahısı (paginated).

#### **Tranzaksiya Məlumatı**
- **Endpoint:** `GET /api/transactions/{id}`
- **İcazə:** İstifadəçi yalnız öz tranzaksiyasına baxa bilər (adminlər istisna olmaqla).
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Cavab (200):** Tək tranzaksiya resursu.

#### **Tranzaksiyanı Silmək**
- **Endpoint:** `DELETE /api/transactions/{id}`
- **İcazə:** `role:superadmin` və ya `permission:delete_transaction` olan digər rollar.
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Cavab (200):** `{"status": true, "message": "..."}`

---
### İnzibati Panel (`Admin`)

#### **Əskinazlar**
- **İcazə:** `role:superadmin|manager`
- **Endpoint-lər:** Standart `apiResource` (`GET`, `POST`, `GET /{id}`, `POST /{id}` (PUT/PATCH ilə), `DELETE /{id}`)
  - **Siyahı:** `GET /api/banknotes`
    - **Query Parametrləri (Filter, optional):**
      - `currency_id`, `name`, `min_transactions_count`, `min_quantity_dispensed` (integer)
      - `status` (boolean)
      - `start_date` / `end_date` (string, format: `d.m.Y`)
  - **Yaratmaq:** `POST /api/banknotes`
    - **Body:** `currency_id`, `name`, `quantity` (**required**)
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Cavab:** Müvafiq əskinaz resursu və ya siyahısı.

#### **Tərcümələr**
- **İcazə:** `role:superadmin`
- **Endpoint-lər:** `apiResource` (update və show xaric)
  - **Siyahı:** `GET /api/translations`
  - **Yaratmaq:** `POST /api/translations`
    - **Body:** `key`, `az_value` (**required**); `ru_value`, `en_value` (optional)
  - **Silmək:** `DELETE /api/translations/{id}`
- **Header:** `Authorization: Bearer {token}` (**required**)
- **Cavab:** Müvafiq tərcümə resursu, siyahısı və ya status mesajı.
