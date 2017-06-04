<?php

namespace CoRex\Laravel\Model;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModelBuilder
{
    private $connection;
    private $table;
    private $guardedAttributes;
    private $tokens;
    private $schema;
    private $modelParser;

    /**
     * Default model namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Models';

    /**
     * ModelBuilder constructor.
     *
     * @param string $connection
     * @param string $table
     * @throws \Exception
     */
    public function __construct($connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->guardedAttributes = [];
        $this->tokens = [];

        // Get schema.
        if (!$this->getDoctrineSchemaManager()->tablesExist([$this->table])) {
            throw new \Exception('Table [' . $this->table . '] not found.');
        }
        $this->schema = $this->getDoctrineSchemaManager()->listTableDetails($this->table);
        $this->modelParser = new ModelParser($this->getModelFilename());
    }

    /**
     * Get model filename.
     *
     * @return string
     */
    public function getModelFilename()
    {
        return $this->buildFilename('');
    }

    /**
     * Set guarded attributes.
     *
     * @param array $guardedAttributes
     */
    public function setGuardedAttributes(array $guardedAttributes)
    {
        $this->guardedAttributes = $guardedAttributes;
    }

    /**
     * Generate model.
     *
     * @return string
     */
    public function generateModel()
    {
        // Base/stub.
        $stub = file_get_contents(dirname(__DIR__) . '/stubs/model.stub');
        $tokens = $this->getTokens();

        // Namespace.
        $stub = str_replace('{{namespace}}', $tokens['namespace'], $stub);

        // Uses.
        if (count($tokens['uses']) > 0) {
            $uses = $tokens['uses'];
            $uses = array_map(function ($use) {
                return "use " . $use . ";";
            }, $uses);
            $uses[] = '';
            $stub = str_replace('{{uses}}', implode("\n", $uses), $stub);
        }

        // phpDoc properties.
        $phpdocProperties = implode("\n", $tokens['phpdocProperties']);
        $stub = str_replace('{{phpdocProperties}}', $phpdocProperties, $stub);

        // Class.
        $stub = str_replace('{{Class}}', $tokens['Class'], $stub);

        // Extends.
        $stub = str_replace('{{extends}}', ' extends ' . $tokens['extends'], $stub);

        // Constants.
        $constants = $tokens['constants'];
        if (count($constants) > 0) {
            $constLines = ['// Constants.'];
            foreach ($constants as $const => $value) {
                $constLines[] = $this->getIndent() . 'const ' . $const . ' = ' . $value . ';';
            }
            $constLines[] = '';
            $stub = str_replace('{{constants}}', implode("\n", $constLines), $stub);
        }

        // Connection.
        $stub = str_replace('{{connection}}', $tokens['connection'], $stub);

        // Table.
        $stub = str_replace('{{table}}', $tokens['table'], $stub);

        // Fillable.
        $fillableLines = $this->getSplitLines($this->getLinelength(), $tokens['fillable'], $this->getIndent());
        if (count($fillableLines) == 1 && strlen(trim($fillableLines[0])) + 30 < $this->getLinelength()) {
            $fillableString = trim($fillableLines[0]);
        } else {
            $fillableString = "\n" . implode("\n", $fillableLines) . "\n" . $this->getIndent();
        }
        $stub = str_replace('{{fillable}}', $fillableString, $stub);

        // Guarded.
        $guardedLines = $this->getSplitLines($this->getLinelength(), $tokens['guarded'], $this->getIndent());
        if (count($guardedLines) == 1 && strlen(trim($guardedLines[0])) + 30 < $this->getLinelength()) {
            $guardedString = trim($guardedLines[0]);
        } else {
            $guardedString = "\n" . implode("\n", $guardedLines) . "\n" . $this->getIndent();
        }
        $stub = str_replace('{{guarded}}', $guardedString, $stub);

        // Preserved lines.
        $preservedLines = $tokens['preservedLines'];
        if (count($preservedLines) > 0) {
            $preservedLines = array_map(function ($preservedLine) {
                return $this->getIndent() . $preservedLine;
            }, $preservedLines);
            $stub = str_replace('{{preservedLines}}', implode("\n", $preservedLines), $stub);
        }

        // Timestamps.
        $timestamps = $tokens['timestamps'] ? 'true' : 'false';
        $stub = str_replace('{{timestamps}}', $timestamps, $stub);

        return $this->removeTagLines($stub);
    }

