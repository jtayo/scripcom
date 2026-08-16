<?php

namespace App\Enums;

enum EventType: string
{
    case PortalOpened = 'portal.opened';
    case PortalLoaded = 'portal.loaded';
    case PhoneSubmitted = 'phone.submitted';
    case OtpRequested = 'otp.requested';
    case OtpVerified = 'otp.verified';
    case PackageSelected = 'package.selected';
    case VoucherRedeemed = 'voucher.redeemed';
    case VideoStarted = 'video.started';
    case VideoProgress = 'video.progress';
    case VideoCompleted = 'video.completed';
    case VideoSkipped = 'video.skipped';
    case VideoFailed = 'video.failed';
    case PaymentStarted = 'payment.started';
    case PaymentSuccessful = 'payment.successful';
    case PaymentFailed = 'payment.failed';
    case InternetGranted = 'internet.granted';
    case SessionStarted = 'session.started';
    case SessionEnded = 'session.ended';
    case SessionExpired = 'session.expired';
    case BandwidthUpdated = 'bandwidth.updated';
    case HotspotUp = 'hotspot.up';
    case HotspotDown = 'hotspot.down';
    case ErrorOccurred = 'error.occurred';

    public function label(): string
    {
        return str($this->value)->replace('.', ' ')->title();
    }
}
