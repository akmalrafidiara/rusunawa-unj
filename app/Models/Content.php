<?php

// app/Models/Content.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content_key',
        'content_value',
        'content_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Ini adalah kuncinya:
        // 'content_value' => 'array', // Jika selalu array/JSON string
    ];

    /**
     * Get the content_value attribute, handling potential JSON.
     *
     * @param  string  $value
     * @return mixed
     */
    public function getContentValueAttribute($value)
    {
        // Coba decode sebagai JSON. Jika gagal, kembalikan nilai asli.
        $decoded = json_decode($value, true); // true untuk array asosiatif

        // Periksa apakah decoding berhasil dan hasilnya bukan null (yang bisa jadi string "null")
        // atau jika itu adalah array yang valid (bukan sekadar angka atau boolean yang di-json_encode)
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Jika bukan JSON yang valid atau bukan array setelah decode, kembalikan nilai asli
        return $value;
    }

    /**
     * Set the content_value attribute, encoding arrays to JSON.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setContentValueAttribute($value)
    {
        // Jika nilai yang diberikan adalah array, encode menjadi JSON string
        if (is_array($value)) {
            $this->attributes['content_value'] = json_encode($value);
        } else {
            // Jika bukan array, simpan apa adanya (sebagai string)
            $this->attributes['content_value'] = $value;
        }
    }
}