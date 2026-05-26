<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Auth\Controllers;

use VeloCMS\Core\Auth;
use VeloCMS\Core\Controller;
use VeloCMS\Modules\Auth\Models\PasswordResetModel;
use VeloCMS\Modules\Auth\Models\UserModel;

/**
 * Handles self-service password reset for admin users.
 *
 * Flow:
 *   GET  /admin/password/reset        → showRequest()  — enter e-mail form
 *   POST /admin/password/reset        → sendReset()    — generate + mail token
 *   GET  /admin/password/reset/[token]→ showForm()     — enter new password form
 *   POST /admin/password/reset/[token]→ reset()        — validate token + save password
 */
class PasswordResetController extends Controller
{
    private PasswordResetModel $resetModel;
    private UserModel          $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->resetModel = new PasswordResetModel();
        $this->userModel  = new UserModel();
    }

    // ── Step 1: Show "enter your e-mail" form ────────────────────────────────

    public function showRequest(): void
    {
        if (Auth::check()) {
            $this->redirect('/admin');
        }

        $this->render('admin/password_reset_request', []);
    }

    // ── Step 2: Process the e-mail, send reset link ───────────────────────────

    public function sendReset(): void
    {
        Auth::verifyCsrf();

        if (Auth::check()) {
            $this->redirect('/admin');
        }

        $email = filter_var($this->input('email', ''), FILTER_VALIDATE_EMAIL);

        // No user enumeration: same message whether email exists or not
        $successMsg = t('password_reset.email_sent');

        if ($email !== false) {
            $user = $this->userModel->getByEmail($email);
            if ($user !== null) {
                $rawToken = $this->resetModel->createToken((int) $user['id']);
                $this->sendResetMail($user, $rawToken);
            }
        }

        $this->redirectWithSuccess('/admin/password/reset', $successMsg);
    }

    // ── Step 3: Show "enter new password" form ────────────────────────────────

    public function showForm(string $token): void
    {
        if (Auth::check()) {
            $this->redirect('/admin');
        }

        $row = $this->resetModel->findValidToken($token);

        if ($row === null) {
            $this->redirectWithError(
                '/admin/password/reset',
                t('password_reset.token_invalid')
            );
        }

        $this->render('admin/password_reset_form', ['token' => $token]);
    }

    // ── Step 4: Validate token + save new password ────────────────────────────

    public function reset(string $token): void
    {
        Auth::verifyCsrf();

        if (Auth::check()) {
            $this->redirect('/admin');
        }

        $row = $this->resetModel->findValidToken($token);

        if ($row === null) {
            $this->redirectWithError(
                '/admin/password/reset',
                t('password_reset.token_invalid')
            );
        }

        $password        = (string) $this->input('password', '');
        $passwordConfirm = (string) $this->input('password_confirm', '');

        if (mb_strlen($password) < 8) {
            $this->redirectWithError(
                '/admin/password/reset/' . urlencode($token),
                t('error.password_min')
            );
        }

        if ($password !== $passwordConfirm) {
            $this->redirectWithError(
                '/admin/password/reset/' . urlencode($token),
                t('error.password_mismatch')
            );
        }

        // Save new password, mark token as used, purge expired tokens
        $this->userModel->setPassword((int) $row['user_id'], $password);
        $this->resetModel->markUsed((int) $row['id']);
        $this->resetModel->purgeExpired();

        $this->redirectWithSuccess('/admin/login', t('password_reset.success'));
    }

    // ── Internal helpers ──────────────────────────────────────────────────────

    private function sendResetMail(array $user, string $rawToken): void
    {
        $resetUrl  = rtrim($_ENV['APP_URL'] ?? '', '/') . '/admin/password/reset/' . urlencode($rawToken);
        $siteName  = function_exists('setting') ? setting('site_name', 'VeloCMS') : 'VeloCMS';
        $recipient = $user['email'];
        $subject   = '[' . $siteName . '] ' . t('password_reset.mail_subject');

        $body  = t('password_reset.mail_greeting') . ' ' . ($user['name'] ?? '') . ",\n\n";
        $body .= t('password_reset.mail_body') . "\n\n";
        $body .= $resetUrl . "\n\n";
        $body .= t('password_reset.mail_expiry') . "\n\n";
        $body .= t('password_reset.mail_ignore') . "\n\n";
        $body .= '-- ' . "\n" . $siteName;

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
        $headers .= 'From: ' . $siteName . ' <noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '>' . "\r\n";
        $headers .= 'X-Mailer: VeloCMS' . "\r\n";

        mail($recipient, $subject, $body, $headers);
    }

}
