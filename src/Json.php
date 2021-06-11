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
     * @var array структура
     */
    private $structure;
    /**
     * @var boolean выводить заголовок
     */
    private $headerShown = true;
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
     * Вывод заголовка
     * @param boolean $shown
     */
    public function setHeader($shown)
    {
        $this->headerShown = $shown;

        return $this;
    }
    /**
     * Запись в файл
     * @param string $filename
     * @param string|null $path
     * @param array $except
     */
    public function saveAs($filename, $path = null, $except = [])
    {
        if ($this->structure) {
            $this->saveStructureAs($filename);
            return;
        }

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
    private function saveArrayAs($filename, $json, $except)
    {
        if ($this->headerShown) {
            $keys = implode(';', array_keys($json[0]));
            shell_exec("echo $keys >> $filename");
        }

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
    private function saveObjectAs($filename, $json, $except)
    {
        static::getRidOfUnexpected($json, $except);

        if ($this->headerShown) {
            $keys = implode(';', array_keys($json));
            shell_exec("echo $keys >> $filename");
        }

        $values = implode(';', array_values($json));
        shell_exec("echo $values >> $filename");
    }
    /**
     * Структура json
     * @param string|null $path
     * @param array $except
     */
    public function getStructure($except = [], $path = null)
    {
        $this->structure = [];

        $json = ArrayHelper::getValue($this->jsonData, $path);

        if (ArrayHelper::isSequential($json)) {
            $this->getArrayStructure($json, $except);
        }
        else {
            $this->getObjectStructure($json, $except);
        }

        return $this;
    }
    /**
     * Структура объекта
     * @param array $json
     * @param array $except
     */
    private function getObjectStructure($json, $except)
    {
        $this->getRidOfUnexpected($json, $except);

        foreach ($json as $key => $value) {
            $this->structure[$key] = gettype($value);
        }
    }
    /**
     * Структура массива
     * @param array $json
     * @param array $except
     */
    private function getArrayStructure($json, $except)
    {
        $this->getObjectStructure($json[0], $except);
    }
    /**
     * Сохранение структуры
     * @param string $filename
     */
    private function saveStructureAs($filename)
    {
        $structure = json_encode($this->structure, JSON_PRETTY_PRINT);

        file_put_contents($filename, $structure);
    }
    /**
     * Вырезать ненужное из массива
     * @param array $json
     * @param array $except
     */
    private static function getRidOfUnexpected(&$json, $except)
    {
        foreach ($except as $item) {
            unset($json[$item]);
        }
    }
}