<?php
/**
 * Created by IntelliJ IDEA.
 * User: dave
 * Date: 8/12/18
 * Time: 4:54 PM
 */

class GSuiteLoginForm extends LoginForm
{
    protected $authenticator_class = 'GSuiteAuthenticator';

    private static $allowed_actions = array('dologin', 'logout');

    public function __construct($controller, $name, $fields = null, $actions = null,
                                $checkCurrentUser = true) {

        Requirements::css(GSUITE_AUTH_DIR . '/css/gsuite.css');
        Requirements::javascript('//apis.google.com/js/platform.js');
        Requirements::javascript(GSUITE_AUTH_DIR . '/javascript/signin.js');
        Requirements::set_force_js_to_bottom(true);

        $fields = FieldList::create(
            HiddenField::create("token", null, ''),
            HiddenField::create("AuthenticationMethod", null, $this->authenticator_class, $this),
            GSuiteLoginButton::create("button")
        );

        $actions = FieldList::create(
            FormAction::create('dologin', _t('Member.BUTTONLOGIN', "Log in"))
                ->addExtraClass('hidden')
        );

        parent::__construct($controller, $name, $fields, $actions);
    }

    public function dologin($data) {
        $member = GSuiteAuthenticator::authenticate($data, $this);
        if ($member == null) {
            return $this->controller->redirectBack();
        }

        $member->logIn();

        # make sure this has a sane default
        $url = Session::get('BackURL');
        Session::clear('BackURL');
        return $this->controller->redirect($url);
    }
}