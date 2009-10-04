<?php // PHP 4.3.0 or greater

/** The Most Basic Ajax - PHP Error Debugger
 *
 * Formaldehyde aim is to make easy and to speed up JavaScript - PHP
 * interactions development/debug via a single, complete, unobtrusive,
 * lightweight, fast, and reliable file: formaldehyde.php
 * 
 * This file will trap the entire output buffer and will release
 * it only at the end of any kind of process.
 * If errors or Exceptions, both user defined or native, fatal errors included,
 * are triggered/raised during called page execution, formaldehyde will
 * return a JavaScript Error like JSON Object inside a page with status == 500
 *
 * Please note that errors are generated accordingly with defined
 * error_reporting level. If notices are suppressed, notices
 * will not generate a response 500
 * 
 * @example     a basic XMLHttpRequest request
 *
 *  var xhr = new XMLHttpRequest;
 *  // mypage.php should require 'formaldehyde.php';
 *  xhr.open("get", "mypage.php", false);
 *  xhr.send(null);
 * 
 *  if(xhr.status === 500){
 *      if(xhr.getResponseHeader("X-Formaldehyde") !== null)
 *          console.log(JSON.parse(xhr.responseText));
 *      else
 *          console.log(xhr.responseText);
 *  } else
 *      console.log(xhr.responseText);
 * --------------------------------------------------------------
 *
 * Copyright (c) 2009 Andrea Giammarchi
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category    Ajax
 * @author      Andrea Giammarchi
 * @copyright   2009 Andrea Giammarchi
 * @version     CVS: $Id: formaldehyde.php, v 1.10 2009/09/14 20:10:03 andrea.giammarchi Exp $
 * @license     http://www.opensource.org/licenses/mit-license.php
 * @project     http://code.google.com/p/formaldehyde/
 * @blog        http://webreflection.blogspot.com/
 */

/**
 * If false, Formaldehyde functions will be available
 * but formaldehyde_init will not be called.
 * It is possible to use $_SERVER checks to understand
 * if this file is used locally or remotly and set
 * the "flag" automatically.
 */
define('FORMALDEHYDE', true);

// functions list alphabetic order
// Note: if FORMALDEHYDE is active (deployment)
// these functions should never be called manually
// Otherwise these functions are available
// but never executed (production)

/**
 * output buffer manager
 *
 * The output is trapped but it should never be released
 * until the end of the process.
 * In an error occurred the buffer is replaced
 * with JSON representation of this error
 * with a status 500 response and plain/text as content-type.
 * If everything is OK, the output is simply released
 * at the end of the execution and no header will be modified.
 *
 * @param   bool    error occurred. If true, overwrite the output buffer with JSON string.
 * @param   string  buffer to wrap or the JSON object
 * @param   mixed   if true set buffer return to true.
 * @return  string  empty string or stored buffer.
 */
function formaldehyde_buffer($stop, $string = '', $return = null){
    static  $buffer = false,
            $error  = false,
            $output = ''
    ;
    if($stop === true){
        $error = true;
        $output = $string;
    }
    elseif(!$error)
        $output.= $string
    ;
    if($return === true)
        $buffer = true
    ;
    return $error || $buffer ? $output : '';
}

/**
 * error code to error name
 *
 * Unless we are not that geek, it is better to understand
 * the error type by name.
 * e.g. "E_ERROR" rather than 1
 *
 * @param   int     error code
 * @return  string  error name
 */
function formaldehyde_code($code){
    switch($code){
        case      1:$error='E_ERROR';break;
        case      2:$error='E_WARNING';break;
        case      4:$error='E_PARSE';break;
        case      8:$error='E_NOTICE';break;
        case     16:$error='E_CORE_ERROR';break;
        case     32:$error='E_CORE_WARNING';break;
        case     64:$error='E_COMPILE_ERROR';break;
        case    128:$error='E_COMPILE_WARNING';break;
        case    256:$error='E_USER_ERROR';break;
        case    512:$error='E_USER_WARNING';break;
        case   1024:$error='E_USER_NOTICE';break;
        case   2048:$error='E_STRICT';break;
        case   4096:$error='E_RECOVERABLE_ERROR';break;
        case   8192:$error='E_DEPRECATED';break;
        case  16384:$error='E_USER_DEPRECATED';break;
        default    :$error='E_ALL';break;
    }
    return $error;
}

/**
 * JSON.parse wrapper
 *
 * Recent PHP version have a native, fast, reliable
 * json_encode function. This is the predefined one
 * to generate the JSON response if there is an error.
 * If PHP is old enough, there are two other options.
 * The good old JSON_Service, via Pear, or a quick
 * simple and fast personal implementation.
 * In the first case, we need to include that package
 * manually but it is probably unnecessary thanks
 * to default parser good enough for every
 * Formaldehyde purpose.
 *
 * @param   mixed   generic variable to encode
 * @return  string  JSON string
 * @optional        http://mike.teczno.com/JSON/JSON.phps
 */
