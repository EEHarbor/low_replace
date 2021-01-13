<?php
/**
 * EEHarbor PHAR Loader for low_replace
 * Register a new auto-loader to handle loading files from the
 * PHAR in various environments (w/ opcache, w/o phar stream, etc).
 *
 * PHP Version >= 5.3
 *
 * @category   Helper
 * @package    ExpressionEngine
 * @subpackage Foundation
 * @author     EEHarbor <help@eeharbor.com>
 * @license    https://eeharbor.com/license EEHarbor Add-on License
 * @link       https://eeharbor.com/low_replace
 */

spl_autoload_register(
    function ($class_name) {
        // This is the PHAR path for this addon
        $phar_path = PATH_THIRD . 'low_replace/low_replace.phar';
        $phar_stream_path = 'phar://' . $phar_path;
        $tmp_phar_path = sys_get_temp_dir() . '/pharextract/low_replace';

        $namespace = 'Low\Replace';

        // Add 'FluxCapacitor' when checking our namespace otherwise we could have
        // a false positive if the class file does not extend anything in FluxCapacitor
        // as the raw namespace would still match.
        $check_namespace = $namespace . '\FluxCapacitor';

        // If the class name does not start with the check_namespace, it's not from here
        // or it's not extending FluxCapacitor so return early.
        if (substr($class_name, 0, strlen($check_namespace)) !== $check_namespace) {
            return null;
        }

        // Now lets strip out the namespace
        $class_name = str_replace($namespace, '', $class_name);

        // Format the class name to filesystem format (\Model\Example\ => Model/Example)
        $class_name = trim($class_name, '\\');
        $class_name = str_replace('\\', '/', $class_name);

        // Make sure the phar exists.
        if (file_exists($phar_path)) {
            // If the phar extension is loaded, use that, otherwise, use the self-extraction method.
            if (in_array('phar', stream_get_wrappers()) && class_exists('Phar', 0)) {
                $phar_stream = true;

                include_once $phar_path;

                // Build the path directly into the phar file right to the file we need.
                // You can't do this if the phar extension is not loaded.
                $class_file_path = $phar_stream_path;
            } else {
                $phar_stream = false;

                // If the extension is not loaded, we have to load the phar like a
                // normal file. There is code inside the phar that will extract the
                // files into the server's tmp folder where we can then include the
                // files we need individually (it'll remove the temp files after).
                include_once $phar_path;

                // Build the path to the file in the temp directory.
                $class_file_path = $tmp_phar_path;
            }

            // Add the actual class filename to whatever path we need to use.
            $class_file = $class_file_path . '/' . $class_name . '.php';

            // If class file exists either in the phar or temp dir, load it.
            if (file_exists($class_file) && ($phar_stream === true || ($phar_stream === false && stream_resolve_include_path($class_file)) !== false)) {
                $opcache_config = false;

                if (function_exists('opcache_get_configuration')) {
                    $opcache_config = @opcache_get_configuration();
                }

                // You can't include a query string on an include unless it's being included as a phar stream.
                if ($phar_stream === true && !empty($opcache_config) && !empty($opcache_config['directives']['opcache.validate_permission']) && $opcache_config['directives']['opcache.validate_permission'] === true) {
                    include $class_file . '?nocache=' . microtime(true);
                } else {
                    include $class_file;
                }
            } else {
                throw new \Exception('Class at location ' . $class_file . ' not found.');
            }
        } else {
            throw new \Exception('Add-on PHAR file at location ' . $phar_path . ' not found.');
        }
    }
);
