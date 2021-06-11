<?php

namespace sagittaracc;

/**
 * Хелпер json обработки
 * 
 * @author sagittaracc <sagittaracc@gmail.com>
 */
class Json
{
    /**
     * @var array данные json
     */
    private $jsonData;
    /**
     * @var array буффер
     */
    private $buf;
    /**
     * Загружает json данные
     * @param string $input может быть json строкой или путь к файлу .json
     */
    function __construct($input)
    {
        $jsonData = json_decode($input, true);

        if (!$jsonData) {
            $jsonData = json_decode(file_get_contents($input), true);
        }

        $this->jsonData = $jsonData;
    }
    /**
     * Алиас для конструктора
     * @param string $input может быть json строкой или путь к файлу .json
     */
    public static function load($input)
    {
        return new static($input);
    }
    /**
     * Запись в файл
     * @param string $filename
     * @param string|null $path
     * @param array $except
     */
    public function saveAs($filename, $path = null, $except = [])
    {
        $json = ArrayHelper::getValue($this->jsonData, $path);

        if (ArrayHelper::isSequential($json)) {
            $this->saveArrayAs($filename, $json, $except);
        }
        else {
            $this->saveObjectAs($filename, $json, $except);
        }
    }
    /**
     * Сохраняет массив из json
     * @param string $filename
     * @param string|null $path
     * @param array $except
     */
    private static function saveArrayAs($filename, $json, $except)
    {
        $keys = implode(';', array_keys($json[0]));
        shell_exec("echo $keys >> $filename");

        foreach ($json as $line) {
            static::getRidOfUnexpected($line, $except);

            $values = implode(';', array_values($line));
            shell_exec("echo $values >> $filename");
        }
    }
    /**
     * Сохраняет объект из json
     * @param string $filename
     * @param string|null $path
     * @param array $except
     */
    private static function saveObjectAs($filename, $json, $except)
    {
        static::getRidOfUnexpected($json, $except);

        $keys = implode(';', array_keys($json));
        $values = implode(';', array_values($json));

        shell_exec("echo $keys >> $filename");
        shell_exec("echo $values >> $filename");
    }
    /**
     * Вырезать ненужное из массива
     */
    private static function getRidOfUnexpected(&$json, $except)
    {
        foreach ($except as $item) {
            unset($json[$item]);
        }
    }
}