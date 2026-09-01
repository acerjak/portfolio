<?php

namespace App\Enums;

enum InquiryReason: string
{
    case General = 'general';
    case BrandDeal = 'brand_deal';
    case ContractJob = 'contract_job';
    case ProjectIdea = 'project_idea';

    public function label(): string
    {
        return match ($this) {
            self::General => 'General',
            self::BrandDeal => 'Brand deal',
            self::ContractJob => 'Contract jobs',
            self::ProjectIdea => 'Project ideas',
        };
    }
}
