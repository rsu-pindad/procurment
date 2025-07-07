<?php

namespace App\Enums;

enum InputType : string
{
    const SELECT_INPUT = 'select_input';
    const FILE_INPUT = 'file_input';
    const TEXT_INPUT = 'text_input';
    const TEXTAREA_INPUT = 'textarea_input';
    const DATE_PICKER = 'date_picker';
    const READONLY_NOTE = 'readonly_note';

    public static function all(): array
    {
        return [
            self::SELECT_INPUT,
            self::FILE_INPUT,
            self::TEXT_INPUT,
            self::TEXTAREA_INPUT,
            self::DATE_PICKER,
            self::READONLY_NOTE,
        ];
    }

    public static function labels(): array
    {
        return [
            self::SELECT_INPUT => 'Dropdown Vendor',
            self::FILE_INPUT => 'Upload Dokumen',
            self::TEXT_INPUT => 'Input Text',
            self::TEXTAREA_INPUT => 'Textarea',
            self::DATE_PICKER => 'Date Picker',
            self::READONLY_NOTE => 'Catatan',
        ];
    }
}
