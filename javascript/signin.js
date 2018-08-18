function onSignIn(googleUser) {
    (function($) {
        var idToken = googleUser.getAuthResponse().id_token,
            form = $('#GSuiteLoginForm_LoginForm');

        // prevent auto signin. Not crazy about this approach
        var auth2 = gapi.auth2.getAuthInstance();
        auth2.disconnect();

        form.find('input[name="token"]').val(idToken);
        form.find('input[type="submit"]').click();
    })(jQuery);
}