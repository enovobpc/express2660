<?php
namespace App\Models;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\File;
use Psr\Log\LogLevel;
use ReflectionClass, Auth;

class LogViewer {

    /**
     * @var string file
     */
    private static $file;

    /**
     * File max size
     */
    const MAX_FILE_SIZE = 52428800; // 50MB

    /**
     * @param string $file
     */
    public static function setFile($file) {
        $file = self::pathToLogFile($file);

        if (File::exists($file)) {
            self::$file = $file;
        }
    }

    /**
     * Return path to a log file
     *
     * @param $file
     * @return string
     * @throws \Exception
     */
    public static function pathToLogFile($file) {
        $logsPath = storage_path('logs');

        if (File::exists($file)) { // try the absolute path
            return $file;
        }

        $file = $logsPath . '/' . $file;

        // check if requested file is really in the logs directory
        if (dirname($file) !== $logsPath) {
            throw new \Exception('Não existe o ficheiro de registo.');
        }

        return $file;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public static function getFileName() {
        return basename(self::$file);
    }

    /**
     * List all logs
     *
     * @return array
     */
    public static function all() {
        $log = array();

        $log_levels = self::getLogLevels();

        $pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/';

        if (!self::$file) {
            $log_file = self::getFiles();
            if(!count($log_file)) {
                return [];
            }
            self::$file = $log_file[0];
        }

        if (File::size(self::$file) > self::MAX_FILE_SIZE) return null;

        $file = File::get(self::$file);

        preg_match_all($pattern, $file, $headings);

        if (!is_array($headings)) return $log;

        $logData = preg_split($pattern, $file);

        if ($logData[0] < 1) {
            array_shift($logData);
        }

        foreach ($headings as $h) {
            for ($i=0, $j = count($h); $i < $j; $i++) {
                foreach ($log_levels as $level_key => $levelValue) {
                    if (strpos(strtolower($h[$i]), '.' . $levelValue)) {

                        preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(\w+)\.' . $level_key . ': (.*?)( in .*?:[0-9]+)?$/', $h[$i], $current);

                        if (!isset($current[3])) continue;

                        $log[] = array(
                            'context'       => $current[2],
                            'level'         => $levelValue,
                            'level_color'   => trans('admin/log_viewer.colors.' . $levelValue),
                            'level_icon'    => trans('admin/log_viewer.icons.' . $levelValue),
                            'date'          => $current[1],
                            'text'          => $current[3],
                            'in_file'       => isset($current[4]) ? $current[4] : null,
                            'stack'         => preg_replace("/^\n*/", '', $logData[$i])
                        );
                    }
                }
            }
        }

        return array_reverse($log);
    }

    /**
     * @param bool $basename
     * @return array
     */
    public static function getFiles($basename = false) {
        $files = glob(storage_path() . '/logs/*');
        $files = array_reverse($files);
        $files = array_filter($files, 'is_file');
        if ($basename && is_array($files)) {
            foreach ($files as $k => $file) {
                $files[$k] = basename($file);
            }
        }
        return array_values($files);
    }

    /**
     * Return log levels
     *
     * @return array
     */
    private static function getLogLevels() {
        $class = new ReflectionClass(new LogLevel);
        return $class->getConstants();
    }

    /**
     * Get log trace
     *
     * @param $exception
     * @param $request
     */
    public static function getTrace($exception, $message = null, $adicionalParams = []) {

        $trace = '';

        if(!empty($exception)) {
            $trace = get_class($exception);
        }

        if(!empty($message)) {
            $trace = $message;
        } elseif(@$exception->getMessage()) {
            $trace.= ': ' . $exception->getMessage();
        }

        $trace.= ' in ['. Request::method() . '] '.Request::url().':999999<br/>';
        $trace.= 'Method: ' . Request::method() . '<br/>';

        if(!empty($adicionalParams)) {
            $trace.= 'Params: ' . json_encode($adicionalParams) . '<br/>';
        }

        if(!empty(Request::all())) {
            $trace.= 'Input: ' . json_encode(Request::except(['_token'])) . '<br/>';
        }

        if(@Auth::user()->id) {
            $trace.= 'User ID: ' . @Auth::user()->id . '<br/>';
        }

        if(@Auth::guard('customer')->user()->id) {
            $trace .= 'Customer ID: ' . @Auth::guard('customer')->user()->id . '<br/>';
        }

        if(!empty($exception) && !empty($exception->getFile())) {
            $trace.= 'File: ' . $exception->getFile() .'<br/>';
        }

        if(!empty($exception) && !empty($exception->getLine())) {
            $trace.= 'Line: ' . $exception->getLine() .'<br/>';
        }

        $trace.= 'User-Agent: ' . @$_SERVER['HTTP_USER_AGENT'] . '<br/>';
        $trace.= 'IP: ' . client_ip() . '<br/>';

        return $trace;
    }
}