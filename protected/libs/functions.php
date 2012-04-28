<?
/**
 * PHP 5.2 compatibility
 */
if (function_exists('lcfirst') === false)
{
    function lcfirst($str)
    {
        $str[0] = strtolower($str[0]);
        return $str;
    }
}

/**
 * PHP 5.2 compatibility
 */
if (function_exists('get_called_class') === false)
{
    function get_called_class()
    {
        $bt    = debug_backtrace();
        $lines = file($bt[1]['file']);
        preg_match('/([a-zA-Z0-9\_]+)::' . $bt[1]['function'] . '/', $lines[$bt[1]['line'] - 1], $matches);
        return $matches[1];
    }
}


/**
 * @param $message
 * @return string
 */
function t($message)
{
    $translated_message = Yii::t('main', $message);
    if ($translated_message != $message)
    {
        return $translated_message;
    }

    $messages = LanguageMessage::getList();
    if (!in_array($message, $messages))
    {
        $language_message = new LanguageMessage();
        $language_message->message  = $message;
        $language_message->category = LanguageMessage::DEFAULT_CATEGORY;
        //$language_message->save();
    }

    return $message;
}

/*--------------------debug functions----------------*/
function p($data)
{
    echo '<pre>';
    CVarDumper::dump($data, 1000, false);
    echo '</pre>';
}


function v($data)
{
    echo "<pre>" . var_dump($data) . "</pre>";
}



/**
 * Debug функция, использемая только для отладки
 *
 * @param $var
 * @param int $skipCount
 * @param int $depth
 */
function dump($var, $skipCount = 0, $depth = 2)
{
    static $startSkipCount = 0;
    static $localSkipCount = 0;

    if ($startSkipCount == 0) {
        $startSkipCount = $localSkipCount = $skipCount;
    }
    else
    {
        $localSkipCount--;
    }

    if ($localSkipCount == 0)
    {
        $startSkipCount = 0;

        echo '<pre>';
        CVarDumper::dump($var, $depth, true);
        echo '</pre>';

        exit();
    }
}

/**
 * Выводит текст и завершает приложение (применяется в ajax-действиях)
 *
 * @param string|array $text текст|массив для вывода
 */
function stop($data = '')
{
    if (is_array($data))
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
    else
    {
        echo $data;
    }

    exit();
}
