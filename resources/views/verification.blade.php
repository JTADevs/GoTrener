<!DOCTYPE html>
<html>
<head>
    <title>Weryfikacja Email</title>
</head>
<body>
    <h1>Cześć, {{ $name }}!</h1>
    <p>Dziękujemy za rejestrację w GO_TRENER.</p>
    <p>Kliknij w poniższy link, aby potwierdzić swój adres e-mail:</p>
    <p>
        <a href="{{ url('/confirm_account?token=' . $verifyCode . '&id=' . $tokenId) }}">
            Potwierdź email
        </a>
    </p>
</body>
</html>
