<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $nombre
 * @property string $cedula
 * @property string $imagen
 */
class Usuario extends \yii\db\ActiveRecord
{

    public $archivo;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'cedula'], 'required'],
            [['nombre'], 'string', 'max' => 255],
            [['cedula'], 'string', 'max' => 10],
            [['archivo'], 'file', 'extensions' => 'jpg,png'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'cedula' => 'Cedula',
            'archivo' => 'Imagen',
        ];
    }
}
