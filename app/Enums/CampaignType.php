<?php

namespace App\Enums;

enum CampaignType: string
{
    case Health = 'health';
    case Tourism = 'tourism';
    case Revenue = 'revenue';
    case Emergency = 'emergency';
    case Education = 'education';
    case Agriculture = 'agriculture';
    case Environment = 'environment';
    case Commercial = 'commercial';
    case Civic = 'civic';
    case Social = 'social';

    public function label(): string
    {
        return str($this->value)->title();
    }
}
