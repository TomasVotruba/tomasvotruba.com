<?php

// ref https://rezakhademix.medium.com/laravel-11-no-http-kernel-no-casts-no-console-kernel-721c62adb6ef

Schedule::command('app:tweet-post')->weekdays()->at('08:00')->timezone('Europe/Paris');
