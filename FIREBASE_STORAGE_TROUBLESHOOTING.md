# Rozwiązanie błędu: "The specified bucket does not exist"

## Problem
Otrzymujesz błąd: `The specified bucket does not exist.`

## Przyczyna
Firebase Storage nie został aktywowany w Twoim projekcie Firebase lub bucket ma nieprawidłową nazwę.

## Rozwiązanie

### Krok 1: Aktywuj Firebase Storage w Firebase Console

1. Przejdź do [Firebase Console](https://console.firebase.google.com/)
2. Wybierz swój projekt
3. W menu po lewej stronie kliknij **Storage**
4. Jeśli widzisz przycisk **"Get Started"** lub **"Rozpocznij"**, kliknij go
5. Wybierz lokalizację dla swojego storage (np. `europe-west3` dla Europy)
6. Kliknij **Done**

### Krok 2: Znajdź nazwę swojego bucket

Po aktywacji Storage, zobaczysz nazwę bucket na górze strony. Zazwyczaj wygląda to tak:
```
gs://your-project-id.appspot.com
```

lub

```
gs://your-project-id.firebasestorage.app
```

**Skopiuj tę nazwę (bez `gs://`)!**

### Krok 3: Zaktualizuj plik .env

Otwórz plik `.env` i zaktualizuj lub dodaj:

```env
FIREBASE_STORAGE_DEFAULT_BUCKET=your-project-id.appspot.com
```

**WAŻNE:** Użyj dokładnie tej nazwy, którą skopiowałeś z Firebase Console (bez `gs://`)!

### Krok 4: Wyczyść cache konfiguracji

```bash
php artisan config:clear
php artisan cache:clear
```

### Krok 5: Ustaw reguły bezpieczeństwa

W Firebase Console → Storage → Rules, wklej:

```
rules_version = '2';
service firebase.storage {
  match /b/{bucket}/o {
    // Publiczny odczyt dla wszystkich
    match /{allPaths=**} {
      allow read;
    }
    
    // Avatary - tylko właściciel może zapisywać
    match /avatars/{userId}.{extension} {
      allow write: if request.auth != null && request.auth.uid == userId;
    }
    
    // Galerie - tylko właściciel może zapisywać
    match /gallery/{userId}/{allPaths=**} {
      allow write: if request.auth != null && request.auth.uid == userId;
    }
  }
}
```

Kliknij **Publish**.

### Krok 6: Zrestartuj serwer

Zatrzymaj i uruchom ponownie `php artisan serve`.

## Weryfikacja

Spróbuj przesłać plik ponownie. Jeśli nadal masz problem, sprawdź:

1. Czy nazwa bucket w `.env` jest poprawna
2. Czy Firebase Storage jest aktywny w konsoli
3. Czy plik `.env` został zapisany
4. Czy uruchomiłeś `php artisan config:clear`

## Alternatywne rozwiązanie (jeśli bucket ma inną nazwę)

Jeśli Twój bucket ma nazwę w formacie `your-project-id.firebasestorage.app`, użyj jej zamiast `.appspot.com`:

```env
FIREBASE_STORAGE_DEFAULT_BUCKET=your-project-id.firebasestorage.app
```

## Sprawdzenie nazwy bucket przez kod

Możesz sprawdzić dostępne buckety uruchamiając:

```bash
php artisan tinker
```

Następnie w tinkerze:

```php
$firebase = app(\App\Services\FirebaseService::class);
$storage = $firebase->storage();
$bucket = $storage->getBucket();
echo $bucket->name();
```

To pokaże nazwę bucket, który próbuje użyć aplikacja.
