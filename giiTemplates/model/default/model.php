<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $properties array list of properties (property => [type, name. comment]) */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($properties as $property => $data): ?>
 * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [<?= empty($rules) ? '' : ("\n            " . implode(",\n            ", $rules) . ",\n        ") ?>];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    /**
     * Gets query for [[<?= $name ?>]].
     *
     * @return <?= $relationsClassHints[$name] . "\n" ?>
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * {@inheritdoc}
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>

<?php
if (isset($labels['created_at']) || isset($labels['updated_at']) || isset($labels['created_by']) || isset($labels['updated_by'])):
?>
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
<?php
    /**
     * 1. $this->created_at = $this->updated_at = time();
     * 2. $this->created_by = $this->updated_by = Yii::$app->getUser()->getId();
     * 3. $this->updated_at = time();
     * 4. $this->updated_by = Yii::$app->getUser()->getId();
     */
    $line1 = '';
    $line2 = '';
    $line3 = '';
    $line4 = '';
    if (isset($labels['created_at'])) {
        $line1 = '$this->created_at';
    }
    if (isset($labels['updated_at'])) {
        if ($line1) {
            $line1 .= ' = $this->updated_at';
        } else {
            $line1 = '$this->updated_at';
        }
        $line3 = '$this->updated_at';
    }

    if (isset($labels['created_by'])) {
        if ($line2) {
            $line2 .= ' = $this->created_by';
        } else {
            $line2 = '$this->created_by';
        }
    }
    if (isset($labels['updated_by'])) {
        if ($line2) {
            $line2 .= ' = $this->updated_by';
        } else {
            $line2 = '$this->updated_by';
        }
        $line4 = '$this->updated_by';
    }
    $line1 && $line1 .= ' = time();';
    $line3 && $line3 .= ' = time();';
    $line2 && $line2 .= ' = Yii::$app->getUser()->getId();';
    $line4 && $line4 .= ' = Yii::$app->getUser()->getId();';

?>
            if ($insert) {
    <?= $line1 . "\n" ?>
    <?= $line2 . "\n" ?>
    } else {
    <?= $line3 . "\n" ?>
    <?= $line4 ?>
    }

            return true;
        } else {
            return false;
        }
    }
<?php endif; ?>
}
