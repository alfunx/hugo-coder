<?php

// CONTACT FORM
// ============
//
// Based on the Contact-Form of The Hugo Zen theme.
// See: https://github.com/frjo/hugo-theme-zen#contact-form
//
// 1. Copy this file, i.e. `themes/hugo-coder/php/contact.php`, to
// `static/php/contact.php`.
//
// 2. In the copied file, adjust the variable `$address` to your own e-mail
// address. You can also change the mail subject prefix by adjusting the
// variable `$prefix`. The prefix will be placed in square braces, such that the
// complete subject may look like: `[Website feedback] Some subject`.
//
// 3. Add the shortcode `{{< contact >}}` to a page, e.g. `content/contact.md`.

// set the e-mail address that submission should be sent to
$address = 'info@example.com';

// set the e-mail subject prefix
$prefix = 'Website feedback';


// DO NOT EDIT ANYTHING BELOW, UNLESS YOU KNOW WHAT YOU ARE DOING


// check that referer is local server
if (!isset($_SERVER['HTTP_REFERER']) || (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != $_SERVER['SERVER_NAME'])) {
  exit;
}

// check that the submission address is valid
if ((bool) filter_var(trim($address), FILTER_VALIDATE_EMAIL)) {
  // also set sender/return path header to this address to avoid SPF errors
  $to = $sender = trim($address);
}
else {
  exit;
}

// url of form (for redirect)
$form_url = strtok($_SERVER['HTTP_REFERER'], '?');

// check if this is a post request
if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_POST)) {
  _redirect($form_url);
}

// check if fake url was provided (i.e. spam bot)
if (!empty($_POST['url'])) {
  _redirect($form_url);
}

// check if name was provided
if (isset($_POST['name'])) {
  $name = _clean_encode_text($_POST['name']);
}
else {
  _redirect($form_url);
}

// check if e-mail address was provided and is valid
if (isset($_POST['email']) && (bool) filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
  $email = trim($_POST['email']);
}
else {
  _redirect($form_url);
}

// check if subject was provided
if (isset($_POST['subject'])) {
  $prefix = _clean_encode_text($prefix);
  $subject = _clean_encode_text($_POST['subject']);
  $subject = "[$prefix] $subject";
}
else {
  _redirect($form_url);
}

// check if message was provided
if (isset($_POST['message'])) {
  $message = chunk_split(base64_encode($_POST['message']));
}
else {
  _redirect($form_url);
}

// construct the mail with headers
$headers = [
  'From'                      => "$name <$email>",
  'Sender'                    => $sender,
  'Return-Path'               => $sender,
  'MIME-Version'              => '1.0',
  'Content-Type'              => 'text/plain; charset=utf-8',
  'Content-Transfer-Encoding' => 'base64',
  'X-Mailer'                  => 'Hugo - Coder',
];

// send mail, suppress errors and set return-path (with "-f" option)
$success = @mail($to, $subject, $message, $headers, '-f' . $sender);
_redirect($form_url, $success);


// helper functions:

function _clean_encode_text($text) {
  $text = htmlspecialchars(trim(strip_tags($text)), ENT_NOQUOTES, 'UTF-8');
  return '=?UTF-8?B?' . base64_encode($text) . '?=';
}

function _redirect($url, $success = false) {
  header('Location: ' . $url . '?' . ($success ? 'submitted' : 'error'));
  exit;
}
