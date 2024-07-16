<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!empty($_POST['email'])) {
    // Enable / Disable SMTP
    $enable_smtp = 'yes'; // yes OR no

    // Email Receiver Address
    $receiver_email = 'janhavi.n@sumadhuragroup.com';

    // Email Receiver Name for SMTP Email
    $receiver_name = 'Sumadhura Group';

    // Email Subject
    $subject = 'Lead from The Epitome by Sumadhura Landing page';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Constructing the email message
        $message = '<html>
        <head>
            <title>Lead Information</title>
            <style>
                table, th, td {
                    border: 1px solid black;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 8px;
                }
            </style>
        </head>
        <body>';

        // Add lead information to the message
        $lead_info = '';
        foreach ($_POST as $fieldname => $fieldvalue) {
            if (!empty($fieldvalue) && $fieldname !== 'redirect' && $fieldname !== 'submission_url' && strpos($fieldname, 'utm_') !== 0) {
                $fieldname = str_replace('_', ' ', $fieldname);
                $fieldname = ucwords($fieldname);
                $lead_info .= "<p><b>$fieldname:</b> " . htmlspecialchars($fieldvalue) . "</p>";
            }
        }

        // Add submission URL to UTM parameters
        if (!empty($_POST['submission_url'])) {
            $utm_info = "<p><b>Submission URL:</b> " . htmlspecialchars($_POST['submission_url']) . "</p>";
        }

        // Add UTM parameters to the message in a table format
        $utm_params_table = '<table width="50%" align="center" cellpadding="8" cellspacing="0">';
        $utm_params_table .= '<tr><td colspan="2" align="center"><b>UTM Parameters</b></td></tr>';

        $utm_params = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        foreach ($utm_params as $utm_param) {
            $utm_value = !empty($_POST[$utm_param]) ? htmlspecialchars($_POST[$utm_param]) : 'null';
            $utm_param_pretty = ucwords(str_replace('_', ' ', $utm_param));
            $utm_params_table .= "<tr><td><b>$utm_param_pretty</b></td><td>$utm_value</td></tr>";
        }

        $utm_params_table .= '</table>';

        // Combine lead information and UTM parameters in the message
        $message .= $lead_info . $utm_info . $utm_params_table;

        $message .= '</body></html>';

        // Sending the email
        if ($enable_smtp == 'no') { // Simple Email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: ' . $receiver_name . ' <' . filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) . '>' . "\r\n";
            if (mail($receiver_email, $subject, $message, $headers)) {
                echo '{ "alert": "alert alert-success alert-dismissable", "message": "Your message has been sent successfully!" }';
            } else {
                echo '{ "alert": "alert alert-danger alert-dismissable", "message": "Your message could not be sent!" }';
            }
        } else { // SMTP
            require 'phpmailer/PHPMailer.php';
            require 'phpmailer/Exception.php';
            require 'phpmailer/SMTP.php';

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Your SMTP Host
            $mail->SMTPAuth = true;
            $mail->Username = 'marketing@sumadhuragroup.com'; // Your Email Address
            $mail->Password = 'vjdxlvldbgzldnvv'; // Your Email Password
            $mail->SMTPSecure = 'ssl'; // Your Secure Connection ('ssl' or 'tls')
            $mail->Port = 465; // Your Port (587 for TLS, 465 for SSL)
            $mail->setFrom($receiver_email, $receiver_name);
            $mail->addAddress($receiver_email, $receiver_name);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $message;

            if ($mail->send()) {
                echo '{ "alert": "alert alert-success alert-dismissable", "message": "Your message has been sent successfully!" }';
            } else {
                echo '{ "alert": "alert alert-danger alert-dismissable", "message": "Your message could not be sent!" }';
            }
        }
    }
} else {
    echo '{ "alert": "alert alert-danger alert-dismissable", "message": "Please add an email address!" }';
}
?>
