<?php

namespace LetMeSendEmail\Laravel\Enums;

enum WebhookEventEnum: string
{
    case EMAIL_SENT = 'email.sent';
    case EMAIL_DELIVERED = 'email.delivered';
    case EMAIL_DELIVERY_DELAYED = 'email.delivery_delayed';
    case EMAIL_COMPLAINED = 'email.complained';
    case EMAIL_BOUNCED = 'email.bounced';
    case EMAIL_OPENED = 'email.opened';
    case EMAIL_CLICKED = 'email.clicked';
    case EMAIL_RECEIVED = 'email.received';
    case EMAIL_REJECTED = 'email.rejected';
    case EMAIL_FAILED = 'email.failed';
    case EMAIL_SCAN_FAILED = 'email.scan_failed';
    case EMAIL_RENDERING_FAILURE = 'email.rendering_failure';

    /**
     * Human-readable title for frontend
     */
    public function title(): string
    {
        return match ($this) {
            self::EMAIL_SENT => 'Email Sent',
            self::EMAIL_DELIVERED => 'Email Delivered',
            self::EMAIL_DELIVERY_DELAYED => 'Delivery Delayed',
            self::EMAIL_COMPLAINED => 'Spam Complaint',
            self::EMAIL_BOUNCED => 'Email Bounced',
            self::EMAIL_OPENED => 'Email Opened',
            self::EMAIL_CLICKED => 'Link Clicked',
            self::EMAIL_RECEIVED => 'Email Received',
            self::EMAIL_REJECTED => 'Email Reject',
            self::EMAIL_FAILED => 'Email Failed',
            self::EMAIL_SCAN_FAILED => 'Email Scan Failed',
            self::EMAIL_RENDERING_FAILURE => 'Rendering Failure',
        };
    }

    /**
     * Short description for frontend
     */
    public function description(): string
    {
        return match ($this) {
            self::EMAIL_SENT =>'Triggered when an email is successfully sent to the mail server.',
            self::EMAIL_DELIVERED =>'Triggered when the email is successfully delivered to the recipient.',
            self::EMAIL_DELIVERY_DELAYED =>'Triggered when email delivery is delayed by the receiving mail server.',
            self::EMAIL_COMPLAINED =>'Triggered when the recipient marks the email as spam.',
            self::EMAIL_BOUNCED =>'Triggered when the email is rejected by the recipient\'s mail server.',
            self::EMAIL_OPENED =>'Triggered when the recipient opens the email.',
            self::EMAIL_CLICKED => 'Triggered when the recipient clicks a link inside the email.',
            self::EMAIL_RECEIVED => 'Triggered when an inbound email is received.',
            self::EMAIL_REJECTED => 'Triggered when the email is rejected.',
            self::EMAIL_FAILED => 'Triggered when the email fails to send due to an error.',
            self::EMAIL_SCAN_FAILED => 'Triggered when the email attachments scan failed.',
            self::EMAIL_RENDERING_FAILURE => 'Triggered when template not rendered properly.',
        };
    }

    /**
     * Return all event values as array
     */
    public static function values(): array
    {
        return array_map(fn(self $event) => $event->value, self::cases());
    }
}
