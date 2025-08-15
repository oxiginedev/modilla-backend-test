<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case CLOSED = 'closed';
}
