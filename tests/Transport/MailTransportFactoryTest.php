<?php

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\File;
use LetMeSendEmail\Laravel\Facades\LetMeSendEmail;
use LetMeSendEmail\Laravel\Support\FakeUtil;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

it('can_get_transport', function () {
    config([
        'letmesendemail.key' => 'test',
    ]);

    $manager = app()->get(MailManager::class);

    $transport = $manager->createSymfonyTransport(['transport' => 'letmesendemail']);

    expect((string) $transport)->toBe('letmesendemail');
});

it('can_send_email', function () {
    config([
        'letmesendemail.key' => 'test',
    ]);

    LetMeSendEmail::fake(path: 'emails/send');

    $payload = FakeUtil::resolveFakeRequest('emails/send')['body'];

    $email = (new Email())
        ->from($payload['from'])
        ->to(new Address($payload['to']))
        ->cc($payload['cc'][0])
        ->bcc($payload['bcc'][0])
        ->replyTo($payload['replyTo'])
        ->subject($payload['subject'])
        ->text($payload['text'])
        ->html($payload['html']);

    $manager = app()->get(MailManager::class);
    $transport = $manager->createSymfonyTransport(['transport' => 'letmesendemail']);

    /** @var \Symfony\Component\Mailer\SentMessage $sentMessage */
    $sentMessage = $transport->send($email);

    expect($sentMessage->getMessageId())->toBeString();
});


it('can_send_email_with_attachments', function () {
    config([
        'letmesendemail.key' => 'test',
    ]);

    LetMeSendEmail::fake(path: 'emails/send');

    $payload = FakeUtil::resolveFakeRequest('emails/send')['body'];

    $email = (new Email())
        ->from($payload['from'])
        ->to(new Address($payload['to']))
        ->cc($payload['cc'][0])
        ->bcc($payload['bcc'][0])
        ->replyTo($payload['replyTo'])
        ->subject($payload['subject'])
        ->text($payload['text'])
        ->html($payload['html']);

    $email->attach(
        'Lorem ipsum dolor sit amet consectetur adipisicing elit. Eius odio nemo dolor iusto, repellendus explicabo ipsa at, laudantium necessitatibus officiis alias officia, ducimus tempore? Ad id iusto ab tempore sunt!',
        'lorem-ipsum.txt',
        'text/plain'
    );

    $manager = app()->get(MailManager::class);
    $transport = $manager->createSymfonyTransport(['transport' => 'letmesendemail']);

    /** @var \Symfony\Component\Mailer\SentMessage $sentMessage */
    $sentMessage = $transport->send($email);

    expect($sentMessage->getMessageId())->toBeString();
});
