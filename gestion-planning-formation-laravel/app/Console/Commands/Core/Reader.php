<?php

namespace App\Console\Commands\Core;

/**
 * Class Reader
 * Reader for migrations files Laravel
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/01/2020
*/
class Reader {

    /**
     * @var string
     */
    protected $dirname = '';

    /**
     * @var array
     */
    protected $allClasses = [];

    /**
     * Reader constructor.
     * @param $dirname
     */
    public function __construct($dirname)
    {
        $this->init($dirname);
    }

    /**
     * Init the dir
     * @param $dirname
     */
    protected function init($dirname) {
        if( ! is_dir(dirname($dirname))) {
            throw new \RuntimeException('Le dossier "'.$this->dirname.'" est non accessible !');
        }
        else
        {
            $this->dirname = $dirname;
            $this->setAllClasses();
        }
    }

    /**
     * Find structure of classe
     *
     * @param $filename
     *
     * @return array
     */
    protected function getAllDatas($filename) {

        $tabLine = $this->getTabLines($filename);
        $className = $this->findClassName($tabLine);

        $tab = [];
        if($className !== false) {
            $tab[$className] = $this->getAttrs($tabLine);
        }

        return $tab;

    }

    /**
     * @param $tabLine
     * @return bool
     */
    protected function findClassName($tabLine) {

        foreach ($tabLine as $line) {
            // if(preg_match("@class Create([^./ ]+)Table extends Migration@i", $line, $matches)) {
            if(preg_match("@Schema::create\('([^./ ]+)', function@i", $line, $matches)) {
                return ucfirst($matches[1]);
            }
        }

        return false;
    }

    /**
     * Get matched type of data
     *
     * @param $type
     * @param $name
     * @return string
     */
    protected function getMatchedType($name, $type) {

        switch ($name) {
            case 'image':
                $ret = 'image';
                break;
            case 'email':
                $ret = 'email';
                break;
            default:
                switch ($type) {
                    case 'bigIncrements':
                    case 'tinyInteger':
                    case 'integer':
                    case 'unsignedBigInteger':
                    case 'bigInteger':
                    case 'unsignedInteger':
                    case 'smallInteger':
                        $ret = 'integer';
                        break;
                    case 'double':
                    case 'float':
                        $ret = 'numeric';
                        break;
                    case 'dateTime':
                    case 'timestamps':
                    case 'timestamp':
                    case 'date':
                        $ret = 'date';
                        break;
                    case 'char':
                    case 'string':
                        $ret = 'string';
                        break;
                    case 'text':
                        $ret = 'text';
                        break;
                    case 'boolean':
                        $ret = 'boolean';
                        break;
                    default:
                        $ret = 'string';
                }
        }
        return $ret;
    }

    /**
     * Get all the Attributes of classes
     *
     * @param $tabLine
     * @return array
     */
    protected function getAttrs($tabLine) {
        $tab = [];

        foreach ($tabLine as $line) {

            if(preg_match("@table->([\w]+)\('([\w]+)@i", $line, $matches)) {
                $name = $matches[2];
                $type = $this->getMatchedType($name, $matches[1]);
                $unique = ($name == 'id' or stristr($line, 'unique')) ? true : false;
                $nullable = stristr($line, 'nullable') ? true : false;

                if($type != 'foreign' and $name != 'author_id') {
                    $tab[$name] = [
                        'type' => $type,
                        'unique' => $unique,
                        'nullable' => $nullable,
                    ];
                }
            }
        }

        //print_r($tab);
        return $tab;

    }

    /**
     * Get list of lines
     * @param $filename
     * @return array
     */
    protected function getTabLines($filename) {
        $fic = fopen($filename, 'r');

        $listLines = [];
        if($fic) {
            while (($buffer = fgets($fic, 4096)) !== false) {
                $listLines[] = $buffer;
            }
        }
        return $listLines;
    }

    /**
     * List the files in dir
     */
    public function getListFiles() {
        $tab = [];
        $tabFiles = scandir($this->dirname);
        foreach ($tabFiles as $file) {
            if($file != '.' and $file != '..') {
                if(is_readable($this->dirname.'/'.$file)) {
                   $tab[] = $this->dirname.'/'.$file;
                }
            }
        }
        return $tab;
    }

    /**
     * Get all classes of off files
     *
     * @return array
     */
    public function getAllClasses() {
        return $this->allClasses;
    }

    /**
     * Contruct all classes from files
     */
    protected function setAllClasses() {

        $listFiles = $this->getListFiles();

        $tab = [];
        foreach ($listFiles as $file) {
            $new = $this->getAllDatas($file);
            $tab = array_merge($tab, $new);
        }

        $this->allClasses = $tab;

    }

    /**
     * Get the only classes of this array
     *
     * @param array $tabRequired
     * @return array
     */
    public function getOnlyClasses(array $tabRequired) {

        $ret = [];

        $tabClasses = $this->getAllClasses();
        foreach ($tabClasses as $classe => $value) {
            if(array_search($classe, $tabRequired) > -1) {
                $ret = array_merge($ret, [$classe => $value]);
            }
        }

        return $ret;
    }

    /**
     * Test Method
     */
    public function test() {
        $classes = $this->getAllClasses();
        print_r($classes);
    }

}

/**
 * Test
 */
function test() {

    $line = "table->string('name', 45)->unique();";

    //Clean the memory
    if(preg_match("@table->([\w]+)\('([\w]+)'\, [\d]+\)@i", $line, $matches)) {
        print_r("Find !\n");
        print_r($matches);
        print_r(stristr("table->string('name', 45)->unique();", 'unique'));
    }
    else
        print_r("Not found !\n");
}

