<?

require_once "config.php";

class Utils {

    public static function uuidv4(string $data = null): string {

        // generate 128 random bits
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // version 0b0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // bits 6-7 = 0b10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

    }

    public static function generateCSRFToken(string $sessionUUID): string {

        return hash_hmac(HMAC_ALG, $sessionUUID, CSRF_SECRET);

    }

    public static function validateCSRFToken(string $sessionUUID, string $csrfToken): bool {

        return hash_hmac(HMAC_ALG, $sessionUUID, CSRF_SECRET) === $csrfToken;

    }

}