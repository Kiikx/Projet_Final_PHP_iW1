<?php

class Mailer
{
    private static string $apiKey; // Remplacez par votre clé API

        // Initialiser l'API Key depuis .env
        public static function init()
        {
            if (getenv('RESEND_API_KEY')) {
                self::$apiKey = getenv('RESEND_API_KEY');
            } else {
                die("❌ ERREUR : RESEND_API_KEY non défini dans .env");
            }
        }

    public static function sendEmail(string $to, string $subject, string $htmlContent): bool
    {
        self::init();

        $url = "https://api.resend.com/emails";

        $data = [
            "from" => "onboarding@resend.dev", // Modifier avec un domaine validé par Resend
            "to" => [$to],
            "subject" => $subject,
            "html" => $htmlContent
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . self::$apiKey,
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200 || $httpCode === 202;
    }
}
