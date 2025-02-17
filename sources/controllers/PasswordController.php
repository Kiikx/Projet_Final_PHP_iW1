<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../requests/PasswordRequest.php";
require_once __DIR__ . "/../core/Database.php";
require_once __DIR__ . "/../core/Mailer.php"; // Ajout du Mailer

class PasswordController
{
    public static function forgot(): void
    {
        $errors = [];
        $success = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $request = new PasswordRequest();
            $errors = $request->validateForgot();

            if (empty($errors)) {
                $user = User::findOneByEmail($request->email);
                $token = bin2hex(random_bytes(32));
                $expires_at = (new DateTime('now', new DateTimeZone('UTC')))
                ->modify('+2 hour')
                ->format('Y-m-d H:i:s');


                $pdo = Database::getConnection();
                $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) 
                                       VALUES (:user_id, :token, :expires_at)
                                       ON DUPLICATE KEY UPDATE token = :token, expires_at = :expires_at");
                $stmt->execute([
                    "user_id" => $user->id,
                    "token" => $token,
                    "expires_at" => $expires_at
                ]);

                // Générer le lien de réinitialisation
                $baseUrl = getenv("BASE_URL") ?: "http://localhost:8000";
                $resetLink = "$baseUrl/password/reset?token=$token";
                // $resetLink = "http://localhost:8000/password/reset?token=$token";

                // Construire l'email en HTML
                $emailContent = "
                    <h1>Réinitialisation du mot de passe</h1>
                    <p>Bonjour <strong>{$user->username}</strong>,</p>
                    <p>Vous avez demandé une réinitialisation de votre mot de passe.</p>
                    <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
                    <p><a href='$resetLink'>$resetLink</a></p>
                    <p>Ce lien expirera dans 1 heure.</p>
                    <br>
                    <p>Si vous n'avez pas demandé de changement, ignorez cet email.</p>
                ";

                // Envoyer l'email avec Resend
                $emailSent = Mailer::sendEmail($user->email, "Réinitialisation de votre mot de passe", $emailContent);

                if ($emailSent) {
                    $success = "Un email de réinitialisation a été envoyé !";
                } else {
                    $errors[] = "Erreur lors de l'envoi de l'email.";
                }
            }
        }

        require_once __DIR__ . "/../views/password/forgot.php";
    }

    public static function reset(): void
    {
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $request = new PasswordRequest();
            $errors = $request->validateReset();

            if (empty($errors)) {
                $pdo = Database::getConnection();

                // Vérifier si le token est valide et non expiré
                $stmt = $pdo->prepare("SELECT user_id FROM password_resets WHERE token = :token AND expires_at > NOW()");
                $stmt->execute(["token" => $request->token]);
                $row = $stmt->fetch();

                if (!$row) {
                    $errors[] = "Le lien est invalide ou expiré.";
                } else {
                    // Mettre à jour le mot de passe de l'utilisateur
                    User::updatePassword($row["user_id"], $request->password);

                    // Supprimer le token après utilisation
                    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
                    $stmt->execute(["token" => $request->token]);

                    echo "Mot de passe réinitialisé avec succès !";
                    return;
                }
            }
        }

        require_once __DIR__ . "/../views/password/reset.php";
    }

}
