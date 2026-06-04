<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class KodeBukuFormat implements ValidationRule
{
    public function passes($attribute, $value): bool
    {
        return (bool) preg_match('/^BK-[A-Z]{2,4}-\d{3}$/', (string) $value);
    }

    /**
     * Validasi pesan error
     *
     * @return string
     */
    public function message(): string
    {
        return 'Format kode buku harus: BK-XXX-000 (contoh: BK-PROG-001)';
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
