# GoTrener

Aplikacja do zarządzania treningami i dietami z integracją Firebase.

## Technologie

- **Backend**: Laravel 12
- **Frontend**: Vue.js 3 + Inertia.js
- **Baza danych**: Firebase Firestore
- **Storage**: Firebase Storage
- **Autentykacja**: Firebase Authentication
- **PDF**: Spatie Laravel PDF

## Wymagania

- PHP 8.2+
- Composer
- Node.js & NPM
- Konto Firebase

## Instalacja

1. Sklonuj repozytorium:
```bash
git clone <repository-url>
cd GoTrener
```

2. Zainstaluj zależności:
```bash
composer install
npm install
```

3. Skopiuj plik `.env.example` do `.env`:
```bash
cp .env.example .env
```

4. Wygeneruj klucz aplikacji:
```bash
php artisan key:generate
```

5. Skonfiguruj Firebase:
   - Pobierz plik credentials JSON z Firebase Console
   - Umieść go w katalogu projektu
   - Zaktualizuj `.env`:
   ```env
   FIREBASE_CREDENTIALS=path/to/firebase-credentials.json
   FIREBASE_API_KEY=your-api-key
   FIREBASE_STORAGE_DEFAULT_BUCKET=your-project.appspot.com
   ```

6. Uruchom migracje:
```bash
php artisan migrate
```

7. Zbuduj frontend:
```bash
npm run build
# lub dla development
npm run dev
```

## Firebase Storage

Aplikacja używa Firebase Storage do przechowywania obrazów (awatary, galerie).

### Szybki start

Zobacz [FIREBASE_STORAGE_QUICKSTART.md](FIREBASE_STORAGE_QUICKSTART.md) dla szczegółowych instrukcji.

### Migracja z lokalnego storage

Jeśli masz już pliki w lokalnym storage:

```bash
# Test migracji (dry-run)
php artisan storage:migrate-to-firebase --dry-run

# Właściwa migracja
php artisan storage:migrate-to-firebase
```

Więcej informacji: [FIREBASE_STORAGE_MIGRATION.md](FIREBASE_STORAGE_MIGRATION.md)

## Uruchomienie aplikacji

### Development

```bash
# Uruchom serwer deweloperski, queue i Vite jednocześnie
composer dev

# Lub osobno:
php artisan serve
php artisan queue:listen
npm run dev
```

### Production

```bash
npm run build
php artisan serve
```

## Testy

```bash
# Wszystkie testy
php artisan test

# Tylko testy Firebase Storage
php artisan test --filter=FirebaseStorageTest
```

## Struktura projektu

```
app/
├── Console/Commands/      # Polecenia Artisan
├── Http/Controllers/      # Kontrolery
├── Repository/            # Warstwa dostępu do danych
└── Services/              # Serwisy (Firebase, etc.)

resources/
├── js/
│   ├── Components/        # Komponenty Vue
│   ├── Pages/             # Strony Inertia
│   └── Layouts/           # Layouty
└── css/                   # Style

config/
├── firebase.php           # Konfiguracja Firebase
└── ...
```

## Funkcjonalności

- ✅ Autentykacja użytkowników (email/hasło + Google)
- ✅ Profile użytkowników (klienci i trenerzy)
- ✅ Zarządzanie treningami
- ✅ Zarządzanie dietami
- ✅ System wiadomości (chat)
- ✅ Galerie zdjęć
- ✅ Generowanie PDF z dietami
- ✅ Firebase Storage dla obrazów

## Licencja

Ten projekt jest licencjonowany na zasadach MIT.
