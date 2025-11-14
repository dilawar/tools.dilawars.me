<?php

/*
 * This file is part of the proprietary project.
 *
 * This file and its contents are confidential and protected by copyright law.
 * Unauthorized copying, distribution, or disclosure of this content
 * is strictly prohibited without prior written consent from the author or
 * copyright owner.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Services;

use Assert\Assert;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailService
{
    /**
     * PHPMailer client.
     */
    private readonly PHPMailer $phpMailer;

    public function __construct(bool $enableException = true, bool $debug = false)
    {
        log_message('info', 'Creating a new Email client...');
        $this->phpMailer = new PHPMailer($enableException);
        $this->phpMailer->isSMTP();

        $this->phpMailer->Host = (string) getenv('email.SMTPHost');
        $this->phpMailer->Port = intval(getenv('email.SMTPPort'));
        log_message('info', 'Using smtp host='.$this->phpMailer->Host
            .' port='.$this->phpMailer->Port);

        $this->phpMailer->Username = (string) getenv('email.SMTPUser');
        $this->phpMailer->Password = (string) getenv('email.SMTPPass');

        Assert::that($this->phpMailer->Username)->minLength(4);
        Assert::that($this->phpMailer->Password)->minLength(16);

        $this->phpMailer->SMTPAuth = true;

        $this->phpMailer->isHTML(true);
        if ($debug) {
            $this->phpMailer->SMTPDebug = SMTP::DEBUG_SERVER;
        }
    }

    /**
     * Send email.
     */
    private function _sendEmail(string $to, string $subject, string $body): void
    {
        log_message('info', sprintf('Sending email to %s with %s', $to, $subject));
        $this->phpMailer->setFrom('noreply@dilawars.me', 'MaxFlow Tools');
        $this->phpMailer->addAddress($to);

        $this->phpMailer->Subject = $subject;
        $this->phpMailer->Body = $body;
        $this->phpMailer->send();
        log_message('info', 'Email has been sent.');
    }

    /**
     * Send email to given address. If `$sendOnce` is `true`, send only once.
     */
    public function sendEmail(string $to, string $subject, string $body, bool $sendOnce = true): void
    {
        $content = "{$to}\n\n{$subject}\n\n{$body}";
        $contentHash = hash('sha256', $content);

        log_message('info', sprintf('Sending email to %s with %s, content hash %s.', $to, $subject, $contentHash));
        $hashFile = $this->checksumpath($contentHash);
        if (is_file($hashFile) && $sendOnce) {
            log_message('info', 'This email is already sent. Doing nothing...');

            return;
        }

        $this->_sendEmail($to, $subject, $body);

        // if successful, generate the hashfile.
        file_put_contents($hashFile, $content);
        log_message('info', 'Email has been sent.');
    }

    private function checksumPath(?string $filename): string
    {
        $dir = WRITEPATH.'sent_email';
        if (! is_dir($dir)) {
            mkdir($dir, recursive: true);
        }

        if ($filename) {
            return $dir.('/'.$filename);
        }

        return $dir;
    }

    /**
     * Notify $email that article $item has is no longer behind paywall.
     *
     * @param array<string, string > $article
     */
    public function sendLwnEmailArticleNotBehindPaywall(string $email, array $article, bool $sendOnce = true): void
    {
        $title = $article['title'];
        log_message('info', sprintf("'%s' is no longer behind paywall ", $title).json_encode($article));
        $twig = service('twig');
        $body = $twig->render('lwn_open.html.twig', $article);
        $this->sendEmail($email, subject: sprintf("'%s' is no longer behind LWN paywall", $title), body: $body, sendOnce: $sendOnce);
    }

    public function notifyDevs(string $body, ?string $subject = null): void
    {
        $this->sendEmail('maxflow-dev-log@googlegroups.com', $subject ?? 'Event From MaxFlow Tools', $body);
    }
}
