<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendActivationEmail($email, $link) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'signalbox319@gmail.com';
        $mail->Password = 'hwhginubevdoiutz'; // tanpa spasi!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('signalbox319@gmail.com', 'Sistem Gudang');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Aktivasi Akun Anda';
        $mail->Body = "Klik tautan berikut untuk mengaktifkan akun Anda: <a href='$link'>$link</a>";

        $mail->send();
        echo "Email aktivasi berhasil dikirim!";
    } catch (Exception $e) {
        echo "Gagal mengirim email: {$mail->ErrorInfo}";
    }
}

function sendResetEmail($email, $link) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'signalbox319@gmail.com';
        $mail->Password = 'hwhginubevdoiutz'; // tanpa spasi!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('signalbox319@gmail.com', 'Sistem Gudang');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password Anda';
        $mail->Body = "Klik tautan berikut untuk mereset password Anda: <a href='$link'>$link</a>";

        $mail->send();
        echo "Tautan reset password telah dikirim ke email Anda.";
    } catch (Exception $e) {
        echo "Gagal mengirim email: {$mail->ErrorInfo}";
    }
}
?>
