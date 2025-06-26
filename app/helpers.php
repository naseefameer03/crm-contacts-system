   <?php

    if (!function_exists('first_letter')) {
        function first_letter($name)
        {
            return substr($name, 0, 1);
        }
    }
