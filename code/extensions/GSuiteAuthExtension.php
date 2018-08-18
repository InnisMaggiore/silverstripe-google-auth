<?php
/**
 * Created by IntelliJ IDEA.
 * User: dave
 * Date: 8/17/18
 * Time: 8:02 AM
 */

class GSuiteAuthExtension extends SiteTreeExtension
{
    public function MetaTags(&$tags)
    {

        $clientId = Config::inst()
            ->get('GSuiteAuthExtension', 'client_id');

         #TODO: don't show me on every page
        $tags .= <<<META
    <meta name="google-signin-client_id" content="$clientId">
META;

    }
}