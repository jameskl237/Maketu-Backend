<?php 

namespace App\Helpers;

/**
 * Class HttpStatus
 * Liste centralisée des codes de statut HTTP avec des constantes nommées.
 */
class HttpStatus
{
    // ✅ 1xx - Informations
    public const CONTINUE = 100;                      // La requête initiale a été reçue, en attente de suite.
    public const SWITCHING_PROTOCOLS = 101;           // Le serveur change de protocole selon la demande du client.

    // ✅ 2xx - Succès
    public const OK = 200;                            // Requête réussie.
    public const CREATED = 201;                       // Ressource créée avec succès.
    public const ACCEPTED = 202;                      // Requête acceptée, traitement en cours.
    public const NO_CONTENT = 204;                    // Requête traitée avec succès, sans retour de contenu.

    // ⚠️ 3xx - Redirections
    public const MOVED_PERMANENTLY = 301;             // Ressource déplacée définitivement.
    public const FOUND = 302;                         // Redirection temporaire.
    public const NOT_MODIFIED = 304;                  // Aucune modification depuis la dernière requête.

    // ❌ 4xx - Erreurs côté client
    public const BAD_REQUEST = 400;                   // Mauvaise requête (souvent validation).
    public const UNAUTHORIZED = 401;                  // Authentification requise.
    public const FORBIDDEN = 403;                     // Accès interdit.
    public const NOT_FOUND = 404;                     // Ressource introuvable.
    public const METHOD_NOT_ALLOWED = 405;            // Méthode HTTP non autorisée.
    public const CONFLICT = 409;                      // Conflit de données (ex : doublon).
    public const UNPROCESSABLE_ENTITY = 422;          // Requête bien formée mais invalide (souvent validation).
    public const TOO_MANY_REQUESTS = 429;             // Trop de requêtes (rate limit).

    // 💥 5xx - Erreurs serveur
    public const INTERNAL_SERVER_ERROR = 500;         // Erreur interne du serveur.
    public const NOT_IMPLEMENTED = 501;               // Fonctionnalité non implémentée.
    public const BAD_GATEWAY = 502;                   // Mauvaise réponse d’un serveur intermédiaire.
    public const SERVICE_UNAVAILABLE = 503;           // Service temporairement indisponible.
    public const GATEWAY_TIMEOUT = 504;               // Timeout en tant que passerelle.

    // ✅ Utilité : accès par nom lisible
    public static function getMessage(int $code): string
    {
        return match ($code) {
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::ACCEPTED => 'Accepted',
            self::NO_CONTENT => 'No Content',
            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
            self::CONFLICT => 'Conflict',
            self::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            self::TOO_MANY_REQUESTS => 'Too Many Requests',
            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            self::SERVICE_UNAVAILABLE => 'Service Unavailable',
            default => 'Unknown Status',
        };
    }
}
