<?php
require_once DRBBACKUP_BASE_DIR . '/modules/class-wpadm-result.php';
require_once DRBBACKUP_BASE_DIR . '/modules/class-wpadm-command.php';
require_once DRBBACKUP_BASE_DIR . '/modules/class-wpadm-command-context.php';
require_once DRBBACKUP_BASE_DIR . '/modules/class-wpadm-queue.php';
require_once DRBBACKUP_BASE_DIR . '/modules/class-wpadm-running.php';
require_once DRBBACKUP_BASE_DIR . '/modules/class-wpadm-command-factory.php';


if (!class_exists('WPAdm_Core')) {
    class WPAdm_Core {

        /*
        *  "fly" POST from server 
        * @var array
        */
        private $request = array();

        /*
        * public key for auth
        * @var string
        */
        private $pub_key;

        /*
        * work result
        * @var WPAdm_Result
        */
        private $result;

        private $plugin;

        public $name = '',
        $time = '';

        public static $cron = true;

        public static $pl_dir;

        public static $error = '';

        public static $plugin_name;

        private static $cron_method = array('local_backup', 'send_to_dropbox', 'full_backup_dropbox');


        public function __construct(array $request, $plugin = '', $plugin_dir = '') {
            $this->result = new WPAdm_Result();
            $this->result->setResult(WPAdm_Result::WPADM_RESULT_ERROR);
            $this->request = $request;
            $this->plugin = $plugin;
            self::$pl_dir = $plugin_dir;
            self::$plugin_name = $plugin;
            // auth request
            if (!$this->auth()) {
                return;
            }                          
            if ('connect' == $request['method']) {
                $this->connect();
            } elseif ('local' == $request['method']){

            } elseif($obj = $this->getObject($request['method'], $request['params'])) {
                if (!is_null($obj) && !is_bool($obj) && $obj->isError()) {
                    $this->result = $obj->get_results();
                } elseif(!is_null($obj) && !is_bool($obj)) {
                    if (isset($obj->name)) {
                        $this->name = $obj->name;
                    }
                    if (isset($obj->time)) {
                        $this->time = $obj->time;
                    }   
                    $this->result = $obj->getResult();
                }
            } else {
                $this->result->setError('Unknown method "' . $request['method'] . '"');
            }
        }


        /**
        * return path to tmp dir
        * @return string
        */
        static public function getTmpDir() {
            $tmp_dir = self::$pl_dir . '/tmp';
            self::mkdir($tmp_dir);
            if (!file_exists($tmp_dir . '/index.php')) {
                file_put_contents($tmp_dir . '/index.php', '');
            }
            return $tmp_dir;
        }

        static public function getPluginDir() {
            return self::$pl_dir;
        }

        /**
        * @param string $method
        * @param mixed $params
        * @return null|WPAdm_Method_Class
        */
        private function getObject($method, $params) {
            if (!preg_match("|[a-zA-Z0-9_\-]|", $method)) {
                return null;
            }
            if (function_exists('mb_strtolower')) {
                $method = mb_strtolower($method); 
            } else {
                $method = strtolower($method); 
            }
            $class_file = self::$pl_dir . "/methods/class-wpadm-method-" . str_replace('_', '-', $method) . ".php";
            if (file_exists($class_file)) {
                require_once $class_file;
                $tmp = explode('_', str_replace('-', '_', $method));
                foreach($tmp as $k=>$m) {
                    $tmp[$k] = ucfirst(strtolower($m));
                }
                $method = implode('_', $tmp);

                $class_name = "WPAdm_Method_{$method}";
                if (!class_exists($class_name)) {
                    $this->getResult()->setError("Class '$class_name' not found");
                    $this->getResult()->setResult(WPAdm_result::WPADM_RESULT_ERROR);
                    return null;
                }
                if (in_array( strtolower( $method ), self::$cron_method) && self::$cron) {
                    WPAdm_Running::setCommand( strtolower($this->request['method']), $params );
                    WPAdm_Running::run();
                    self::$cron = true;
                    return true;
                } else {      
                    return new $class_name($params);
                }

            }
            return null;

        }


        public static function getLog()
        {
            $file_log = self::getTmpDir() . '/log.log';
            if (file_exists($file_log)) {
                return @file_get_contents($file_log);
            }
            return "";
        }

        private function connect() {

            add_option('wpadm_pub_key', $this->pub_key);
            $this->result->setResult(WPAdm_Result::WPADM_RESULT_SUCCESS);

            $sendData['system_data'] = get_system_data();
            $data['actApi'] = 'setStats';
            $data['site'] = get_option('siteurl');
            $data['data'] = wpadm_pack($sendData);
            if (!class_exists('WP_Http')) {
                include_once ABSPATH . WPINC . '/class-http.php';
            }

            $remote            = array();
            $remote['body']    = $data;
            $remote['timeout'] = 20;

            $result = wp_remote_post(WPADM_URL_BASE . "/api/", $remote);
        }
        public static function setPluginDIr($dir)
        {
            self::$pl_dir = $dir;
        }

        /*
        * Авторизация запроса
        */
        private function auth() {
            $this->pub_key = get_option('wpadm_pub_key');
            $methods_local = array('local_backup', 'send-to-dropbox', 'local_restore', 'local', 'queue_controller', 'local_send_to_s3');
            if ( in_array($this->request['method'], $methods_local) ) {
                return true;
            }
            self::log($this->request['method']);
            if (empty($this->pub_key)) {
                if ('connect' == $this->request['method']) {
                    $this->pub_key = $this->request['params']['pub_key'];
                } else {
                    $this->getResult()->setError('Activate site in WPAdm.com for work to plugins.');
                    return false;
                }
            } elseif ('connect' == $this->request['method']) {
                if( $this->pub_key != $this->request['params']['pub_key'] ){
                    $this->getResult()->setError('Ошибка. Воспользуйтесь переподключением плагина.');
                    return false;
                }
            } elseif('queue_controller' == $this->request['method']) {
                //todo: проверить, что запустили сами себя
                return true;

            } 

            $sign = md5(serialize($this->request['params']));
            //openssl_public_decrypt($this->request['sign'], $request_sign, $this->pub_key);
            $ret = $this->verifySignature($this->request['sign'], $this->request['sign2'], $this->pub_key, $sign);


            //$ret = ($sign == $request_sign);
            if (!$ret) {
                $this->getResult()->setError("Неверная подпись");
            }
            return $ret;
        }


        /**
        * Создаем папку
        * @param $dir
        */
        static public function mkdir($dir) {
            if(!file_exists($dir)) {
                @mkdir($dir, 0755);
                if (!is_dir($dir)) {
                    self::$error = str_replace("&s", $dir, __('Failed to create a file, please check the permissions on the folders "&s".','dropbox-backup') );
                } else {
                    //todo: права доступа
                    @file_put_contents($dir . '/index.php', '<?php echo "Hello World!"; ');
                    if ( !is_writable($dir . '/index.php') ) {
                        self::$error = str_replace("&s", $dir, __('Failed to create a file, please check the permissions on the folders "&s".','dropbox-backup') );
                    }
                }
            }
            return self::$error;
        }

        /**
        * @return WPAdm_result result
        */
        public function getResult() {
            return $this->result;
        }


        public function verifySignature($sign, $sign2, $pub_key, $text) {
            if (function_exists('openssl_public_decrypt')) {
                openssl_public_decrypt($sign, $request_sign, $pub_key);
                $ret = ($text == $request_sign);
                return $ret;
            } else {
                set_include_path(get_include_path() . PATH_SEPARATOR . self::getPluginDir() . '/modules/phpseclib');
                require_once 'Crypt/RSA.php';
                $rsa = new Crypt_RSA();
                $rsa->loadKey($pub_key);
                $ret = $rsa->verify($text, $sign2);
                return $ret;
            }
        }

        /**
        * @param $sign
        * @param $request_sign
        * @param $pub_key
        */
        public function openssl_public_decrypt($sign, &$request_sign, $pub_key) {
            //openssl_public_decrypt($sign, $request_sign, $pub_key);

        }


        static public function log($txt, $class='') {
            if (!empty($txt)) {
                $log_file = self::getTmpDir() . '/log.log';
                file_put_contents($log_file, date("Y-m-d H:i:s") ."\t{$class}\t{$txt}\n", FILE_APPEND);
            }
        }

        /**
        * Удаляет директорию со всем содержимым
        * @param $dir
        */
        static function rmdir($dir) {
            if (is_dir($dir)) {
                $files = glob($dir. '/*');
                foreach($files as $f) {
                    if ($f == '..' or $f == '.') {
                        continue;
                    }
                    if (is_dir($f)) {
                        self::rmdir($f);
                    }
                    if (file_exists($f)) {
                        @unlink($f);
                    }
                }
                @rmdir($dir);
            } elseif (is_file($dir)) {
                @unlink($dir);
            }
        }
        static function dir_writeble($dir)
        {
            $error = self::mkdir($dir);
            $ret = true;
            if (empty($dir)) {
                @file_put_contents($dir . "/test", "Hello World!!!");
                if (!@is_writable($dir . "/test") && @filesize($dir . "/test") == 0) {
                    $ret = false;
                }
                @unlink($dir . "/test");
            }
            return $ret;
        }
    }
}
