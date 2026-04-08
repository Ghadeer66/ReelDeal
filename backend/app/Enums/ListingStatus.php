<?php

namespace App\Enums;

enum ListingStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case Live = 'live';
    case Paused = 'paused';
    case Rejected = 'rejected';
    case Sold = 'sold';
}