if(function_exists('json_encode')){
    function formaldehyde_encode($o){
        return json_encode($o);
    }
} elseif(class_exists('Services_JSON')) {
    function formaldehyde_encode($o){
        $json = new Services_JSON;
        return $json->encode($o);
    }
} else {
    function formaldehyde_encode($o){
        static  $find   = array("\x08", "\x09", "\x0a", "\x0c", "\x0d", "\x22", "\x5c"),
                $replace= array("\\b",  "\\t",  "\\n",  "\\f",  "\\r",  "\\\"", "\\\\"),
                $escape = true
        ;
        if($escape){
            for($i = 0x00; $i < 0x20; ++$i){
                $find[] = chr($i);
                $replace[] = '\x'.($i < 0x10 ? '0' : '').base_convert($i, 10, 16);
            }
            for($i = 0x7f; $i < 0xa0; ++$i){
                $find[] = chr($i);
                $replace[] = '\x'.base_convert($i, 10, 16);
            }
            $escape = false;
        }
        switch(true){
            case    is_array($o):
                if(array_keys($o) === range(0, count($o) - 1))
                    return '['.implode(',', array_map('formaldehyde_encode', $o)).']';
            case    is_object($o):
                $result = array();
                foreach($o as $key => $value)
                    $result[] = '"'.str_replace($find, $replace, $key).'":'.formaldehyde_encode($value);
                return '{'.implode(',', $result).'}';
            case    is_string($o):
                return '"'.str_replace($find, $replace, $o).'"';
            case    is_bool($o):
                return $o ? 'true' : 'false';
            case    is_float($o):
            case    is_double($o):
                return is_finite($o) ? (float) $o : 'null';
            case    is_integer($o):
                return is_finite($o) ? (int) $o : 'null';
            default:
                return 'null';
        }
    }
}

/**
 * error and Exception handler
 *
 * callback used for both set_error_handler
 * and set_exception_handler
 *
 * @param   mixed   generated Exception or the error code
 * @param   string  optional error message
 * @param   string  optional file name
 * @param   int     optional line number
 * @param   object  optional stack but debug_backtrace() will be used instead
 */
function formaldehyde_error($exception, $message = '', $fileName = '', $lineNumber = 0, $stack = null){
    is_object($exception) ?
        formaldehyde_output(
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTrace(),
            $exception
        ) :
        formaldehyde_output(
            $exception,
            $message,
            $fileName,
            $lineNumber,
            debug_backtrace(),
            false
        )
    ;
}

/**
 * last action to execute
 *
 * Both error handler and shutdown function could try to output something.
 * This could happen specially with Fatal and other unexpected errors.
 * To avoid problems, both functions will call this exit one
 * which is flagged to be executed only once.
 *
 * This function performs these tasks:
 *  1 - set X-Formaldehyde-Log header with optional logs
 *  2 - set X-Formaldehyde header with elapsed time
 *  3 - set output buffer with the error, or the noraml output
 *
 * @param   bool    error occurred. If true, overwrite the output buffer with JSON string.
 * @param   string  buffer to wrap or the JSON object
 * @param   mixed   if true set buffer return to true.
 * @return  string  empty string or stored buffer.
 */
function formaldehyde_exit($stop, $string, $return){
    static $execute = true;
    if($execute){
        $execute = false;
        @header('X-Formaldehyde-Log: '.formaldehyde_encode(formaldehyde_remove_recursion(formaldehyde_log())));
        @header('X-Formaldehyde: '.round(formaldehyde_time(), 6));
        formaldehyde_buffer($stop, $string, $return);
    }
}

/**
 * output handler
 *
 * send buffer to append and return buffer manager result
 *
 * @param   string  output buffer to append
 * @return  string  buffer manager returned string
 */
function formaldehyde_handler($string){
    return formaldehyde_buffer(false, $string);
}

/**
 * init function
 *
 * If FORMALDEHYDE is true, this function will be
 * automatically called.
 * This function performs these tasks:
 *  1 - trap current microtime
 *  2 - set output buffer handler
 *  3 - set error handler
 *  4 - register shutdown function
 *  5 - if compatible, set exception handler
 */
function formaldehyde_init(){
    formaldehyde_time();
    ini_set('display_errors', 0);
    ob_start('formaldehyde_handler', 1, false);
    set_error_handler('formaldehyde_error');
    register_shutdown_function('formaldehyde_shutdown');
    if(function_exists('set_exception_handler'))
        set_exception_handler('formaldehyde_error')
    ;
}

