<?php
/**
 * Created by IntelliJ IDEA.
 * User: dave
 * Date: 8/11/18
 * Time: 1:59 PM
 */

class GSuiteAuthenticator extends Authenticator
{
    /**
     * @var string
     */

    private $domain;

    public function __construct()
    {
        $this->domain = Config::inst()->get('GSuiteAuthenticator', 'domain');
    }

    /**
     * @return string
     */
    public static function get_name()
    {
        return Config::inst()->get('GSuiteAuthenticator', 'name');
    }

    /**
     * @param Controller $controller
     * @return GSuiteLoginForm
     */
    public static function get_login_form(Controller $controller)
    {
        return GSuiteLoginForm::create($controller, "LoginForm");
    }

    /**
     * Authenticate a user with Google and verify the domain matches the one
     * specified in config
     *
     * @param array $data
     * @param Form $form
     * @return Member
     */
    public static function authenticate($data, Form $form = null)
    {
        $token =  $data['token'];
        $clientId = Config::inst()->get('GSuiteAuthExtension', 'client_id');
        $domain = Config::inst()->get('GSuiteAuthenticator', 'domain');

        # TODO: move to separate service, make testable
        $client = new Google_Client([ 'client_id' => $clientId ]);
        $payload = $client->verifyIdToken($token);

        if ($payload) {
            if (!array_key_exists('hd', $payload)) {
                return null; # auth failed
            }

            if ($payload['hd'] != $domain) {
                return null; # auth failed
            }

            $email = $payload['email'];
            $firstName = $payload['given_name'];
            $lastName = $payload['family_name'];

            # Note: these may never be needed..leaving here for reference
            # $photo = $payload['picture'];
            # $emailVerified = $payload['email_verified']; # not sure what this var is for

            /***
             * @var $member Member
             */

            Session::set('gsuite_token', $payload['jti']);
            $member = Member::get()
                ->filter(Member::config()->unique_identifier_field, $email)
                ->first();

            # TODO: if no member exists, create one with a default group
            $createNew = Config::inst()->get('GSuiteAuthenticator', 'create_new_users');
            if (!$member && $createNew) {
                $member = self::create_member($email, $firstName, $lastName);
            }

            if ($member) {
                $passwordLength = Config::inst()->get('GSuiteAuthenticator', 'new_password_length');
                $newPassword = self::generate_password($passwordLength);
                $member->changePassword($newPassword);
            }

            return $member;
        }
    }

    private static function create_member($email, $firstName, $lastName) {
        $defaultGroupCode = Config::inst()->get('GSuiteAuthenticator', 'default_new_user_group');

        # TODO: handle non-existing group or force a default if it doesn't exist
        $defaultGroup = Group::get()
            ->filter('Code', $defaultGroupCode)
            ->first();

        $member = new Member();
        $member->FirstName = $firstName;
        $member->Surname = $lastName;
        $member->Email = $email;
        $member->write();
        $member->Groups()->add($defaultGroup);
        return $member;
    }

    private static function generate_password($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}