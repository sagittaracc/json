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
     * @var string строка поиска в json
     */
    private $path = null;
    /**
     * @var array игнорируемые узлы
     */
    private $except = [];
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
     * Задать path
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
    /**
     * Алиас для setPath
     * @param string $path
     */
    public function read($path)
    {
        return $this->setPath($path);
    }
    /**
     * Задать игнорируемые узлы
     * @param array $except
     */
    public function setExcept($except)
    {
        $this->except = $except;

        return $this;
    }
    /**
     * Алиас для setExcept
     * @param array $except
     */
    public function except($except)
    {
        return $this->setExcept($except);
    }
    /**
     * Сброс настроек
     */
    public function flush()
    {
        $this->path = null;
        $this->except = [];
    }
    /**
     * Запись в файл
     * @param string $filename
     */
    public function saveAs($filename)
    {
        if ($this->structure) {
            $this->saveStructureAs($filename);
            return;
        }

        $json = ArrayHelper::getValue($this->jsonData, $this->path);

        if (ArrayHelper::isSequential($json)) {
            $this->saveArrayAs($filename, $json);
        }
        else {
            $this->saveObjectAs($filename, $json);
        }

        $this->flush();
    }
    /**
     * Сохраняет массив из json
     * @param string $filename
     * @param array $json
     */
    private function saveArrayAs($filename, $json)
    {
        if ($this->headerShown) {
            $keys = implode(';', array_keys($json[0]));
            shell_exec("echo $keys >> $filename");
        }

        foreach ($json as $line) {
            $this->getRidOfUnexpected($line);

            $values = implode(';', array_values($line));
            shell_exec("echo $values >> $filename");
        }
    }
    /**
     * Сохраняет объект из json
     * @param string $filename
     * @param array $json
     */
    private function saveObjectAs($filename, $json)
    {
        $this->getRidOfUnexpected($json);

        if ($this->headerShown) {
            $keys = implode(';', array_keys($json));
            shell_exec("echo $keys >> $filename");
        }

        $values = implode(';', array_values($json));
        shell_exec("echo $values >> $filename");
    }
    /**
     * Структура json
     */
    public function getStructure()
    {
        $this->structure = [];

        $json = ArrayHelper::getValue($this->jsonData, $this->path);

        if (ArrayHelper::isSequential($json)) {
            $this->getArrayStructure($json);
        }
        else {
            $this->getObjectStructure($json);
        }

        return $this;
    }
    /**
     * Структура объекта
     * @param array $json
     */
    private function getObjectStructure($json)
    {
        $this->getRidOfUnexpected($json);

        foreach ($json as $key => $value) {
            $this->structure[$key] = gettype($value);
        }
    }
    /**
     * Структура массива
     * @param array $json
     */
    private function getArrayStructure($json)
    {
        $this->getObjectStructure($json[0]);
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
     */
    private function getRidOfUnexpected(&$json)
    {
        foreach ($this->except as $item) {
            unset($json[$item]);
        }
    }
}