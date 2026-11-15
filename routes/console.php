<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('denimforge:monitor')->everyFiveMinutes();
