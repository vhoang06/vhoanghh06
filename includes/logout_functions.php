<?php
// includes/logout_functions.php

function getLogoutRedirectTarget(string $redirectParam = null): string {
    return $redirectParam === 'admin' ? 'admin/login.php' : 'index.php';
}
