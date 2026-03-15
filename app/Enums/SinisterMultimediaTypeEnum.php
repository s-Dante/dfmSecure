<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum SinisterMultimediaTypeEnum: string
{
    use EnumHelper;

    case PHOTO = 'photo';
    case VIDEO = 'video';
    case DOCUMENT = 'document';
    case AUDIO = 'audio';

     public function label(): string
    {
        return match ($this) {
            self::PHOTO => 'Foto',
            self::VIDEO => 'Video',
            self::DOCUMENT => 'Documento',
            self::AUDIO => 'Audio',
        };
    }
}
