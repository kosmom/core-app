Core Framework debugger panel

place this code in index.php after c\mvc::init()

```php
register_shutdown_function(function () {
  if (empty(c\core::$data['dump_file'])) return;
  chdir(c\mvc::$base__DIR__);
  $response = [
    'datetime' => date('d.m.Y H:i:s'),
    'url' => c\mvc::getUrl(),
    'avg' => sys_getloadavg(),
    'lang' => c\core::$lang,
    'debug' => c\core::$debug,
    'env' => c\core::$env,
    'version' => c\core::$version,
    'included_files' => get_included_files(),
    'memory_usage' => memory_get_usage(),
    'memory_peak_usage' => memory_get_peak_usage(),
    'header_list' => headers_list(),
    'response_code' => http_response_code(),
    'server' => $_SERVER,
    'data' => c\core::$data,
  ];
  if (c\mvc::$file_links) $response['route'] = c\mvc::$file_links;
  if ($input = file_get_contents("php://input")) $response['input'] = $input;
  if ($_POST) $response['post'] = $_POST;
  if ($_GET) $response['get'] = $_GET;
  if ($_FILES) $response['files'] = $_FILES;
  if ($_COOKIE) $response['cookie'] = $_COOKIE;
  if ($_SESSION) $response['session'] = $_SESSION;
  if (c\core::$watch) $response['watch'] = c\core::$watch;
  if (c\debug::$debugBuffer) $response['debug_buffer'] = c\debug::$debugBuffer;
  c\filedata::appendDataPart(c\core::$data['dump_file'], $response);
});
```

and write

```php
c\core::$data['dump_file'] = 'logs/debugger/some-filename.log';
```

in place what you want to watch