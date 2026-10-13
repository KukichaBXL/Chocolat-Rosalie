<?php
// path: model/Mailer.php
// typage strict
declare(strict_types=1);

namespace model;

use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

// Envoie un e-mail. Une seule méthode pour tout le site : Mailer::envoyer(...).
//   MAIL_TRANSPORT = 'log'  (par défaut) : rien ne part, le message est écrit dans logs/emails.log (développement, démo)
//   MAIL_TRANSPORT = 'smtp' : envoi réel avec symfony/mailer (voir composer.json) : MAIL_HOST, MAIL_PORT, MAIL_USER,
//                             MAIL_PASS, MAIL_SECURE ('tls' = STARTTLS port 587, 'ssl' = port 465) et MAIL_VERIFY_PEER (false si le certificat pose problème)
// Les secrets (MAIL_PASS...) vivent dans config-prod.php, jamais dans le dépôt Git.
class Mailer
{
    // renvoie true si le message est parti (ou écrit dans le journal), false sinon. Ne lance jamais d'exception :
    // une panne d'e-mail ne doit pas casser la page.
    public static function envoyer(string $destinataire, string $sujet, string $texte, ?string $html = null): bool
    {
        // on retire les retours à la ligne des champs d'en-tête : sinon on pourrait y glisser d'autres en-têtes
        $destinataire = str_replace(["\r", "\n"], '', $destinataire);
        $sujet = str_replace(["\r", "\n"], ' ', $sujet);
        if (filter_var($destinataire, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        try {
            $transport = defined('MAIL_TRANSPORT') ? MAIL_TRANSPORT : 'log';
            if ($transport === 'smtp') {
                self::smtp($destinataire, $sujet, $texte, $html);
            } else {
                self::journal($destinataire, $sujet, $texte);
            }
            return true;
        } catch (\Throwable $e) {
            error_log('[Maison Rosalie] e-mail non envoyé à ' . $destinataire . ' : ' . $e->getMessage());
            return false;
        }
    }

    private static function expediteur(): string
    {
        return defined('MAIL_FROM') ? MAIL_FROM : 'no-reply@maisonrosalie.be';
    }

    private static function nomExpediteur(): string
    {
        return defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Maison Rosalie';
    }

    // transport « log » : on écrit le message dans logs/emails.log
    private static function journal(string $destinataire, string $sujet, string $texte): void
    {
        $ligne = '[' . date('Y-m-d H:i:s') . "]\n"
            . 'À : ' . $destinataire . "\n"
            . 'Sujet : ' . $sujet . "\n\n"
            . $texte . "\n\n";
        file_put_contents(RACINE_PATH . '/logs/emails.log', $ligne, FILE_APPEND | LOCK_EX);
    }

    // transport « smtp » : symfony/mailer fait le dialogue avec le serveur
    private static function smtp(string $destinataire, string $sujet, string $texte, ?string $html): void
    {
        if (!class_exists(Transport::class)) {
            throw new \RuntimeException('symfony/mailer n\'est pas installé : lancez « composer install » (ou envoyez le dossier vendor/).');
        }
        $hote = defined('MAIL_HOST') ? MAIL_HOST : 'localhost';
        $port = defined('MAIL_PORT') ? (int) MAIL_PORT : 587;
        $secure = defined('MAIL_SECURE') ? MAIL_SECURE : 'tls';

        // smtps = chiffrement dès la connexion (port 465) ; smtp = STARTTLS (port 587) si le serveur le propose
        $dsn = ($secure === 'ssl' ? 'smtps' : 'smtp') . '://'
            . urlencode(defined('MAIL_USER') ? MAIL_USER : '') . ':' . urlencode(defined('MAIL_PASS') ? MAIL_PASS : '')
            . '@' . $hote . ':' . $port;
        $options = [];
        if (defined('MAIL_VERIFY_PEER') && MAIL_VERIFY_PEER === false) {
            $options[] = 'verify_peer=0';
        }
        if ($secure === '') {
            $options[] = 'auto_tls=false';
        }
        if ($options !== []) {
            $dsn .= '?' . implode('&', $options);
        }

        $message = (new Email())
            ->from(new Address(self::expediteur(), self::nomExpediteur()))
            ->to($destinataire)
            ->subject($sujet)
            ->text($texte);
        if ($html !== null) {
            $message->html($html);
        }
        (new SymfonyMailer(Transport::fromDsn($dsn)))->send($message);
    }
}
