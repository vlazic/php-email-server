<?php

use Sabre\HTTP;

require_once 'vendor/autoload.php';

function send_email(HTTP\RequestInterface $request, HTTP\ResponseInterface $response, $data)
{
    try {
        // Create the Transport
        $transport = (new Swift_SmtpTransport($data['smtp_server'], $data['smtp_port'], $data['smtp_encryption']))
            ->setUsername($data['smtp_user'])
            ->setPassword($data['smtp_password']);

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message($data['subject']))
            ->setFrom($data['from'])
            ->setTo($data['to'])
            ->setBody(strip_tags($data['message']))
            ->addPart($data['message'], 'text/html');

        if (is_array($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                $attachment = (new Swift_Attachment())
                    ->setFilename($attachment->filename) // document.pdf
                    ->setContentType($attachment->content_type) // 'application/pdf'
                    ->setBody(base64_decode($attachment->body));

                $message->attach($attachment);
            }
        }

        // Send the message
        $result = $mailer->send($message);

        // echo $message->toString();

        return_success($request, $response, true);
    } catch (\Throwable $th) {
        // show more detailed error log if DEBUG is true
        if ($_ENV['DEBUG']) {
            return_error($request, $response, $th->__toString());
        } else {
            return_error($request, $response, 'Server problem while sending email.');
        }
    }
}