/**
 * basic log storage - experimental
 * 
 * It could be useful to save some extra log
 * without generating an error.
 * This simple function accepts key value pairs
 * and for each key create a list of values.
 * e.g. formaldehyde_log('test', 1);
 *      formaldehyde_log('test', 2);
 * $log === array('test' => array(1, 2));
 *
 * The $log storage will be parsed into JSON
 * and returned in any case as
 * X-Formaldehyde-Log header
 *
 * @param   string  generic key to storage
 * @param   mixed   generic JSON compatible value to save
 * @return  object  associative array with stored key/value pairs
 */
function formaldehyde_log($key = '', $value = null){
    static  $log = array();
    if($key !== '')
        isset($log[$key]) ? $log[$key][] = $value : $log[$key] = array($value);
    return $log;
}

/**
 * create formaldehyde output if an error occurred
 *
 * If something went wrong this function will overwrite the
 * content-type header setting it as text/plain
 * The status code will be 500
 * The response will be a JavaScript Error like JSON Object.
 * For example, it is possible to create our own ServerError
 * JavaScript constructor in this way:
 * @example
 *
 *  function ServerError(e){
 *      // e will be the formaldehyde object
 *      for(var key in e)
 *          this[key] = e[key]
 *      ;
 *  };
 *  // Firebug shows Errors in a different way
 *  // it is more easy to spot them via console
 *  // if the name is part of the prototype
 *  (ServerError.prototype = new Error).name = "ServerError";
 *
 *  if(xhr.status === 500 && xhr.getResponseHeader("X-Formaldehyde") !== null)
 *      console.log(JSON.parse(xhr.responseText));
 *
 *  // ServerError: [E_WARNING] failed to ...
 * -------------------------------------------------------
 *
 * @param   indt    error code
 * @param   string  error message
 * @param   string  file name
 * @param   int     line number
 * @param   object  Exception getTrace() or debug_backtrace()
 */
function formaldehyde_output($code, $message, $fileName, $lineNumber, $stack, $exception){
    $is_object = is_object($exception);
    if($is_object || (error_reporting() & $code)){
        $error = new stdClass;
        $error->message = '['.($is_object ? ($code ? $code : get_class($exception)) : formaldehyde_code($code)).'] '.strip_tags($message);
        $error->fileName = $fileName;
        $error->lineNumber = $lineNumber;
        $error->stack = formaldehyde_remove_recursion($stack);
        $json = formaldehyde_encode($error);
        @header('Content-Type: text/plain', true, 500);
        formaldehyde_exit(true, $json, null);
        exit($json);
    }
}

/**
 * recursion manager
 *
 * stored log, stack trace, or getTrace, could contain recursions.
 * This callback aim is to remove recursions from a generic var.
 * This function is based on native serialize one.
 *
 * @param   mixed   generic variable to manage
 * @return  mixed   same var without recursion problem
 */
function formaldehyde_remove_recursion($o){
    static  $replace;
    if(!isset($replace))
        $replace = create_function('$m','$r="\x00{$m[1]}ecursion_";return \'s:\'.strlen($r.$m[2]).\':"\'.$r.$m[2].\'";\';')
    ;
    if(is_array($o) || is_object($o)){
        $re = '#(r|R):([0-9]+);#';
        $serialize = serialize($o);
        if(preg_match($re, $serialize)){
            $last = $pos = 0;
            while(false !== ($pos = strpos($serialize, 's:', $pos))){
                $chunk = substr($serialize, $last, $pos - $last);
                if(preg_match($re, $chunk)){
                    $length = strlen($chunk);
                    $chunk = preg_replace_callback($re, $replace, $chunk);
                    $serialize = substr($serialize, 0, $last).$chunk.substr($serialize, $last + ($pos - $last));
                    $pos += strlen($chunk) - $length;
                }
                $pos += 2;
                $last = strpos($serialize, ':', $pos);
                $length = substr($serialize, $pos, $last- $pos);
                $last += 4 + $length;
                $pos = $last;
            }
            $serialize = substr($serialize, 0, $last).preg_replace_callback($re, $replace, substr($serialize, $last));
            $o = unserialize($serialize);
        }
    }
    return $o;
}

/**
 * shutdown handler
 *
 * register_shutdown is probably the most simple
 * way to intercept fatal errors.
 * This function should be performed an instant before
 * the last call to the output buffer one. * 
 */
function formaldehyde_shutdown(){
    $error = error_get_last();
    empty($error) ?
        formaldehyde_exit(false, '', true) :
        formaldehyde_output($error['type'], $error['message'], $error['file'], $error['line'], debug_backtrace(), false)
    ;
}

/**
 * request execution time
 * 
 * @return  float   elapsed time or current microtime
 */
function formaldehyde_time(){
    static $time;
    return isset($time) ? microtime(true) - $time : $time = microtime(true);
}

// ready to debug ?
if(FORMALDEHYDE)
    formaldehyde_init()
;
?>