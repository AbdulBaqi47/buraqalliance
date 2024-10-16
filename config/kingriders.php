<?php

/*
|--------------------------------------------------------------------------
| Kingriders Settings
|--------------------------------------------------------------------------
|
| Settings like api urls will be defined here
|   if setting is related to delivery system (app.kingriders.net), it should be prefixed by "ds" like "ds_url"
|
*/

return [
    'apikey' => "a952b5296f7f9bc7e0e65c3b635f58ff", // Random hash, its static by inner apps

    'ds_url' => env("kingriders_ds_url", "https://app.kingriders.net"),
];