    /**
     * Get fillable attributes.
     *
     * @return array
     */
    private function getFillableAttributes()
    {
        $fillableAttributes = [];
        foreach ($this->schema->getColumns() as $name => $column) {
            if (!in_array($name, $this->guardedAttributes)) {
                $fillableAttributes[] = $name;
            }
        }
        return $fillableAttributes;
    }

    /**
     * Get tokens.
     *
     * @return array
     */
    private function getTokens()
    {
        $namespace = Config::value('namespace');
        if (Config::value('addConnection')) {
            $namespace .= '\\' . ucfirst($this->connection);
        }
        $extends = last(explode('\\', Config::value('extends', Constants::DEFAULT_MODEL_CLASS)));
        if (count($this->tokens) == 0) {
            $this->tokens = [];
            $this->tokens['namespace'] = $namespace;
            $this->tokens['uses'] = $this->modelParser->getUses();
            $this->tokens['phpdocProperties'] = $this->getDocProperties();
            $this->tokens['Class'] = Str::studly($this->table);
            $this->tokens['extends'] = $extends;
            $this->tokens['connection'] = $this->connection;
            $this->tokens['table'] = $this->table;
            $this->tokens['fillable'] = $this->getFillableAttributes();
            $this->tokens['guarded'] = $this->guardedAttributes;
            $this->tokens['constants'] = $this->getConstants();
            $this->tokens['preservedLines'] = $this->modelParser->getPreservedLines();
            $this->tokens['timestamps'] = $this->modelParser->getTimestamps();
        }
        return $this->tokens;
    }

    /**
     * Get doc properties.
     *
     * @return array
     * @throws \Exception
     */
    private function getDocProperties()
    {
        $properties = [];
        $fillableAttributes = $this->getFillableAttributes();
        foreach ($this->schema->getColumns() as $name => $column) {
            if (in_array($name, $fillableAttributes)) {

                // Convert types.
                $type = $column->getType()->getName();
                $type = $type == 'varchar' ? 'string' : $type;
                $type = $type == 'longblob' ? 'string' : $type;
                $type = $type == 'longtext' ? 'string' : $type;
                $type = $type == 'datetime' ? 'string' : $type;
                $type = $type == 'date' ? 'string' : $type;
                $type = $type == 'text' ? 'string' : $type;
                $type = $type == 'tinyint' ? 'int' : $type;
                $type = $type == 'bigint' ? 'int' : $type;
                $type = $type == 'smallint' ? 'int' : $type;
                $type = $type == 'timestamp' ? 'int' : $type;
                $properties[] = ' * @property ' . $type . ' $' . $name . ' [' . $this->getAttributes($column) . ']';
            }
        }
        return $properties;
    }

    /**
     * Get attributes.
     *
     * @param Column $column
     * @return string
     */
    private function getAttributes(Column $column)
    {
        $attributes = 'TYPE=' . strtoupper($column->getType()->getName());
        $attributes .= ', NULLABLE=' . intval(!$column->getNotnull());
        $attributes .= ', DEFAULT="' . $column->getDefault() . '"';
        return $attributes;
    }

    /**
     * Get indent.
     *
     * @return string
     */
    private function getIndent()
    {
        return Config::value('indent', Constants::DEFAULT_INDENT);
    }

    /**
     * Get indent.
     *
     * @return string
     */
    private function getLinelength()
    {
        $lineLength = intval(Config::value('length', Constants::DEFAULT_LINE_LENGTH));
        if ($lineLength == 0) {
            $lineLength = Constants::DEFAULT_LINE_LENGTH;
        }
        return $lineLength;
    }

    /**
     * Get split lines.
     *
     * @param integer $length
     * @param array $items
     * @param string $prefix
     * @return array
     */
    private function getSplitLines($length, array $items, $prefix)
    {
        $prefix2 = str_repeat($prefix, 2);
        $lines = [$prefix2];
        if (count($items) == 0) {
            return $lines;
        }
        foreach ($items as $item) {

            // Prepare item.
            $preparedItem = '\'' . $item . '\'';

            // Add line if adding item makes line to long.
            if (strlen($lines[count($lines) - 1] . ' ' . $preparedItem) > $length) {
                $lines[] = $prefix2;
            }

            // Add item.
            if ($lines[count($lines) - 1] != $prefix) {
                $lines[count($lines) - 1] .= ' ';
            }
            $lines[count($lines) - 1] .= $preparedItem;
            if ($item != last($items)) {
                $lines[count($lines) - 1] .= ',';
            }
        }
        return $lines;
    }

