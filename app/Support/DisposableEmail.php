<?php

namespace App\Support;

/**
 * Disposable / throwaway / reserved-test email domain blocklist. Mirrors the Biolinx
 * list (the two sites are separate repos, so the list is duplicated by design) so a
 * junk/temp/test email can't subscribe on a lander popup or get forwarded to Biolinx.
 */
class DisposableEmail
{
    public const DOMAINS = [
        'mailinator.com', 'mailinator2.com', 'mailinator.net', 'notmailinator.com', 'reallymymail.com',
        'thisisnotmyrealemail.com', 'binkmail.com', 'bobmail.info', 'chammy.info', 'devnullmail.com',
        '10minutemail.com', '10minutemail.net', '10minutemail.co', '10minutemail.org', '20minutemail.com',
        '10minemail.com', '15qm.com', '33mail.com', 'tempmail.com', 'temp-mail.org', 'temp-mail.io',
        'tempmail.net', 'tempmailo.com', 'tempmail.de', 'tempmailaddress.com', 'tempinbox.com',
        'tempemail.com', 'tempemail.net', 'tempemails.com', 'tempr.email', 'tmpmail.org', 'tmpmail.net',
        'tmpeml.com', 'tmail.ws', 'temp-mail.ru', 'minuteinbox.com', 'mail-temp.com', 'tempemail.co',
        'guerrillamail.com', 'guerrillamail.info', 'guerrillamail.net', 'guerrillamail.org',
        'guerrillamail.biz', 'guerrillamail.de', 'guerrillamailblock.com', 'sharklasers.com',
        'grr.la', 'spam4.me', 'pokemail.net',
        'yopmail.com', 'yopmail.fr', 'yopmail.net', 'cool.fr.nf', 'jetable.fr.nf', 'nospam.ze.tc',
        'nomail.xl.cx', 'mega.zik.dj', 'speed.1s.fr', 'courriel.fr.nf', 'moncourrier.fr.nf',
        'monemail.fr.nf', 'monmail.fr.nf',
        'throwawaymail.com', 'throwam.com', 'trashmail.com', 'trashmail.net', 'trashmail.de',
        'trash-mail.com', 'trashymail.com', 'trashymail.net', 'trashinbox.com', 'wegwerfmail.de',
        'wegwerfmail.net', 'wegwerfmail.org', 'mailmetrash.com', 'mailmoat.com', 'mt2009.com',
        'mt2014.com', 'mt2015.com', 'thankyou2010.com', 'trash2009.com', 'kurzepost.de',
        'getnada.com', 'nada.email', 'maildrop.cc', 'dropmail.me', 'dropmail.ga', '10mail.org',
        'spamherelots.com', 'spambox.us',
        'dispostable.com', 'mailnesia.com', 'fakeinbox.com', 'fakemail.net', 'fakemailgenerator.com',
        'mohmal.com', 'emailondeck.com', 'mintemail.com', 'spamgourmet.com', 'discard.email',
        'mailcatch.com', 'mailexpire.com', 'jetable.org', 'mytrashmail.com', 'mailtothis.com',
        'getairmail.com', 'emailfake.com', 'generator.email', 'harakirimail.com', 'incognitomail.com',
        'anonbox.net', 'anonymbox.com', 'deadaddress.com', 'despam.it', 'spamfree24.org',
        'mailnull.com', 'e4ward.com', 'gishpuppy.com', 'kasmail.com', 'sneakemail.com', 'spamex.com',
        'spamhole.com', 'spaml.com', 'einrot.com', 'fleckens.hu', 'gustr.com', 'dayrep.com',
        'armyspy.com', 'cuvox.de', 'rhyta.com', 'superrito.com', 'teleworm.us', 'vomoto.com',
        'owlymail.com', 'mailde.de', 'byom.de', 'hidemail.de', 'objectmail.com', 'proxymail.eu',
        'rcpt.at', 'safetymail.info', 'sofimail.com', 'sogetthis.com', 'soodonims.com', 'spamavert.com',
        'suremail.info', 'willselfdestruct.com', 'dodgit.com', 'dontreg.com', 'fastacura.com',
        'fastchevy.com', 'mailin8r.com', 'tradermail.info', 'veryrealemail.com', 'klzlk.com',
        'inboxbear.com', 'luxusmail.org', 'mailpoof.com', 'robot-mail.com', 'tafmail.com', 'vipmail.pw',
        'mailcuk.com', 'inboxkitten.com', 'emltmp.com', 'mailsac.com', 'spambog.com', 'spambog.de',
        'spambog.ru', 'mailedu.de', 'one-time.email', 'tempail.com', 'fviek.com', 'snapmail.cc',
        'burnermail.io', 'mail7.io', 'mailpwr.com', 'mailto.plus', 'fexpost.com', 'fexbox.org',
        'rover.info', 'chitthi.in', 'tutuapp.bid', 'inbox.lv', 'simplelogin.io', 'mailbox.in.ua',
        'easytrashmail.com', 'inst.green', 'crazymailing.com', 'tempmail.plus', 'altmails.com',
        'disposablemail.com', 'gettempmail.com', 'spam.la', 'spamobox.com', 'mailcatch.org',
    ];

    public static function isDisposable(string $email): bool
    {
        $domain = strtolower(trim(substr(strrchr($email, '@') ?: '', 1)));
        if ($domain === '') {
            return true;
        }
        // RFC 2606 reserved + test domains (test@example.com, foo@bar.test, etc.).
        if (in_array($domain, ['example.com', 'example.net', 'example.org'], true)) {
            return true;
        }
        foreach (['.test', '.invalid', '.localhost', '.example', '.local'] as $tld) {
            if (str_ends_with($domain, $tld)) {
                return true;
            }
        }
        return in_array($domain, self::DOMAINS, true);
    }
}
