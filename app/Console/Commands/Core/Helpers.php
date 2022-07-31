<?php

namespace App\Console\Commands\Core;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Trait Helpers
 *
 * @package App\Console\Commands\Core
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date 07/01/2020
 */
trait Helpers {

    protected $write = false;

    /**
     * now() time
     *
     * @return string
     */
    protected function now() {
        try {
            return (new Carbon('now', new \DatetimeZone(config('app.timezone'))))->format('d/m/Y H:i');
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }

    /**
     * Power
     *
     * @param array $tabClasses
     * @param $output_dir
     * @param $type
     */
    protected function traitement(array $tabClasses, $output_dir, $type) {

        //Check existence of main dir
        if( ! file_exists($output_dir)) {
            system('mkdir '.$output_dir);
        }

        foreach ($tabClasses as $classeName => $datas) {

            $classeName = $this->removePlural($classeName);

            switch ($type) {
                case 'controller':
                    $filename = $output_dir.'/'.ucfirst($classeName).'Controller.php';
                    break;
                case 'test':
                    $filename = $output_dir.'/'.ucfirst($classeName).'Test.php';
                    break;
                case 'repository':
                    $filename = $output_dir.'/'.ucfirst($classeName).'Repository.php';
                    break;
                case 'resource':
                    $filename = $output_dir.'/'.ucfirst($classeName).'Resource.php';
                    break;
                case 'seeder':
                    $filename = $output_dir.'/'.ucfirst($classeName).'Seeder.php';
                    break;
                default:
                    $filename = $output_dir.'/'.ucfirst($classeName).'.php';
            }

            //Vérifier que le fichier de sortie n'existe pas
            if(file_exists($filename))
            {
                if ( ! $this->confirm('Le fichier <<'.$filename.'>> existe, souhaitez-vous écraser ?'))
                {
                    $this->info('Commande annulée avec succès !');
                    continue;
                }
            }

            $this->info('En cours ...');
            $this->write($classeName, $datas, $filename);

        }

        $this->info('Operations terminées avec succes');

    }

    /**
     * Views generator
     *
     * @param array $tabClasses
     * @param $output_dir
     */
    protected function traitementView(array $tabClasses, $output_dir) {

        //Check existence of main dir
        if( ! file_exists($output_dir)) {
            system('mkdir '.$output_dir);
        }

        foreach ($tabClasses as $classeName => $datas) {

            $classeName = $this->removePlural($classeName);
            $dir_name = $output_dir.'/'.strtolower($classeName).'s';
            $createfile = $dir_name.'/create.blade.php';
            $editfile = $dir_name.'/edit.blade.php';
            $form_createfile = $dir_name.'/form_create.blade.php';
            $indexfile = $dir_name.'/index.blade.php';
            $showfile = $dir_name.'/show.blade.php';

            //Checking dirname existence
            if(file_exists($dir_name))
            {
                if ( ! $this->confirm('Dir <<'.$dir_name.'>> alreally exists, do you wan to erase it ?'))
                {
                    $this->info('Successful cancel this command !');
                    continue;
                }
                else {
                    system('rm -rf '.$dir_name);
                }
            }

            // Created new empty dir
            mkdir($dir_name);

            $this->info('En cours ...');

            $this->simpleWrite($createfile, $this->getCreateContent($classeName, $datas));
            $this->simpleWrite($editfile, $this->getEditContent($classeName, $datas));
            $this->simpleWrite($form_createfile, $this->getFormCreateContent($classeName, $datas));
            $this->simpleWrite($indexfile, $this->getIndexContent($classeName, $datas));
            $this->simpleWrite($showfile, $this->getShowContent($classeName, $datas));

        }

        $this->info('Operations terminées avec succes');

    }

    /**
     * Check before writing into file
     *
     * @param $filename
     * @param $content
     * @return bool
     */
    protected function checkBeforeWrite(string $filename, string $content) {

        //Check the file existence
        if(file_exists($filename)) {
            if ( ! $this->confirm('The file <<'.$filename.'>> already exists, Do you wan to erase it ?')) {
                $this->info('Successful cancel this command !');
                return false;
            }
        }

        $this->simpleWrite($filename, $content);

        return true;

    }

    /**
     * Check and create the output dir
     * @param $output_dir
     */
    protected function checkAndCreateTheOutputDir($output_dir) {
        //Check and create the output dir
        if( ! file_exists($output_dir)) {
            system('mkdir '.$output_dir);
        }
    }

    /**
     * Removing the plural on a word
     *
     * @param string $text
     * @return bool|string
     */
    private function removePlural(string  $text) {
        if(substr($text, (strlen($text) - 1)) == 's') {
            $text = substr($text, 0, (strlen($text) - 1));
        }
        return $text;
    }

    /**
     * Writing the content in the file
     *
     * @param $name - Name of classe
     * @param $chemin
     * @param array $datas - Array of datas
     *
     * @return int|false
     */
    protected function write($name, array $datas, $chemin)
    {
        $name = ucfirst($name);
        $content = $this->getContent($name, $datas);

        return $this->simpleWrite($chemin, $content);
    }

    /**
     * Writing into the filesystem
     * @param $filename
     * @param $content
     * @return bool|int
     */
    protected function simpleWrite($filename, $content) {
        return file_put_contents($filename, $content);
    }

    /**
     * Get the first key name in an array of mapping data
     *
     * @param array $data
     * @return int|string
     */
    private function getFirstKeyName(array $data)
    {
        foreach($data as $field => $attrs) {
            if( ! empty($field) and $field != 'id') {
                return $field;
            }
        }

        return '';
    }

    /**
     * Get the first optional key name in an array of mapping data
     *
     * @param array $data
     * @return int|string
     */
    private function getFirstOptionalName(array $data)
    {
        foreach($data as $field => $attrs) {
            if( ! empty($field) and $field != 'id' and $attrs['nullable']) {
                return $field;
            }
        }

        return '';
    }

    /**
     * Get the first unique key name in an array
     *
     * @param array $data
     * @return int|string
     */
    private function getFirstUniqueName(array $data)
    {
        foreach($data as $field => $attrs) {
            if( ! empty($field) and $field != 'id' and $attrs['unique']) {
                return $field;
            }
        }

        return '';
    }

    /**
     * Get the clause Where on datas
     *
     * @param array $datas
     *
     * @return string
     */
    protected function getSearchWhere(array $datas)
    {
        $ch = '';

        $i = 0;
        foreach($datas as $field => $attrs)
        {
            if(trim($attrs['type']) != 'integer' and ! \Str::endsWith($field, '_id')) {

                if( ! $i++) {
                    $ch .= '
				$query->where(\''.$field.'\', \'LIKE\', \'%\'.$q.\'%\')';
                }
                else {
                    $ch .= '
					->orWhere(\''.$field.'\', \'LIKE\', \'%\'.$q.\'%\')';
                }
            }
        }

        $ch .= $ch ? ';' : '';

        return $ch;
    }

    /**
     * Get elements of header
     *
     * @param array $data
     * @param int $nb
     * @return string
     */
    private function getHeaderTable(array $data, int $nb) : string
    {
        $ret = '';
        $count = 0;

        foreach($data as $field => $attrs) {

            if( ! empty($field) and $field != 'id') {

                if(\Str::endsWith($field, '_id')) {
                    $field = substr($field, 0, stripos($field, '_id'));
                }
                $ret .= '
                        <th class="text-success text-center">'.$this->getDisplayName(ucfirst($field)).'</th>';
                $count++;
            }

            if($count == $nb)
                break;
        }

        return $ret;
    }

    /**
     * Get the table content
     *
     * @param $name
     * @param array $datas
     * @param int $nb
     * @return string
     */
    private function getTdTable($name, array $datas, int $nb) : string
    {
        $lowerName = strtolower($name);
        $ret = '';
        $count = 0;

        foreach($datas as $field => $attrs) {

            if( ! empty($field) and $field != 'id' && $field != 'image') {

                if($count == 0) {
                    $ret .= '
                        <td>
                            <a href="{{ route(\''.$lowerName.'s.show\', [$'.$lowerName.'->id]) }}" title="Voir tous les détails">
                                {{ ucfirst($'.$lowerName.'->'.$field.') }}
                            </a>
                        </td>
                        ';
                }
                else {
                    if(\Str::endsWith($field, '_id')) {

                        $otherClass = substr($field, 0, stripos($field, '_id'));
                        $otherDatas = $this->getDatasForClassName(ucfirst($otherClass).'s', $this->tabClasses);
                        $otherFirstKey = $otherDatas ? $this->getFirstKeyName($otherDatas) : 'name';

                        $cont = '
                            @if($'.$lowerName.'->'.$otherClass.')
                                <a href="{{ route(\''.$otherClass.'s.show\', [$'.$lowerName.'->'.$otherClass.'->id])}}" title="Voir cet élément">
                                    {{ ucfirst($'.$lowerName.'->'.$otherClass.'->'.$otherFirstKey.') }}
                                </a>
                            @endif
                        ';
                    }
                    else {
                        $cont = '{{ ucfirst($' . $lowerName . '->' . $field . ') }}';
                    }

                    $ret .= '
                        <td>'.$cont.'</td>
                        ';
                }

                $count++;
            }

            if($count == $nb)
                break;
        }

        return $ret;
    }

    /**
     * Construct datas to fill the database
     *
     * @param array $data
     * @param $part
     *
     * @return string
     */
    protected function getDonnees(array $data, $part)
    {
        $ch = '[';

        $i = 0;
        foreach($data as $field => $attrs)
        {
            if( ! empty($field) and $field != 'id') {
                if ($part) {
                    if ($i == 0) {
                        $i++;
                        continue;
                    }
                }

                if(\Str::endsWith($field, '_id')) {
                    $otherClasse = substr($field, 0, stripos($field, '_id'));
                    $ch .= '
			\'' . trim($field) . '\' => '.$this->getClassToSearch(ucfirst($otherClasse)).'::create($this->get'.ucfirst($otherClasse).'())->id,';
                }
                elseif (stristr($field, 'password')) {
                    $ch .= '
            \'' . trim($field) . '\' => Hash::make(\'123456\'),';
                }
                else {
                    $ch .= '
			\'' . trim($field) . '\' => ' . $this->valOfType($attrs);
                }
            }
        }

        $ch .= '
		]';

        return $ch;
    }

    /**
     * Get datas for others classes presents in array
     *
     * @param array $datas
     * @return string
     */
    protected function getOthersDatasTable(array $datas)
    {
        $ret = '';
        foreach ($datas as $field => $attrs) {

            if ( ! empty($field) and $field != 'id') {

                if (\Str::endsWith($field, '_id')) {

                    $otherClass = ucfirst(substr($field, 0, stripos($field, '_id')));

                    $classToSearch = $this->getClassToSearch($otherClass);

                    $otherDatas = $this->getDatasForClassName($classToSearch . 's', $this->tabClasses);

                    if($otherDatas) {

                        $ret .= '
    /**
     * Array of datas <<' . $otherClass . '>>
     *
     * @return array
     * @throws \Exception
     */
    protected function get' . $otherClass . '()
    {
         return ' . $this->getDonnees($otherDatas, false) . ';
    }
';
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * Get real class Name
     *
     * @param $className
     * @return string
     */
    protected function getClassToSearch($className) {
        return $className == 'Author' ? 'User' : $className;
    }


    /**
     * Get import Hash header if password exists in array of datas
     *
     * @param array $datas
     * @return string
     */
    protected function getImportHeaders(array $datas) {

        $ret = '';
        if(array_key_exists('password', $datas)) {
            $ret = '
use Illuminate\Support\Facades\Hash;';
        }

        return $ret;
    }

    /**
     * Get the import statement for models
     *
     * @param array $tabClasses
     * @return string
     */
    protected function getImportAllModel(array $tabClasses) {

        $ret = '';
        $i = 0;
        foreach ($tabClasses as $className => $datas) {

            $upperName = ucfirst($this->removePlural($className));
            if ( ! $this->isPivotTable($upperName, $tabClasses) and $upperName != 'Password_reset') {
                $ret .= $i++ ? ', ' : '';
                $ret .= $upperName;
            }
        }

        if($ret) {
            $ret = '
use App\{'.$ret.'};';
        }

        return $ret;
    }

    /**
     * Générer une valeur pour le test d'une donnée en fonction d'un type
     *
     * @param $attrs
     * @return string
     */
    protected function valOfType($attrs) {

        $ret = '';

        switch ($attrs['type']) {
            case 'email':
                $ret = '\Str::random(10).\'@site.com\',';
                break;
            case 'date':
                $ret = 'Carbon::now()->subHours(random_int(1, 10))->format(\'Y-m-d\'),';
                break;
            case 'dateTime':
                $ret = 'Carbon::now()->subHours(random_int(1, 10))->format(\'Y-m-d H:i:s\'),';
                break;
            case 'integer':
            case 'double':
            case 'numeric':
                $ret = 'random_int(10, 100),';
                break;
            case 'boolean':
                $ret = 'random_int(0, 1),';
                break;
            case 'char':
                $ret = '\Str::random(1),';
                break;
            case 'text':
                $ret = '\Str::random(random_int(7, 15)).\' \'.\Str::random(random_int(10, 20)),';
                break;
            default:
                $ret = '\Str::random(random_int(10, 15)),';
        }

        return $ret;

    }

    /**
     * @param array $datas
     * @param int $num
     * @return string
     */
    protected function getUniqueRules(array $datas, $num = 1) {

        $ret = '';
        foreach($datas as $name => $attrs) {
            if($attrs['unique'] and ! empty($name) and $name != 'id') {
                $ret .= '
                \''.$name.'\' => [
                    \'required\',
					\''.$attrs['type'].'\','.($attrs['type'] == 'string' ? '
                    \'max:100\',' : '').'
					Rule::unique($this->table)'.($num == 1 ? '->ignore($id)' : '').',
                ],';
            }
        }

        $ret = '$tab1 = [
                '.$ret.'
            ];';

        return $ret;

    }

    /**
     * Get the validators rules
     *
     * @param array $datas
     * @return string
     */
    protected function getSaveDatas(array $datas) {
        $ret = '';
        foreach($datas as $name => $attrs) {
            if( ! $attrs['unique'] and ! empty($name) and $name != 'id') {

                //Find the relation existence
                $relation = '';
                if(\Str::endsWith($name, '_id')) {
                    $className = substr($name, 0, stripos($name, '_id'));
                     $relation = '
                \'exists:'.$className.'s,id\',';
					$type = 'numeric';
                }
				else {
					//Change text to string
					$type = $attrs['type'] == 'text' ? 'string' : $attrs['type'];
				}

                $ret .= '
            \''.$name.'\' => [
                '.($attrs['nullable'] ? '\'nullable\'' : '\'required\'').',
			    \''.$type.'\','.($type == 'string' ? '
			    \'max:100\',' : '')
                .$relation.'
            ],';
            }
        }

        $ret = '
        $tab2 = [
            '.$ret.'
        ];';

        return $ret;
    }

    /**
     * Get the relationnal associated tables for truncate statement
     *
     * @param array $datas
     * @return string
     */
    protected function getRelationalTruncate(array $datas) : string {
        $ret = '';
        foreach($this->getRelationalsTablesNames($datas) as $className) {
            if (strtolower($className) !== 'user')
                $ret .= '
        '.ucfirst($className).'::truncate();';
        }

        return $ret;
    }

    /**
     * Get the relational associated tables name
     *
     * @param array $datas
     * @return string
     */
    protected function getRelationalTableName(array $datas) : string {
        $ret = '';
        $i = 0;
        foreach($this->getRelationalsTablesNames($datas) as $className) {
            if (strtolower($className) !== 'user') {
                if($i++) $ret .= ', ';
                $ret .= ucfirst($className);
            }
        }

        return $ret;
    }

    /**
     * Get the relationnal associated tables name
     *
     * @param $className
     * @param array $datas
     * @return string
     */
    protected function getStatementsForOthersSelect($className, array $datas) : string {
        $ret = '';
        foreach($this->getRelationalsTablesNames($datas) as $otherClasse) {
            $ret .= '
            $'.$otherClasse.'s = $this->'.$className.'Repository->get'.ucfirst($otherClasse).'ForSelect();';
        }

        return $ret;
    }

    /**
     * Get the relationnal associated tables for compact
     *
     * @param array $datas
     * @param $withCompact
     * @return string
     */
    protected function getStatementsForCompact(array $datas, $withCompact) : string {
        $ret = '';
        $i = 0;
        foreach($this->getRelationalsTablesNames($datas) as $otherClasse) {
            if($i++)
                $ret .= ', ';
            $ret .= '\''.$otherClasse.'s\'';
        }

        if($ret && $withCompact) {
            $ret = ',
            compact('.$ret.')';
        }
        elseif($ret) {
            $ret = ', '.$ret;
        }

        return $ret;
    }

    /**
     * Get all relationnals tables names
     *
     * @param array $datas
     * @return array
     */
    protected function getRelationalsTablesNames(array $datas) : array {

        $ret = [];
        foreach($datas as $name => $attrs) {
            if( ! empty($name) and $name != 'id' and \Str::endsWith($name, '_id')) {
                $className = $name == 'author_id' ? 'User' : substr($name, 0, stripos($name, '_id'));

                if( ! array_search($className, $ret)) {
                    $ret[] = $className;
                }
            }
        }

        return $ret;

    }

    /**
     * Get datas values for a classe
     *
     * @param $classe
     * @param array $tabClasses
     * @return array|mixed
     */
    protected function getDatasForClassName($classe, array $tabClasses) {

        if(array_key_exists($classe, $tabClasses)) {
            return $tabClasses[$classe];
        }
        else {
            return [];
        }
    }

    /**
     * Check in tab of classes if the given name is the pivot table name
     * <p>
     *      e.g: if the name is "use_role" and the tables "users" and "roles" exists
     *      then the "user_role" is the pivot table
     * </p>
     * @param $name
     * @param array $tabClasse
     * @return bool
     */
    protected function isPivotTable($name, array $tabClasse) {

        if(stristr($name, '_')) {
            $tab = explode('_', $name);
            if(count($tab) === 2 and
                array_key_exists(ucfirst($tab[0]).'s', $tabClasse) and
                array_key_exists(ucfirst($tab[1]).'s', $tabClasse)) {
                    return true;
            }
        }

        return false;
    }

    /**
     * Get the values of the relationnal associated tables for create form
     *
     * @param array $datas
     * @return string
     */
    protected function getSelectTableForForm(array $datas) : string {

        $ret = '';
        foreach($this->getRelationalsTablesNames($datas) as $otherClasse) {
            $ret .= '
                    \''.$otherClasse.'s\' => $'.$otherClasse.'s,';
        }

        return $ret;
    }

    /**
     * Get display name from an attribute
     * <p>Example: If the name is "first_name", then the display name must be "First Name"</p>
     *
     * @param string $name
     * @return string
     */
    protected function getDisplayName(string $name) {
        $ret = '';
        $tabWords = explode('_', $name);
        foreach ($tabWords as $word) {
            $ret .= $word. ' ';
        }

        return trim($ret);
    }

    /**
     * Make the trait for all seeders
     *
     * @param array $tabClasses
     * @return string
     */
    protected function getContentCommonForSeeders(array $tabClasses) {

        $ret = '<?php
'.$this->getImportAllModel($tabClasses).'
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Trait CommonForSeeders
 *
 * @author Jerome Dh <jdieuhou@gmail.com>
 * @date '.$this->now().'
 */
trait CommonForSeeders
{
';
        foreach ($tabClasses as $className => $datas) {

            $upperName = ucfirst($this->removePlural($className));
            $ret .= '
    /**
     * Array of datas <<'.$upperName.'>>
     *
     * @return array
     * @throws \Exception
     */
    protected function get'.$upperName.'()
    {
         return '.$this->getDonnees($datas, false).';
    }
';
        }

        $ret .= '
}';
        return $ret;
    }

}