    /**
     * Get doctrine schema manager.
     *
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private function getDoctrineSchemaManager()
    {
        return DB::connection($this->connection)->getDoctrineSchemaManager();
    }

    /**
     * Remove tag lines (not used).
     *
     * @param string $stub
     * @return string
     */
    private function removeTagLines($stub)
    {
        $result = [];
        $stub = str_replace("\r", '', $stub);
        $lines = explode("\n", $stub);
        foreach ($lines as $line) {
            if (Str::contains($line, '{{') && Str::contains($line, '}}')) {
                continue;
            }
            $result[] = $line;
        }
        return implode("\n", $result) . "\n";
    }

    /**
     * Get constants.
     *
     * @return array
     * @throws \Exception
     */
    private function getConstants()
    {
        $constSettings = Config::constSettings($this->connection, $this->table);
        $constants = [];
        if ($constSettings !== null) {

            // Extract name of fields.
            if (!isset($constSettings['id'])) {
                throw new \Exception('Field "id" not set.');
            }
            if (!isset($constSettings['name'])) {
                throw new \Exception('Field "name" not set.');
            }
            $idField = $constSettings['id'];
            $nameField = $constSettings['name'];
            $prefix = isset($constSettings['prefix']) ? (string)$constSettings['prefix'] : '';
            $suffix = isset($constSettings['suffix']) ? (string)$constSettings['suffix'] : '';
            $replace = isset($constSettings['replace']) ? $constSettings['replace'] : [];

            // Get data.
            $query = DB::connection($this->connection)->table($this->table);
            if ($idField != '') {
                $query->orderBy($idField);
            }
            $rows = $query->get();
            if (count($rows) == 0) {
                return [];
            }

            // Determine if value is string.
            $quotes = $this->ifStringInRows($rows->toArray(), $idField);

            // Check if fields exists in rows.
            if (!isset($rows[0]->{$idField})) {
                throw new \Exception('Field "' . $idField . '" does not exist in data.');
            }
            if (!isset($rows[0]->{$nameField})) {
                throw new \Exception('Field "' . $nameField . '" does not exist in data.');
            }

            // Find constants.
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $constant = mb_strtoupper($row->{$nameField});
                    $constant = $this->replaceCharacters($constant, $replace);
                    $constant = $prefix . $constant . $suffix;
                    $value = $row->{$idField};
                    if ($quotes) {
                        $value = '\'' . $value . '\'';
                    }
                    $constants[$constant] = $value;
                }
            }
        }
        return $constants;
    }

    /**
     * Determine if key in rows is strings.
     *
     * @param array $rows
     * @param string $key
     * @return boolean
     */
    private function ifStringInRows(array $rows, $key)
    {
        $stringInRows = false;
        if (count($rows) == 0) {
            return $stringInRows;
        }
        foreach ($rows as $row) {
            if (isset($row->{$key}) && !is_numeric($row->{$key})) {
                $stringInRows = true;
            }
        }
        return $stringInRows;
    }

    /**
     * Replace characters.
     *
     * @param string $data
     * @param array $replace
     * @return mixed
     */
    private function replaceCharacters($data, array $replace)
    {
        $data = mb_strtoupper($data);
        $data = str_replace(
            ['-', '.', ',', ';', ':', ' ', '?', '\'', '"', '#', '%', '&', '/', '\\', '(', ')'],
            '_',
            $data
        );
        $replace = array_merge(Constants::STANDARD_REPLACE, $replace);
        foreach ($replace as $from => $to) {
            $data = str_replace(mb_strtoupper($from), mb_strtoupper($to), $data);
        }
        return $data;
    }

    /**
     * Get model filename.
     *
     * @param string $type
     * @return string
     */
    private function buildFilename($type)
    {
        $parts = [Config::value('path')];
        if (Config::value('addConnection')) {
            $parts[] = ucfirst($this->connection);
        }
        $parts[] = Str::studly($this->table) . ucfirst($type) . '.php';
        return implode('/', $parts);
    }
}