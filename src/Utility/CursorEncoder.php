<?php declare(strict_types=1);

namespace App\Utility;

final class CursorEncoder
{
    public function __construct(
        private readonly string $secret
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function encode(?array $data): ?string
    {
        if ($data === null) {
            return null;
        }

        $payload = [
            'v' => 1,
            'd' => $data,
        ];

        $json = json_encode(
            $payload,
            JSON_THROW_ON_ERROR
        );

        $encodedPayload = $this->base64UrlEncode($json);

        $signature = hash_hmac(
            'sha256',
            $encodedPayload,
            $this->secret,
            true
        );

        $encodedSignature = $this->base64UrlEncode($signature);

        return $encodedPayload . '.' . $encodedSignature;
    }

    /**
     * @return array<string, mixed>
     */
    public function decode(?string $cursor): ?array
    {
        if ($cursor === null || $cursor === '') {
            return null;
        }
        
        $parts = explode('.', $cursor);

        if (count($parts) !== 2) {
            throw new \RuntimeException(
                'Invalid cursor format'
            );
        }

        [$encodedPayload, $encodedSignature] = $parts;

        // Recalcul de la signature
        $expectedSignature = $this->base64UrlEncode(
            hash_hmac(
                'sha256',
                $encodedPayload,
                $this->secret,
                true
            )
        );

        // Protection timing attack
        if (
            !hash_equals(
                $expectedSignature,
                $encodedSignature
            )
        ) {
            throw new \RuntimeException(
                'Cursor tampering detected'
            );
        }

        $json = $this->base64UrlDecode(
            $encodedPayload
        );

        $payload = json_decode(
            $json,
            true,
            flags: JSON_THROW_ON_ERROR
        );

        return $payload['d'];
    }

    private function base64UrlEncode(
        string $data
    ): string {
        return rtrim(
            strtr(
                base64_encode($data),
                '+/',
                '-_'
            ),
            '='
        );
    }

    private function base64UrlDecode(
        string $data
    ): string {
        return base64_decode(
            strtr($data, '-_', '+/')
        );
    }
}
